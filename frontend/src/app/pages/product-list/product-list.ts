import { Component, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule } from '@angular/forms';
import { RouterLink, ActivatedRoute, Router } from '@angular/router';
import { ProductService, ProductList, Category } from '../../services/product.service';
import { CartService } from '../../services/cart.service';
import { AuthService } from '../../services/auth.service';
import { debounceTime, distinctUntilChanged } from 'rxjs';

@Component({
  selector: 'app-product-list',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterLink],
  templateUrl: './product-list.html'
})
export class ProductListComponent implements OnInit {
  filterForm: FormGroup;
  products = signal<ProductList[]>([]);
  categories = signal<Category[]>([]);
  suggestions = signal<any[]>([]);
  
  isLoading = signal<boolean>(false);

  constructor(
    private fb: FormBuilder,
    private productService: ProductService,
    private cartService: CartService,
    private authService: AuthService,
    private route: ActivatedRoute,
    private router: Router
  ) {
    this.filterForm = this.fb.group({
      category: [''],
      gender: [''],
      metal: [''],
      search: ['']
    });
  }

  ngOnInit(): void {
    this.fetchCategories();
    this.fetchProducts();

    // Listen to query parameters (e.g. from global search or banner clicks)
    this.route.queryParams.subscribe(params => {
      if (params['category'] || params['search']) {
        this.filterForm.patchValue({
          category: params['category'] || '',
          search: params['search'] || ''
        }, { emitEvent: false });
        this.fetchProducts();
      }
    });

    // Listen to form value changes and trigger refetch
    this.filterForm.valueChanges
      .pipe(debounceTime(300), distinctUntilChanged())
      .subscribe(() => {
        this.fetchProducts();
      });

    // Setup autocomplete search listener
    this.filterForm.get('search')?.valueChanges
      .pipe(debounceTime(200), distinctUntilChanged())
      .subscribe(val => {
        if (val && val.length > 2) {
          this.productService.getSearchSuggestions(val).subscribe(res => {
            if (res.success) {
              this.suggestions.set(res.suggestions);
            }
          });
        } else {
          this.suggestions.set([]);
        }
      });
  }

  fetchCategories(): void {
    this.productService.getCategories().subscribe({
      next: (data) => {
        this.categories.set(Array.isArray(data) ? data : (data as any).results || []);
      }
    });
  }

  fetchProducts(): void {
    this.isLoading.set(true);
    const formVals = this.filterForm.value;
    
    this.productService.getProducts({
      category: formVals.category,
      gender: formVals.gender,
      metal: formVals.metal,
      search: formVals.search
    }).subscribe({
      next: (data) => {
        this.isLoading.set(false);
        this.products.set(Array.isArray(data) ? data : (data as any).results || []);
      },
      error: () => {
        this.isLoading.set(false);
      }
    });
  }

  selectSuggestion(name: string): void {
    this.filterForm.patchValue({ search: name });
    this.suggestions.set([]);
  }

  addToCart(productId: number, event: Event): void {
    event.stopPropagation(); // prevent card click redirect
    if (!this.authService.isAuthenticated()) {
      this.router.navigate(['/login']);
      return;
    }
    
    this.cartService.addToCart(productId, 1).subscribe({
      next: () => {
        alert('Item added to cart!');
      },
      error: () => {
        alert('Failed to add item.');
      }
    });
  }

  toggleWishlist(productId: number, event: Event): void {
    event.stopPropagation(); // prevent card click redirect
    if (!this.authService.isAuthenticated()) {
      this.router.navigate(['/login']);
      return;
    }
    
    this.cartService.toggleWishlist(productId).subscribe({
      next: (res) => {
        alert(res.message);
      }
    });
  }
}
