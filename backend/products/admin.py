from django.contrib import admin
from .models import Metal, MetalPurity, GoldRate, Category, Supplier, Product, ProductImage

class ProductImageInline(admin.TabularInline):
    model = ProductImage
    extra = 1

@admin.register(Product)
class ProductAdmin(admin.ModelAdmin):
    list_display = ['name', 'product_code', 'sku', 'category', 'metal', 'price', 'stock_quantity', 'is_visible']
    search_fields = ['name', 'product_code', 'sku', 'search_keywords']
    list_filter = ['is_visible', 'is_featured', 'metal', 'gender']
    prepopulated_fields = {'slug': ('name',)}
    inlines = [ProductImageInline]

@admin.register(Category)
class CategoryAdmin(admin.ModelAdmin):
    list_display = ['name', 'slug', 'parent', 'making_charges', 'making_charges_type', 'is_active']
    prepopulated_fields = {'slug': ('name',)}

@admin.register(GoldRate)
class GoldRateAdmin(admin.ModelAdmin):
    list_display = ['metal_purity', 'rate_per_gram', 'effective_date']
    list_filter = ['effective_date', 'metal_purity']

admin.site.register(Metal)
admin.site.register(MetalPurity)
admin.site.register(Supplier)
