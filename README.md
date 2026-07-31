# CloudPOS ERP SaaS

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel 12" />
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php" alt="PHP 8.2" />
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql" alt="MySQL 8.0" />
  <img src="https://img.shields.io/badge/Redis-7-DC382D?style=for-the-badge&logo=redis" alt="Redis 7" />
  <img src="https://img.shields.io/badge/Docker-Ready-2496ED?style=for-the-badge&logo=docker" alt="Docker Ready" />
</p>

A modern Multi-Tenant SaaS Cloud POS and ERP platform designed for scaling retail operations across multiple companies, branches, and business units from a single codebase. The system is structured around three core operational layers: a Super Admin SaaS control plane, a Company (tenant) ERP dashboard, and a Branch POS terminal tailored to daily sales and inventory workflows.

---

## 1. Project Overview

CloudPOS ERP SaaS is a multi-tenant business platform built for companies that need a unified commerce and operations stack across distributed retail locations. It combines point-of-sale, inventory tracking, branch management, payroll, procurement, financial controls, and tenant-level administration into a single Laravel-powered application.

This repository is designed for SaaS-style deployment, where a platform owner can provision and manage many independent tenant companies, each with its own admins, branches, users, products, sales, and business rules. The solution is especially well suited for:

- Retail chains and franchise operations
- Multi-location product distributors
- POS-driven businesses with branch-level reporting
- ERP environments that require tenant isolation and centralized administration

### Business model supported

The application supports a super-admin-driven SaaS model with:

- Platform-level company onboarding and tenant provisioning
- Subscription and plan management
- Tenant scoping and role-based access control
- Global system configuration and platform administration
- Domain or tenant-level separation for business data and users

### Core architectural idea

The platform is intentionally layered to separate concerns:

- Super Admin: manages the SaaS platform and all tenant companies
- Company (Tenant): owns business operations and ERP workflows
- Branch: executes the daily sales and inventory operations on the ground

This layered architecture helps reduce operational risk and makes the system scalable across multiple businesses while keeping each tenant logically isolated.

---

## 2. Core Features

### 🏢 Super Admin Panel

The Super Admin layer acts as the SaaS control center for the entire ecosystem.

- Tenant provisioning and company onboarding
- Subscription plan management and renewals
- Company lifecycle management and access control
- Global platform configuration and settings
- User and role administration
- System health and application information views
- Database backup generation and restore management
- Business modules, invoice templates, barcode settings, and global reference data
- Platform reports and tenant usage analytics

### 🏢 Company (Tenant) ERP Panel

The Company panel is the tenant-level ERP workspace for managing the business holistically.

#### Master Data & Operations

- Products, categories, suppliers, customers, and attributes
- Inventory management and stock adjustments
- Purchases, expenses, transfers, and supplier management
- Quotations and commercial proposals
- Purchase returns and sales returns
- Sales and operational reporting

#### Financial Management

- Cashbook and cash account management
- Balance tracking and transfers
- Expenses and payment tracking
- Loan authority and loan tracking
- Asset registration and tracking
- Financial reporting and business summaries

#### Human Resources & Payroll

- Department and employee management
- Salary increments and payroll processing
- Payslips and payroll status tracking
- HR records with tenant-level visibility

### 🏪 Branch (POS) Panel

The Branch panel is optimized for storefront and counter operations.

- Point-of-sale checkout flow
- Inventory visibility and stock adjustments
- Sales tracking and branch sales history
- Barcode printing for product labels
- Daily sales reporting and branch performance views
- Inventory reports and stock movement summaries
- Sales return processing at the branch level
- Shift management and POS workflow control

---

## 3. Tech Stack

| Layer | Technology | Purpose |
| --- | --- | --- |
| Backend | Laravel 12 | Core application framework and business logic |
| Runtime | PHP 8.2 | Server-side application runtime |
| Database | MySQL 8.0 | Primary relational datastore for tenants, transactions, and ERP entities |
| Cache / Queue | Redis | Sessions, caching, queue processing, background jobs |
| Frontend | Blade + Tailwind CSS | Server-rendered views and modern UI styling |
| Build tooling | Vite + Node.js | Frontend asset bundling and optimization |
| Containerization | Docker + Docker Compose | Local development environment and service orchestration |
| Testing | PHPUnit | Automated application testing |
| PDF | DOMPDF | Invoice and printable document generation |
| Auth / RBAC | Laravel Sanctum + Spatie Permissions | Security, access rules, and tenant-aware authorization |

### Key runtime characteristics

- Multi-tenant ready architecture with tenant-aware access and scoping
- Role-based permission system for SaaS, company, and branch teams
- Queue-driven processing with Redis and database-backed jobs
- Docker-first local setup for consistent development environments
- Modular ERP and POS design for future expansion

---

## 4. Project Structure

```text
.
├── app/                          # Laravel application code
│   ├── Console/
│   ├── Http/
│   ├── Models/
│   ├── Observers/
│   ├── Policies/
│   ├── Providers/
│   ├── Services/
│   └── Traits/
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── docker/
│   └── nginx/
├── public/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
├── routes/
├── storage/
├── tests/
├── .env.example
├── composer.json
├── package.json
├── docker-compose.yml
├── Dockerfile
├── Dockerfile.assets
├── artisan
├── vite.config.js
├── tailwind.config.js
├── phpunit.xml
├── README.md
└── README_DOCKER.md
```

