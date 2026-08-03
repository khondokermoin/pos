# 🚀 Cloud POS — Hostinger Deployment Guide (বাংলা)

> **Laravel 12 + React (Inertia.js) + MySQL**  
> Hostinger Shared Hosting / Business Hosting এ deploy করার সম্পূর্ণ গাইড

---

## 📋 আপনার কাছ থেকে যা যা লাগবে (Checklist)

Hostinger এ deploy করার আগে নিচের তথ্যগুলো সংগ্রহ করুন:

| #   | তথ্য                       | কোথায় পাবেন                              | উদাহরণ              |
| --- | -------------------------- | ----------------------------------------- | ------------------- |
| 1   | **Domain Name**            | আপনার কেনা domain                         | `pos.example.com`   |
| 2   | **Hostinger hPanel Login** | hostinger.com এ login                     | email + password    |
| 3   | **Database Name**          | hPanel → Databases → MySQL                | `u123456_pos`       |
| 4   | **Database Username**      | hPanel → Databases → MySQL                | `u123456_pos`       |
| 5   | **Database Password**      | DB তৈরির সময় যা দিয়েছেন                 | `YourDBPass123`     |
| 6   | **Database Host**          | সাধারণত `localhost`                       | `localhost`         |
| 7   | **Gmail App Password**     | Google Account → Security → App Passwords | 16-digit code       |
| 8   | **SSLCommerz Store ID**    | sslcommerz.com dashboard                  | `yourstore123`      |
| 9   | **SSLCommerz Password**    | sslcommerz.com dashboard                  | `yourstore123@ssl`  |
| 10  | **SSH Access**             | hPanel → SSH Access (Enable করুন)         | username + password |

---

## 🛠️ STEP-BY-STEP DEPLOYMENT

### ✅ STEP 1 — Hostinger এ PHP Version Set করুন

1. **hPanel** এ login করুন → **Advanced** → **PHP Configuration**
2. PHP Version: **8.2** বা **8.3** select করুন
3. নিচের PHP Extensions enable করুন:
    - ✅ `pdo_mysql`
    - ✅ `mbstring`
    - ✅ `openssl`
    - ✅ `tokenizer`
    - ✅ `xml`
    - ✅ `ctype`
    - ✅ `json`
    - ✅ `bcmath`
    - ✅ `fileinfo`
    - ✅ `curl`
    - ✅ `zip`
    - ✅ `gd`
4. **Save** করুন

---

### ✅ STEP 2 — MySQL Database তৈরি করুন

1. **hPanel** → **Databases** → **MySQL Databases**
2. **Create New Database** click করুন
3. নিচের তথ্য note করুন:
    ```
    Database Name : u123456_pos       ← এটা note করুন
    Username      : u123456_pos       ← এটা note করুন
    Password      : আপনার password    ← এটা note করুন
    Host          : localhost
    ```

---

### ✅ STEP 3 — Project Files Upload করুন

#### পদ্ধতি A: Git দিয়ে (Recommended — SSH লাগবে)

1. hPanel → **SSH Access** → Enable করুন
2. Terminal/PuTTY দিয়ে SSH connect করুন:
    ```bash
    ssh username@yourdomain.com -p 65002
    ```
3. `public_html` folder এ যান:
    ```bash
    cd ~/public_html
    ```
4. GitHub থেকে clone করুন:
    ```bash
    git clone https://github.com/khondokermoin/pos.git .
    ```

#### পদ্ধতি B: File Manager দিয়ে (SSH ছাড়া)

1. Local machine এ project এর সব files ZIP করুন
2. hPanel → **File Manager** → `public_html` folder এ যান
3. ZIP file upload করুন → Extract করুন
4. সব files `public_html` এর ভেতরে থাকবে

> ⚠️ **গুরুত্বপূর্ণ:** `public_html` folder এর ভেতরে project এর সব files থাকবে।  
> অর্থাৎ `public_html/app/`, `public_html/public/`, `public_html/composer.json` ইত্যাদি।

---

### ✅ STEP 4 — .env File তৈরি করুন

1. File Manager এ `.env.hostinger` file টি copy করুন → rename করুন `.env`
2. `.env` file open করুন এবং নিচের values পরিবর্তন করুন:

```env
APP_NAME="Cloud POS"
APP_ENV=production
APP_KEY=                          ← খালি রাখুন (Step 5 এ generate হবে)
APP_DEBUG=false
APP_URL=https://yourdomain.com    ← আপনার domain

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u123456_pos           ← আপনার DB name
DB_USERNAME=u123456_pos           ← আপনার DB username
DB_PASSWORD=YourDBPass123         ← আপনার DB password

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your@gmail.com      ← আপনার Gmail
MAIL_PASSWORD=xxxx xxxx xxxx xxxx ← Gmail App Password
MAIL_FROM_ADDRESS=noreply@yourdomain.com

SSLCOMMERZ_STORE_ID=your_store_id
SSLCOMMERZ_STORE_PASSWORD=your_store_pass
SSLCOMMERZ_IS_SANDBOX=false       ← Live payment এ false
```

---

### ✅ STEP 5 — SSH Terminal এ Commands Run করুন

SSH connect করার পর `public_html` folder এ যান এবং একে একে run করুন:

