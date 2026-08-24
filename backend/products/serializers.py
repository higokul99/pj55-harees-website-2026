from rest_framework import serializers
from .models import Metal, MetalPurity, GoldRate, Category, Supplier, Product, ProductImage

class MetalSerializer(serializers.ModelSerializer):
    class Meta:
        model = Metal
        fields = ['id', 'name']

class MetalPuritySerializer(serializers.ModelSerializer):
    metal_name = serializers.CharField(source='metal.name', read_only=True)

    class Meta:
        model = MetalPurity
        fields = ['id', 'metal', 'metal_name', 'name', 'purity_value']

class GoldRateSerializer(serializers.ModelSerializer):
    purity_name = serializers.CharField(source='metal_purity.name', read_only=True)

    class Meta:
        model = GoldRate
        fields = ['id', 'metal_purity', 'purity_name', 'rate_per_gram', 'effective_date']

class CategorySerializer(serializers.ModelSerializer):
    class Meta:
        model = Category
        fields = [
            'id', 'parent', 'name', 'slug', 'description',
            'making_charges', 'making_charges_type', 'waste_percentage', 'is_active'
        ]

class ProductImageSerializer(serializers.ModelSerializer):
    class Meta:
        model = ProductImage
        fields = ['id', 'image_path', 'alt_text', 'sort_order', 'is_primary']

class ProductListSerializer(serializers.ModelSerializer):
    primary_image = serializers.SerializerMethodField()
    category_name = serializers.CharField(source='category.name', read_only=True)
    metal_name = serializers.CharField(source='metal.name', read_only=True)

    class Meta:
        model = Product
        fields = [
            'id', 'product_code', 'sku', 'name', 'slug', 'category', 'category_name',
            'metal_name', 'stock_quantity', 'price', 'is_featured', 'gender', 'primary_image', 'specifications'
        ]

    def get_primary_image(self, obj):
        primary = obj.images.filter(is_primary=True).first()
        if primary:
            return ProductImageSerializer(primary).data
        # Fallback to the first image if no primary is specified
        first = obj.images.first()
        if first:
            return ProductImageSerializer(first).data
        return None

class ProductDetailSerializer(serializers.ModelSerializer):
    images = ProductImageSerializer(many=True, read_only=True)
    category_details = CategorySerializer(source='category', read_only=True)
    purity_details = MetalPuritySerializer(source='metal_purity', read_only=True)

    class Meta:
        model = Product
        fields = '__all__'
