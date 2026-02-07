# AI Coding Guidelines for BusinessPoint

## Architecture Overview
This is a Laravel 12 application with Inertia.js + Vue 3 SPA architecture. The backend serves APIs for both web frontend and mobile apps.

**Key Patterns:**
- **Repository Pattern**: All data access through repositories extending `BaseRepository` (app/Repositories/BaseRepository.php)
- **Service Layer**: Business logic in services (app/Services/) that inject repositories
- **Observer Pattern**: Automatic auditing via `AuditLogObserver` on models
- **API Responses**: Use `ApiResponse::success()` and `ApiResponse::error()` for consistent JSON responses

## Code Structure
```
app/
├── Http/Controllers/Api/V1/     # API controllers extending BaseController
├── Repositories/                # Data access layer extending BaseRepository
├── Services/                    # Business logic layer
├── Observers/                   # Model event observers (e.g., AuditLogObserver)
└── Models/                      # Eloquent models
```

## API Design
- **Versioned**: All APIs under `/api/v1/`
- **Middleware**: `api_key` for public routes, `auth:sanctum` for protected user routes
- **Admin Backend**: Session-based auth under `/admin/` routes
- **Response Format**: `{success: bool, message: string, data: any, errors?: array}`

## Frontend (Vue 3 + Inertia)
- **State Management**: Pinia stores in `resources/js/store/`
- **Routing**: Vue Router with Ziggy for Laravel route helpers
- **Styling**: Tailwind CSS v4
- **Build**: Vite with hot reload

## Development Workflow
1. **Setup**: `composer install && php artisan key:generate && php artisan migrate && npm install`
2. **Development**: `php artisan serve` (backend) + `npm run dev` (frontend)
3. **Testing**: `php artisan test` (PHPUnit)
4. **Build**: `npm run build` for production assets

## Conventions
- **Controllers**: Extend `BaseController`, inject services, return `$this->success()` or `$this->error()`
- **Repositories**: Extend `BaseRepository` for CRUD with built-in pagination/filters
- **Services**: Handle validation, business rules, coordinate repositories
- **Models**: Use observers for auditing; exclude sensitive fields via `getAuditExcludes()`
- **Media**: Upload to structured folders (resize images), associate via polymorphic `Media` model
- **Exports**: Include branding headers/footers/watermarks for CSV/Excel/PDF

## Adding New Features
1. Create migration + model
2. Add repository extending `BaseRepository`
3. Create service with business logic
4. Add API controller in `Api/V1/` with validation
5. Register routes in `routes/api/v1.php` or `web.php`
6. Add frontend Vue components in `resources/js/Pages/`
7. Update Pinia stores if needed

## Key Files
- `app/Helpers/ApiResponse.php`: Standardized API responses
- `app/Repositories/BaseRepository.php`: Pagination, filtering, sorting logic
- `app/Observers/AuditLogObserver.php`: Automatic change logging
- `routes/api/v1.php`: API route definitions
- `resources/js/app.js`: Vue app entry point</content>
<parameter name="filePath">c:\xampp\htdocs\BusinessPoint\.github\copilot-instructions.md