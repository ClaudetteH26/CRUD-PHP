# Facebook-Style Login System + Employee CRUD

A simplified Facebook-inspired login system integrated with a complete Employee Management CRUD application. This project demonstrates modern web development practices including authentication, session management, form validation, and database operations.

## 📋 Project Overview

**Platform Chosen:** Facebook  
**Project Type:** Mini Social Network Login System + CRUD (Simplified Student Version)

This project implements a Facebook-style authentication system that redirects users to a fully functional Employee CRUD dashboard after successful login.

## ✨ Features Implemented

### 1. UI Design
- ✅ Facebook-inspired sign-up page with modern, clean design
- ✅ Facebook-inspired sign-in page matching platform aesthetics
- ✅ Dashboard/Home page (Employee CRUD interface)
- ✅ Responsive design for mobile and desktop

### 2. Sign Up Page
- ✅ **Username** field (unique)
- ✅ **Full Name** field
- ✅ **Email** field (unique, validated)
- ✅ **Password** field (minimum 6 characters)
- ✅ **Confirm Password** field
- ✅ Complete form validation with error messages

### 3. Form Validation
- ✅ **No empty fields** - All fields are required
- ✅ **Email validation** - Must be a valid email format
- ✅ **Password length** - Minimum 6 characters
- ✅ **Password match** - Password and confirmation must match
- ✅ **Error messages** - Clear, user-friendly error messages for each validation failure
- ✅ **Username uniqueness** - Prevents duplicate usernames
- ✅ **Email uniqueness** - Prevents duplicate email addresses

### 4. Sign In Page
- ✅ Login using **username OR email** + password
- ✅ Comprehensive error messages:
  - Wrong password
  - Account not found
  - Empty fields
- ✅ Success message after registration

### 5. Google Login (Mocked)
- ✅ "Login with Google" button with Google logo
- ✅ Simulates successful Google authentication
- ✅ Creates mock Google user if doesn't exist
- ✅ Redirects to dashboard after mock login
- ✅ **Note:** This is a simulation - no real Google API integration

### 6. Login Success → Redirect to CRUD
- ✅ After successful login (normal or Google), redirects to Employee CRUD dashboard
- ✅ Dashboard includes:
  - Create new employees
  - Read/View employee list
  - Update employee information
  - Delete employees
  - View employee reports by role
  - Print reports functionality