```bash
# 1. Composer install (vendor folder তৈরি হবে)
composer install --no-dev --optimize-autoloader

# 2. App Key Generate
php artisan key:generate

# 3. Database Migration (সব tables তৈরি হবে)
php artisan migrate --force

# 4. Database Seed (initial data + Super Admin তৈরি হবে)
php artisan db:seed --force

# 5. Storage Symlink (file upload কাজ করবে)
php artisan storage:link

# 6. Cache Optimize (site fast হবে)
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

> 💡 **অথবা** একটি command এ সব করুন:
>
> ```bash
> bash deploy.sh
> ```

---

### ✅ STEP 6 — Frontend Assets Build করুন (Local Machine এ)

Hostinger Shared Hosting এ `npm` নাও থাকতে পারে।  
তাই **local machine এ** build করে upload করুন:

```bash
# Local machine এ (আপনার PC তে)
npm install
npm run build
```

এরপর `public/build/` folder টি Hostinger এর `public_html/public/build/` এ upload করুন।

> ✅ আপনার project এ `public/build/` folder ইতিমধ্যে থাকলে এই step skip করুন।

---

### ✅ STEP 7 — File Permissions Set করুন

SSH Terminal এ:

```bash
# Storage এবং cache folder এ write permission দিন
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
```

---

### ✅ STEP 8 — Domain Point করুন

যদি আলাদা domain registrar থেকে domain কিনে থাকেন:

1. Domain registrar এ login করুন
2. **DNS Settings** → **Nameservers** পরিবর্তন করুন:
    ```
    ns1.dns-parking.com
    ns2.dns-parking.com
    ```
    (Hostinger এর nameserver hPanel এ পাবেন)

অথবা **A Record** add করুন:

```
Type : A
Name : @
Value: [Hostinger Server IP]  ← hPanel এ পাবেন
```

---

## 🔐 Default Login Credentials

Deploy এর পর এই credentials দিয়ে login করুন:

| Role              | Email                       | Password   |
| ----------------- | --------------------------- | ---------- |
| **Super Admin**   | `superadmin@system.com`     | `password` |
| **Company Admin** | `admin@alpharetail.example` | `password` |

> ⚠️ **Deploy এর পরপরই password পরিবর্তন করুন!**

---

## 🔧 Gmail App Password তৈরির নিয়ম

1. [myaccount.google.com](https://myaccount.google.com) এ যান
2. **Security** → **2-Step Verification** চালু করুন
3. **Security** → **App Passwords** এ যান
4. App: **Mail**, Device: **Other** → নাম দিন "Cloud POS"
5. Generate করুন → **16-digit password** পাবেন
6. এই password `.env` এর `MAIL_PASSWORD` তে দিন

---

## ❗ সাধারণ সমস্যা ও সমাধান

### সমস্যা 1: 500 Internal Server Error

```bash
# Debug mode চালু করুন (সাময়িক)
# .env এ: APP_DEBUG=true
# Error দেখার পর আবার false করুন

# Log দেখুন:
cat storage/logs/laravel.log
```

### সমস্যা 2: Storage Permission Error

```bash
chmod -R 777 storage
chmod -R 777 bootstrap/cache
```

### সমস্যা 3: Class Not Found / Composer Error

```bash
composer dump-autoload --optimize
```

### সমস্যা 4: Cache পুরনো data দেখাচ্ছে

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### সমস্যা 5: Database Connection Error

- `.env` এর `DB_HOST` → `localhost` দিন
- DB name, username, password ঠিক আছে কিনা check করুন
- hPanel এ DB user টি DB তে assigned আছে কিনা দেখুন

### সমস্যা 6: Page Not Found (404)

- `public/.htaccess` file আছে কিনা check করুন
- hPanel → **Advanced** → **Apache Handlers** → `mod_rewrite` enable আছে কিনা দেখুন

---

## 📁 Hostinger এ Folder Structure

```
public_html/                    ← সব project files এখানে
├── app/
├── bootstrap/
├── config/
├── database/
├── public/                     ← এই folder এর contents accessible
│   ├── .htaccess
│   ├── index.php
│   ├── build/                  ← npm run build এর output
│   └── storage/                ← symlink
├── resources/
├── routes/
├── storage/
├── vendor/                     ← composer install এ তৈরি হবে
├── .env                        ← আপনার credentials
├── .htaccess                   ← root htaccess (public/ এ redirect করে)
├── artisan
└── composer.json
```

---

## 📞 Hostinger Support

- **Live Chat**: hostinger.com → Help Center
- **hPanel**: hpanel.hostinger.com
- **SSH Port**: 65002 (default)
- **PHP Version**: 8.2+ required

---

## ✅ Final Checklist

- [ ] PHP 8.2+ set করা হয়েছে
- [ ] MySQL Database তৈরি করা হয়েছে
- [ ] সব files upload করা হয়েছে
- [ ] `.env` file সঠিক credentials দিয়ে পূরণ করা হয়েছে
- [ ] `composer install` run করা হয়েছে
- [ ] `php artisan key:generate` run করা হয়েছে
- [ ] `php artisan migrate --force` run করা হয়েছে
- [ ] `php artisan db:seed --force` run করা হয়েছে
- [ ] `php artisan storage:link` run করা হয়েছে
- [ ] `public/build/` folder upload করা হয়েছে
- [ ] Storage permissions set করা হয়েছে
- [ ] Domain point করা হয়েছে
- [ ] Super Admin password পরিবর্তন করা হয়েছে
- [ ] `APP_DEBUG=false` নিশ্চিত করা হয়েছে

---

_তৈরি করা হয়েছে Cloud POS Inventory Management System এর জন্য_
