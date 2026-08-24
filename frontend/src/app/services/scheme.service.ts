import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface GoldSchemePlan {
  id: number;
  scheme_code: string;
  scheme_name: string;
  monthly_installment: string;
  bonus_amount: string;
  final_value: string;
  status: string;
}

export interface SchemePayment {
  id: number;
  amount: string;
  payment_date: string;
  receipt_no: string;
}

export interface UserScheme {
  id: number;
  scheme_type: string;
  scheme_name: string;
  monthly_amount: string;
  start_date: string;
  status: string;
  code: string;
  months_completed: number;
  payments?: SchemePayment[];
  bonus_amount: number;
  final_value: number;
  payment_pending?: boolean;
}

export interface PassbookData {
  scheme: UserScheme;
  total_paid: number;
  bonus: number;
  final_value: number;
  payments: SchemePayment[];
}

@Injectable({
  providedIn: 'root'
})
export class SchemeService {
  private apiUrl = 'http://localhost:8000/api/v1/schemes';

  constructor(private http: HttpClient) {}

  getPlans(): Observable<GoldSchemePlan[]> {
    return this.http.get<GoldSchemePlan[]>(`${this.apiUrl}/plans/`);
  }

  getMySchemes(): Observable<UserScheme[]> {
    // Django ViewSet returns list of schemes directly or wrapped
    return this.http.get<UserScheme[]>(`${this.apiUrl}/my-schemes/`);
  }

  enroll(schemeCode: string): Observable<{ success: boolean; message: string; data: UserScheme }> {
    return this.http.post<{ success: boolean; message: string; data: UserScheme }>(`${this.apiUrl}/my-schemes/enroll/`, { scheme_code: schemeCode });
  }

  makePayment(schemeId: number, paymentMethod: string = 'online'): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/my-schemes/${schemeId}/pay/`, { payment_method: paymentMethod });
  }

  getPassbook(schemeId: number): Observable<{ success: boolean; data: PassbookData }> {
    return this.http.get<{ success: boolean; data: PassbookData }>(`${this.apiUrl}/my-schemes/${schemeId}/passbook/`);
  }
}
