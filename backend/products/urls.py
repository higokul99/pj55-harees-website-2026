from django.urls import path, include
from rest_framework.routers import DefaultRouter
from .views import CategoryViewSet, ProductViewSet, SearchSuggestionsView, LatestGoldRatesView

router = DefaultRouter()
router.register(r'categories', CategoryViewSet, basename='category')
router.register(r'products', ProductViewSet, basename='product')

urlpatterns = [
    path('', include(router.urls)),
    path('search/suggestions/', SearchSuggestionsView.as_view(), name='search_suggestions'),
    path('gold-rates/', LatestGoldRatesView.as_view(), name='gold_rates'),
]
