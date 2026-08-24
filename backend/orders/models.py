from django.db import models
from django.conf import settings
from products.models import Product

class Order(models.Model):
    DELIVERY_CHOICES = [
        ('home', 'Home Delivery'),
        ('pickup', 'In-Store Pickup'),
    ]

    PAYMENT_STATUS_CHOICES = [
        ('pending', 'Pending'),
        ('completed', 'Completed'),
        ('failed', 'Failed'),
        ('refunded', 'Refunded'),
    ]

    STATUS_CHOICES = [
        ('pending', 'Pending'),
        ('processing', 'Processing'),
        ('completed', 'Completed'),
        ('cancelled', 'Cancelled'),
    ]

    user = models.ForeignKey(settings.AUTH_USER_MODEL, on_delete=models.CASCADE, related_name='orders')
    fullname = models.CharField(max_length=255)
    email = models.EmailField()
    phone = models.CharField(max_length=20)
    pincode = models.CharField(max_length=20)
    address = models.TextField()
    city = models.CharField(max_length=100)
    state = models.CharField(max_length=100)
    delivery_type = models.CharField(max_length=10, choices=DELIVERY_CHOICES, default='home')

    total_amount = models.DecimalField(max_digits=12, decimal_places=2)
    discount_amount = models.DecimalField(max_digits=12, decimal_places=2, default=0.00)
    final_amount = models.DecimalField(max_digits=12, decimal_places=2)

    payment_method = models.CharField(max_length=50, null=True, blank=True)
    payment_status = models.CharField(max_length=20, choices=PAYMENT_STATUS_CHOICES, default='pending')
    status = models.CharField(max_length=20, choices=STATUS_CHOICES, default='pending')

    merchant_order_id = models.CharField(max_length=64, unique=True, null=True, blank=True)
    phonepe_order_id = models.CharField(max_length=64, null=True, blank=True)
    phonepe_transaction_id = models.CharField(max_length=64, null=True, blank=True)

    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    def __str__(self):
        return f"Order #{self.id} by {self.user.email} (Status: {self.status})"

class OrderItem(models.Model):
    order = models.ForeignKey(Order, on_delete=models.CASCADE, related_name='items')
    product = models.ForeignKey(Product, on_delete=models.SET_NULL, null=True, blank=True)
    product_code = models.CharField(max_length=100)
    table_name = models.CharField(max_length=100)
    quantity = models.IntegerField()
    price = models.DecimalField(max_digits=12, decimal_places=2)

    metal_cost = models.DecimalField(max_digits=12, decimal_places=2, default=0.00)
    making_charges = models.DecimalField(max_digits=12, decimal_places=2, default=0.00)
    gst = models.DecimalField(max_digits=12, decimal_places=2, default=0.00)
    metal_type = models.CharField(max_length=50, null=True, blank=True)

    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    def __str__(self):
        return f"{self.product_code} (Qty: {self.quantity})"
