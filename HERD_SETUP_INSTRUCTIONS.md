# 🚀 Financial Tracker - Quick Setup with Laravel Herd

## Step 1: Open Laravel Herd

1. **Launch Laravel Herd** from your applications
2. Click the **+ button** or **"New Project"** option

## Step 2: Link Your Project

1. Select **"Link"** (instead of creating new)
2. Browse to: `e:\Projects\Financial Tracker`
3. Click **"Link"**

## Step 3: Use Herd's Terminal

1. Once linked, look for your project in Herd's list
2. Click the **three-dot menu** next to your project
3. Select **"Terminal"** or **"Open in Terminal"**

This opens a terminal **with PHP and Composer already configured**!

## Step 4: Run Setup Commands

In the Herd terminal, run these commands **one by one**:

### Command 1: Install Dependencies
```bash
composer install
```
⏳ This takes 2-5 minutes (wait for it to complete)

### Command 2: Generate App Key
```bash
php artisan key:generate
```

### Command 3: Create Database
First, you need to create the MySQL database. In the same terminal:

```bash
mysql -h 127.0.0.1 -u root -p
```

When prompted for password, press Enter (usually no password on localhost)

Then paste this:
```sql
CREATE DATABASE financial_tracker;
EXIT;
```

**If MySQL isn't available locally**, you have two options:

**Option A: Use Docker (Recommended)**
- Ensure Docker Desktop is running
- Then: `docker-compose up -d`
- Wait 30 seconds for containers to start
- Skip steps below and go to "Step 5: Start Server"

**Option B: Use SQLite instead (Faster for testing)**
- Edit `.env` file:
  ```
  DB_CONNECTION=sqlite
  DB_DATABASE=database.sqlite
  ```
- Then run migrations

### Command 4: Run Migrations
```bash
php artisan migrate --seed
```

This creates all tables and seeds test data

✅ **Test User created:**
- Email: `test@example.com`
- Password: `password`

## Step 5: Start Development Server

```bash
php artisan serve
```

Your API is now running at: **http://localhost:8000**

## Step 6: Test Your API

Open **Postman** or **Thunder Client** (VS Code extension) and test:

```
GET http://localhost:8000/api/categories
Headers: Authorization: Bearer {your_token}
```

First, get a token by logging in:
```
POST http://localhost:8000/api/login
Body (JSON):
{
  "email": "test@example.com",
  "password": "password"
}
```

---

## 🎯 Quick Reference

| Command | What it does |
|---------|-------------|
| `composer install` | Download all PHP packages |
| `php artisan key:generate` | Create encryption key |
| `php artisan migrate --seed` | Create tables + test data |
| `php artisan serve` | Start development server |

## ❌ Troubleshooting

**"Command not found"**
- Make sure you're using Herd's terminal (not PowerShell)
- Herd terminal is labeled "Terminal" in the Herd app

**"SQLSTATE[HY000] [2002] Connection refused"**
- MySQL isn't running
- Use SQLite option instead (edit `.env`)

**"Composer timeout"**
- Run: `composer install --no-interaction`

---

**Once your server is running, you're ready for Step 2 - the frontend!** 🎉


echo "# Financial-tracker" >> README.md
git init
git add README.md
git commit -m "first commit"
git branch -M main
git remote add origin https://github.com/pasindi15/Financial-tracker.git
git push -u origin main