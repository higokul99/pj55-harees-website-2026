import { Component, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterLink, Router } from '@angular/router';
import { ProductService, ProductDetail, ProductImage } from '../../services/product.service';
import { CartService } from '../../services/cart.service';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-product-detail',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './product-detail.html'
})
export class ProductDetailComponent implements OnInit {
  product = signal<ProductDetail | null>(null);
  selectedImage = signal<ProductImage | null>(null);
  isLoading = signal<boolean>(true);
  errorMessage = signal<string | null>(null);
  
  quantity = signal<number>(1);

  constructor(
    private route: ActivatedRoute,
    private productService: ProductService,
    private cartService: CartService,
    private authService: AuthService,
    private router: Router
  ) {}

  ngOnInit(): void {
    const slug = this.route.snapshot.paramMap.get('slug');
    if (slug) {
      this.fetchProductDetails(slug);
    } else {
      this.errorMessage.set('Product not found.');
      this.isLoading.set(false);
    }
  }

  fetchProductDetails(slug: string): void {
    this.productService.getProductDetail(slug).subscribe({
      next: (res) => {
        this.isLoading.set(false);
        if (res.success && res.data) {
          this.product.set(res.data);
          
          // Select primary image or first available
          const primary = res.data.images.find(img => img.is_primary);
          this.selectedImage.set(primary || res.data.images[0] || null);
        } else {
          this.errorMessage.set('Failed to retrieve product information.');
        }
      },
      error: (err) => {
        this.isLoading.set(false);
        this.errorMessage.set(err.error?.detail || 'Product does not exist.');
      }
    });
  }

  changeImage(img: ProductImage): void {
    this.selectedImage.set(img);
  }

  incrementQuantity(): void {
    this.quantity.update(q => q + 1);
  }

  decrementQuantity(): void {
    this.quantity.update(q => (q > 1 ? q - 1 : 1));
  }

  addToCart(): void {
    if (!this.authService.isAuthenticated()) {
      this.router.navigate(['/login']);
      return;
    }
    const prod = this.product();
    if (!prod) return;

    this.cartService.addToCart(prod.id, this.quantity()).subscribe({
      next: () => {
        alert('Item added to cart successfully!');
      },
      error: () => {
        alert('Failed to add item to cart.');
      }
    });
  }

  toggleWishlist(): void {
    if (!this.authService.isAuthenticated()) {
      this.router.navigate(['/login']);
      return;
    }
    const prod = this.product();
    if (!prod) return;

    this.cartService.toggleWishlist(prod.id).subscribe({
      next: (res) => {
        alert(res.message);
      }
    });
  }
}
