import { Component, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { CheckoutService, PaymentStatusResponse } from '../../services/checkout.service';

@Component({
  selector: 'app-payment-status',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './payment-status.html'
})
export class PaymentStatusComponent implements OnInit {
  merchantOrderId = signal<string | null>(null);
  orderData = signal<any | null>(null);
  isLoading = signal<boolean>(true);
  errorMessage = signal<string | null>(null);

  constructor(
    private route: ActivatedRoute,
    private checkoutService: CheckoutService
  ) {}

  ngOnInit(): void {
    const id = this.route.snapshot.paramMap.get('id');
    this.merchantOrderId.set(id);
    if (id) {
      this.checkStatus(id);
    } else {
      this.isLoading.set(false);
      this.errorMessage.set('Invalid request.');
    }
  }

  checkStatus(id: string): void {
    this.checkoutService.getPaymentStatus(id).subscribe({
      next: (res) => {
        this.isLoading.set(false);
        if (res.success && res.data) {
          this.orderData.set(res.data);
        } else {
          this.errorMessage.set('Failed to check transaction status.');
        }
      },
      error: (err) => {
        this.isLoading.set(false);
        this.errorMessage.set(err.error?.detail || 'Transaction details not found.');
      }
    });
  }
}
