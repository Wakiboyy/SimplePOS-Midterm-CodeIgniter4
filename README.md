# SimplePOS — Complete CodeIgniter 4 Point-of-Sale System

**Assessment:** IT0049 Web System Technologies — Midterm Project  
**Framework:** CodeIgniter 4 · PHP 8.2+ · MySQL / MariaDB (InnoDB)  
**Theme:** Full POS inventory, staff, customers, and sales management

This ZIP contains **finished project-specific application files**, not the copyrighted Composer dependency tree. Install the official **CodeIgniter 4 appstarter** first, then copy/extract these files into it. This preserves a clean, updatable framework installation.

## Features and required pages

| Function | Route | Description |
|---|---|---|
| Sign in | `/login` | Staff authentication, hashed passwords, regenerated session |
| Dashboard | `/`, `/dashboard` | Sales, revenue, inventory, customer metrics, low-stock notices |
| Products | `/products` | Create, edit, list, remove/archive products; 640×640 JPEG photo |
| Customers | `/customers` | Full CRUD; name and valid email required, phone optional. |
| Staff | `/staff` | Full CRUD, secure passwords, 256×256 JPEG avatar; archive past sellers |
| Record sale | `/sales/new` | One product and quantity, optional customer; stock decreases atomically |
| Sales history | `/sales` | Transaction, date, product, customer, staff, quantity, total |
| Receipt | `/sales/{id}` | Printable receipt |

All management pages are **protected by an authentication filter** and all POST forms have CSRF tokens. Auto-routing is disabled.

## Install — Windows PowerShell and XAMPP

**Prerequisites:** PHP 8.2+, Composer, XAMPP with MySQL, and PHP extensions `intl`, `mbstring`, `mysqli`, `fileinfo`, and `gd` enabled. **The GD extension is necessary for the assignment's image-crop requirement.** You do not need Laravel, Node.js, npm or VS Code.

Open XAMPP Control Panel and start **MySQL** and (for phpMyAdmin) **Apache**. Open PowerShell. Paste one command at a time:

```powershell
cd C:\xampp\htdocs
composer create-project codeigniter4/appstarter simplepos-midterm
```

Download `SimplePOS-Midterm-CodeIgniter4.zip` to the Downloads folder and extract it into the **new, existing** starter folder:

```powershell
Expand-Archive -LiteralPath "$HOME\Downloads\SimplePOS-Midterm-CodeIgniter4.zip" -DestinationPath "C:\xampp\htdocs\simplepos-midterm" -Force
cd C:\xampp\htdocs\simplepos-midterm
Copy-Item .env.midterm.example .env
```

**Do not extract into an old TFA project**; this is a different CodeIgniter midterm project.

Create the MySQL database using <http://localhost/phpmyadmin> → **SQL**:

