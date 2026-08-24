import { Component, signal, computed, OnInit } from '@angular/core';
import { RouterOutlet, RouterLink, RouterLinkActive, Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { AuthService } from './services/auth.service';
import { CartService } from './services/cart.service';

@Component({
  imports: [RouterOutlet, RouterLink, RouterLinkActive, CommonModule],
  selector: 'app-root',
  styleUrl: './app.scss',
  templateUrl: './app.html',
})
export class App implements OnInit {
  protected readonly title = signal('frontend');
  
  // Expose services to templates
  isLoggedIn = computed(() => this.authService.isAuthenticated());
  userEmail = computed(() => this.authService.currentUser()?.email || '');
  
  cartCount = computed(() => this.cartService.cartCount());
  wishlistCount = computed(() => this.cartService.wishlistCount());

  constructor(
    private authService: AuthService,
    private cartService: CartService,
    private router: Router
  ) {}

  ngOnInit(): void {
    // Sync counts on startup
    if (this.authService.isAuthenticated()) {
      this.cartService.refreshCounts();
    }
  }

  logout(): void {
    this.authService.logout();
    this.cartService.cartCount.set(0);
    this.cartService.wishlistCount.set(0);
    this.router.navigate(['/login']);
  }
}
