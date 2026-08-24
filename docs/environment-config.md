# Environment Configuration Mapping (Local, Beta, Prod)

This document describes how environment-specific configurations are mapped and loaded across the application for **Local Development**, **Beta Staging**, and **Production** environments.

---

## 1. Django Backend Environment Mapping

To handle environment configuration dynamically, we use a single `settings.py` that reads environment variables, or splits into modular settings files.

### Configuration Variables by Environment

| Configuration Item | Local Development | Beta (Staging) | Production |
| :--- | :--- | :--- | :--- |
| **`ENVIRONMENT`** | `local` | `beta` | `prod` |
| **`DEBUG`** | `True` | `True` | `False` |
| **`ALLOWED_HOSTS`** | `["localhost", "127.0.0.1"]` | `["beta.yourdomain.com"]` | `["yourdomain.com", "www.yourdomain.com"]` |
| **`CORS_ALLOWED_ORIGINS`** | `["http://localhost:4200"]` | `["https://beta.yourdomain.com"]` | `["https://yourdomain.com"]` |
| **`SECURE_SSL_REDIRECT`** | `False` | `True` | `True` |

### Implementation in `backend/config/settings.py`

You can implement this mapping dynamically at the bottom of your settings file:

```python
import os
from pathlib import Path

BASE_DIR = Path(__file__).resolve().parent.parent

# Read the environment variable (default to 'local')
ENV = os.environ.get('ENVIRONMENT', 'local').lower()

# Basic App Security Settings
SECRET_KEY = os.environ.get('SECRET_KEY', 'default-unsafe-key-for-local-dev')

if ENV == 'prod':
    DEBUG = False
    ALLOWED_HOSTS = ["yourdomain.com", "www.yourdomain.com"]
    SECURE_SSL_REDIRECT = True
    SESSION_COOKIE_SECURE = True
    CSRF_COOKIE_SECURE = True
elif ENV == 'beta':
    DEBUG = True
    ALLOWED_HOSTS = ["beta.yourdomain.com"]
    SECURE_SSL_REDIRECT = True
    SESSION_COOKIE_SECURE = True
    CSRF_COOKIE_SECURE = True
else:  # local
    DEBUG = True
    ALLOWED_HOSTS = ["localhost", "127.0.0.1"]
    SECURE_SSL_REDIRECT = False
    SESSION_COOKIE_SECURE = False
    CSRF_COOKIE_SECURE = False

# Database Configuration mapping
# Read values from environment (provided by docker-compose or VPS systemd/env files)
DB_HOST = os.environ.get('DB_HOST', 'localhost')
DB_PORT = os.environ.get('DB_PORT', '5432')
DB_NAME = os.environ.get('DB_NAME', 'jewelry_db')
DB_USER = os.environ.get('DB_USER', 'postgres')
DB_PASSWORD = os.environ.get('DB_PASSWORD', 'postgrespassword')

DATABASES = {
    'default': {
        'ENGINE': 'django.db.backends.postgresql',
        'NAME': DB_NAME,
        'USER': DB_USER,
        'PASSWORD': DB_PASSWORD,
        'HOST': DB_HOST,
        'PORT': DB_PORT,
    }
}

# CORS Origin Mapping
CORS_ALLOWED_ORIGINS = []
if ENV == 'prod':
    CORS_ALLOWED_ORIGINS = ["https://yourdomain.com"]
elif ENV == 'beta':
    CORS_ALLOWED_ORIGINS = ["https://beta.yourdomain.com"]
else:
    CORS_ALLOWED_ORIGINS = ["http://localhost:4200"]
```

---

## 2. Angular Frontend Environment Mapping

Angular handles environment configuration during compile-time using the CLI configurations defined in `angular.json` and files under `src/environments/`.

### Configuration Files Structure

Create three files in `src/environments/`:

#### A. Local Dev: `src/environments/environment.ts`
```typescript
export const environment = {
  production: false,
  environmentName: 'local',
  apiUrl: 'http://localhost:8000/api'
};
```

#### B. Beta/Staging: `src/environments/environment.beta.ts`
```typescript
export const environment = {
  production: false,
  environmentName: 'beta',
  apiUrl: 'https://beta.yourdomain.com/api'
};
```

#### C. Production: `src/environments/environment.prod.ts`
```typescript
export const environment = {
  production: true,
  environmentName: 'production',
  apiUrl: 'https://yourdomain.com/api'
};
```

### Build-Time Mapping in `angular.json`

Add the environment file replacements under the `configurations` block:

```json
{
  "projects": {
    "jewelry-saas-frontend": {
      "architect": {
        "build": {
          "configurations": {
            "beta": {
              "fileReplacements": [
                {
                  "replace": "src/environments/environment.ts",
                  "with": "src/environments/environment.beta.ts"
                }
              ]
            },
            "production": {
              "fileReplacements": [
                {
                  "replace": "src/environments/environment.ts",
                  "with": "src/environments/environment.prod.ts"
                }
              ],
              "optimization": true,
              "outputHashing": "all",
              "sourceMap": false
            }
          }
        }
      }
    }
  }
}
```

### Build Commands

To build the appropriate bundle, run:
- **Local Dev Server**: `ng serve` (uses default `environment.ts`)
- **Beta Build**: `ng build --configuration=beta` (uses `environment.beta.ts`)
- **Production Build**: `ng build --configuration=production` (uses `environment.prod.ts`)
