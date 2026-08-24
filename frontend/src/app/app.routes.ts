import { Routes } from '@angular/router';
import { LoginComponent } from './pages/login/login';
import { RegisterComponent } from './pages/register/register';
import { ForgotPasswordComponent } from './pages/forgot-password/forgot-password';
import { ProfileComponent } from './pages/profile/profile';
import { ProductListComponent } from './pages/product-list/product-list';
import { ProductDetailComponent } from './pages/product-detail/product-detail';
import { CartComponent } from './pages/cart/cart';
import { WishlistComponent } from './pages/wishlist/wishlist';
import { SchemesComponent } from './pages/schemes/schemes';
import { EnrollSchemeComponent } from './pages/enroll-scheme/enroll-scheme';
import { CheckoutComponent } from './pages/checkout/checkout';
import { PaymentSimComponent } from './pages/payment-sim/payment-sim';
import { PaymentStatusComponent } from './pages/payment-status/payment-status';
import { MyOrdersComponent } from './pages/my-orders/my-orders';
import { HomeComponent } from './pages/home/home';
import { authGuard } from './guards/auth.guard';

export const routes: Routes = [
  { path: 'login', component: LoginComponent },
  { path: 'register', component: RegisterComponent },
  { path: 'forgot-password', component: ForgotPasswordComponent },
  { path: 'profile', component: ProfileComponent, canActivate: [authGuard] },
  { path: 'products', component: ProductListComponent },
  { path: 'product/:slug', component: ProductDetailComponent },
  { path: 'cart', component: CartComponent, canActivate: [authGuard] },
  { path: 'wishlist', component: WishlistComponent, canActivate: [authGuard] },
  { path: 'schemes', component: SchemesComponent, canActivate: [authGuard] },
  { path: 'enroll-scheme', component: EnrollSchemeComponent, canActivate: [authGuard] },
  { path: 'checkout', component: CheckoutComponent, canActivate: [authGuard] },
  { path: 'payment-sim/:id', component: PaymentSimComponent, canActivate: [authGuard] },
  { path: 'payment-status/:id', component: PaymentStatusComponent, canActivate: [authGuard] },
  { path: 'my-orders', component: MyOrdersComponent, canActivate: [authGuard] },
  { path: '', component: HomeComponent },
  { path: '**', redirectTo: '' }
];




