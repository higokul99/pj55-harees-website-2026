from rest_framework import serializers
from .models import Order, OrderItem
from products.serializers import ProductListSerializer

class OrderItemSerializer(serializers.ModelSerializer):
    class Meta:
        model = OrderItem
        fields = ['id', 'product', 'product_code', 'table_name', 'quantity', 'price', 'metal_cost', 'making_charges', 'gst', 'metal_type']

class OrderSerializer(serializers.ModelSerializer):
    items = OrderItemSerializer(many=True, read_only=True)

    class Meta:
        model = Order
        fields = [
            'id', 'fullname', 'email', 'phone', 'pincode', 'address', 'city', 'state',
            'delivery_type', 'total_amount', 'discount_amount', 'final_amount',
            'payment_method', 'payment_status', 'status', 'merchant_order_id',
            'phonepe_order_id', 'phonepe_transaction_id', 'created_at', 'items'
        ]
        read_only_fields = ['total_amount', 'final_amount', 'payment_status', 'status', 'merchant_order_id', 'phonepe_order_id', 'phonepe_transaction_id']
