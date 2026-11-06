# Invoice & Budget Management System

A web application built using **Laravel** and **Bootstrap 5** to manage purchase invoices, sales invoices, payments, budgets, expenses, and financial tracking through a clean admin dashboard.

---

## Features

- **Authentication & Dashboard**: Secure login with financial summary (Total Sales, Purchases, Expenses, and Sales Invoice Payment Status).
- **Customer & Supplier Management**: Full CRUD operations for managing business contacts.
- **Product Management**: Manage products and services with pricing and tax information.
- **Purchase Invoices**: Create, edit, view, and delete purchase invoices with dynamic multi-item line calculations (subtotal, tax, discount, and grand total).
- **Sales Invoices**: Complete sales invoice workflow with automatic invoice status calculation (`Paid`, `Pending`, `Overdue`).
- **Payment Management**: Record partial or full payments against both Sales and Purchase invoices with live remaining balance tracking.
- **Budget Management**: Set up monthly or yearly category budgets with visual progress indicators for spent and remaining amounts.
- **Expense Tracking**: Record business expenses linked to allocated budgets to monitor spending limits.
- **Reports & Analytics**: Financial performance overview (Total Sales, Purchases, Expenses, Net Profit) with custom date-range filters, Budget vs Actual variance table, and printable reports.
- **Public API Integration**: Integrated with ExchangeRate-API to fetch live foreign currency exchange rates (USD, EUR, GBP, AED, CAD, SGD to INR) with a quick currency converter widget and offline fallback support.

---

## Requirements

- PHP >= 8.2
- Composer
- MySQL >= 8.0 or MariaDB
- Web Browser

---

## Installation & Setup Guide

Follow these steps to set up and run the project locally:

### 1. Clone the Repository
```bash
git clone <repository-url>
cd invoice-system
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Environment Configuration
Copy the `.env.example` file to `.env`:
```bash
cp .env.example .env
```
*(On Windows PowerShell, use `copy .env.example .env`)*

Generate the application encryption key:
```bash
php artisan key:generate
```

### 4. Database Setup
1. Create a MySQL database (e.g. `invoice_system`).
2. Update your `.env` file with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=invoice_system
   DB_USERNAME=root
   DB_PASSWORD=
   ```

### 5. Run Migrations & Seed Default Admin
Run database migrations along with the default user seeder:
```bash
php artisan migrate --seed
```

### 6. Public API Configuration (Optional)
To display live currency exchange rates on the dashboard, add your API key from [ExchangeRate-API](https://www.exchangerate-api.com) to your `.env` file:
```env
EXCHANGE_RATE_API_KEY=your_api_key_here
```
*(If omitted or offline, the system safely uses cached fallback rates without any errors).*

### 7. Start the Local Server
```bash
php artisan serve
```

The application will be accessible at: `http://127.0.0.1:8000`

---

## Default Login Credentials

After running `php artisan migrate --seed`, you can log in with:

- **Email:** `admin@gmail.com`
- **Password:** `password`

---

## Project Structure Overview

- `app/Http/Controllers/` - Controllers handling business logic (Dashboard, Invoices, Payments, Budgets, Expenses, Reports)
- `app/Models/` - Eloquent models with relationships, polymorphic payments, and calculation accessors
- `resources/views/` - Clean, uniform Blade views built with Bootstrap 5
- `routes/web.php` - Web routes protected by authentication middleware
