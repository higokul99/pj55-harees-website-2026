from django.urls import path, include
from rest_framework.routers import DefaultRouter
from .views import CheckoutView, PhonePeStatusView, PhonePeSimulateCallbackView, MyOrdersViewSet

router = DefaultRouter()
router.register(r'my-orders', MyOrdersViewSet, basename='my-orders')

urlpatterns = [
    path('checkout/', CheckoutView.as_view(), name='order_checkout'),
    path('phonepe/status/<str:merchant_order_id>/', PhonePeStatusView.as_view(), name='phonepe_payment_status'),
    path('phonepe/simulate/', PhonePeSimulateCallbackView.as_view(), name='phonepe_simulate_callback'),
    path('', include(router.urls)),
]
