from django.contrib import admin
from django.urls import path, include
from drf_spectacular.views import (
    SpectacularAPIView,
    SpectacularRedocView,
    SpectacularSwaggerView,
)

urlpatterns = [
    path('admin/', admin.site.urls),
    
    # OpenAPI Schema & Interactive Swagger / ReDoc API Documentation
    path('api/schema/', SpectacularAPIView.as_view(), name='schema'),
    path('api/docs/', SpectacularSwaggerView.as_view(url_name='schema'), name='swagger-ui'),
    path('api/redoc/', SpectacularRedocView.as_view(url_name='schema'), name='redoc'),

    path('api/v1/auth/', include('accounts.urls')),
    path('api/v1/schemes/', include('schemes.urls')),
    path('api/v1/', include('cart.urls')),
    path('api/v1/', include('orders.urls')),
    path('api/v1/', include('products.urls')),
]
