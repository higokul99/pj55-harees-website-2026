from django.contrib import admin
from .models import Order, OrderItem

class OrderItemInline(admin.TabularInline):
    model = OrderItem
    extra = 0
    readonly_fields = ['product', 'product_code', 'table_name', 'quantity', 'price', 'metal_cost', 'making_charges', 'gst', 'metal_type']

@admin.register(Order)
class OrderAdmin(admin.ModelAdmin):
    list_display = ['id', 'merchant_order_id', 'fullname', 'email', 'final_amount', 'payment_status', 'status', 'created_at']
    list_filter = ['payment_status', 'status', 'delivery_type', 'created_at']
    search_fields = ['id', 'merchant_order_id', 'fullname', 'email', 'phone']
    inlines = [OrderItemInline]
