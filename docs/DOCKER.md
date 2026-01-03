# Docker Environment Setup

This project uses separate Docker configurations for **Development** and **Production** environments.

## 📁 Directory Structure

```
docker/
├── common/
│   └── php-fpm/
│       └── Dockerfile         # Base PHP-FPM image
├── development/
│   ├── php-fpm/
│   │   ├── entrypoint.sh      # Dev startup script
│   │   └── php.ini            # Dev PHP config
│   ├── workspace/
│   │   └── Dockerfile         # Dev tools (Node, Composer)
│   └── nginx/
│       ├── Dockerfile
│       └── nginx.conf         # Dev Nginx config
└── production/
    ├── php-fpm/
    │   ├── entrypoint.sh      # Prod startup script
    │   └── php.ini            # Prod PHP config (Opcache + JIT)
    └── nginx/
        ├── Dockerfile
        └── nginx.conf         # Prod Nginx config (Gzip + Cache)
```

## 🚀 Quick Start

### Development Environment

```bash
# Start all services
docker compose -f compose.dev.yaml up -d --build

# View logs
docker compose -f compose.dev.yaml logs -f

# Stop services
docker compose -f compose.dev.yaml down

# Stop and remove volumes (fresh start)
docker compose -f compose.dev.yaml down -v
```

**Access:**
- Application: http://localhost:8000
- phpMyAdmin: http://localhost:8080

### Production Environment (Local Testing)

```bash
# Build production images
docker compose -f compose.prod.yaml build

# Start production services
docker compose -f compose.prod.yaml up -d

# View logs
docker compose -f compose.prod.yaml logs -f

# Stop services
docker compose -f compose.prod.yaml down
```

**Access:**
- Application: http://localhost

## 🛠️ Common Commands

### Running Artisan Commands

**Development:**
```bash
# Using workspace container
docker compose -f compose.dev.yaml exec workspace php artisan migrate
docker compose -f compose.dev.yaml exec workspace php artisan db:seed

# Or using app container
docker compose -f compose.dev.yaml exec app php artisan cache:clear
```

**Production:**
```bash
docker compose -f compose.prod.yaml exec app php artisan migrate --force
```

### Running Composer

**Development:**
```bash
docker compose -f compose.dev.yaml exec workspace composer install
docker compose -f compose.dev.yaml exec workspace composer update
```

### Running NPM

**Development:**
```bash
# Install dependencies
docker compose -f compose.dev.yaml exec workspace npm install

# Build assets
docker compose -f compose.dev.yaml exec workspace npm run build

# Watch for changes (development)
docker compose -f compose.dev.yaml exec workspace npm run dev
```

### Database Access

**Development:**
```bash
# MySQL CLI
docker compose -f compose.dev.yaml exec db mysql -u root -p

# Or use phpMyAdmin at http://localhost:8080
```

## ⚡ Performance Optimization (Windows)

### Recommended Setup for Windows

For **best performance** on Windows:

1. **Use WSL 2 Backend**
   - Open Docker Desktop → Settings → General
   - Enable "Use the WSL 2 based engine"

2. **Store Project in WSL 2 Filesystem**
   ```bash
   # Move project to WSL
   # From Windows: \\wsl$\Ubuntu\home\<username>\projects\library_app
   
   # From WSL terminal:
   cd ~
   mkdir -p projects
   cd projects
   git clone <your-repo> library_app
   ```

3. **Why?**
   - Running from Windows filesystem (`C:\...`) is **10-100x slower** due to cross-filesystem I/O
   - WSL 2 native filesystem provides near-native Linux performance

### Build Speed Optimization

The setup includes several optimizations:

- ✅ **BuildKit** enabled (default in modern Docker)
- ✅ **Multi-stage builds** for smaller images
- ✅ **Layer caching** for dependencies
- ✅ **.dockerignore** to exclude unnecessary files

### Runtime Performance

**Development:**
- Opcache enabled with validation (revalidate_freq=2)
- Relaxed timeouts for debugging
- No asset caching in Nginx

**Production:**
- Opcache with JIT enabled
- Aggressive caching (validate_timestamps=0)
- Gzip compression
- Long cache headers for static assets
- Config/route/view caching

## 🔧 Troubleshooting

### Permission Issues

If you encounter permission errors:

```bash
# Development
docker compose -f compose.dev.yaml exec app chown -R www-data:www-data storage bootstrap/cache

# Production
docker compose -f compose.prod.yaml exec app chown -R www-data:www-data storage bootstrap/cache
```

### Slow Performance on Windows

1. Check if using WSL 2:
   ```bash
   wsl --list --verbose
   ```

2. Verify project location:
   ```bash
   pwd
   # Should show: /home/<username>/... (not /mnt/c/...)
   ```

3. If still slow, check Docker Desktop resources:
   - Settings → Resources → WSL Integration
   - Allocate more CPU/Memory if needed

### Database Connection Issues

```bash
# Check if database is ready
docker compose -f compose.dev.yaml exec db mysqladmin ping -h localhost -u root -p

# View database logs
docker compose -f compose.dev.yaml logs db
```

### Rebuilding from Scratch

```bash
# Development
docker compose -f compose.dev.yaml down -v
docker compose -f compose.dev.yaml build --no-cache
docker compose -f compose.dev.yaml up -d

# Production
docker compose -f compose.prod.yaml down -v
docker compose -f compose.prod.yaml build --no-cache
docker compose -f compose.prod.yaml up -d
```

## 📊 Differences: Development vs Production

| Feature | Development | Production |
|---------|-------------|------------|
| Code Mounting | Bind mount (live editing) | Read-only volume |
| Opcache Validation | Enabled (2s) | Disabled (max speed) |
| JIT Compiler | Disabled | Enabled |
| Nginx Caching | Disabled | Aggressive (1 year) |
| Gzip Compression | Disabled | Enabled |
| Debug Mode | Enabled | Disabled |
| phpMyAdmin | Included | Not included |
| Workspace Container | Included | Not included |
| Config Caching | Cleared on start | Cached on start |

## 🎯 Next Steps

1. **First Time Setup:**
   ```bash
   # Copy .env file
   cp .env.example .env
   
   # Update .env with your settings
   # Then start development environment
   docker compose -f compose.dev.yaml up -d --build
   ```

2. **The entrypoint script will automatically:**
   - Install Composer dependencies
   - Install NPM dependencies
   - Run migrations
   - Generate app key (if needed)

3. **Build frontend assets:**
   ```bash
   docker compose -f compose.dev.yaml exec workspace npm run build
   ```

4. **Access the application:**
   - http://localhost:8000

## 📝 Notes

- The old `Dockerfile` and `docker-compose.yml` are kept for reference
- You can delete them once you verify the new setup works
- All entrypoint scripts are automatically made executable
- Database data persists in named volumes
