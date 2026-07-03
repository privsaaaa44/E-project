# Cinevo - Hosting Guide
# Yeh file hosting par upload karne se pehle padh lo

## ✅ Kya Fix Kiya Gaya (Hosting ke liye)

### 1. config.php (SABSE PEHLE EDIT KARO)
Ek jagah se sab kuch set karo:
- Database credentials
- Base URL
- Email/SMTP settings

### 2. Files Jo Ab Local Hain (CDN se nahi)
| File | Pehle | Ab |
|------|-------|-----|
| jQuery | Google CDN | `js/jquery-3.7.1.min.js` |
| Bootstrap JS | CDN | `js/bootstrap.bundle.min.js` |
| Bootstrap CSS | CDN | `css/bootstrap.min.css` |
| Font Awesome (admin) | CDN | `css/lib/fontawesome.all.min.css` |
| Select2 CSS | CDN | `css/lib/select2.min.css` |
| Select2 JS | CDN | `js/lib/select2.min.js` |
| Chart.js | CDN | `js/lib/chart.js` |

> Google Fonts aur SweetAlert2 ab bhi CDN se hain (internet chahiye)

### 3. PHPMailer Fix
- `contact.php` ab `config.php` se credentials leta hai
- `phpmailer_library/mail.php` `__DIR__` use karta hai (path safe)
- Vendor folder browser se protected hai (.htaccess)

---

## 🚀 Hosting Upload Steps

### Step 1: config.php Edit Karo
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'aapka_db_user');     // ← Hosting cPanel DB user
define('DB_PASS', 'aapka_db_password'); // ← Hosting DB password
define('DB_NAME', 'aapka_db_naam');     // ← Hosting DB naam
define('BASE_URL', 'https://aapki-website.com'); // ← Aapka domain
define('MAIL_USERNAME', 'aapka@gmail.com');       // ← Gmail
define('MAIL_PASSWORD', 'xxxx xxxx xxxx xxxx');   // ← Gmail App Password
define('MAIL_TO_ADMIN', 'aapka@gmail.com');       // ← Admin email
```

### Step 2: Database Import
1. cPanel → phpMyAdmin kholain
2. Nayi database banao
3. SQL file import karo
4. `config.php` mein database naam likhain

### Step 3: Files Upload
1. FileZilla ya cPanel File Manager se `Project/` folder ki sari files upload karein
2. `.htaccess` file upload zaroor karein (hidden file hai)
3. `phpmailer_library/` folder bhi upload karein (vendor folder sab se important)

### Step 4: Folder Permissions
```
images/         → 755
phpmailer_library/ → 755
```

### Step 5: Test Karein
- `https://aapki-website.com/index.php` kholain
- Login test karein
- Contact form test karein

---

## ⚠️ Important Notes

1. **PHPMailer vendor folder** — Zaroor upload karo, agar `vendor/` folder nahi hoga to contact form kaam nahi karega
2. **`images/` folder writable hona chahiye** — Admin se poster upload ke liye
3. **Google Fonts** — Hosting par internet chahiye, offline kaam nahi karega
4. **`.htaccess`** — Apache server par kaam karta hai (cPanel hosting). Nginx par nahi chalega
