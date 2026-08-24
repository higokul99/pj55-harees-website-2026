from django.urls import path, include
from rest_framework.routers import DefaultRouter
from .views import GoldSchemeListView, MySchemesViewSet

router = DefaultRouter()
router.register(r'my-schemes', MySchemesViewSet, basename='my-schemes')

urlpatterns = [
    path('plans/', GoldSchemeListView.as_view(), name='gold_schemes_plans'),
    path('', include(router.urls)),
]
