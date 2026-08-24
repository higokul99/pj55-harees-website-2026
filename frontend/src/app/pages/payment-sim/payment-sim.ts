import { Component, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router } from '@angular/router';
import { CheckoutService } from '../../services/checkout.service';

@Component({
  selector: 'app-payment-sim',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './payment-sim.html'
})
export class PaymentSimComponent implements OnInit {
  merchantOrderId = signal<string | null>(null);
  isLoading = signal<boolean>(false);

  constructor(
    private route: ActivatedRoute,
    private checkoutService: CheckoutService,
    private router: Router
  ) {}

  ngOnInit(): void {
    const id = this.route.snapshot.paramMap.get('id');
    this.merchantOrderId.set(id);
  }

  processPayment(status: 'completed' | 'failed'): void {
    const txnId = this.merchantOrderId();
    if (!txnId) return;

    this.isLoading.set(true);
    this.checkoutService.simulateCallback(txnId, status).subscribe({
      next: () => {
        this.isLoading.set(false);
        // Redirect to status check page
        this.router.navigate(['/payment-status', txnId]);
      },
      error: () => {
        this.isLoading.set(false);
        alert('Simulation callback failed.');
      }
    });
  }
}
