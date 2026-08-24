import { Component, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { CartService, CartItem } from '../../services/cart.service';

@Component({
  selector: 'app-cart',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './cart.html'
})
export class CartComponent implements OnInit {
  cartItems = signal<CartItem[]>([]);
  isLoading = signal<boolean>(true);
  
  subtotal = signal<number>(0);
  gst = signal<number>(0);
  grandTotal = signal<number>(0);

  constructor(private cartService: CartService) {}

  ngOnInit(): void {
    this.fetchCart();
  }

  fetchCart(): void {
    this.isLoading.set(true);
    this.cartService.getCartItems().subscribe({
      next: (items) => {
        this.isLoading.set(false);
        // Django Viewset list returns array or page Results
        const list = Array.isArray(items) ? items : (items as any).results || [];
        this.cartItems.set(list);
        this.calculateTotals(list);
      },
      error: () => {
        this.isLoading.set(false);
      }
    });
  }

  calculateTotals(items: CartItem[]): void {
    const sum = items.reduce((acc, item) => acc + item.item_total_price, 0);
    const tax = sum * 0.03; // 3% GST standard on Jewelry in India
    this.subtotal.set(sum);
    this.gst.set(tax);
    this.grandTotal.set(sum + tax);
  }

  incrementQuantity(item: CartItem): void {
    this.cartService.addToCart(item.product, 1).subscribe(() => {
      this.fetchCart();
    });
  }

  decrementQuantity(item: CartItem): void {
    this.cartService.removeFromCart(item.product, 1).subscribe(() => {
      this.fetchCart();
    });
  }

  removeItem(item: CartItem): void {
    this.cartService.removeFromCart(item.product, item.quantity).subscribe(() => {
      this.fetchCart();
    });
  }
}
