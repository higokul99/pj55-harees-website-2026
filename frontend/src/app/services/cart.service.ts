import { Injectable, signal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, tap } from 'rxjs';
import { ProductList } from './product.service';

export interface CartItem {
  id: number;
  product: number;
  product_code: string;
  table_name: string;
  quantity: number;
  product_details: ProductList;
  item_total_price: number;
}

export interface WishlistItem {
  id: number;
  product: number;
  table_name: string;
  product_details: ProductList;
}

@Injectable({
  providedIn: 'root'
})
export class CartService {
  private apiUrl = 'http://localhost:8000/api/v1';

  // State trackers
  cartCount = signal<number>(0);
  wishlistCount = signal<number>(0);

  constructor(private http: HttpClient) {
    this.refreshCounts();
  }

  refreshCounts(): void {
    this.getCartItems().subscribe(items => {
      this.cartCount.set(items.length);
    });
    this.getWishlistItems().subscribe(items => {
      this.wishlistCount.set(items.length);
    });
  }

  getCartItems(): Observable<CartItem[]> {
    return this.http.get<CartItem[]>(`${this.apiUrl}/cart/`);
  }

  addToCart(productId: number, quantity: number = 1): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/cart/add/`, { product_id: productId, quantity }).pipe(
      tap(() => this.refreshCounts())
    );
  }

  removeFromCart(productId: number, quantity: number = 1): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/cart/remove/`, { product_id: productId, quantity }).pipe(
      tap(() => this.refreshCounts())
    );
  }

  getWishlistItems(): Observable<WishlistItem[]> {
    return this.http.get<WishlistItem[]>(`${this.apiUrl}/wishlist/`);
  }

  toggleWishlist(productId: number): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/wishlist/toggle/`, { product_id: productId }).pipe(
      tap(res => {
        if (res.success && res.count !== undefined) {
          this.wishlistCount.set(res.count);
        } else {
          this.refreshCounts();
        }
      })
    );
  }
}
