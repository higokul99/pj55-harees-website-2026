from rest_framework import status, generics, permissions
from rest_framework.response import Response
from rest_framework.views import APIView
from rest_framework_simplejwt.views import TokenObtainPairView
from rest_framework_simplejwt.serializers import TokenObtainPairSerializer
from django.contrib.auth import get_user_model
from django.contrib.auth.hashers import make_password
from .serializers import UserRegisterSerializer, UserProfileSerializer

User = get_user_model()

# Custom Token Obtain Pair Serializer to include user details
class CustomTokenObtainPairSerializer(TokenObtainPairSerializer):
    def validate(self, attrs):
        data = super().validate(attrs)
        serializer = UserProfileSerializer(self.user)
        data['user'] = serializer.data
        return data

class CustomTokenObtainPairView(TokenObtainPairView):
    serializer_class = CustomTokenObtainPairSerializer

# Registration View
class RegisterView(generics.CreateAPIView):
    queryset = User.objects.all()
    permission_classes = [permissions.AllowAny]
    serializer_class = UserRegisterSerializer

    def create(self, request, *args, **kwargs):
        serializer = self.get_serializer(data=request.data)
        serializer.is_valid(raise_exception=True)
        self.perform_create(serializer)
        headers = self.get_success_headers(serializer.data)
        return Response({
            "success": True,
            "message": "User registered successfully",
            "data": serializer.data
        }, status=status.HTTP_201_CREATED, headers=headers)

# Profile View (Retrieve / Update)
class ProfileView(generics.RetrieveUpdateAPIView):
    permission_classes = [permissions.IsAuthenticated]
    serializer_class = UserProfileSerializer

    def get_object(self):
        return self.request.user

    def retrieve(self, request, *args, **kwargs):
        instance = self.get_object()
        serializer = self.get_serializer(instance)
        return Response({
            "success": True,
            "message": "Profile retrieved successfully",
            "data": serializer.data
        })

    def update(self, request, *args, **kwargs):
        partial = kwargs.pop('partial', False)
        instance = self.get_object()
        serializer = self.get_serializer(instance, data=request.data, partial=partial)
        serializer.is_valid(raise_exception=True)
        self.perform_update(serializer)
        return Response({
            "success": True,
            "message": "Profile updated successfully",
            "data": serializer.data
        })

# Forgot Password View - Step 1: Fetch security question
class ForgotPasswordView(APIView):
    permission_classes = [permissions.AllowAny]

    def post(self, request):
        email = request.data.get('email')
        if not email:
            return Response({
                "success": False,
                "message": "Email is required"
            }, status=status.HTTP_400_BAD_REQUEST)
        
        try:
            user = User.objects.get(email=email)
            return Response({
                "success": True,
                "data": {
                    "email": user.email,
                    "security_question": user.security_question
                }
            })
        except User.DoesNotExist:
            return Response({
                "success": False,
                "message": "No account found with this email address"
            }, status=status.HTTP_404_NOT_FOUND)

# Reset Password View - Step 2: Verify answer and change password
class ResetPasswordView(APIView):
    permission_classes = [permissions.AllowAny]

    def post(self, request):
        email = request.data.get('email')
        security_answer = request.data.get('security_answer')
        new_password = request.data.get('new_password')

        if not all([email, security_answer, new_password]):
            return Response({
                "success": False,
                "message": "Email, security answer, and new password are required"
            }, status=status.HTTP_400_BAD_REQUEST)

        try:
            user = User.objects.get(email=email)
            # Standardizing verification (case-insensitive strip)
            if user.security_answer.strip().lower() == security_answer.strip().lower():
                user.password = make_password(new_password)
                user.save()
                return Response({
                    "success": True,
                    "message": "Password has been reset successfully"
                })
            else:
                return Response({
                    "success": False,
                    "message": "Incorrect answer to security question"
                }, status=status.HTTP_400_BAD_REQUEST)
        except User.DoesNotExist:
            return Response({
                "success": False,
                "message": "User not found"
            }, status=status.HTTP_404_NOT_FOUND)
