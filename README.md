# Modern ERP

ERP profesional para pequeñas y medianas empresas (PYMEs), enfocado en **inventario**, **ventas** y **administración empresarial**.

![Stack](https://img.shields.io/badge/Laravel-12-red) ![React](https://img.shields.io/badge/React-18-blue) ![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-blue) ![Docker](https://img.shields.io/badge/Docker-Compose-2496ED)

## Características

- Dashboard ejecutivo con métricas y gráficas interactivas
- Módulos: Inventario, Compras, Ventas, Clientes, Proveedores
- Kardex (historial de movimientos con costo promedio)
- Roles y permisos granulares (Spatie Permission)
- Reportes exportables PDF / Excel
- Autenticación JWT
- WebSockets (Laravel Reverb) para actualizaciones de inventario en tiempo real
- API REST documentada con Swagger
- Logs de actividad y auditoría
- UI SaaS premium con dark mode (inspirado en Stripe / Linear)
- Búsqueda avanzada, filtros dinámicos y paginación

## Stack tecnológico

| Capa | Tecnología |
|------|------------|
| Backend | Laravel 12, PHP 8.3 |
| Frontend | React 18, Vite, TypeScript, TailwindCSS |
| Base de datos | PostgreSQL 16 |
| Auth | JWT (php-open-source-saver/jwt-auth) |
| Tiempo real | Laravel Reverb + Laravel Echo |
| Contenedores | Docker Compose |

## Inicio rápido con Docker

### Requisitos

- Docker Desktop 4.x+
- Docker Compose v2

### Levantar el entorno

```bash
docker compose up -d --build
```

| Servicio | URL |
|----------|-----|
| Frontend | http://localhost:5173 |
| API Backend | http://localhost:8000 |
| Swagger Docs | http://localhost:8000/api/documentation |
| WebSocket (Reverb) | ws://localhost:8080 |
| PostgreSQL | localhost:5432 |

### Credenciales demo

| Usuario | Contraseña | Rol |
|---------|------------|-----|
| admin@erp.local | password | Administrador |
| gerente@erp.local | password | Gerente |

## Desarrollo local (sin Docker)

### Backend

```bash
cd backend
cp .env.example .env
composer install
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed
php artisan serve
php artisan reverb:start   # terminal separada
```

### Frontend

```bash
cd frontend
npm install
npm run dev
```

Variables en `frontend/.env`:

```env
VITE_API_URL=http://localhost:8000/api
VITE_WS_HOST=localhost
VITE_WS_PORT=8080
VITE_WS_KEY=erp-key-local
```

## Estructura del proyecto

```
Proyecto1/
├── backend/                 # API Laravel
│   ├── app/
│   │   ├── Http/Controllers/Api/
│   │   ├── Services/        # Lógica de negocio
│   │   ├── Events/
│   │   └── Exports/
│   ├── database/migrations/
│   └── tests/
├── frontend/                # SPA React
│   └── src/
│       ├── components/
│       ├── pages/
│       └── stores/
├── docker-compose.yml
└── .github/workflows/       # CI/CD
```

## API principal

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| POST | `/api/auth/login` | Login JWT |
| GET | `/api/dashboard` | Métricas ejecutivas |
| CRUD | `/api/products` | Inventario |
| CRUD | `/api/customers` | Clientes |
| CRUD | `/api/suppliers` | Proveedores |
| GET/POST | `/api/sales` | Ventas |
| GET/POST | `/api/purchases` | Compras |
| GET | `/api/kardex` | Movimientos Kardex |
| GET | `/api/reports/sales` | Reporte ventas |
| GET | `/api/activity-logs` | Auditoría |

Documentación completa: `php artisan l5-swagger:generate` → `/api/documentation`

## Tests

```bash
cd backend
php artisan test
# o
vendor/bin/phpunit
```

## CI/CD

GitHub Actions ejecuta automáticamente:

- Tests PHPUnit (backend)
- Build del frontend
- Lint TypeScript

Ver `.github/workflows/ci.yml`

## Arquitectura

```
┌─────────────┐     JWT REST      ┌──────────────┐
│  React SPA  │ ◄──────────────► │ Laravel API  │
└──────┬──────┘                   └──────┬───────┘
       │ WebSocket                      │
       ▼                                ▼
┌─────────────┐                   ┌──────────────┐
│   Reverb    │                   │  PostgreSQL  │
└─────────────┘                   └──────────────┘
```

- **Capa de servicios**: `InventoryService`, `SaleService`, `PurchaseService`
- **Kardex**: registro atómico con costo promedio ponderado
- **Permisos**: middleware `permission:*` por módulo
- **Eventos**: `InventoryUpdated` broadcast en canal `inventory`

## Licencia

2026 Modern ERP
