from django.db import models

class Metal(models.Model):
    name = models.CharField(max_length=100, unique=True)
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return self.name

class MetalPurity(models.Model):
    metal = models.ForeignKey(Metal, on_delete=models.CASCADE, related_name='purities')
    name = models.CharField(max_length=100)
    purity_value = models.DecimalField(max_digits=6, decimal_places=4, null=True, blank=True)
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"{self.metal.name} - {self.name}"

class GoldRate(models.Model):
    metal_purity = models.ForeignKey(MetalPurity, on_delete=models.CASCADE, related_name='rates')
    rate_per_gram = models.DecimalField(max_digits=10, decimal_places=2)
    effective_date = models.DateField()
    created_at = models.DateTimeField(auto_now_add=True)

    class Meta:
        indexes = [
            models.Index(fields=['effective_date', 'metal_purity']),
        ]

    def __str__(self):
        return f"{self.metal_purity} - {self.rate_per_gram} ({self.effective_date})"

class Category(models.Model):
    MAKING_CHARGES_CHOICES = [
        ('percent', 'Percentage'),
        ('fixed', 'Fixed Amount'),
    ]

    parent = models.ForeignKey('self', on_delete=models.CASCADE, null=True, blank=True, related_name='children')
    name = models.CharField(max_length=255)
    slug = models.SlugField(max_length=255, unique=True)
    description = models.TextField(null=True, blank=True)
    making_charges = models.DecimalField(max_digits=10, decimal_places=2, default=0.00)
    making_charges_type = models.CharField(max_length=10, choices=MAKING_CHARGES_CHOICES, default='percent')
    waste_percentage = models.DecimalField(max_digits=5, decimal_places=2, default=0.00)
    is_active = models.BooleanField(default=True)
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    def __str__(self):
        return self.name

class Supplier(models.Model):
    name = models.CharField(max_length=255)
    contact_info = models.TextField(null=True, blank=True)
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return self.name

