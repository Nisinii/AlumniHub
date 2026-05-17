# AR Alumni Platform — Complete Postman Testing Guide

## Setup (do this once)

- In Postman → Settings → General → turn ON **"Automatically follow redirects"**
- In Postman → Settings → General → turn ON **"Send cookies"**
- Set `Content-Type: application/json` AND `Accept: application/json` headers on all requests. (This tells our Smart Renderer to output JSON instead of HTML views).
- **Base URL:** `http://localhost/alumni-influencers`

---

## PART 1 — AUTHENTICATION

### Step 1 — Register

**Method:** `POST`  
**URL:** `http://localhost/alumni-influencers/auth/register`

**Body:**
```json
{
  "first_name": "John",
  "last_name": "Doe",
  "email": "john@gmail.com",
  "password": "Test@1234",
  "confirm_password": "Test@1234"
}
```

**Expected response (201):**
```json
{
  "status": 201,
  "message": "Registration successful. Please verify your email.",
  "dev_verify_url": "http://localhost/alumni-influencers/auth/verify/abc123..."
}
```

> **Notes:** Copy the `dev_verify_url` for Step 2.

---

### Step 1b — Register with weak password (should fail)

**Method:** `POST`  
**URL:** `http://localhost/alumni-influencers/auth/register`

**Body:**
```json
{
  "first_name": "Jane",
  "last_name": "Smith",
  "email": "jane@gmail.com",
  "password": "password",
  "confirm_password": "password"
}
```

**Expected response (422):**
```json
{
  "status": 422,
  "message": "Validation failed",
  "errors": [
    "Need uppercase.",
    "Need a number.",
    "Need a special character."
  ]
}
```

---

### Step 2 — Verify Email

**Method:** `GET`  
**URL:** Paste the `dev_verify_url` from Step 1, e.g.  
`http://localhost/alumni-influencers/auth/verify/292b46df6782da3cb71...`

**Expected response (200):**
```json
{
  "status": 200,
  "message": "Email verified! Please log in."
}
```

---

### Step 3 — Resend Verification Email

**Method:** `POST`  
**URL:** `http://localhost/alumni-influencers/auth/resend_verification`

**Body:**
```json
{
  "email": "john@gmail.com"
}
```

**Expected response (200):**
```json
{
  "status": 200,
  "message": "If unverified, a new link has been sent."
}
```

---

### Step 4 — Login

**Method:** `POST`  
**URL:** `http://localhost/alumni-influencers/auth/login`

**Body:**
```json
{
  "email": "john@gmail.com",
  "password": "Test@1234"
}
```

**Expected response (200):**
```json
{
  "status": 200,
  "message": "Login successful"
}
```

> **Notes:** Postman stores the CI session cookie automatically. All subsequent requests are now authenticated.

---

### Step 5 — Forgot Password

**Method:** `POST`  
**URL:** `http://localhost/alumni-influencers/auth/forgot_password`

**Body:**
```json
{
  "email": "john@gmail.com"
}
```

**Expected response (200):**
```json
{
  "status": 200,
  "message": "If email exists, a reset link was sent.",
  "dev_reset_url": "http://localhost/alumni-influencers/auth/reset_password/xyz789..."
}
```

---

### Step 6 — Reset Password

**Method:** `POST`  
**URL:** Paste the `dev_reset_url` from Step 5

**Body:**
```json
{
  "password": "NewPass@5678",
  "confirm_password": "NewPass@5678"
}
```

**Expected response (200):**
```json
{
  "status": 200,
  "message": "Password updated."
}
```

---

## PART 2 — PROFILE MANAGEMENT

> **Important:** Log back in (Step 4) before testing these.

### Step 7 — Save Core Profile

**Method:** `POST`  
**URL:** `http://localhost/alumni-influencers/profile/edit`

**Body:**
```json
{
  "bio": "Senior software engineer with 10 years experience in cloud computing.",
  "linkedin_url": "https://linkedin.com/in/johndoe"
}
```

**Expected response (200):**
```json
{
  "status": 200,
  "message": "Profile updated successfully."
}
```

---

### Step 7b — Save Core Profile (WITH Image Upload)

**Method:** `POST`  
**URL:** `http://localhost/alumni-influencers/profile/edit`

**Headers:**

> ⚠️ **CRITICAL:** Uncheck or delete the `Content-Type: application/json` header for this specific request! Postman will automatically generate the correct `multipart/form-data` boundary header for you. Keep `Accept: application/json` active.

**Body:**

1. Click the **Body** tab.
2. Select the **form-data** radio button.
3. Add the following keys and values:

