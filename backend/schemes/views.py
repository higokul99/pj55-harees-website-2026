from rest_framework import viewsets, generics, permissions, status
from rest_framework.response import Response
from rest_framework.decorators import action
from django.shortcuts import get_object_or_404
from django.utils import timezone
import hashlib
import random
import uuid
from .models import GoldScheme, UserScheme, SchemePayment
from .serializers import GoldSchemeSerializer, UserSchemeSerializer, SchemePaymentSerializer

class GoldSchemeListView(generics.ListAPIView):
    queryset = GoldScheme.objects.filter(status='active')
    serializer_class = GoldSchemeSerializer
    permission_classes = [permissions.AllowAny]

class MySchemesViewSet(viewsets.ModelViewSet):
    serializer_class = UserSchemeSerializer
    permission_classes = [permissions.IsAuthenticated]

    def get_queryset(self):
        return UserScheme.objects.filter(user=self.request.user).order_by('-id')

    @action(detail=False, methods=['post'])
    def enroll(self, request):
        scheme_code = request.data.get('scheme_code')
        if not scheme_code:
            return Response({"success": False, "message": "Scheme code is required"}, status=status.HTTP_400_BAD_REQUEST)

        gold_scheme = get_object_or_404(GoldScheme, scheme_code=scheme_code, status='active')

        # Check for completed but active schemes & auto-archive
        UserScheme.objects.filter(
            user=request.user,
            months_completed__gte=11,
            status='active'
        ).update(status='completed')

        # Check for existing active schemes (legacy only permits one active scheme at a time)
        has_active = UserScheme.objects.filter(user=request.user, status='active').exists()
        if has_active:
            return Response({
                "success": False,
                "message": "You already have an active scheme. Complete it before enrolling in a new one."
            }, status=status.HTTP_400_BAD_REQUEST)

        # Generate unique Scheme Number (GS-HASH-USERID-RAND)
        user_id = request.user.id
        user_hash = hashlib.md5(str(user_id).encode()).hexdigest()[:4].upper()
        padded_id = str(user_id).zfill(4)
        rand = random.randint(100, 999)
        scheme_number = f"GS-{user_hash}-{padded_id}-{rand}"

        user_scheme = UserScheme.objects.create(
            user=request.user,
            scheme_type=gold_scheme.scheme_code,
            scheme_name=gold_scheme.scheme_name,
            monthly_amount=gold_scheme.monthly_installment,
            start_date=timezone.now().date(),
            status='active',
            code=scheme_number,
            months_completed=0
        )

        return Response({
            "success": True,
            "message": f"Successfully enrolled in {gold_scheme.scheme_name}!",
            "data": UserSchemeSerializer(user_scheme).data
        }, status=status.HTTP_201_CREATED)

    @action(detail=True, methods=['post'])
    def pay(self, request, pk=None):
        user_scheme = self.get_object()

        if user_scheme.status != 'active':
            return Response({
                "success": False,
                "message": "This scheme is already completed or inactive."
            }, status=status.HTTP_400_BAD_REQUEST)

        # Check if they already paid this month
        current_month = timezone.now().month
        current_year = timezone.now().year
        already_paid = user_scheme.payments.filter(
            payment_date__month=current_month,
            payment_date__year=current_year
        ).exists()

        if already_paid:
            return Response({
                "success": False,
                "message": "This month's installment payment is already recorded."
            }, status=status.HTTP_400_BAD_REQUEST)

        amount = user_scheme.monthly_amount
        receipt_no = f"RCPT-{uuid.uuid4().hex[:12].upper()}"

        payment = SchemePayment.objects.create(
            user=request.user,
            scheme=user_scheme,
            amount=amount,
            receipt_no=receipt_no
        )

        # Increment months completed
        user_scheme.months_completed += 1
        if user_scheme.months_completed >= 11:
            user_scheme.status = 'completed'
            user_scheme.save()
            message = "Congratulations! You have completed all 11 installments of your Gold Scheme!"
        else:
            user_scheme.save()
            message = f"Payment of ₹{amount} recorded successfully."

        return Response({
            "success": True,
            "message": message,
            "data": {
                "receipt_no": receipt_no,
                "amount": amount,
                "months_completed": user_scheme.months_completed,
                "status": user_scheme.status
            }
        })

    @action(detail=True, methods=['get'])
    def passbook(self, request, pk=None):
        user_scheme = self.get_object()
        serializer = self.get_serializer(user_scheme)
        payments = user_scheme.payments.all().order_by('payment_date')
        
        total_paid = sum(p.amount for p in payments)
        gold_scheme = GoldScheme.objects.filter(scheme_code=user_scheme.scheme_type).first()
        bonus = gold_scheme.bonus_amount if gold_scheme else 0.00
        final_value = gold_scheme.final_value if gold_scheme else 0.00

        return Response({
            "success": True,
            "data": {
                "scheme": serializer.data,
                "total_paid": total_paid,
                "bonus": bonus,
                "final_value": final_value,
                "payments": SchemePaymentSerializer(payments, many=True).data
            }
        })
