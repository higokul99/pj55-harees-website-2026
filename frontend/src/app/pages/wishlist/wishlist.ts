import { Component, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { CartService, WishlistItem } from '../../services/cart.service';

@Component({
  selector: 'app-wishlist',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './wishlist.html'
})
export class WishlistComponent implements OnInit {
  wishlistItems = signal<WishlistItem[]>([]);
  isLoading = signal<boolean>(true);

  constructor(
    private cartService: CartService
  ) {}

  ngOnInit(): void {
    this.fetchWishlist();
  }

  fetchWishlist(): void {
    this.isLoading.set(true);
    this.cartService.getWishlistItems().subscribe({
      next: (items) => {
        this.isLoading.set(false);
        const list = Array.isArray(items) ? items : (items as any).results || [];
        this.wishlistItems.set(list);
      },
      error: () => {
        this.isLoading.set(false);
      }
    });
  }

  removeItem(item: WishlistItem): void {
    this.cartService.toggleWishlist(item.product).subscribe(() => {
      this.fetchWishlist();
    });
  }

  moveToCart(item: WishlistItem): void {
    // Add to cart
    this.cartService.addToCart(item.product, 1).subscribe(() => {
      // Remove from wishlist
      this.removeItem(item);
    });
  }
}