```sql
CREATE DATABASE IF NOT EXISTS pos_midterm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

The sample `.env` uses local XAMPP's usual MySQL account (`root`, blank password). If your MySQL credentials are different, edit `.env` using `notepad .env`. For a hosted application use a dedicated limited-privilege MySQL user, not root.

Verify extensions before migrations:

```powershell
php check-requirements.php
```

If `GD MISSING`, open `notepad C:\xampp\php\php.ini`, search for `;extension=gd`, change to `extension=gd`, save, close and reopen PowerShell. Likewise enable `extension=intl`, `extension=mbstring`, `extension=mysqli` if disabled. Restart the PHP server after editing php.ini.

Run these commands **in order**:

```powershell
php spark migrate
php spark db:seed PosDemoSeeder
php spark db:seed AdminSeeder
php spark serve
```

**IMPORTANT:** `AdminSeeder` prints a unique generated password **once** in PowerShell. Save it somewhere private. The initial username is `admin`; the password is not included in this ZIP and is not stored in plaintext. If staff already exist, the seeder will not overwrite them. Do not include this password in screenshots, videos, GitHub, or your report.

Visit **http://localhost:8080/login**. After logging in, explore the dashboard and navigation. Keep the serving PowerShell terminal open. Press `Ctrl+C` when done; your database and project files stay saved.

## Database design

The migration `app/Database/Migrations/2026-11-24-000001_CreatePosTables.php` creates **products**, **customers**, **users**, and **sales** with the exact main columns from the rubric plus `updated_at` and `is_active` for audit-friendly CRUD. All tables use **InnoDB**. Foreign keys: `sales.product_id → products.id`, `sales.customer_id → customers.id` (nullable, `SET NULL` on delete), and `sales.sold_by → users.id`.

The demo seeder creates **8 products** and **6 customers**. The separate admin seeder creates the first staff account with a securely randomized password and `password_hash()`. Seeders avoid inserting duplicate demo records if you run them again.

## How checkout protects inventory

`SaleRecorder::record()` starts an InnoDB transaction, locks the chosen product with `SELECT ... FOR UPDATE`, checks active status and stock, then executes `UPDATE ... WHERE stock_quantity >= ?`. The server computes the total from the stored price and quantity, inserts a sale, and commits. If the quantity is too high or any step fails, the transaction rolls back. **A rejected sale inserts nothing and changes no inventory.**

For past sales, referenced products and staff accounts are **archived instead of physically deleted**. This keeps the sales history and foreign keys valid. Customers may be deleted; their historical sales remain listed as Walk-in / Guest.

## Upload processing

`UploadedImage::prepare()` accepts only a validated JPG, PNG or WebP file up to 2 MB, randomly generates a new filename, center-crops the picture to **640 × 640 product JPEG** or **256 × 256 staff JPEG**, and stores the prepared output under `public/uploads/`. It does **not** retain the original upload. Old photos are removed when replaced. Never commit any real personal photo or private upload to GitHub. The image code requires PHP GD.

## Project architecture

```text
app/
  Config/Routes.php               Defined GET/POST routes only; no auto-routing
  Config/Filters.php              CSRF globally + auth alias
  Filters/AuthFilter.php          Sessions and active-account checks
  Controllers/                    Auth, Dashboard, Products, Customers, Staff, Sales
  Models/                         ProductModel, CustomerModel, StaffModel, SaleModel
  Services/SaleRecorder.php       Atomic stock-safe checkout
  Services/UploadedImage.php      Image validation and cropping service
  Database/Migrations/           MySQL schema with foreign keys
  Database/Seeds/                Sample products/customers and first admin account
  Views/                         Blade-free PHP views with shared layout
public/assets/                    Local CSS, JS and SVG icons
public/uploads/                   Runtime image output (ignored by Git)
```

## Development, hosting and GitHub

For your GitHub repository, commit the **complete CodeIgniter starter project plus these source files**. Include `composer.json`, `composer.lock`, `app/`, `public/`, migration and seeders, README and `.env.midterm.example`. Do **not** commit `vendor/`, `.env`, `writable/` session/log/cache content, or `public/uploads/` user files. Run `composer install --no-dev --optimize-autoloader` on the host, configure the hosted `.env`, create the database, run `php spark migrate` and provision the initial staff account securely. Point the site's document root at the **public/** directory (never the project root), serve over HTTPS, and set `CI_ENVIRONMENT=production`. Configure write permissions to `writable/` and `public/uploads/`. **Do not expose `AdminSeeder` on a publicly reachable deployment before a trusted operator controls setup.**

### Submission requirements

The rubric specifically asks for (1) a **GitHub repository link** with all raw project source, migrations, and seeders and (2) a **live hosted working website link**. Local screenshots are useful evidence but do not substitute for these two URLs. See `SUBMISSION-CHECKLIST.md` and `TEST-PLAN.md` for required screenshots and test cases.

### Credits

Built for CodeIgniter 4; framework documentation: <https://codeigniter4.github.io/userguide/>. The CSS/HTML/SVG for this project is self-contained. Sample customer information is fictional.
