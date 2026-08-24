import { Component, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterLink } from '@angular/router';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterLink],
  templateUrl: './register.html'
})
export class RegisterComponent {
  registerForm: FormGroup;
  errorMessage = signal<string | null>(null);
  successMessage = signal<string | null>(null);
  isLoading = signal<boolean>(false);

  // Common security questions options
  securityQuestions = [
    'What was the name of your first school?',
    'What was the name of your first pet?',
    'What is your mother\'s maiden name?',
    'What city were you born in?',
    'What was the model of your first car?'
  ];

  constructor(
    private fb: FormBuilder,
    private authService: AuthService,
    private router: Router
  ) {
    this.registerForm = this.fb.group({
      fullname: ['', [Validators.required, Validators.maxLength(100)]],
      name: [''], // Laravel compatibility
      email: ['', [Validators.required, Validators.email]],
      phone: ['', [Validators.required, Validators.pattern(/^[0-9]{10,15}$/)]],
      password: ['', [Validators.required, Validators.minLength(6)]],
      security_question: ['', [Validators.required]],
      security_answer: ['', [Validators.required]],
      address1: ['', [Validators.required]],
      address2: [''],
      city: ['', [Validators.required, Validators.maxLength(50)]],
      state: ['', [Validators.required, Validators.maxLength(50)]],
      pincode: ['', [Validators.required, Validators.pattern(/^[0-9]{5,10}$/)]],
      dob: ['1970-01-01', [Validators.required]],
      anniversary: [null],
      landmark: ['']
    });
  }

  onSubmit(): void {
    if (this.registerForm.invalid) {
      this.registerForm.markAllAsTouched();
      return;
    }

    this.isLoading.set(true);
    this.errorMessage.set(null);
    this.successMessage.set(null);

    // Filter null/empty fields before submitting
    const payload = { ...this.registerForm.value };
    if (!payload.name) payload.name = payload.fullname;

    this.authService.register(payload).subscribe({
      next: (res) => {
        this.isLoading.set(false);
        this.successMessage.set('Registration successful! Redirecting to login...');
        setTimeout(() => {
          this.router.navigate(['/login']);
        }, 2000);
      },
      error: (err) => {
        this.isLoading.set(false);
        // Display validation errors if available, else standard message
        if (err.error?.errors) {
          const errors = err.error.errors;
          const firstErrorKey = Object.keys(errors)[0];
          this.errorMessage.set(`${firstErrorKey}: ${errors[firstErrorKey][0]}`);
        } else {
          this.errorMessage.set(err.error?.message || 'Registration failed. Email or Phone may already be registered.');
        }
      }
    });
  }
}
