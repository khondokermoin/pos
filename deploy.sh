#!/bin/bash
# ============================================================
#  Cloud POS — Hostinger Deployment Script
#  Hostinger SSH Terminal এ এই script run করুন
#  Usage: bash deploy.sh
#
#  ⚠️  NOTE: public/build/ এবং .env gitignore এ আছে।
#  তাই এগুলো আলাদাভাবে upload করতে হবে:
#    1. Local PC তে: npm run build
#    2. public/build/ folder FTP/File Manager দিয়ে upload করুন
#    3. .env file FTP/File Manager দিয়ে upload করুন
# ============================================================

echo "╔══════════════════════════════════════════════════╗"
echo "║     Cloud POS — Hostinger Deploy Script          ║"
echo "╚══════════════════════════════════════════════════╝"
echo ""

# ── Step 1: Composer Dependencies Install ─────────────────
echo "▶ [1/7] Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction
echo "✅ Composer done."
echo ""

# ── Step 2: .env Setup ────────────────────────────────────
if [ ! -f ".env" ]; then
    echo "▶ [2/7] .env file not found! Copying from .env.hostinger..."
    cp .env.hostinger .env
    echo "⚠️  Please edit .env file with your actual credentials!"
else
    echo "▶ [2/7] .env file already exists. Skipping copy."
fi
echo ""

# ── Step 3: Generate App Key ──────────────────────────────
echo "▶ [3/7] Generating application key..."
php artisan key:generate --force
echo "✅ App key generated."
echo ""

# ── Step 4: Run Migrations ────────────────────────────────
echo "▶ [4/7] Running database migrations..."
php artisan migrate --force
echo "✅ Migrations done."
echo ""

# ── Step 5: Seed Database ─────────────────────────────────
echo "▶ [5/7] Seeding database..."
php artisan db:seed --force
echo "✅ Database seeded."
echo "   Super Admin: superadmin@system.com / password"
echo ""

# ── Step 6: Storage Link ──────────────────────────────────
echo "▶ [6/7] Creating storage symlink..."
# পুরনো symlink বা directory থাকলে আগে মুছে দাও
if [ -L "public/storage" ] || [ -d "public/storage" ]; then
    echo "   ⚠️  Old symlink/directory found. Removing..."
    rm -rf public/storage
fi
# Hostinger এ exec() disabled তাই relative symlink সরাসরি তৈরি করা হচ্ছে
# (php artisan storage:link absolute path ব্যবহার করে, Hostinger এ কাজ করে না)
cd public && ln -s ../storage/app/public storage && cd ..
echo "✅ Storage symlink created (relative path)."
echo ""

# ── Step 7: Optimize & Cache ──────────────────────────────
echo "▶ [7/7] Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
echo "✅ Application optimized."
echo ""

echo "╔══════════════════════════════════════════════════╗"
echo "║          ✅  Deployment Complete!                 ║"
echo "╠══════════════════════════════════════════════════╣"
echo "║  Super Admin Login:                              ║"
echo "║    Email    : superadmin@system.com              ║"
echo "║    Password : password  (পরিবর্তন করুন!)         ║"
echo "╚══════════════════════════════════════════════════╝"
