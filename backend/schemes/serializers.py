from rest_framework import serializers
from .models import GoldScheme, UserScheme, SchemePayment

class GoldSchemeSerializer(serializers.ModelSerializer):
    class Meta:
        model = GoldScheme
        fields = '__all__'

class SchemePaymentSerializer(serializers.ModelSerializer):
    class Meta:
        model = SchemePayment
        fields = ['id', 'amount', 'payment_date', 'receipt_no']

class UserSchemeSerializer(serializers.ModelSerializer):
    payments = SchemePaymentSerializer(many=True, read_only=True)
    bonus_amount = serializers.SerializerMethodField()
    final_value = serializers.SerializerMethodField()

    class Meta:
        model = UserScheme
        fields = [
            'id', 'scheme_type', 'scheme_name', 'monthly_amount',
            'start_date', 'status', 'code', 'months_completed',
            'payments', 'bonus_amount', 'final_value'
        ]

    def get_bonus_amount(self, obj):
        scheme = GoldScheme.objects.filter(scheme_code=obj.scheme_type).first()
        return scheme.bonus_amount if scheme else 0.00

    def get_final_value(self, obj):
        scheme = GoldScheme.objects.filter(scheme_code=obj.scheme_type).first()
        return scheme.final_value if scheme else 0.00
