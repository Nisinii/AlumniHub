# AlumniHub

> **Alumni–Student Engagement Platform.**
> A full-stack web platform that transforms static alumni listings into a living, self-sustaining marketplace connecting students with successful graduates through a blind-bidding sponsorship system and a real-time university analytics dashboard.

<img width="1262" height="615" alt="image" src="https://github.com/user-attachments/assets/08c71ca5-b3d8-4a86-9991-140910c7b663" />
<img width="1266" height="616" alt="image" src="https://github.com/user-attachments/assets/9dd22330-aa4a-4e2d-a3db-7ff2dcceb5a2" />
<img width="1265" height="616" alt="image" src="https://github.com/user-attachments/assets/8007a58e-4bdb-4333-ba05-fc6bda59498c" />
<img width="1265" height="619" alt="image" src="https://github.com/user-attachments/assets/72462040-4efe-41d7-b2aa-f0874446ac22" />

***

## The Concept

**AlumniHub** is a two-part platform commissioned to revolutionise alumni engagement at a university. The existing system — a static webpage listing graduates — lacked interactivity, real-time data, and any meaningful connection between current students and alumni.

AlumniHub solves this with a dual architecture:

- **Part 1 — The API & Alumni Platform:** Alumni register, build rich professional profiles, and compete for a featured *"Alumni of the Day"* slot through a daily blind-bidding system sponsored by certification and licensing bodies.
- **Part 2 — The University Analytics Dashboard:** A dedicated client application that consumes the alumni API to surface real-time curriculum insights — detecting skills gaps, emerging career pathways, and industry trends from live alumni data.

***

## Key Features

* **Alumni Registration & Authentication:** University-domain email registration, email verification, secure session management, bcrypt password hashing, and password reset flows.
* **Rich Alumni Profiles:** Full professional profiles including employment history, LinkedIn URL, degrees, certifications, professional licences, short courses, and profile image upload.
* **Blind Bidding System:** Alumni place bids for a daily featured slot without seeing the current highest bid — receiving only win/lose feedback. Bids can only be increased, and each alumnus is capped at 3 featured wins per calendar month (extendable to 4 via event attendance).
* **Automated Winner Selection:** A scheduled midnight process selects the highest bidder, marks their profile as *Alumni of the Day*, and dispatches email notifications.
* **Scoped API Key Security:** Bearer-token authentication with granular permission scoping — the analytics dashboard receives `read:alumni` and `read:analytics` permissions, while the AR mobile client receives `read:alumniofday`. Usage statistics (timestamps, endpoints accessed) and token revocation are fully supported.
* **University Analytics Dashboard:** Interactive charts (bar, line, pie, radar, doughnut) visualising alumni career outcomes, skills gaps, certification trends, and programme-level insights — all sourced live from the API.
* **Public Developer API:** A `GET /alumni/today` endpoint and full Swagger/OpenAPI documentation with interactive UI at `/api-docs`.
* **Export & Reporting:** CSV/PDF export of filtered alumni data, downloadable chart images, and custom report generation with saved filter presets.

***

## How It Works

The platform is built across two separate back-end services:

1. **CodeIgniter (PHP)** serves as the primary application layer — handling alumni registration, profile management, bidding logic, session authentication, and the Swagger-documented RESTful API.
2. **Express.js (Node.js)** powers the university analytics dashboard client, consuming the CodeIgniter API using scoped bearer tokens to retrieve and visualise alumni data.

The database is normalised to **3NF**, with proper foreign key constraints, indexed queries, and separate tracking tables for API key usage and bid history. Security is enforced through Helmet.js-equivalent headers, CORS configuration, rate limiting on sensitive endpoints, and comprehensive input validation with SQL injection and XSS prevention.

***

## Tech Stack & Skills

* **Backend (API):** PHP, CodeIgniter 4
* **Backend (Dashboard Client):** Node.js, Express.js
* **Database:** MySQL (3NF normalised schema)
* **Frontend:** HTML, CSS, JavaScript, Chart.js
* **Security:** Bcrypt, Bearer tokens, API key scoping, Helmet.js, CSRF protection, rate limiting
* **API Documentation:** Swagger / OpenAPI
* **Architecture:** RESTful API, client-agnostic API design, scoped permissions

***

## Getting Started

Follow these steps to set up AlumniHub on your local machine.

### Prerequisites

* PHP >= 8.1
* Composer
* Node.js >= 18.x and npm
* MySQL

***

### 1. Clone the Repository

```bash
git clone https://github.com/YOUR_USERNAME/AlumniHub.git
cd AlumniHub
```

***

### 2. Set Up the API Backend (CodeIgniter)

1. Navigate to the CodeIgniter application:

```bash
cd alumni-api
```

2. Install PHP dependencies:

```bash
composer install
```

3. Copy the environment file and configure it:

```bash
cp env .env
```

4. Open `.env` and set your database credentials and mail settings:

```env
database.default.hostname = localhost
database.default.database = alumniHub
database.default.username = YOUR_DB_USER
database.default.password = YOUR_DB_PASSWORD

# Email (for verification & notifications)
email.SMTPHost     = YOUR_SMTP_HOST
email.SMTPUser     = YOUR_EMAIL
email.SMTPPass     = YOUR_EMAIL_PASSWORD
```

5. Run database migrations:

```bash
php spark migrate
```

6. Start the development server:

```bash
php spark serve
```

The API will be available at `http://localhost:8080`. Swagger documentation is accessible at `http://localhost:8080/api-docs`.

***

### 3. Set Up the Analytics Dashboard (Express.js)

Open a second terminal window.

1. Navigate to the dashboard client:

```bash
cd alumni-dashboard
```

2. Install dependencies:

```bash
npm install
```

3. Copy the environment file and add your scoped API key:

```bash
cp .env.example .env
```

```env
API_BASE_URL=http://localhost:8080
API_KEY=YOUR_ANALYTICS_DASHBOARD_API_KEY
PORT=3000
```

4. Start the dashboard server:

```bash
npm start
```

The analytics dashboard will be available at `http://localhost:3000`.

***

### 4. Scheduled Jobs

The automated midnight winner selection requires a cron job or task scheduler. Add the following to your server's crontab:

```bash
0 0 * * * php /path/to/alumni-api/spark scheduled:run
```

***

## API Overview

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| `GET` | `/api/alumni/today` | Public | Get today's featured alumnus |
| `GET` | `/api/alumni` | `read:alumni` | List all alumni profiles |
| `GET` | `/api/analytics` | `read:analytics` | Retrieve aggregated analytics data |
| `POST` | `/api/bids` | Authenticated | Place or update a bid |
| `GET` | `/api/bids/status` | Authenticated | Check win/lose bid status |

Full interactive documentation is available at `/api-docs` (Swagger UI).

***

## Author

**Nisini Niketha** *Software Engineer & Digital Architect*

* [GitHub](https://github.com/Nisinii)
* [LinkedIn](https://www.linkedin.com/in/nisini-niketha/)
* [Contact](mailto:wnisini.niketha@gmail.com)
