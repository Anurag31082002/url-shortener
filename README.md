# URL Shortener

A multi-company short URL app built with **PHP 8.5**, **Laravel 12** and **MySQL**.

There are three roles: **Super Admin**, **Admin** and **Member**. Each company has its own users. There is no public sign-up. Everyone joins through an invitation link.

## Features

- Multiple companies, each with its own users
- Three roles: Super Admin, Admin and Member
- Login and logout, with a separate dashboard for each role
- Super Admin creates a company and invites its first Admin
- Admin invites other Admins or Members in their own company
- Members create short URLs
- Short URLs are public and redirect to the original URL
- Each short URL shows the short link, destination URL, click count and created date
- Super Admin sees all short URLs, Admin sees their company's short URLs, Member sees only their own

## Installation

Requirements: PHP 8.5, Composer, MySQL, Node.js and npm.

If `php` is not on your PATH (for example with XAMPP), use the full path to `php.exe` instead.

**1. Clone the project**

```bash
git clone https://github.com/<your-username>/url-shortener.git
```

**2. Go into the project folder**

```bash
cd url-shortener
```

**3. Install PHP packages**

```bash
composer install
```

**4. Create the `.env` file**

```bash
cp .env.example .env
```

On Windows PowerShell, use `copy .env.example .env`.

**5. Generate the app key**

```bash
php artisan key:generate
```

## Database

**1. Create a MySQL database**

```sql
CREATE DATABASE url_shortener;
```

**2. Add the database details in `.env`**

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=url_shortener
DB_USERNAME=root
DB_PASSWORD=
```

## Migration and seeder

**1. Run the migrations to create the tables**

```bash
php artisan migrate
```

**2. Run the seeder to create the Super Admin account**

```bash
php artisan db:seed
```

The seeder creates only the Super Admin. Use this account to log in for the first time:

| Role | Email | Password |
|---|---|---|
| Super Admin | `superadmin@example.com` | `Admin@123` |

## Run the project

**1. Start the server**

```bash
php artisan serve
```

Open http://127.0.0.1:8000/login in your browser.

## Testing

Run the automated tests with:

```bash
php artisan test
```

The tests check that:

- Admin and Member can create short URLs
- Super Admin cannot create short URLs
- Admin sees only the short URLs of their own company
- Member sees only the short URLs they created
- Short URLs are public and redirect to the original URL

## Use of AI tool

I used the Claude AI tool to review and correct the database schema and to get help with the UI design.

## Screenshots and how the flow works

### 1. Super Admin invites a company

The Super Admin logs in and opens **Invite a new client**. They enter the company name and the Admin's email, then click **Create client and Admin invite**. The company and the Admin invitation are created together.

<img width="1909" height="1052" alt="Super Admin dashboard" src="https://github.com/user-attachments/assets/768db227-aaae-47cf-a9d8-352629294042" />

### 2. Admin accepts the invitation

The Super Admin copies the invitation link and sends it to the Admin manually. Invitations are not emailed. The Admin opens the link, enters their name and a password, and clicks **Accept invitation**.

<img width="1909" height="1106" alt="Admin accepts invitation" src="https://github.com/user-attachments/assets/f1eaf873-2372-4060-9fa1-df38c53f2cc2" />

### 3. Admin dashboard

After accepting, the Admin lands on the company dashboard. It shows company URLs, total clicks and team members. The Admin can create an invitation by entering a name and email and choosing the role Admin or Member.

<img width="1919" height="1081" alt="Admin dashboard" src="https://github.com/user-attachments/assets/df03019e-d9fb-4824-a122-bbf357056be1" />

### 4. Admin copies the invitation link

After clicking **Create invitation**, the link is shown at the top of the dashboard. The Admin clicks **Copy link** and sends it to the new user.

<img width="1917" height="1097" alt="Invitation link" src="https://github.com/user-attachments/assets/8e175e39-ccfa-4940-b4f7-b9a9c907eaa6" />

### 5. Member accepts the invitation and sets a password

The Member opens the link, confirms their name, sets a password and clicks **Accept invitation**. Then they can log in.

<img width="1910" height="1062" alt="Member accepts invitation" src="https://github.com/user-attachments/assets/433b52be-0819-442b-a4d5-ef5df68726f4" />

### 6. Member creates a short URL

On the Member dashboard, the Member pastes a long URL into the box and clicks **Shorten URL**.

<img width="1901" height="1053" alt="Short URL creation" src="https://github.com/user-attachments/assets/989eb451-3912-4c99-a38b-999a23bfca04" />

### 7. Short URL list with destination and click count

The new short link appears in the list with its destination URL, click count and created date. A Member sees only the links they created.

<img width="1893" height="1037" alt="Short links list" src="https://github.com/user-attachments/assets/94930302-13b5-4a00-9b91-cb471c5e2538" />

<img width="1906" height="1040" alt="Member dashboard with links and clicks" src="https://github.com/user-attachments/assets/15de7392-3a10-498c-8ff3-2d72da7b9d8b" />

### 8. Opening the short URL

When anyone opens the short link (no login needed), the app adds one to the click count and redirects to the original URL.

<img width="1912" height="1052" alt="Short URL redirect" src="https://github.com/user-attachments/assets/0803a5c7-6f93-478b-9e3f-061d0dd1852a" />

### Role summary

| Role | Can do | Cannot do |
|---|---|---|
| Super Admin | Invite a company's first Admin. See all companies and all short URLs. | Create short URLs |
| Admin | See all short URLs and users in their company. Invite Admins or Members. | Create short URLs |
| Member | Create short URLs. See only their own short URLs. | Invite users |