---

## 5. Installation & Setup Guide (Docker Focused)

This project is designed to run locally with Docker using a compose-based environment. The repository includes a ready-to-use Docker setup for the app, MySQL, Redis, Nginx, and the asset build service.

### Prerequisites

Before beginning, make sure the following are installed on your machine:

- Docker Desktop or Docker Engine
- Docker Compose
- Git
- A terminal such as PowerShell, bash, or zsh

### 1. Clone the repository

```bash
git clone <repository-url>
cd cloud-pos-inventory-v5
```

### 2. Copy environment variables

Create your local environment file from the template:

```bash
copy .env.example .env
```

On Linux/macOS:

```bash
cp .env.example .env
```

The Docker stack is configured for the following containerized services:

- App container running Laravel/PHP
- MySQL 8.0 database container
- Redis container
- Nginx reverse proxy on port 8080
- Node/Vite asset build service

### 3. Build and start the stack

Start the multi-container environment:

```bash
docker compose up -d --build
```

This will build the application image and launch the environment in the background.

### 4. Install Composer dependencies

Once the containers are running, use the app container to install PHP dependencies:

```bash
docker compose exec app composer install
```

If the application key is not already generated, run:

```bash
docker compose exec app php artisan key:generate
```

### 5. Run the optimized migrations and seeders

The project includes the full seeded data model needed for a realistic SaaS, company, branch, and POS experience. To initialize the database and seed demo data, run:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

This command resets the database, runs the full migration set, and seeds the application with the default SaaS data model.

> The repository is structured to support a curated set of optimized migrations and seeders for tenant, company, branch, roles, permissions, demo products, and business configuration data.

### 6. Verify the app is running

Open the application in your browser:

```text
http://localhost:8080
```

If the app is not reachable immediately, check container health and logs:

```bash
docker compose ps
docker compose logs -f app
```

### 7. Common seeded access credentials

The default seeder creates demo users and tenant structure for local testing. Typical seeded accounts include:

- Super Admin: `superadmin@system.com` / `password`
- Company Admins: `admin@alpharetail.example`, `admin@betaelectronics.example`, `admin@gammafashion.example` / `password`
- Branch Managers and Salesmen are generated for each seeded tenant and branch

These credentials are useful for validating the SaaS, ERP, and branch flows quickly.

---

## 6. Local Development Commands

### Start the stack

```bash
docker compose up -d
```

### Stop the stack

```bash
docker compose down
```

### Restart containers

```bash
docker compose restart
```

### Watch logs

```bash
docker compose logs -f
```

### Run artisan commands inside the app container

```bash
docker compose exec app php artisan migrate

docker compose exec app php artisan db:seed

docker compose exec app php artisan queue:work
```

### Frontend build commands

```bash
docker compose run --rm node npm install
docker compose run --rm node npm run build
```

---

## 7. Environment and Configuration Notes

The repository uses a Laravel-style configuration pattern with environment variables defined in `.env`.

The Docker compose configuration includes the following default service values:

- `DB_HOST=db`
- `DB_DATABASE=cloudpos`
- `DB_USERNAME=cloudpos`
- `DB_PASSWORD=secret`
- `REDIS_HOST=redis`
- App is exposed via Nginx on port `8080`

For production or shared environments, update your `.env` values and do not store secrets in version control.

---

## 8. License

This project is distributed under the MIT license unless otherwise specified in a project file or repository policy.

---

## 9. Project Status

This documentation-first phase is intentionally focused on product clarity, architecture understanding, and local onboarding. CI/CD and production automation are deferred until the current application architecture and operational workflows are fully stabilized.

---

## 10. Why this project matters

CloudPOS ERP SaaS is more than a retail POS application; it is a business platform for modern SaaS operations. It blends multi-tenant administration, ERP financial management, and branch-level transaction processing into a unified system that can support real business complexity without forcing a fragmented toolchain.

The application is built to help organizations:

- manage multiple companies and branches from one platform
- centralize core operations while keeping each tenant isolated
- reduce operational friction between executive dashboards and store-level activity
- standardize business processes across a growing retail footprint

---

## 11. Contributing

Contributions are welcome. The recommended approach is to:

1. Fork the repository
2. Create a feature branch
3. Implement a focused change
4. Validate the Laravel app behavior locally
5. Open a pull request with a clear explanation of the change

For ongoing development, keep documentation and onboarding clarity in sync with feature changes.

---

## 12. Support

For architectural questions, environment setup issues, or feature review, use the repository issues and project discussions for structured communication. For local development, the Docker workflow remains the primary supported onboarding path.

---

### Summary

CloudPOS ERP SaaS is a production-oriented, multi-tenant inventory and POS platform designed for Laravel-based SaaS delivery. It combines the flexibility of a modern ERP with the speed and simplicity of a retail point-of-sale system, all while supporting scalable operations across independent tenant companies and branch locations.

If you are setting up the project locally, the recommended path is:

```bash
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan migrate:fresh --seed
```

Then open:

```text
http://localhost:8080
```
