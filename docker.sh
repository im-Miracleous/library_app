#!/bin/bash

# Docker Helper Script for Library App
# Usage: ./docker.sh [dev|prod] [command]

# Handle special case where first param is a command (like 'status')
if [[ "$1" == "status" || "$1" == "help" || "$1" == "list" || "$1" == "prune" || "$1" == "reset" ]]; then
    COMMAND="$1"
    ENV="dev" # Default, not used for special commands
else
    ENV=${1:-dev}
    COMMAND=${2:-up}
fi

COMPOSE_FILE="compose.${ENV}.yaml"

if [[ "$COMMAND" != "status" && "$COMMAND" != "help" && "$COMMAND" != "list" && "$COMMAND" != "prune" && "$COMMAND" != "reset" && ! -f "$COMPOSE_FILE" ]]; then
    echo "❌ Error: $COMPOSE_FILE not found!"
    echo "Usage: ./docker.sh [dev|prod] [command]"
    exit 1
fi

case $COMMAND in
    up)



        # Check if OTHER environment is running (Port Conflict Check)
        if [ "$ENV" = "dev" ]; then OTHER_ENV="prod"; else OTHER_ENV="dev"; fi
        OTHER_RUNNING=$(docker ps --filter "name=library_.*_${OTHER_ENV}" --format "{{.Names}}" 2>/dev/null)
        if [ -n "$OTHER_RUNNING" ]; then
            echo "⚠️  WARNING: The OTHER environment ($OTHER_ENV) is currently running!"
            echo "   Running both simultaneously WILL cause port conflicts (80/8000/3306 etc)."
            echo "   Recommended: ./docker.sh $OTHER_ENV down"
            echo ""
            read -p "   Are you sure you want to continue? (y/N) " -r
            echo ""
            if [[ ! $REPLY =~ ^[Yy]$ ]]; then
                echo "🚫 Operation cancelled."
                exit 1
            fi
        fi

        echo "🚀 Starting $ENV environment..."
        docker compose -f $COMPOSE_FILE up -d "${@:3}"
        ;;
    down)
        echo "🛑 Stopping $ENV environment..."
        docker compose -f $COMPOSE_FILE down
        ;;
    restart)
        echo "🔄 Restarting $ENV environment..."
        docker compose -f $COMPOSE_FILE restart
        ;;
    logs)
        docker compose -f $COMPOSE_FILE logs -f
        ;;
    ps)
        docker compose -f $COMPOSE_FILE ps
        ;;
    shell)
        if [ "$ENV" = "dev" ]; then
            docker compose -f $COMPOSE_FILE exec workspace sh
        else
            docker compose -f $COMPOSE_FILE exec app sh
        fi
        ;;
    artisan)
        shift 2
        # Always use app container for artisan (has correct PHP version)
        docker compose -f $COMPOSE_FILE exec app php artisan "$@"
        ;;
    composer)
        shift 2
        if [ "$ENV" = "dev" ]; then
            docker compose -f $COMPOSE_FILE exec workspace composer "$@"
        else
            docker compose -f $COMPOSE_FILE exec app composer "$@"
        fi
        ;;
    npm)
        shift 2
        docker compose -f $COMPOSE_FILE exec workspace npm "$@"
        ;;
    build)
        echo "🔨 Building $ENV images..."
        docker compose -f $COMPOSE_FILE build
        ;;
    status)
        echo "📊 Checking running environments..."
        echo ""

        # Check if Docker is available and running
        if ! docker info > /dev/null 2>&1; then
            echo "❌ Error: Docker command is not found or Docker is not running."
            echo "   Please ensure Docker Desktop is started and WSL integration is enabled."
            exit 1
        fi
        
        # Check development
        devContainers=$(docker ps --filter "name=library_.*_dev" --format "{{.Names}}" 2>/dev/null)
        if [ -n "$devContainers" ]; then
            echo "✅ DEVELOPMENT environment is RUNNING"
            echo "   Access: http://localhost:8000"
            echo "   phpMyAdmin: http://localhost:8080"
            echo ""
            docker ps --filter "name=library_.*_dev" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
        else
            echo "⭕ Development environment is NOT running"
        fi
        
        echo ""
        
        # Check production
        prodContainers=$(docker ps --filter "name=library_.*_prod" --format "{{.Names}}" 2>/dev/null)
        if [ -n "$prodContainers" ]; then
            echo "✅ PRODUCTION environment is RUNNING"
            echo "   Access: http://localhost"
            echo ""
            docker ps --filter "name=library_.*_prod" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
        else
            echo "⭕ Production environment is NOT running"
        fi
        
        echo ""
        echo "💡 Tip: Kedua environment bisa jalan bersamaan, tapi akan konflik port!"
        echo "   Sebaiknya hanya jalankan satu environment pada satu waktu."
        ;;
    fresh)
        echo "🧹 Fresh start for $ENV environment..."
        docker compose -f $COMPOSE_FILE down -v
        docker compose -f $COMPOSE_FILE build --no-cache
        docker compose -f $COMPOSE_FILE up -d
        ;;
    prune)
        echo "⚠️  🔴 DANGER ZONE: PRUNE DOCKER SYSTEM 🔴 ⚠️"
        echo "============================================="
        echo "This command will execute: 'docker system prune -a'"
        echo "It will REMOVE:"
        echo "  - ❌ All STOPPED containers"
        echo "  - ❌ All unused networks"
        echo "  - ❌ All UNUSED IMAGES (not used by any running container)"
        echo "  - ❌ All build cache"
        echo ""
        echo "Ensure the Environment/Container you want to keep is currently RUNNING (UP)."
        echo "Otherwise, its image will be deleted!"
        echo ""
        read -p "Are you ABSOLUTELY sure you want to proceed? (y/N) " -r
        echo ""
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            echo "🚫 Operation cancelled."
            exit 0
        fi
        
        echo "🗑️  Pruning system..."
        docker system prune -a
        ;;
    reset)
        echo "🧨  NUCLEAR OPTION: RESET PROJECT 🧨"
        echo "===================================="
        echo "This will destroy EVERYTHING for this project:"
        echo "  - 🛑 Stop and Remove all project containers"
        echo "  - 🗑️  Remove all project volumes (DATABASE DATA WILL BE LOST!)"
        echo "  - 🗑️  Remove project images (library_app, library_web, etc)"
        echo "  - 🧹  Remove internal networks"
        echo ""
        echo "AFTERMATH (What to do next):"
        echo "  1. Run './docker.sh dev up'"
        echo "  2. Wait for build/download"
        echo "  3. Run './docker.sh dev artisan migrate:fresh --seed' (to restore DB)"
        echo ""
        read -p "Are you SURE you want to wipe this project? (y/N) " -r
        echo ""
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            echo "🚫 Operation cancelled."
            exit 0
        fi

        echo "💥 Destroying project resources..."
        # Down with volumes for both envs found
        docker compose -f compose.dev.yaml down -v --rmi all --remove-orphans 2>/dev/null
        docker compose -f compose.prod.yaml down -v --rmi all --remove-orphans 2>/dev/null
        echo "✅ Project reset complete."
        ;;
    help|list)
        echo "📖  Docker Helper Script Guide"
        echo "=============================="
        echo "Usage: ./docker.sh [dev|prod] [command]"
        echo ""
        echo "Commands:"
        echo "  up       - Start environment"
        echo "  down     - Stop environment"
        echo "  restart  - Restart environment"
        echo "  logs     - View logs"
        echo "  ps       - List containers"
        echo "  status   - Check which environment is running"
        echo "  shell    - Open shell (dev: workspace, prod: app)"
        echo "  artisan  - Run artisan command (e.g., artisan migrate)"
        echo "  composer - Run composer command"
        echo "  npm      - Run npm command (dev only)"
        echo "  build    - Rebuild images"
        echo "  fresh    - Fresh start (removes volumes)"
        echo "  prune    - 🗑️  Clear all unused images/containers (DANGER)"
        echo "  reset    - 🧨  Wipe ALL project data/volumes/images (NUCLEAR)"
        echo ""
        echo "Examples:"
        echo "  ./docker.sh dev up"
        echo "  ./docker.sh prune"
        echo "  ./docker.sh status"
        echo "  ./docker.sh dev artisan migrate"
        echo "  ./docker.sh help"
        ;;
    *)
        echo "Available commands:"
        echo "  up       - Start environment"
        echo "  down     - Stop environment"
        echo "  restart  - Restart environment"
        echo "  logs     - View logs"
        echo "  ps       - List containers"
        echo "  status   - Check which environment is running"
        echo "  shell    - Open shell"
        echo "  artisan  - Run artisan command"
        echo "  composer - Run composer command"
        echo "  npm      - Run npm command (dev only)"
        echo "  build    - Rebuild images"
        echo "  fresh    - Fresh start (removes volumes)"
        ;;
esac
