from django.db import models
from django.conf import settings

class GoldScheme(models.Model):
    STATUS_CHOICES = [
        ('active', 'Active'),
        ('inactive', 'Inactive'),
    ]

    scheme_code = models.CharField(max_length=50, unique=True)
    scheme_name = models.CharField(max_length=255)
    monthly_installment = models.DecimalField(max_digits=10, decimal_places=2)
    bonus_amount = models.DecimalField(max_digits=10, decimal_places=2)
    final_value = models.DecimalField(max_digits=10, decimal_places=2)
    status = models.CharField(max_length=15, choices=STATUS_CHOICES, default='active')
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"{self.scheme_name} ({self.scheme_code})"

class UserScheme(models.Model):
    STATUS_CHOICES = [
        ('active', 'Active'),
        ('completed', 'Completed'),
    ]

    user = models.ForeignKey(settings.AUTH_USER_MODEL, on_delete=models.CASCADE, related_name='schemes')
    scheme_type = models.CharField(max_length=50) # matches GoldScheme.scheme_code
    scheme_name = models.CharField(max_length=255)
    monthly_amount = models.DecimalField(max_digits=10, decimal_places=2)
    start_date = models.DateField()
    status = models.CharField(max_length=15, choices=STATUS_CHOICES, default='active')
    code = models.CharField(max_length=100, unique=True)
    months_completed = models.IntegerField(default=0)
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"{self.code} - {self.user.email} ({self.status})"

class SchemePayment(models.Model):
    user = models.ForeignKey(settings.AUTH_USER_MODEL, on_delete=models.CASCADE, related_name='scheme_payments')
    scheme = models.ForeignKey(UserScheme, on_delete=models.CASCADE, related_name='payments')
    amount = models.DecimalField(max_digits=10, decimal_places=2)
    payment_date = models.DateTimeField(auto_now_add=True)
    receipt_no = models.CharField(max_length=100, unique=True)

    def __str__(self):
        return f"{self.receipt_no} - {self.amount}"
