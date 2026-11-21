# URL Shortener

A multi-company short-link app built with **Laravel 12** and **PHP 8.5**.

Every company has its own users, and each role gets its own dashboard: **Super Admin**, **Admin** and **Member**.

Access is **invitation-only**. There is no public sign-up. The only account created by the seeder is the Super Admin. The Super Admin invites a company's first Admin, and that Admin invites everyone else.

---

## Features

- Multiple companies, each with its own Admins and Members
- Role-based login with a separate dashboard for each role
- Invitation links (shared manually, not emailed)
- Short URL creation with a unique `/s/{code}` link
- Public short links that redirect to the original URL
- Click tracking: every visit increments the click count
- Link tables showing the short link, destination URL, clicks and created date
- Per-role visibility rules (see [Role behavior](#role-behavior))
- Automated tests

## Tech stack

| Area | Choice |
|---|---|
| Language | PHP 8.5 |
| Framework | Laravel 12 |
| Database | MySQL |
| Seeder | Creates the Super Admin account only |
| Frontend | Blade + Vite |

## Requirements

- PHP 8.5
- Composer
- MySQL
- Node.js and npm (only needed to build the frontend assets)

---

## Installation

These steps assume you have cloned the repository. Run each command from the project folder in PowerShell. The examples use the XAMPP PHP path; if `php` is on your PATH, you can use `php` instead.

**1. Clone the repository**

```powershell
git clone https://github.com/<your-username>/url-shortener.git
```

**2. Go into the project folder**

```powershell
cd url-shortener
```

**3. Install PHP dependencies**

```powershell
composer install
```

**4. Create the environment file**

```powershell
copy .env.example .env
```

**5. Generate the application key**

```powershell
D:\xampp8.2\xampp\php\php.exe artisan key:generate
```

**6. Create the MySQL database**

Create an empty database, for example from phpMyAdmin or the MySQL console:

```sql
CREATE DATABASE url_shortener;
```

**7. Set the database details in `.env`**

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=url_shortener
DB_USERNAME=root
DB_PASSWORD=
```

**8. Run the migrations**

This creates all the tables (companies, users, invitations, short URLs and so on).

```powershell
D:\xampp8.2\xampp\php\php.exe artisan migrate
```

**9. Run the seeder**

This creates the Super Admin account.

```powershell
D:\xampp8.2\xampp\php\php.exe artisan db:seed
```

**10. Install frontend dependencies**

```powershell
npm install
```

**11. Build the frontend**

```powershell
npm run build
```

**12. Start the application**

```powershell
D:\xampp8.2\xampp\php\php.exe artisan serve
```

Open <http://127.0.0.1:8000/login> and sign in as the Super Admin. After login, each role is sent to its own dashboard.

> Steps 8 and 9 can be combined as `artisan migrate --seed`.

---

## Super Admin account

The seeder creates one account. This is for **local testing only**, so change the password before deploying anywhere.

| Role | Email | Password |
|---|---|---|
| Super Admin | `superadmin@example.com` | `Admin@123` |

Admins and Members are not seeded. They are created through invitation links (see [How it works](#how-it-works)).

---

## Creating a Laravel 12 project from scratch

If you are building the project yourself instead of cloning it, this is how the base project is set up.

**1. Check your PHP version (must be 8.5)**

```powershell
D:\xampp8.2\xampp\php\php.exe -v
```

**2. Create a new Laravel 12 project**

```powershell
composer create-project laravel/laravel url-shortener "12.*"
```

**3. Go into the project folder**

```powershell
cd url-shortener
```

**4. Check that it runs**

```powershell
D:\xampp8.2\xampp\php\php.exe artisan serve
```

Open <http://127.0.0.1:8000>. You should see the Laravel welcome page.

**5. Connect MySQL**

Create the database and set the `DB_*` values in `.env` as shown in [Installation](#installation).

**6. Create a migration**

```powershell
D:\xampp8.2\xampp\php\php.exe artisan make:migration create_companies_table
```

Add your columns in the new file under `database/migrations`, then run it:

```powershell
D:\xampp8.2\xampp\php\php.exe artisan migrate
```

**7. Create a seeder**

```powershell
D:\xampp8.2\xampp\php\php.exe artisan make:seeder SuperAdminSeeder
```

Add the Super Admin user in the seeder's `run()` method, register it in `DatabaseSeeder`, then run it:

```powershell
D:\xampp8.2\xampp\php\php.exe artisan db:seed
```

Useful migration commands:

| Command | What it does |
|---|---|
| `artisan migrate` | Runs new migrations |
| `artisan migrate:status` | Shows which migrations have run |
| `artisan migrate:rollback` | Undoes the last batch of migrations |
| `artisan migrate:fresh --seed` | Drops all tables, re-migrates and re-seeds |

---

## Role behavior

| Role | Can do | Cannot do |
|---|---|---|
| **Super Admin** | Create a client company with its first Admin invitation. View all companies and all short URLs across the platform. | Create short URLs |
| **Admin** | View every short URL and user in their own company. Invite another Admin or a Member to their own company. | Create short URLs. See other companies. |
| **Member** | Create short URLs. View only the URLs they created. | Invite users |

Short URLs use the format `/s/{code}`. Opening one (no login needed) increments its click count and redirects to the original URL.

---

## How it works

1. The **Super Admin** signs in, enters a company name and the first Admin's email, and creates the invitation.
2. The app creates the company and the Admin invitation, and shows the invitation link.
3. The Super Admin sends that link to the Admin manually.
4. The **Admin** opens the link, confirms their name and sets a password. They land on the company dashboard.
5. The Admin uses **Invite team member**: enter name and email, choose a role (Admin or Member), and send the generated link.
6. The **Member** opens the link, sets a password and signs in.
7. The Member pastes a long URL and clicks **Shorten URL**. The short link appears in their list with its destination and click count.
8. Anyone who opens the short link is redirected to the original URL, and the click count goes up.

> Invitations are **not emailed**. The dashboard shows the direct invitation URL with a **Copy link** button. Share it with the invitee yourself.

---

## Screenshots

### Super Admin dashboard

Invite a new client by entering the company name and the Admin's email.

<img width="1909" height="1052" alt="Super Admin dashboard" src="https://github.com/user-attachments/assets/768db227-aaae-47cf-a9d8-352629294042" />

### Admin accepts the invitation

The invited Admin confirms their name and sets a password.

<img width="1909" height="1106" alt="Admin accepts invitation" src="https://github.com/user-attachments/assets/f1eaf873-2372-4060-9fa1-df38c53f2cc2" />

### Admin dashboard

Company URLs, total clicks, team members and the **Invite team member** form.

<img width="1919" height="1081" alt="Admin dashboard" src="https://github.com/user-attachments/assets/df03019e-d9fb-4824-a122-bbf357056be1" />

### Invitation link

After creating an invitation, the link is shown at the top with a **Copy link** button.

<img width="1917" height="1097" alt="Invitation link" src="https://github.com/user-attachments/assets/8e175e39-ccfa-4940-b4f7-b9a9c907eaa6" />

### Member accepts the invitation

<img width="1910" height="1062" alt="Member accepts invitation" src="https://github.com/user-attachments/assets/433b52be-0819-442b-a4d5-ef5df68726f4" />

### Creating a short URL

<img width="1901" height="1053" alt="Short URL creation" src="https://github.com/user-attachments/assets/989eb451-3912-4c99-a38b-999a23bfca04" />

### Short link list with destination and click count

<img width="1893" height="1037" alt="Short links list" src="https://github.com/user-attachments/assets/94930302-13b5-4a00-9b91-cb471c5e2538" />

<img width="1906" height="1040" alt="Member dashboard with links and clicks" src="https://github.com/user-attachments/assets/15de7392-3a10-498c-8ff3-2d72da7b9d8b" />

### Opening a short URL

Visiting the short link redirects to the original destination.

<img width="1912" height="1052" alt="Short URL redirect" src="https://github.com/user-attachments/assets/0803a5c7-6f93-478b-9e3f-061d0dd1852a" />

---

## Testing the invitation flow

1. Sign in as Super Admin and use **Invite a new client**. Enter the company name and the new Admin's email.
2. Copy the invitation URL shown at the top of the dashboard and open it in a signed-out (private) window.
3. Enter a name and password as the invited Admin. You are signed in to the new company's Admin dashboard.
4. Use **Invite team member** to invite an Admin or Member. The invited account is always placed in the signed-in Admin's company; the form does not accept a company ID.
5. Open the Member invitation link, set a password and sign in as the Member. Create a few short URLs.
6. Check visibility: the Admin sees every URL in their company, the Member sees only their own, and the Super Admin sees the platform-wide list.
7. Open a generated `/s/{code}` URL in a signed-out browser and confirm it redirects and the click count increases.

## Running tests

```powershell
D:\xampp8.2\xampp\php\php.exe artisan test
```

## Resetting the database

To drop all tables, re-run the migrations and re-create the Super Admin:

```powershell
D:\xampp8.2\xampp\php\php.exe artisan migrate:fresh --seed
```

> **Warning:** this deletes all existing data in the database.
