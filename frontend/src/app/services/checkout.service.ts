import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface CheckoutResponse {
  success: boolean;
  message: string;
  data: {
    order_id: number;
    merchant_order_id: string;
    payment_url: string;
    amount: number;
  };
}

export interface PaymentStatusResponse {
  success: boolean;
  data: {
    order_id: number;
    merchant_order_id: string;
    payment_status: string;
    status: string;
    final_amount: number;
  };
}

@Injectable({
  providedIn: 'root'
})
export class CheckoutService {
  private apiUrl = 'http://localhost:8000/api/v1';

  constructor(private http: HttpClient) {}

  processCheckout(billingData: any): Observable<CheckoutResponse> {
    return this.http.post<CheckoutResponse>(`${this.apiUrl}/checkout/`, billingData);
  }

  getPaymentStatus(merchantOrderId: string): Observable<PaymentStatusResponse> {
    return this.http.get<PaymentStatusResponse>(`${this.apiUrl}/phonepe/status/${merchantOrderId}/`);
  }

  getMyOrders(): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/my-orders/`);
  }

  simulateCallback(merchantOrderId: string, status: 'completed' | 'failed'): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/phonepe/simulate/`, {
      merchant_order_id: merchantOrderId,
      payment_status: status
    });
  }
}
