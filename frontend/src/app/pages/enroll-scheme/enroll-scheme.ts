import { Component, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterLink } from '@angular/router';
import { SchemeService, GoldSchemePlan } from '../../services/scheme.service';

@Component({
  selector: 'app-enroll-scheme',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './enroll-scheme.html'
})
export class EnrollSchemeComponent implements OnInit {
  plans = signal<GoldSchemePlan[]>([]);
  isLoading = signal<boolean>(true);
  isSubmitting = signal<boolean>(false);
  errorMessage = signal<string | null>(null);

  constructor(
    private schemeService: SchemeService,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.fetchPlans();
  }

  fetchPlans(): void {
    this.schemeService.getPlans().subscribe({
      next: (data) => {
        this.isLoading.set(false);
        this.plans.set(Array.isArray(data) ? data : (data as any).results || []);
      },
      error: () => {
        this.isLoading.set(false);
      }
    });
  }

  enrollInScheme(plan: GoldSchemePlan): void {
    if (this.isSubmitting()) return;

    this.isSubmitting.set(true);
    this.errorMessage.set(null);

    this.schemeService.enroll(plan.scheme_code).subscribe({
      next: (res) => {
        this.isSubmitting.set(false);
        alert(res.message);
        this.router.navigate(['/schemes']);
      },
      error: (err) => {
        this.isSubmitting.set(false);
        this.errorMessage.set(err.error?.message || 'Failed to enroll in the selected scheme. You may already have an active scheme.');
      }
    });
  }
}
