from django.contrib import admin
from .models import GoldScheme, UserScheme, SchemePayment

class SchemePaymentInline(admin.TabularInline):
    model = SchemePayment
    extra = 0
    readonly_fields = ['payment_date', 'receipt_no', 'amount']

@admin.register(UserScheme)
class UserSchemeAdmin(admin.ModelAdmin):
    list_display = ['code', 'user', 'scheme_name', 'monthly_amount', 'months_completed', 'status', 'start_date']
    list_filter = ['status', 'scheme_type', 'start_date']
    search_fields = ['code', 'user__email', 'scheme_name']
    inlines = [SchemePaymentInline]

@admin.register(GoldScheme)
class GoldSchemeAdmin(admin.ModelAdmin):
    list_display = ['scheme_name', 'scheme_code', 'monthly_installment', 'bonus_amount', 'final_value', 'status']
    list_filter = ['status']

@admin.register(SchemePayment)
class SchemePaymentAdmin(admin.ModelAdmin):
    list_display = ['receipt_no', 'user', 'scheme', 'amount', 'payment_date']
    search_fields = ['receipt_no', 'user__email', 'scheme__code']
