import { Component, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { AuthService, UserProfile } from '../../services/auth.service';

@Component({
  selector: 'app-profile',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './profile.html'
})
export class ProfileComponent implements OnInit {
  profileForm!: FormGroup;
  errorMessage = signal<string | null>(null);
  successMessage = signal<string | null>(null);
  isLoading = signal<boolean>(false);

  constructor(
    private fb: FormBuilder,
    protected authService: AuthService
  ) {}

  ngOnInit(): void {
    this.initForm(this.authService.currentUser());
  }

  private initForm(user: UserProfile | null): void {
    this.profileForm = this.fb.group({
      fullname: [user?.fullname || '', [Validators.required, Validators.maxLength(100)]],
      phone: [user?.phone || '', [Validators.required, Validators.pattern(/^[0-9]{10,15}$/)]],
      address1: [user?.address1 || '', [Validators.required]],
      address2: [user?.address2 || ''],
      city: [user?.city || '', [Validators.required, Validators.maxLength(50)]],
      state: [user?.state || '', [Validators.required, Validators.maxLength(50)]],
      pincode: [user?.pincode || '', [Validators.required, Validators.pattern(/^[0-9]{5,10}$/)]],
      dob: [user?.dob || '1970-01-01', [Validators.required]],
      anniversary: [user?.anniversary || null],
      landmark: [user?.landmark || '']
    });
  }

  onSubmit(): void {
    if (this.profileForm.invalid) {
      this.profileForm.markAllAsTouched();
      return;
    }

    this.isLoading.set(true);
    this.errorMessage.set(null);
    this.successMessage.set(null);

    this.authService.updateProfile(this.profileForm.value).subscribe({
      next: (res) => {
        this.isLoading.set(false);
        this.successMessage.set('Profile updated successfully!');
      },
      error: (err) => {
        this.isLoading.set(false);
        this.errorMessage.set(err.error?.message || 'Failed to update profile details.');
      }
    });
  }

  onLogout(): void {
    this.authService.logout();
  }
}