| Key | Type | Value |
|-----|------|-------|
| `bio` | Text | `"Updating my bio with a photo!"` |
| `linkedin_url` | Text | `"https://linkedin.com/in/johndoe"` |
| `profile_image` | File | *(Hover over the key row, click the hidden dropdown that says "Text", change it to "File", and select a `.jpg` or `.png` from your computer)* |

**Expected response (200):**
```json
{
  "status": 200,
  "message": "Profile updated successfully.",
  "page_title": "Edit Profile",
  "profile": {
    "id": 1,
    "user_id": 1,
    "bio": "Updating my bio with a photo!",
    "profile_image": "user_1_1711652400.jpg"
  }
}
```

---

### Step 8 — Add a Degree

**Method:** `POST`  
**URL:** `http://localhost/alumni-influencers/profile/add/degrees`

**Body:**
```json
{
  "degree_name": "BSc Computer Science",
  "institution": "University of Eastminster",
  "degree_url": "https://eastminster.ac.uk/degrees/bsc-cs",
  "completion_date": "2018-06-30"
}
```

**Expected response (201):**
```json
{
  "status": 201,
  "message": "Degrees added."
}
```

---

### Step 9 — Add Employment (Current Job)

**Method:** `POST`  
**URL:** `http://localhost/alumni-influencers/profile/add/employment`

**Body:**
```json
{
  "job_title": "Senior Software Engineer",
  "company": "Google",
  "start_date": "2020-01-15",
  "end_date": "",
  "description": "Led a backend team."
}
```

**Expected response (201):**
```json
{
  "status": 201,
  "message": "Employment added."
}
```

> **Notes:** Leave `end_date` empty to indicate currently employed.

---

### Step 10 — Get Full Profile

**Method:** `GET`  
**URL:** `http://localhost/alumni-influencers/profile`

**Expected response (200):**
```json
{
  "status": 200,
  "message": "Profile retrieved.",
  "user": { "id": 1, "first_name": "John", "last_name": "Doe", "email": "john@gmail.com" },
  "profile": { "bio": "Senior software engineer...", "linkedin_url": "https://linkedin.com/in/johndoe" },
  "degrees": [{ "id": 1, "degree_name": "BSc Computer Science", "..." : "..." }],
  "employment": [{ "id": 1, "job_title": "Senior Software Engineer", "..." : "..." }],
  "completion": { "percentage": 50, "completed": 4, "total": 8, "missing": ["profile_image", "licence"] }
}
```

---

### Step 11 — Update a Degree

**Method:** `POST`  
**URL:** `http://localhost/alumni-influencers/profile/edit_item/degrees/1`

**Body:**
```json
{
  "degree_name": "BSc Computer Science (Hons)",
  "institution": "University of Eastminster",
  "degree_url": "https://eastminster.ac.uk/degrees/bsc-cs-hons",
  "completion_date": "2018-07-15"
}
```

**Expected response (200):**
```json
{
  "status": 200,
  "message": "Degrees updated."
}
```

---

### Step 12 — Delete a Degree

**Method:** `POST`  
**URL:** `http://localhost/alumni-influencers/profile/delete/degrees/1`

**Expected response (200):**
```json
{
  "status": 200,
  "message": "Degrees deleted."
}
```

---

## PART 3 — BIDDING SYSTEM

### Step 13 — Get Sponsor Offers

**Method:** `GET`  
**URL:** `http://localhost/alumni-influencers/bidding/sponsors`

**Expected response (200):**
```json
{
  "status": 200,
  "message": "Sponsor offers retrieved.",
  "offers": [ { "id": 1, "sponsor_name": "Phantasmagoria Ltd", "amount": "500.00", "status": "pending" } ],
  "balance": 0
}
```

---

### Step 14 — Accept Sponsor Offer

**Method:** `POST`  
**URL:** `http://localhost/alumni-influencers/bidding/accept_offer/1`

**Expected response (200):**
```json
{
  "status": 200,
  "message": "Offer accepted. Balance updated."
}
```

---

### Step 15 — Get Complete Bidding Dashboard

**Method:** `GET`  
**URL:** `http://localhost/alumni-influencers/bidding`

**Expected response (200):**
```json
{
  "status": 200,
  "message": "Bidding dashboard retrieved.",
  "active_bid": null,
  "bid_status": { "has_bid": false, "message": "You have not placed a bid for tomorrow yet." },
  "monthly_status": { "wins_this_month": 0, "monthly_limit": 3, "remaining_slots": 3 },
  "tomorrow_slot": { "slot_date": "2026-04-03", "total_bids": 0 },
  "balance": 500,
  "appearance": { "this_month": 0, "all_time": 0 },
  "bidding_open": true
}
```

