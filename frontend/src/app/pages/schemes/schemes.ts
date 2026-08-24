import { Component, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { SchemeService, UserScheme, PassbookData } from '../../services/scheme.service';

@Component({
  selector: 'app-schemes',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './schemes.html'
})
export class SchemesComponent implements OnInit {
  mySchemes = signal<UserScheme[]>([]);
  completedCount = signal<number>(0);
  
  isLoading = signal<boolean>(true);
  isPaying = signal<boolean>(false);
  
  // Passbook view state
  viewPassbookData = signal<PassbookData | null>(null);

  constructor(private schemeService: SchemeService) {}

  ngOnInit(): void {
    this.fetchMySchemes();
  }

  fetchMySchemes(): void {
    this.isLoading.set(true);
    this.schemeService.getMySchemes().subscribe({
      next: (data) => {
        this.isLoading.set(false);
        const list = Array.isArray(data) ? data : (data as any).results || [];
        
        // Auto-check for monthly payment requirements
        const currentMonth = new Date().getMonth();
        const currentYear = new Date().getFullYear();
        
        list.forEach((scheme: UserScheme) => {
          if (scheme.payments) {
            const hasPaidThisMonth = scheme.payments.some(pay => {
              const payDate = new Date(pay.payment_date);
              return payDate.getMonth() === currentMonth && payDate.getFullYear() === currentYear;
            });
            scheme.payment_pending = !hasPaidThisMonth;
          } else {
            // Default to true if not populated
            scheme.payment_pending = true;
          }
        });

        this.mySchemes.set(list);
        this.completedCount.set(list.filter((s: any) => s.status === 'completed').length);
      },
      error: () => {
        this.isLoading.set(false);
      }
    });
  }

  payInstallment(scheme: UserScheme): void {
    if (this.isPaying()) return;
    
    this.isPaying.set(true);
    this.schemeService.makePayment(scheme.id, 'online').subscribe({
      next: (res) => {
        this.isPaying.set(false);
        alert(res.message || 'Payment recorded successfully!');
        this.fetchMySchemes();
      },
      error: (err) => {
        this.isPaying.set(false);
        alert(err.error?.message || 'Payment processing failed.');
      }
    });
  }

  viewPassbook(scheme: UserScheme): void {
    this.schemeService.getPassbook(scheme.id).subscribe({
      next: (res) => {
        if (res.success) {
          this.viewPassbookData.set(res.data);
        }
      }
    });
  }

  closePassbook(): void {
    this.viewPassbookData.set(null);
  }
}
