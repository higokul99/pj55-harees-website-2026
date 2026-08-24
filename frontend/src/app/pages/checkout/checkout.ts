import { Component, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { CartService, CartItem } from '../../services/cart.service';
import { CheckoutService } from '../../services/checkout.service';

@Component({
  selector: 'app-checkout',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './checkout.html'
})
export class CheckoutComponent implements OnInit {
  checkoutForm!: FormGroup;
  cartItems = signal<CartItem[]>([]);
  
  subtotal = signal<number>(0);
  gst = signal<number>(0);
  grandTotal = signal<number>(0);
  
  isLoading = signal<boolean>(false);
  errorMessage = signal<string | null>(null);

  constructor(
    private fb: FormBuilder,
    private authService: AuthService,
    private cartService: CartService,
    private checkoutService: CheckoutService,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.fetchCart();
    this.initForm();
  }

  private initForm(): void {
    const user = this.authService.currentUser();
    this.checkoutForm = this.fb.group({
      fullname: [user?.fullname || '', [Validators.required]],
      email: [user?.email || '', [Validators.required, Validators.email]],
      phone: [user?.phone || '', [Validators.required]],
      pincode: [user?.pincode || '', [Validators.required]],
      address: [user?.address1 || '', [Validators.required]],
      city: [user?.city || '', [Validators.required]],
      state: [user?.state || '', [Validators.required]],
      delivery_type: ['home', [Validators.required]]
    });
  }

  fetchCart(): void {
    this.cartService.getCartItems().subscribe({
      next: (items) => {
        const list = Array.isArray(items) ? items : (items as any).results || [];
        this.cartItems.set(list);
        
        const sum = list.reduce((acc: number, item: CartItem) => acc + item.item_total_price, 0);
        this.subtotal.set(sum);
        this.gst.set(sum * 0.03);
        this.grandTotal.set(sum + (sum * 0.03));
      }
    });
  }

  onSubmit(): void {
    if (this.checkoutForm.invalid) {
      this.checkoutForm.markAllAsTouched();
      return;
    }

    this.isLoading.set(true);
    this.errorMessage.set(null);

    this.checkoutService.processCheckout(this.checkoutForm.value).subscribe({
      next: (res) => {
        this.isLoading.set(false);
        if (res.success && res.data.payment_url) {
          // Redirect to simulated payment page
          window.location.href = res.data.payment_url;
        } else {
          this.errorMessage.set('Could not initialize payment gateway.');
        }
      },
      error: (err) => {
        this.isLoading.set(false);
        this.errorMessage.set(err.error?.message || 'Checkout failed. Please try again.');
      }
    });
  }
}
