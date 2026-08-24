# Frontend Technology Stack Details (Angular)

This document outlines the libraries, tools, and frameworks used in the Jewelry SaaS Angular frontend repository.

## 1. Core Framework & Language
- **Angular (v17+)** (`@angular/core`, `@angular/common`): The core client-side web application framework.
- **TypeScript 5**: The primary language for defining components, modules, services, and structural models.
- **RxJS**: For reactive programming using Observables, particularly for handling events, asynchronous operations, and backend HTTP data streams.

## 2. Routing & Navigation
- **Angular Router** (`@angular/router`): Built-in client-side router supporting lazy loading of modules, router guards for authentication, and child routes.

## 3. Styling & Layout
- **Tailwind CSS 4** (`tailwindcss`): Utility-first CSS framework integrated with the Angular build process.
- **Angular Material** (Optional, e.g. `@angular/material`): Prebuilt, accessible components following Google's Material Design guidelines.

## 4. UI Components & Visuals
- **FontAwesome / Lucide Icons**: Icon sets integrated into Angular components.
- **Ngx-Charts / Chart.js**: For data visualization.
- **Ngx-Toastr / Angular Hot Toast**: Toast notification system.

## 5. Forms & Validation
- **Reactive Forms** (`@angular/forms`): Standard module for model-driven, reactive form management and complex input validation.

## 6. HTTP Client & State Management
- **HttpClientModule** (`@angular/common/http`): Built-in module for executing HTTP REST requests with interceptors for attaching authentication headers/tokens.
- **NgRx or Akita** (Optional, as needed): For global state management.

## 7. Developer Tooling & Testing
- **Angular CLI**: Tooling for scaffolding, building, running, and testing the application.
- **Vite & Esbuild**: Modern, high-performance builder used under the hood in modern Angular versions for faster compilation.
- **Jasmine & Karma / Jest**: For writing and executing unit tests.
