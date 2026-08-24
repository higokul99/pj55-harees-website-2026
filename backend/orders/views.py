from rest_framework import viewsets, status, permissions
from rest_framework.response import Response
from rest_framework.views import APIView
from django.shortcuts import get_object_or_404
from django.conf import settings
from cart.models import CartItem
from .models import Order, OrderItem
from .serializers import OrderSerializer
import uuid
import hashlib
import base64
import requests
import json

class CheckoutView(APIView):
    permission_classes = [permissions.IsAuthenticated]

    def post(self, request):
        user = request.user
        cart_items = CartItem.objects.filter(user=user)

        if not cart_items.exists():
            return Response({
                "success": False,
                "message": "Your shopping cart is empty."
            }, status=status.HTTP_400_BAD_REQUEST)

        # Retrieve request details
        fullname = request.data.get('fullname', user.fullname)
        email = request.data.get('email', user.email)
        phone = request.data.get('phone', user.phone)
        pincode = request.data.get('pincode', user.pincode)
        address = request.data.get('address', user.address1)
        city = request.data.get('city', user.city)
        state = request.data.get('state', user.state)
        delivery_type = request.data.get('delivery_type', 'home')

        # Calculate pricing
        subtotal = sum(float(item.product.price or 0) * item.quantity for item in cart_items)
        gst_tax = subtotal * 0.03  # 3% GST on jewelry
        total_amount = subtotal
        final_amount = subtotal + gst_tax

        # Generate transaction merchant order id
        merchant_order_id = f"TXN-{uuid.uuid4().hex[:12].upper()}"

        # Create Order
        order = Order.objects.create(
            user=user,
            fullname=fullname,
            email=email,
            phone=phone,
            pincode=pincode,
            address=address,
            city=city,
            state=state,
            delivery_type=delivery_type,
            total_amount=total_amount,
            final_amount=final_amount,
            payment_method='phonepe',
            payment_status='pending',
            status='pending',
            merchant_order_id=merchant_order_id
        )

        # Create OrderItems
        from decimal import Decimal
        for item in cart_items:
            OrderItem.objects.create(
                order=order,
                product=item.product,
                product_code=item.product_code,
                table_name=item.table_name,
                quantity=item.quantity,
                price=item.product.price or Decimal('0.00'),
                metal_cost=(item.product.price or Decimal('0.00')) * Decimal('0.8'),
                making_charges=(item.product.price or Decimal('0.00')) * Decimal('0.17'),
                gst=(item.product.price or Decimal('0.00')) * Decimal('0.03'),
                metal_type=item.product.metal.name if item.product.metal else 'Gold'
            )

        # Clear user's cart
        cart_items.delete()

        # Build PhonePe Payload
        payload = {
            "merchantId": settings.PHONEPE_MERCHANT_ID,
            "merchantTransactionId": merchant_order_id,
            "merchantUserId": f"USER_{user.id}",
            "amount": int(float(final_amount) * 100),  # Convert to Paise
            "redirectUrl": f"http://localhost:8080/payment-status/{merchant_order_id}",
            "redirectMode": "REDIRECT",
            "callbackUrl": "http://localhost:8000/api/v1/orders/phonepe/simulate/",
            "paymentInstrument": {
                "type": "PAY_PAGE"
            }
        }

        # Base64 encode the payload
        json_payload = json.dumps(payload)
        base64_payload = base64.b64encode(json_payload.encode('utf-8')).decode('utf-8')

        # Checksum calculation: SHA256(Base64_Payload + "/pg/v1/pay" + SaltKey) + "###" + SaltIndex
        string_to_hash = base64_payload + "/pg/v1/pay" + settings.PHONEPE_SALT_KEY
        hash_value = hashlib.sha256(string_to_hash.encode('utf-8')).hexdigest()
        x_verify = f"{hash_value}###{settings.PHONEPE_SALT_INDEX}"

        headers = {
            "Content-Type": "application/json",
            "X-VERIFY": x_verify
        }

        phonepe_url = "https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay"
        
        try:
            response = requests.post(phonepe_url, json={"request": base64_payload}, headers=headers, timeout=10)
            response_data = response.json()
            
            if response_data.get("success") and "data" in response_data:
                payment_url = response_data["data"]["instrumentResponse"]["redirectInfo"]["url"]
            else:
                # Fallback to simulation page if API configuration errors out
                payment_url = f"http://localhost:8080/payment-sim/{merchant_order_id}"
        except Exception:
            # Fallback to simulation page if offline or timed out
            payment_url = f"http://localhost:8080/payment-sim/{merchant_order_id}"

        return Response({
            "success": True,
            "message": "Order created successfully. Redirecting to payment...",
            "data": {
                "order_id": order.id,
                "merchant_order_id": merchant_order_id,
                "payment_url": payment_url,
                "amount": float(final_amount)
            }
        }, status=status.HTTP_201_CREATED)