---

### Step 16 — Place a Bid

**Method:** `POST`  
**URL:** `http://localhost/alumni-influencers/bidding/place`

**Body:**
```json
{
  "bid_amount": 250
}
```

**Expected response (201):**
```json
{
  "status": 201,
  "message": "Bid placed successfully.",
  "bid_id": 1,
  "bid_date": "2026-04-03"
}
```

---

### Step 17 — Increase Bid

**Method:** `POST`  
**URL:** `http://localhost/alumni-influencers/bidding/update`

**Body:**
```json
{
  "new_amount": 300
}
```

**Expected response (200):**
```json
{
  "status": 200,
  "message": "Bid increased successfully."
}
```

---

### Step 18 — Record University Event

**Method:** `POST`  
**URL:** `http://localhost/alumni-influencers/bidding/record_event`

**Body:**
```json
{
  "event_name": "Alumni Networking Day",
  "event_date": "2026-04-01"
}
```

**Expected response (201):**
```json
{
  "status": 201,
  "message": "Event recorded! 4th slot unlocked this month."
}
```

---

### Step 19 — Run Midnight Process (Select Winner)

**Method:** `POST`  
**URL:** `http://localhost/alumni-influencers/bidding/run_midnight`

**Body (optional):**
```json
{
  "date": "2026-04-03"
}
```

**Expected response (200):**
```json
{
  "status": 200,
  "message": "Midnight selection complete. Winner processed and funds deducted.",
  "success": true,
  "winner_user_id": 1,
  "bid_amount": 300,
  "feature_date": "2026-04-03",
  "total_bids": 1
}
```

---

### Step 20 — Get Today's Featured Alumnus (PUBLIC AR ENDPOINT)

**Method:** `GET`  
**URL:** `http://localhost/alumni-influencers/bidding/api_today`

> **Notes:** This is the endpoint the AR client uses.

**Expected response (200):**
```json
{
  "status": 200,
  "message": "Featured alumnus retrieved.",
  "page_title": "Alumnus of the Day",
  "featured": {
    "feature_date": "2026-04-03",
    "activated_at": "2026-04-03 00:00:05",
    "alumni": { "id": 1, "name": "John Doe", "email": "john@gmail.com" }
  },
  "details": { "...": "full profile payload..." }
}
```

---

## PART 4 — API KEY MANAGEMENT (DEVELOPERS)

> **Important:** You must be logged in as an Admin or Developer to access these. Update user role in PHPMyAdmin to `developer` first.

### Step 21 — Generate an API Key

**Method:** `POST`  
**URL:** `http://localhost/alumni-influencers/apikey/generate`

**Body:**
```json
{
  "key_name": "AR Headset Client",
  "scope": "read"
}
```

**Expected response (201):**
```json
{
  "status": 201,
  "message": "API key generated. Store it securely.",
  "id": 1,
  "key": "a3f8c2d1e4b7...",
  "scope": "read"
}
```

> **Notes:** Copy the `key` value for Step 23.

---

### Step 22 — View Key Usage Statistics

**Method:** `GET`  
**URL:** `http://localhost/alumni-influencers/apikey/stats/1`

**Expected response (200):**
```json
{
  "status": 200,
  "message": "Usage stats retrieved.",
  "stats": {
    "key_id": 1,
    "key_name": "AR Headset Client",
    "total_calls": 0,
    "by_endpoint": [],
    "recent_calls": []
  }
}
```

---

### Step 23 — Call Public Endpoint with API Key

Test the middleware authentication without a session cookie.

**Method:** `GET`  
**URL:** `http://localhost/alumni-influencers/bidding/api_today`

**Headers:**
```
Authorization: Bearer a3f8c2d1e4b7...
```

**Expected response (200):** The same JSON payload from Step 20.

---

---

### Step 34 — Get API Keys Dashboard (List all keys)

**Method:** `GET`  
**URL:** `http://localhost/alumni-influencers/apikey`

**Expected response (200):**
```json
{
  "status": 200,
  "message": "API Key dashboard.",
  "page_title": "API Key Management",
  "keys": [
    {
      "id": 1,
      "user_id": 1,
      "key_name": "AR Headset Client",
      "scope": "read",
      "is_active": "1",
      "total_calls": 0
    }
  ],
  "new_key": null
}
```

**Method:** `POST`  
**URL:** `http://localhost/alumni-influencers/apikey/revoke/1`

**Expected response (200):**
```json
{
  "status": 200,
  "message": "API key revoked."
}
```