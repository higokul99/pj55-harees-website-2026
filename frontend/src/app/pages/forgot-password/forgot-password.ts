import { Component, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterLink } from '@angular/router';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-forgot-password',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterLink],
  templateUrl: './forgot-password.html'
})
export class ForgotPasswordComponent {
  emailForm: FormGroup;
  resetForm: FormGroup;
  
  step = signal<number>(1); // Step 1: Enter Email. Step 2: Answer Question & Set Password.
  securityQuestion = signal<string | null>(null);
  emailAddress = signal<string | null>(null);

  errorMessage = signal<string | null>(null);
  successMessage = signal<string | null>(null);
  isLoading = signal<boolean>(false);

  constructor(
    private fb: FormBuilder,
    private authService: AuthService,
    private router: Router
  ) {
    this.emailForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]]
    });

    this.resetForm = this.fb.group({
      security_answer: ['', [Validators.required]],
      new_password: ['', [Validators.required, Validators.minLength(6)]]
    });
  }

  onFetchQuestion(): void {
    if (this.emailForm.invalid) {
      this.emailForm.markAllAsTouched();
      return;
    }

    this.isLoading.set(true);
    this.errorMessage.set(null);

    const email = this.emailForm.get('email')?.value;
    this.authService.forgotPassword(email).subscribe({
      next: (res) => {
        this.isLoading.set(false);
        if (res.success && res.data) {
          this.emailAddress.set(email);
          this.securityQuestion.set(res.data.security_question);
          this.step.set(2);
        } else {
          this.errorMessage.set('Could not fetch security question.');
        }
      },
      error: (err) => {
        this.isLoading.set(false);
        this.errorMessage.set(err.error?.message || 'Email address not found.');
      }
    });
  }

  onResetPassword(): void {
    if (this.resetForm.invalid) {
      this.resetForm.markAllAsTouched();
      return;
    }

    this.isLoading.set(true);
    this.errorMessage.set(null);

    const payload = {
      email: this.emailAddress(),
      security_answer: this.resetForm.get('security_answer')?.value,
      new_password: this.resetForm.get('new_password')?.value
    };

    this.authService.resetPassword(payload).subscribe({
      next: (res) => {
        this.isLoading.set(false);
        this.successMessage.set('Password updated successfully! Redirecting to login...');
        setTimeout(() => {
          this.router.navigate(['/login']);
        }, 2000);
      },
      error: (err) => {
        this.isLoading.set(false);
        this.errorMessage.set(err.error?.message || 'Incorrect security answer.');
      }
    });
  }
}
