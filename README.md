# URL Shortener

A Laravel short-link app with separate Super Admin, Admin, Member, Sales, and Manager dashboards.

## Run locally on Windows

From the project directory:

```powershell
D:\xampp8.2\xampp\php\php.exe artisan migrate --seed
npm run build
D:\xampp8.2\xampp\php\php.exe artisan serve
```

Open `http://127.0.0.1:8000/login`. The `/` page also opens the separate login screen. After login, each role is sent to its own dashboard.

Set `DB_CONNECTION` and the database credentials in `.env` before migrating. For a fresh SQLite setup, set `DB_CONNECTION=sqlite` and `DB_DATABASE=database/database.sqlite`.

## Demo accounts

Run the seeder above to create the demo company, users, sample links, and the Super Admin account. These credentials are for local testing only.

| Role | Email | Password |
|---|---|---|
| Super Admin | `superadmin@example.com` | `Admin@123` |
| Admin | `admin@example.com` | `Admin@123` |
| Member | `member@example.com` | `Member@123` |
| Sales | `sales@example.com` | `Sales@123` |
| Manager | `manager@example.com` | `Manager@123` |

Log out between tests, then sign in with the next account. The same demo company is used for the Admin, Member, Sales, and Manager accounts.

## Role behavior

- **Super Admin:** creates a client company and its first Admin invitation; can view all companies and all short URLs, but cannot create URLs.
- **Admin:** views every short URL and user in their own company; can invite Admin or Member users to that company, but cannot create URLs.
- **Member:** creates short URLs and sees only URLs they created; cannot invite users.
- **Sales and Manager:** can create URLs and see the URLs they created.
- Short URLs use `/s/{code}`. Opening one publicly increments its hit count and redirects to the original URL.

### Test the invitation flow

1. Sign in as Super Admin and use **Invite a new client**. Enter the company name and the new Admin's email. No separate company setup step is needed; the company and Admin invitation are created together.
2. The app creates the company and an Admin invitation together. Copy the invitation URL displayed at the top of the dashboard and open it in a signed-out tab.
3. The invited Admin enters their name and password. They are signed in to the new company's Admin dashboard.
4. The Admin uses **Invite team member** to invite an Admin or Member. The invited account is always placed in the signed-in Admin's company; the form does not accept a company ID.
5. The Member signs in and creates short URLs. Admin sees every URL in their company; Member sees only their own. Super Admin can see the platform-wide URL list.
6. Open a generated `/s/{code}` URL in a signed-out browser to verify that it increments hits and redirects.

Invitations are not emailed by this assignment. The app displays the direct invitation URL and provides a copy button; share that URL manually with the invitee.

## Tests

```powershell
D:\xampp8.2\xampp\php\php.exe artisan test
```

To reset the local database and reseed all demo data, run `D:\xampp8.2\xampp\php\php.exe artisan migrate:fresh --seed`. This deletes the existing local database data.
