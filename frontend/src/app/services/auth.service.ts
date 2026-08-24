import { Injectable, signal, computed } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { Observable, tap } from 'rxjs';

export interface UserProfile {
  id: number;
  fullname: string;
  name?: string;
  email: string;
  phone: string;
  security_question: string;
  address1: string;
  address2?: string;
  city: string;
  state: string;
  pincode: string;
  dob: string;
  anniversary?: string;
  landmark?: string;
}

export interface AuthResponse {
  access: string;
  refresh: string;
  user: UserProfile;
}

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private apiUrl = 'http://localhost:8000/api/v1/auth';

  // Signals for state management
  private currentUserSignal = signal<UserProfile | null>(null);
  currentUser = computed(() => this.currentUserSignal());
  isAuthenticated = computed(() => !!this.currentUserSignal());

  constructor(private http: HttpClient, private router: Router) {
    this.loadToken();
  }

  // Load token from storage on startup
  private loadToken(): void {
    const token = localStorage.getItem('access_token');
    const userStr = localStorage.getItem('user_profile');
    if (token && userStr) {
      try {
        this.currentUserSignal.set(JSON.parse(userStr));
      } catch (e) {
        this.logout();
      }
    }
  }

  register(userData: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/register/`, userData);
  }

  login(credentials: { email: string; password: string }): Observable<AuthResponse> {
    return this.http.post<AuthResponse>(`${this.apiUrl}/login/`, credentials).pipe(
      tap(response => {
        localStorage.setItem('access_token', response.access);
        localStorage.setItem('refresh_token', response.refresh);
        localStorage.setItem('user_profile', JSON.stringify(response.user));
        this.currentUserSignal.set(response.user);
      })
    );
  }

  getProfile(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/profile/`).pipe(
      tap(response => {
        if (response.success && response.data) {
          localStorage.setItem('user_profile', JSON.stringify(response.data));
          this.currentUserSignal.set(response.data);
        }
      })
    );
  }

  updateProfile(profileData: any): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/profile/`, profileData).pipe(
      tap(response => {
        if (response.success && response.data) {
          localStorage.setItem('user_profile', JSON.stringify(response.data));
          this.currentUserSignal.set(response.data);
        }
      })
    );
  }

  forgotPassword(email: string): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/forgot-password/`, { email });
  }

  resetPassword(payload: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/reset-password/`, payload);
  }

  logout(): void {
    localStorage.removeItem('access_token');
    localStorage.removeItem('refresh_token');
    localStorage.removeItem('user_profile');
    this.currentUserSignal.set(null);
    this.router.navigate(['/login']);
  }

  getAccessToken(): string | null {
    return localStorage.getItem('access_token');
  }
}
