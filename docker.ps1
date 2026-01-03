# Docker Helper Script for Library App (PowerShell)
# Usage: .\docker.ps1 [dev|prod] [command]

param(
    [string]$Env = "dev",
    [string]$Command = "up",
    [Parameter(ValueFromRemainingArguments=$true)]
    [string[]]$Args
)

# Handle special case where first param is a command (like 'status')
if ($Env -eq "status") {
    $Command = "status"
    $Env = "dev"  # Default, not used for status
}

$ComposeFile = "compose.$Env.yaml"

if ($Command -ne "status" -and -not (Test-Path $ComposeFile)) {
    Write-Host "❌ Error: $ComposeFile not found!" -ForegroundColor Red
    Write-Host "Usage: .\docker.ps1 [dev|prod] [command]"
    exit 1
}

switch ($Command) {
    "up" {
        Write-Host "🚀 Starting $Env environment..." -ForegroundColor Green
        docker compose -f $ComposeFile up -d --build
    }
    "down" {
        Write-Host "🛑 Stopping $Env environment..." -ForegroundColor Yellow
        docker compose -f $ComposeFile down
    }
    "restart" {
        Write-Host "🔄 Restarting $Env environment..." -ForegroundColor Cyan
        docker compose -f $ComposeFile restart
    }
    "logs" {
        docker compose -f $ComposeFile logs -f
    }
    "ps" {
        docker compose -f $ComposeFile ps
    }
    "shell" {
        if ($Env -eq "dev") {
            docker compose -f $ComposeFile exec workspace sh
        } else {
            docker compose -f $ComposeFile exec app sh
        }
    }
    "artisan" {
        # Always use app container for artisan (has correct PHP version)
        docker compose -f $ComposeFile exec app php artisan $Args
    }
    "composer" {
        if ($Env -eq "dev") {
            docker compose -f $ComposeFile exec workspace composer $Args
        } else {
            docker compose -f $ComposeFile exec app composer $Args
        }
    }
    "npm" {
        docker compose -f $ComposeFile exec workspace npm $Args
    }
    "build" {
        Write-Host "🔨 Building $Env images..." -ForegroundColor Magenta
        docker compose -f $ComposeFile build --no-cache
    }
    "status" {
        Write-Host "📊 Checking running environments..." -ForegroundColor Cyan
        Write-Host ""
        
        # Check development
        $devContainers = docker ps --filter "name=library_.*_dev" --format "{{.Names}}" 2>$null
        if ($devContainers) {
            Write-Host "✅ DEVELOPMENT environment is RUNNING" -ForegroundColor Green
            Write-Host "   Access: http://localhost:8000" -ForegroundColor Gray
            Write-Host "   phpMyAdmin: http://localhost:8080" -ForegroundColor Gray
            Write-Host ""
            docker ps --filter "name=library_.*_dev" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
        } else {
            Write-Host "⭕ Development environment is NOT running" -ForegroundColor Gray
        }
        
        Write-Host ""
        
        # Check production
        $prodContainers = docker ps --filter "name=library_.*_prod" --format "{{.Names}}" 2>$null
        if ($prodContainers) {
            Write-Host "✅ PRODUCTION environment is RUNNING" -ForegroundColor Green
            Write-Host "   Access: http://localhost" -ForegroundColor Gray
            Write-Host ""
            docker ps --filter "name=library_.*_prod" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
        } else {
            Write-Host "⭕ Production environment is NOT running" -ForegroundColor Gray
        }
        
        Write-Host ""
        Write-Host "💡 Tip: Kedua environment bisa jalan bersamaan, tapi akan konflik port!" -ForegroundColor Yellow
        Write-Host "   Sebaiknya hanya jalankan satu environment pada satu waktu." -ForegroundColor Yellow
    }
    "fresh" {
        Write-Host "🧹 Fresh start for $Env environment..." -ForegroundColor Yellow
        docker compose -f $ComposeFile down -v
        docker compose -f $ComposeFile build --no-cache
        docker compose -f $ComposeFile up -d
    }
    default {
        Write-Host "Available commands:" -ForegroundColor Cyan
        Write-Host "  up       - Start environment"
        Write-Host "  down     - Stop environment"
        Write-Host "  restart  - Restart environment"
        Write-Host "  logs     - View logs"
        Write-Host "  ps       - List containers"
        Write-Host "  status   - Check which environment is running"
        Write-Host "  shell    - Open shell"
        Write-Host "  artisan  - Run artisan command"
        Write-Host "  composer - Run composer command"
        Write-Host "  npm      - Run npm command (dev only)"
        Write-Host "  build    - Rebuild images"
        Write-Host "  fresh    - Fresh start (removes volumes)"
        Write-Host ""
        Write-Host "Examples:" -ForegroundColor Yellow
        Write-Host "  .\docker.ps1 status"
        Write-Host "  .\docker.ps1 dev up"
        Write-Host "  .\docker.ps1 dev artisan migrate"
        Write-Host "  .\docker.ps1 dev npm run build"
        Write-Host "  .\docker.ps1 prod up"
    }
}
