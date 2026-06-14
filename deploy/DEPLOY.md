# Deploying ANIMEX WEAR to an Alibaba Cloud ECS (Ubuntu)

Stack: Laravel 13 · PHP 8.3 · MySQL · Filament admin · nginx + php-fpm.
The storefront is pre-compiled (`public/store/dist/`), so **Node is optional** on the server.

---

## 0. Alibaba console — open the ports (most common gotcha)
In the ECS **Security Group** inbound rules, allow:
- TCP **22** (SSH), **80** (HTTP), **443** (HTTPS) from `0.0.0.0/0`.

Without this, the site is unreachable even if nginx is running. Note your instance's **public IP**.

---

## 1. Connect
```bash
ssh root@YOUR_PUBLIC_IP        # or the user Alibaba gave you
```

## 2. Install the stack
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y software-properties-common
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update

sudo apt install -y nginx mysql-server git unzip curl \
  php8.3-fpm php8.3-cli php8.3-mysql php8.3-mbstring php8.3-xml \
  php8.3-curl php8.3-zip php8.3-bcmath php8.3-gd php8.3-intl

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

## 3. Create the database
```bash
sudo mysql <<'SQL'
CREATE DATABASE animex CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'animex'@'localhost' IDENTIFIED BY 'CHANGE_ME_STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON animex.* TO 'animex'@'localhost';
FLUSH PRIVILEGES;
SQL
```

## 4. Get the code onto the server
Pick ONE:

**A. Git (recommended)** — push the project to a (private) GitHub repo first, then:
```bash
sudo mkdir -p /var/www && cd /var/www
sudo git clone https://github.com/YOU/animex.git animex
```

**B. Upload from your Windows machine** (run in PowerShell locally, excluding heavy dirs):
```powershell
# from C:\laragon\www\animex
tar --exclude=vendor --exclude=node_modules --exclude=.git -czf animex.tgz .
scp animex.tgz root@YOUR_PUBLIC_IP:/tmp/
```
Then on the server:
```bash
sudo mkdir -p /var/www/animex && cd /var/www/animex
sudo tar -xzf /tmp/animex.tgz
```
> Keep `public/store/dist/` and `public/img/designs/` in the upload — they're the built storefront + artwork. Exclude only `vendor/` and `node_modules/`.

## 5. Configure the app
```bash
cd /var/www/animex
cp .env.production.example .env
nano .env          # set APP_URL=http://YOUR_PUBLIC_IP and DB_PASSWORD

composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan filament:upgrade        # publishes Filament assets to public/
php artisan migrate --seed --force  # creates tables + demo data + admin user
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

## 6. Permissions
```bash
sudo chown -R www-data:www-data /var/www/animex
sudo find /var/www/animex -type d -exec chmod 755 {} \;
sudo chmod -R ug+rwX /var/www/animex/storage /var/www/animex/bootstrap/cache
```

## 7. nginx
```bash
sudo cp deploy/nginx.conf /etc/nginx/sites-available/animex
sudo ln -s /etc/nginx/sites-available/animex /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t && sudo systemctl reload nginx
```

Visit **http://YOUR_PUBLIC_IP** → storefront. **/admin** → login `admin@animex.test` / `password`
(change this immediately in the admin, or via tinker).

---

## 8. (Recommended) Domain + HTTPS
Point an A record to the IP, set `server_name` in the nginx conf and `APP_URL` to `https://yourdomain`, then:
```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com
```
Also set `SESSION_SECURE_COOKIE=true` in `.env` and re-run `php artisan config:cache`.

## Updating later
```bash
cd /var/www/animex && git pull          # (or re-upload)
composer install --no-dev -o
# only if you changed public/store/*.jsx: npm ci && npm run build:store
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
sudo systemctl reload php8.3-fpm
```

## Notes / gotchas
- The storefront pulls React, Bootstrap Icons and Google Fonts from public CDNs — fine for end users, nothing to host.
- 1 GB RAM free tier: if `composer install` is killed, add a swap file:
  `sudo fallocate -l 1G /swapfile && sudo chmod 600 /swapfile && sudo mkswap /swapfile && sudo swapon /swapfile`
- Re-generating product art on the server (optional) needs Node: `npm ci && php artisan designs:generate`.
- Don't commit the real `.env`. `APP_DEBUG=false` in production.
