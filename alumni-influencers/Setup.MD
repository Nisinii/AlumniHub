# AR Alumni Platform — Setup Instructions
 
## Step 1 — Database & Seed Data
 
Open phpMyAdmin at `http://localhost/phpmyadmin`, create a database named `ar_alumni`, and run each SQL file in order:
 
1. `database/V1__create_users_table.sql`
2. `database/V2__create_profile_tables.sql`
3. `database/V3__create_bidding_tables.sql`
4. `database/V4__create_api_key_tables.sql`
5. `database/v5_insert_seed_data.sql` *(This populates test users, profiles, and sponsor balances)*
 
---
 
## Step 2 — Environment Variables (.env)
 
We use environment variables to keep credentials secure.
 
1. Duplicate the `.env.example` file in your root folder and rename the copy to `.env`.
2. Open `.env` and fill in your actual Gmail App Password and Database credentials.
 
> **Note:** Never commit your real `.env` file to GitHub/version control.
 
---
 
## Step 3 — CodeIgniter Config
 
### `application/config/config.php`
 
Ensure your base URL points to the new project folder:
 
```php
$config['base_url']         = 'http://localhost/alumni-influencers/';
$config['index_page']       = '';
$config['encryption_key']   = 'your_random_32_char_string';
$config['sess_driver']      = 'files';
$config['sess_cookie_name'] = 'ar_alumni_session';
$config['sess_expiration']  = 7200;
$config['sess_save_path']   = NULL;
$config['csrf_protection']  = FALSE; // Handled dynamically via MY_Security
```
 
### `application/config/database.php`
 
Configure the database to dynamically read from your `.env` file:
 
```php
$db['default']['hostname'] = getenv('DB_HOST') ?: 'localhost';
$db['default']['username'] = getenv('DB_USER') ?: 'root';
$db['default']['password'] = getenv('DB_PASS') ?: '';
$db['default']['database'] = getenv('DB_NAME') ?: 'ar_alumni';
$db['default']['dbdriver'] = 'mysqli';
```
 
### `application/config/constants.php`
 
Already configured. Change `ALLOWED_EMAIL_DOMAIN` to your university domain for production (e.g., `uni.ac.uk`).
 
---
 
## Step 4 — Autoload
 
In `application/config/autoload.php`, ensure the required libraries and custom helpers are loaded globally:
 
```php
$autoload['libraries'] = array('database', 'session', 'email', 'upload');
$autoload['helper']    = array('url', 'form', 'security_headers', 'api_key');
```
 
---
 
## Step 5 — .htaccess (URL Routing)
 
Create an `.htaccess` file inside the `alumni-influencers/` root folder (at the same level as `index.php`) to remove `index.php` from your URLs:
 
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php/$1 [L]
```
 
> **Note:** In XAMPP's `httpd.conf`, ensure `AllowOverride All` is set for your htdocs directory.
 
---
 
## Step 6 — Uploads Folder Structure
 
Create the following directory to store profile images safely:
 
```
alumni-influencers/uploads/profiles/
```
 
> CodeIgniter will auto-create this on the first upload if permissions allow, but it is best practice to create it manually.
 
---
 
## Step 7 — Testing the Platform
 
Because you ran the `v5_insert_seed_data.sql` file, you can immediately log in using the seed accounts:
 
| Email | Password |
|-------|----------|
| `nisiniweerathunga1814@gmail.com` | `password` |
| `yaweenrox@gmail.com` | `password` |
 
**Important URLs:**
 
| Description | URL |
|-------------|-----|
| Public AR Endpoint | `http://localhost/alumni-influencers/` |
| API Documentation | `http://localhost/alumni-influencers/api-docs` *(must be logged in as Developer/Admin)* |
| Profile Dashboard | `http://localhost/alumni-influencers/profile` |
| Bidding Dashboard | `http://localhost/alumni-influencers/bidding` |
| API Key Management | `http://localhost/alumni-influencers/apikey` |
 
Refer to `POSTMAN_GUIDE.md` for detailed API testing steps.
 
---
 
## Step 8 — Cron Jobs (Production Only)
 
To automate the gamification system, set up a server cron job to resolve the blind auction at midnight:
 
```bash
# Midnight daily — select winner, deduct funds, and activate profile
0 0 * * * curl -X POST http://your-production-site.com/bidding/run_midnight
```
 
For local testing, trigger this manually via Postman: `POST /bidding/run_midnight`