# Docker Helper Script for Library App (PowerShell)
# Usage: .\docker.ps1 [dev|prod] [command]

param(
    [string]$Env = "dev",
    [string]$Command = "up",
    [Parameter(ValueFromRemainingArguments=$true)]
    [string[]]$Args
)

# Handle special case where first param is a command (like 'status', 'help')
if ($Env -eq "status" -or $Env -eq "help" -or $Env -eq "list" -or $Env -eq "prune" -or $Env -eq "reset") {
    $Command = $Env
    $Env = "dev"  # Default, not used for special commands
}

$ComposeFile = "compose.$Env.yaml"

if ($Command -ne "status" -and $Command -ne "help" -and $Command -ne "list" -and $Command -ne "prune" -and $Command -ne "reset" -and -not (Test-Path $ComposeFile)) {
    Write-Host "❌ Error: $ComposeFile not found!" -ForegroundColor Red
    Write-Host "Usage: .\docker.ps1 [dev|prod] [command]"
    exit 1
}

switch ($Command) {
    "up" {

        # Check if OTHER environment is running (Port Conflict Check)
        if ($Env -eq "dev") { $OtherEnv = "prod" } else { $OtherEnv = "dev" }
        $OtherRunning = docker ps --filter "name=library_.*_${OtherEnv}" --format "{{.Names}}" 2>$null
        if ($OtherRunning) {
            Write-Host "⚠️  WARNING: The OTHER environment ($OtherEnv) is currently running!" -ForegroundColor Red
            Write-Host "   Running both simultaneously WILL cause port conflicts (80/8000/3306 etc)."
            Write-Host "   Recommended: .\docker.ps1 $OtherEnv down"
            Write-Host ""
            $confirmation = Read-Host "   Are you sure you want to continue? (y/N)"
            if ($confirmation -notmatch "^[Yy]$") {
                Write-Host "🚫 Operation cancelled." -ForegroundColor Red
                exit 1
            }
            Write-Host ""
        }

        Write-Host "🚀 Starting $Env environment..." -ForegroundColor Green
        docker compose -f $ComposeFile up -d $Args
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
        docker compose -f $ComposeFile build
    }
    "status" {
        Write-Host "📊 Checking running environments..." -ForegroundColor Cyan
        Write-Host ""

        # Check if Docker is available and running
        docker info | Out-Null 2>&1
        if ($LASTEXITCODE -ne 0) {
            Write-Host "❌ Error: Docker command is not found or Docker is not running." -ForegroundColor Red
            Write-Host "   Please ensure Docker Desktop is started." -ForegroundColor Red
            exit 1
        }
        
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
    "prune" {
        Write-Host "⚠️  🔴 DANGER ZONE: PRUNE DOCKER SYSTEM 🔴 ⚠️" -ForegroundColor Red
        Write-Host "=============================================" -ForegroundColor Red
        Write-Host "This command will execute: 'docker system prune -a'"
        Write-Host "It will REMOVE ALL:"
        Write-Host "  - ❌ All STOPPED containers"
        Write-Host "  - ❌ All unused networks"
        Write-Host "  - ❌ All UNUSED IMAGES (not used by any running container)"
        Write-Host "  - ❌ All build cache"
        Write-Host ""
        Write-Host "Ensure the Environment/Container you want to keep is currently RUNNING (UP)." -ForegroundColor Yellow
        Write-Host "Otherwise, its image will be deleted!" -ForegroundColor Yellow
        Write-Host ""
        $confirmation = Read-Host "Are you ABSOLUTELY sure you want to proceed? (y/N)"
        if ($confirmation -notmatch "^[Yy]$") {
            Write-Host "🚫 Operation cancelled." -ForegroundColor Red
            exit 0
        }
        
        Write-Host "🗑️  Pruning system..." -ForegroundColor Yellow
        docker system prune -a
    }
    "reset" {
        Write-Host "🧨  NUCLEAR OPTION: RESET PROJECT 🧨" -ForegroundColor Red
        Write-Host "====================================" -ForegroundColor Red
        Write-Host "This will destroy EVERYTHING for this project:"
        Write-Host "  - 🛑 Stop and Remove all project containers"
        Write-Host "  - 🗑️  Remove all project volumes (DATABASE DATA WILL BE LOST!)"
        Write-Host "  - 🗑️  Remove project images (library_app, library_web, etc)"
        Write-Host "  - 🧹  Remove internal networks"
        Write-Host ""
        Write-Host "AFTERMATH (What to do next):" -ForegroundColor Cyan
        Write-Host "  1. Run '.\docker.ps1 dev up'"
        Write-Host "  2. Wait for build/download"
        Write-Host "  3. Run '.\docker.ps1 dev artisan migrate:fresh --seed' (to restore DB)"
        Write-Host ""
        $confirmation = Read-Host "Are you SURE you want to wipe this project? (y/N)"
        if ($confirmation -notmatch "^[Yy]$") {
            Write-Host "🚫 Operation cancelled." -ForegroundColor Red
            exit 0
        }

        Write-Host "💥 Destroying project resources..." -ForegroundColor Yellow
        # Down with volumes for both envs found
        docker compose -f compose.dev.yaml down -v --rmi all --remove-orphans 2>$null
        docker compose -f compose.prod.yaml down -v --rmi all --remove-orphans 2>$null
        Write-Host "✅ Project reset complete." -ForegroundColor Green
    }
    "help" {
        Write-Host "📖  Docker Helper Script Guide" -ForegroundColor Cyan
        Write-Host "==============================" -ForegroundColor Cyan
        Write-Host "Usage: .\docker.ps1 [dev|prod] [command]"
        Write-Host ""
        Write-Host "Commands:"
        Write-Host "  up       - Start environment"
        Write-Host "  down     - Stop environment"
        Write-Host "  restart  - Restart environment"
        Write-Host "  logs     - View logs"
        Write-Host "  ps       - List containers"
        Write-Host "  status   - Check which environment is running"
        Write-Host "  shell    - Open shell (dev: workspace, prod: app)"
        Write-Host "  artisan  - Run artisan command"
        Write-Host "  composer - Run composer command"
        Write-Host "  npm      - Run npm command (dev only)"
        Write-Host "  build    - Rebuild images"
        Write-Host "  fresh    - Fresh start (removes volumes)"
        Write-Host "  prune    - 🗑️  Clear all unused images/containers (DANGER)" --ForegroundColor Red
        Write-Host "  reset    - 🧨  Wipe ALL project data/volumes/images (NUCLEAR)" --ForegroundColor Red
        Write-Host ""
        Write-Host "Examples:" -ForegroundColor Yellow
        Write-Host "  .\docker.ps1 dev up"
        Write-Host "  .\docker.ps1 prune"
        Write-Host "  .\docker.ps1 reset"
        Write-Host "  .\docker.ps1 status"
        Write-Host "  .\docker.ps1 dev artisan migrate"
        Write-Host "  .\docker.ps1 help"
    }
    "list" {
        # Alias for help
        # Reuse logic or call help
        Write-Host "See help above." 
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
