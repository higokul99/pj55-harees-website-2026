import { Component, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { CheckoutService } from '../../services/checkout.service';

@Component({
  selector: 'app-my-orders',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './my-orders.html'
})
export class MyOrdersComponent implements OnInit {
  orders = signal<any[]>([]);
  isLoading = signal<boolean>(true);

  constructor(private checkoutService: CheckoutService) {}

  ngOnInit(): void {
    this.fetchOrders();
  }

  fetchOrders(): void {
    this.isLoading.set(true);
    this.checkoutService.getMyOrders().subscribe({
      next: (data) => {
        this.isLoading.set(false);
        this.orders.set(Array.isArray(data) ? data : (data as any).results || []);
      },
      error: () => {
        this.isLoading.set(false);
      }
    });
  }
}