class Product(models.Model):
    GENDER_CHOICES = [
        ('Male', 'Male'),
        ('Female', 'Female'),
        ('Unisex', 'Unisex'),
        ('Kids', 'Kids'),
    ]

    product_code = models.CharField(max_length=100, unique=True)
    sku = models.CharField(max_length=100, unique=True, null=True, blank=True)
    name = models.CharField(max_length=255)
    slug = models.SlugField(max_length=255, unique=True)
    description = models.TextField(null=True, blank=True)

    category = models.ForeignKey(Category, on_delete=models.SET_NULL, null=True, blank=True, related_name='products')
    metal = models.ForeignKey(Metal, on_delete=models.SET_NULL, null=True, blank=True, related_name='products')
    metal_purity = models.ForeignKey(MetalPurity, on_delete=models.SET_NULL, null=True, blank=True, related_name='products')
    supplier = models.ForeignKey(Supplier, on_delete=models.SET_NULL, null=True, blank=True, related_name='products')

    stock_quantity = models.IntegerField(default=0)
    is_featured = models.BooleanField(default=False)
    is_visible = models.BooleanField(default=True)
    delist = models.BooleanField(default=False)

    gender = models.CharField(max_length=10, choices=GENDER_CHOICES, default='Unisex')
    size = models.CharField(max_length=100, null=True, blank=True)
    gross_weight = models.DecimalField(max_digits=10, decimal_places=5, default=0.00000)
    net_weight = models.DecimalField(max_digits=10, decimal_places=5, null=True, blank=True)
    product_model = models.CharField(max_length=100, null=True, blank=True)
    manufacture_time = models.CharField(max_length=100, null=True, blank=True)

    # Stones
    stone_available = models.BooleanField(default=False)
    stone_desc = models.CharField(max_length=255, null=True, blank=True)
    stone_color = models.CharField(max_length=100, null=True, blank=True)
    stone_shape = models.CharField(max_length=100, null=True, blank=True)
    stone_count = models.IntegerField(null=True, blank=True)
    stone_weight = models.DecimalField(max_digits=10, decimal_places=5, null=True, blank=True)
    stone_cost = models.DecimalField(max_digits=15, decimal_places=2, null=True, blank=True)

    # Diamonds
    diamond_available = models.BooleanField(default=False)
    dia_desc = models.TextField(null=True, blank=True)
    dia_cent = models.DecimalField(max_digits=10, decimal_places=5, null=True, blank=True)
    dia_count = models.IntegerField(null=True, blank=True)
    dia_cut = models.CharField(max_length=100, null=True, blank=True)
    dia_color = models.CharField(max_length=100, null=True, blank=True)
    dia_clarity = models.CharField(max_length=100, null=True, blank=True)
    dia_shape = models.CharField(max_length=100, null=True, blank=True)

    # Beads
    beads_available = models.BooleanField(default=False)
    beads_desc = models.TextField(null=True, blank=True)
    beads_color = models.CharField(max_length=100, null=True, blank=True)
    beads_count = models.IntegerField(null=True, blank=True)
    beads_weight = models.DecimalField(max_digits=10, decimal_places=5, null=True, blank=True)
    beads_cost = models.DecimalField(max_digits=15, decimal_places=2, null=True, blank=True)

    # Pearls
    pearls_available = models.BooleanField(default=False)
    pearls_desc = models.TextField(null=True, blank=True)
    pearls_color = models.CharField(max_length=100, null=True, blank=True)
    pearls_count = models.IntegerField(null=True, blank=True)
    pearls_weight = models.DecimalField(max_digits=10, decimal_places=5, null=True, blank=True)
    pearls_cost = models.DecimalField(max_digits=15, decimal_places=2, null=True, blank=True)

    # Pricing Cache & SEO
    price = models.DecimalField(max_digits=15, decimal_places=2, null=True, blank=True)
    search_keywords = models.TextField(null=True, blank=True)
    tags = models.TextField(null=True, blank=True)
    specifications = models.JSONField(null=True, blank=True)

    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    class Meta:
        indexes = [
            models.Index(fields=['price']),
            models.Index(fields=['name']),
        ]

    def save(self, *args, **kwargs):
        if not self.product_code:
            brand_code = "H"  # Default to 'H' for Harees
            
            # Metal Code (G/S/P)
            metal_name = self.metal.name.upper() if self.metal else "GOLD"
            metal_code = "G"
            if "SILVER" in metal_name:
                metal_code = "S"
            elif "PLATINUM" in metal_name:
                metal_code = "P"

            # Purity Code (e.g. 22, 18, 24)
            purity_name = self.metal_purity.name if self.metal_purity else "22 Karat"
            import re
            purity_match = re.search(r'\d+', purity_name)
            purity_code = purity_match.group(0) if purity_match else "22"

            # Category Code (e.g. R, E, N, B)
            cat_name = self.category.name.upper() if self.category else "RINGS"
            category_code = cat_name[0] if cat_name else "R"

            prefix = f"{brand_code}{metal_code}{purity_code}{category_code}"

            # Calculate sequential serial number
            last_product = Product.objects.filter(product_code__startswith=prefix).order_by('-product_code').first()
            if last_product:
                last_code = last_product.product_code
                try:
                    last_serial = int(last_code[len(prefix):])
                except ValueError:
                    last_serial = 0
            else:
                last_serial = 0

            new_serial = last_serial + 1
            self.product_code = f"{prefix}{str(new_serial).zfill(6)}"

        if not self.slug:
            from django.utils.text import slugify
            self.slug = slugify(self.name)

        super().save(*args, **kwargs)

    def __str__(self):
        return self.name

class ProductImage(models.Model):
    product = models.ForeignKey(Product, on_delete=models.CASCADE, related_name='images')
    image_path = models.CharField(max_length=255)
    alt_text = models.CharField(max_length=255, null=True, blank=True)
    sort_order = models.IntegerField(default=0)
    is_primary = models.BooleanField(default=False)
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"Image for {self.product.name} ({self.id})"