### 7. Platform Style Matching
- ✅ **Colors:** Facebook blue (#1877f2), Facebook green (#42b72a)
- ✅ **Layout:** Two-column layout with logo on left, form on right
- ✅ **Button styles:** Facebook-style rounded buttons
- ✅ **Error messages:** Facebook-style error display
- ✅ **Typography:** Helvetica/Arial font family matching Facebook
- ✅ **Design elements:** Shadows, borders, and spacing matching Facebook aesthetic

## 🗄️ Database Structure

### Users Table
```sql
- id (Primary Key, Auto Increment)
- username (Unique, VARCHAR 50)
- name (VARCHAR 150)
- email (Unique, VARCHAR 190)
- password_hash (VARCHAR 255) - Bcrypt hashed
- remember_token_hash (VARCHAR 255, Nullable)
- remember_token_expires (INT, Nullable)
- created_at (Timestamp)
```

### Employee Table
```sql
- emp_id (Primary Key, Auto Increment)
- firstname (VARCHAR 121)
- lastname (VARCHAR 121)
- role (VARCHAR 122)
```

## 🚀 Getting Started

### Prerequisites
- XAMPP (Apache + MySQL)
- PHP 7.4 or higher
- MySQL 5.7 or higher

### Installation Steps

#### Step 1: Start XAMPP Services
1. Open **XAMPP Control Panel**
2. Click **Start** for **Apache**
3. Click **Start** for **MySQL**

#### Step 2: Setup Database
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Create database: `company`
3. Import `company.sql` file
4. **OR** if users table exists, run `add_username_field.sql` to add username column

#### Step 3: Configure Database Connection
Edit `config.php` if needed:
```php
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'company';
```

#### Step 4: Access Application
1. Open browser: `http://localhost/CURD/`
2. You'll be redirected to the Facebook-style login page

### Default Admin Account
- **Username/Email:** `admin` or `admin@example.com`
- **Password:** `admin123`

## 📁 Project Structure

```
CURD/
├── config.php              # Database connection & auth functions
├── login.php               # Facebook-style login page
├── signup.php              # Facebook-style signup page
├── dashboard.php           # Employee CRUD dashboard
├── logout.php              # Logout handler
├── company.sql             # Complete database schema
├── add_username_field.sql  # Migration to add username field
├── create_users_table.sql  # Users table creation script
└── README.md               # This file
```

## 🔐 Security Features

1. **Password Hashing:** Bcrypt with `password_hash()` and `password_verify()`
2. **Prepared Statements:** All database queries use prepared statements to prevent SQL injection
3. **Session Management:** Secure session handling with regeneration
4. **Input Validation:** Server-side validation for all form inputs
5. **XSS Protection:** HTML escaping with `htmlspecialchars()`
6. **CSRF Protection:** Session-based authentication tokens

## 📝 Validation Rules

### Sign Up Validation
- Username: Required, must be unique
- Full Name: Required, minimum 2 characters
- Email: Required, must be valid format, must be unique
- Password: Required, minimum 6 characters
- Confirm Password: Required, must match password

### Sign In Validation
- Login (username/email): Required
- Password: Required
- Error messages for:
  - Empty fields
  - Account not found
  - Incorrect password

## 🎨 UI/UX Features

- **Responsive Design:** Works on desktop, tablet, and mobile
- **Error Feedback:** Clear, actionable error messages
- **Success Messages:** Confirmation after successful registration
- **Google Login Button:** Visual Google branding with SVG icon
- **Facebook Branding:** Logo, colors, and layout matching Facebook

## 🔄 User Flow

1. **New User:**
   - Visit signup page → Fill form → Validation → Account created → Redirect to login → Success message → Login → Dashboard

2. **Existing User:**
   - Visit login page → Enter credentials → Validation → Dashboard

3. **Google Login:**
   - Click "Login with Google" → Mock authentication → Create/Login user → Dashboard

4. **After Login:**
   - Access Employee CRUD Dashboard
   - Create, Read, Update, Delete employees
   - View reports and statistics
   - Print reports

## 📸 Screenshots

*Note: Add screenshots of your application here:*
- Sign Up page
- Sign In page
- Dashboard/Home page
- Error messages
- Success messages
- Google login flow

## 🛠️ Technologies Used

- **Backend:** PHP 7.4+
- **Database:** MySQL/MariaDB
- **Frontend:** HTML5, CSS3
- **Security:** Bcrypt password hashing, Prepared statements
- **Session Management:** PHP Sessions

## 📋 Assignment Requirements Checklist

- ✅ UI Design mockups (code matches Facebook style)
- ✅ Sign Up page with all required fields
- ✅ Complete form validation
- ✅ Sign In page with username/email login
- ✅ Error messages for all scenarios
- ✅ Google Login (mocked/simulated)
- ✅ Redirect to CRUD after login
- ✅ Facebook-style design (colors, layout, buttons)
- ✅ Database with SQL file
- ✅ Documentation (this README)

## 🐛 Known Issues / Challenges Faced

1. **Database Schema Update:** Needed to add username field to existing users table
   - **Solution:** Created migration script `add_username_field.sql`

2. **Google Login Simulation:** Required mock implementation without real API
   - **Solution:** Created mock user creation and login flow

3. **Username/Email Login:** Supporting both login methods
   - **Solution:** Query checks both email and username fields

## 📧 Contact

For questions or issues, contact: ydiasniyonshuti@yahoo.fr

## 📄 License

This project is created for educational purposes as part of a student assignment.

---

**Deadline:** 20/11/2025 18:00  
**Submission:** GitHub repository with code, screenshots, and SQL file
