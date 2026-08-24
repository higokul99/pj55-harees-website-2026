from rest_framework import serializers
from django.contrib.auth import get_user_model
from django.contrib.auth.hashers import make_password

User = get_user_model()

class UserRegisterSerializer(serializers.ModelSerializer):
    password = serializers.CharField(write_only=True, required=True, style={'input_type': 'password'})

    class Meta:
        model = User
        fields = [
            'id', 'fullname', 'name', 'email', 'phone', 'password',
            'security_question', 'security_answer', 'address1', 'address2',
            'city', 'state', 'pincode', 'dob', 'anniversary', 'landmark'
        ]

    def create(self, validated_data):
        # Hash password before saving
        validated_data['password'] = make_password(validated_data['password'])
        return super().create(validated_data)

class UserProfileSerializer(serializers.ModelSerializer):
    class Meta:
        model = User
        fields = [
            'id', 'fullname', 'name', 'email', 'phone',
            'security_question', 'address1', 'address2',
            'city', 'state', 'pincode', 'dob', 'anniversary', 'landmark'
        ]
        read_only_fields = ['email'] # Email is typically not allowed to change to maintain unique identity
