from rest_framework import viewsets, generics, permissions, status
from rest_framework.response import Response
from rest_framework.views import APIView
from django.db.models import Q
from django.shortcuts import get_object_or_404
from .models import Metal, MetalPurity, GoldRate, Category, Product, ProductImage
from .serializers import (
    CategorySerializer, ProductListSerializer, ProductDetailSerializer, GoldRateSerializer
)

class CategoryViewSet(viewsets.ReadOnlyModelViewSet):
    queryset = Category.objects.filter(is_active=True)
    serializer_class = CategorySerializer
    permission_classes = [permissions.AllowAny]
    lookup_field = 'slug'

class ProductViewSet(viewsets.ReadOnlyModelViewSet):
    permission_classes = [permissions.AllowAny]

    def get_queryset(self):
        queryset = Product.objects.filter(is_visible=True, delist=False)
        
        # Filtering parameters
        category_slug = self.request.query_params.get('category')
        metal_id = self.request.query_params.get('metal')
        gender = self.request.query_params.get('gender')
        is_featured = self.request.query_params.get('featured')
        search_query = self.request.query_params.get('search')

        if category_slug:
            queryset = queryset.filter(category__slug=category_slug)
        if metal_id:
            queryset = queryset.filter(metal_id=metal_id)
        if gender:
            queryset = queryset.filter(gender=gender)
        if is_featured:
            queryset = queryset.filter(is_featured=True)
        if search_query:
            queryset = queryset.filter(
                Q(name__icontains=search_query) |
                Q(sku__icontains=search_query) |
                Q(product_code__icontains=search_query) |
                Q(search_keywords__icontains=search_query)
            )
        return queryset

    def get_serializer_class(self):
        if self.action == 'retrieve':
            return ProductDetailSerializer
        return ProductListSerializer

    def retrieve(self, request, *args, **kwargs):
        # Allow looking up products by either ID (digit) or Slug (string)
        lookup = self.kwargs.get('pk')
        queryset = self.get_queryset()
        
        if lookup.isdigit():
            product = get_object_or_404(queryset, pk=lookup)
        else:
            product = get_object_or_404(queryset, slug=lookup)
            
        serializer = self.get_serializer(product)
        return Response({
            "success": True,
            "data": serializer.data
        })

# Search Suggestions view for live search autocomplete
class SearchSuggestionsView(APIView):
    permission_classes = [permissions.AllowAny]

    def get(self, request):
        query = request.query_params.get('q', '').strip()
        if not query:
            return Response({"success": True, "suggestions": []})

        # Match names or SKUs containing the string (limit to 10 for performance)
        products = Product.objects.filter(
            Q(name__icontains=query) | Q(sku__icontains=query),
            is_visible=True, delist=False
        )[:10]

        suggestions = [{"id": p.id, "name": p.name, "slug": p.slug} for p in products]
        return Response({
            "success": True,
            "suggestions": suggestions
        })

# Latest Gold Rate tracker API
class LatestGoldRatesView(generics.ListAPIView):
    serializer_class = GoldRateSerializer
    permission_classes = [permissions.AllowAny]

    def get_queryset(self):
        # Returns gold rates with most recent effective date
        latest_date = GoldRate.objects.order_by('-effective_date').values_list('effective_date', flat=True).first()
        if latest_date:
            return GoldRate.objects.filter(effective_date=latest_date)
        return GoldRate.objects.none()