class PhonePeStatusView(APIView):
    permission_classes = [permissions.IsAuthenticated]

    def get(self, request, merchant_order_id):
        order = get_object_or_404(Order, merchant_order_id=merchant_order_id, user=request.user)

        # Query PhonePe status: GET /pg/v1/status/{merchantId}/{merchantTransactionId}
        path = f"/pg/v1/status/{settings.PHONEPE_MERCHANT_ID}/{merchant_order_id}"
        string_to_hash = path + settings.PHONEPE_SALT_KEY
        hash_value = hashlib.sha256(string_to_hash.encode('utf-8')).hexdigest()
        x_verify = f"{hash_value}###{settings.PHONEPE_SALT_INDEX}"

        headers = {
            "Content-Type": "application/json",
            "X-VERIFY": x_verify,
            "X-MERCHANT-ID": settings.PHONEPE_MERCHANT_ID
        }

        status_url = f"https://api-preprod.phonepe.com/apis/pg-sandbox{path}"
        
        try:
            response = requests.get(status_url, headers=headers, timeout=10)
            response_data = response.json()
            
            if response_data.get("success") and response_data.get("code") == "PAYMENT_SUCCESS":
                order.payment_status = 'completed'
                order.status = 'processing'
                order.phonepe_transaction_id = response_data["data"].get("transactionId", f"T-{uuid.uuid4().hex[:12].upper()}")
                order.save()
            elif response_data.get("code") in ["PAYMENT_ERROR", "INTERNAL_SERVER_ERROR"]:
                order.payment_status = 'failed'
                order.status = 'cancelled'
                order.save()
        except Exception:
            pass # Keep current state if status check fails (or simulation succeeded)

        return Response({
            "success": True,
            "data": {
                "order_id": order.id,
                "merchant_order_id": order.merchant_order_id,
                "payment_status": order.payment_status,
                "status": order.status,
                "final_amount": float(order.final_amount)
            }
        })

class PhonePeSimulateCallbackView(APIView):
    permission_classes = [permissions.AllowAny]

    def post(self, request):
        merchant_order_id = request.data.get('merchant_order_id')
        payment_status = request.data.get('payment_status', 'completed')

        if not merchant_order_id:
            return Response({"success": False, "message": "Merchant order ID is required"}, status=status.HTTP_400_BAD_REQUEST)

        order = get_object_or_404(Order, merchant_order_id=merchant_order_id)
        
        if payment_status == 'completed':
            order.payment_status = 'completed'
            order.status = 'processing'
            order.phonepe_transaction_id = f"T-{uuid.uuid4().hex[:12].upper()}"
            order.save()
            return Response({"success": True, "message": "Payment simulation: Completed."})
        else:
            order.payment_status = 'failed'
            order.status = 'cancelled'
            order.save()
            return Response({"success": True, "message": "Payment simulation: Failed."})

class MyOrdersViewSet(viewsets.ReadOnlyModelViewSet):
    serializer_class = OrderSerializer
    permission_classes = [permissions.IsAuthenticated]

    def get_queryset(self):
        return Order.objects.filter(user=self.request.user).order_by('-id')
