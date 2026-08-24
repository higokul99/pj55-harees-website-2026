from rest_framework import serializers
from .models import CartItem, WishlistItem
from products.serializers import ProductListSerializer

class CartItemSerializer(serializers.ModelSerializer):
    product_details = ProductListSerializer(source='product', read_only=True)
    item_total_price = serializers.SerializerMethodField()

    class Meta:
        model = CartItem
        fields = ['id', 'product', 'product_code', 'table_name', 'quantity', 'product_details', 'item_total_price']
        read_only_fields = ['product_code', 'table_name']

    def get_item_total_price(self, obj):
        if obj.product.price:
            return float(obj.product.price) * obj.quantity
        return 0.00

class WishlistItemSerializer(serializers.ModelSerializer):
    product_details = ProductListSerializer(source='product', read_only=True)

    class Meta:
        model = WishlistItem
        fields = ['id', 'product', 'table_name', 'product_details']
