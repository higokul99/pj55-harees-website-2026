import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface Category {
  id: number;
  parent?: number;
  name: string;
  slug: string;
  description?: string;
  making_charges: string;
  making_charges_type: string;
  waste_percentage: string;
}

export interface ProductImage {
  id: number;
  image_path: string;
  alt_text?: string;
  sort_order: number;
  is_primary: boolean;
}

export interface ProductList {
  id: number;
  product_code: string;
  sku?: string;
  name: string;
  slug: string;
  category: number;
  category_name: string;
  metal_name: string;
  stock_quantity: number;
  price?: string;
  is_featured: boolean;
  gender: string;
  primary_image?: ProductImage;
  specifications?: any;
}

export interface ProductDetail extends ProductList {
  description?: string;
  images: ProductImage[];
  metal_purity: number;
  supplier?: number;
  size?: string;
  gross_weight: string;
  net_weight?: string;
  product_model?: string;
  manufacture_time?: string;
  stone_available: boolean;
  stone_desc?: string;
  stone_color?: string;
  stone_shape?: string;
  stone_count?: number;
  stone_weight?: string;
  stone_cost?: string;
  diamond_available: boolean;
  dia_desc?: string;
  dia_cent?: string;
  dia_count?: number;
  dia_cut?: string;
  dia_color?: string;
  dia_clarity?: string;
  dia_shape?: string;
  beads_available: boolean;
  beads_desc?: string;
  beads_color?: string;
  beads_count?: number;
  beads_weight?: string;
  beads_cost?: string;
  pearls_available: boolean;
  pearls_desc?: string;
  pearls_color?: string;
  pearls_count?: number;
  pearls_weight?: string;
  pearls_cost?: string;
  category_details?: Category;
  purity_details?: { id: number; name: string; purity_value: string };
}

export interface GoldRate {
  id: number;
  metal_purity: number;
  purity_name: string;
  rate_per_gram: string;
  effective_date: string;
}

@Injectable({
  providedIn: 'root'
})
export class ProductService {
  private apiUrl = 'http://localhost:8000/api/v1';

  constructor(private http: HttpClient) {}

  getCategories(): Observable<Category[]> {
    // Django viewsets return paginated or unpaginated list depending on setup,
    // assuming DRF list view structure or custom success envelope
    return this.http.get<Category[]>(`${this.apiUrl}/categories/`);
  }

  getProducts(filters?: {
    category?: string;
    metal?: string;
    gender?: string;
    featured?: boolean;
    search?: string;
  }): Observable<ProductList[]> {
    let params = new HttpParams();
    if (filters) {
      if (filters.category) params = params.set('category', filters.category);
      if (filters.metal) params = params.set('metal', filters.metal);
      if (filters.gender) params = params.set('gender', filters.gender);
      if (filters.featured) params = params.set('featured', String(filters.featured));
      if (filters.search) params = params.set('search', filters.search);
    }
    return this.http.get<ProductList[]>(`${this.apiUrl}/products/`, { params });
  }

  getProductDetail(idOrSlug: string | number): Observable<{ success: boolean; data: ProductDetail }> {
    return this.http.get<{ success: boolean; data: ProductDetail }>(`${this.apiUrl}/products/${idOrSlug}/`);
  }

  getSearchSuggestions(query: string): Observable<{ success: boolean; suggestions: { id: number; name: string; slug: string }[] }> {
    return this.http.get<{ success: boolean; suggestions: any[] }>(`${this.apiUrl}/search/suggestions/`, {
      params: new HttpParams().set('q', query)
    });
  }

  getGoldRates(): Observable<GoldRate[]> {
    return this.http.get<GoldRate[]>(`${this.apiUrl}/gold-rates/`);
  }
}
