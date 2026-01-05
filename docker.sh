#!/bin/bash

# Docker Helper Script for Library App
# Usage: ./docker.sh [dev|prod] [command]

# Handle special case where first param is a command (like 'status')
if [ "$1" = "status" ]; then
    COMMAND="status"
    ENV="dev" # Default, not used for status
else
    ENV=${1:-dev}
    COMMAND=${2:-up}
fi

COMPOSE_FILE="compose.${ENV}.yaml"

if [ "$COMMAND" != "status" ] && [ ! -f "$COMPOSE_FILE" ]; then
    echo "❌ Error: $COMPOSE_FILE not found!"
    echo "Usage: ./docker.sh [dev|prod] [command]"
    exit 1
fi

case $COMMAND in
    up)
        echo "🚀 Starting $ENV environment..."
        docker compose -f $COMPOSE_FILE up -d --build
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
        docker compose -f $COMPOSE_FILE build --no-cache
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
