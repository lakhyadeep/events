# UAT Deployment Guide (GitHub Actions)

This guide walks you through setting up automated deployments to your UAT server whenever changes are pushed to the `develop` branch or triggered manually via GitHub Actions.

---

## 1. Required GitHub Repository Secrets

Go to your repository on GitHub:
**Settings** → **Secrets and variables** → **Actions** → **New repository secret**.

Add the following 5 secrets:

| Secret Name | Description | Example Value |
|---|---|---|
| `UAT_SSH_HOST` | Server IP address or domain name | `192.168.1.100` or `uat.events.example.com` |
| `UAT_SSH_USER` | SSH user with write access to the deployment directory | `deploy` or `ubuntu` |
| `UAT_SSH_KEY` | Private SSH key (OpenSSH format) used to authenticate | Content of `id_ed25519` private key |
| `UAT_SSH_PORT` | SSH port (optional, defaults to 22 if not set) | `22` |
| `UAT_DEPLOY_PATH` | Absolute path to the Laravel root on the UAT server | `/var/www/events` |

---

## 2. Generating & Configuring SSH Deployment Key

### Step 2.1: Generate a dedicated key pair (on your local machine or server)
```bash
ssh-keygen -t ed25519 -C "github-actions-uat" -f ~/.ssh/github_actions_uat
```
*(Press Enter when prompted for passphrase so the key has no passphrase, allowing GitHub Actions to authenticate automatically).*

This creates:
- `~/.ssh/github_actions_uat` (Private key → copy the entire content into `UAT_SSH_KEY` in GitHub Secrets)
- `~/.ssh/github_actions_uat.pub` (Public key → needs to be added to the UAT server)

### Step 2.2: Add public key to the UAT server
Log into your UAT server:
```bash
cat >> ~/.ssh/authorized_keys << 'EOF'
<PASTE_CONTENTS_OF_github_actions_uat.pub_HERE>
EOF
chmod 600 ~/.ssh/authorized_keys
chmod 700 ~/.ssh
```

---

## 3. Initial UAT Server Setup

Ensure your UAT server has the initial repository clone and environment file ready:

### Step 3.1: Clone the repository to the deployment path
```bash
sudo mkdir -p /var/www/events
sudo chown -R $USER:$USER /var/www/events
git clone -b develop https://github.com/lakhyadeep/events.git /var/www/events
cd /var/www/events
```

### Step 3.2: Set up `.env` on the server
Create and configure your `.env` for UAT:
```bash
cp .env.example .env
# Edit database and app URL settings:
nano .env
```
Key configuration settings to check in `.env`:
- `APP_ENV=staging` (or `APP_ENV=uat`)
- `APP_DEBUG=false`
- `APP_URL=https://uat.events.example.com`
- Set your database credentials (`DB_CONNECTION`, `DB_DATABASE`, etc.)

Generate the app key:
```bash
php artisan key:generate
```

### Step 3.3: Permissions & Directory Setup
```bash
sudo chown -R $USER:www-data /var/www/events
chmod -R 775 storage bootstrap/cache
```

If your web server user is `www-data`, ensure the SSH user can write to `storage` and `bootstrap/cache`.

---

## 4. How the GitHub Actions Workflow Works

The workflow [.github/workflows/deploy-uat.yml](file:///.github/workflows/deploy-uat.yml) performs:

1. **Automated Testing** (`test` job):
   - Runs on PHP 8.3 and Node 22 on Ubuntu runner.
   - Executes `composer install`, `npm ci && npm run build`, and `php artisan test`.
   - Prevents deployment if tests fail.

2. **Frontend Asset Compilation** (`build-assets` job):
   - Compiles Vite assets (`npm run build`) in CI.
   - Avoids RAM exhaustion or missing Node.js dependencies on the UAT server.

3. **Remote Deployment** (`deploy` job):
   - Places app into maintenance mode (`php artisan down --render="errors::503"`).
   - Fetches and resets to `origin/develop`.
   - Runs `composer install --no-dev --optimize-autoloader`.
   - SCP transfers pre-built Vite assets to `public/build`.
   - Runs `php artisan migrate --force`.
   - Ensures `php artisan storage:link`.
   - Clears and rebuilds configuration, route, view, and event caches (`php artisan optimize`).
   - Restarts queue worker (`php artisan queue:restart`).
   - Brings app back up (`php artisan up`).

---

## 5. Triggering a Deployment

- **Automatic**: Push or merge a pull request into the `develop` branch.
- **Manual**: Go to **Actions** tab on GitHub → Select **Deploy to UAT** → Click **Run workflow** (select `develop` branch and optionally toggle automated tests).
