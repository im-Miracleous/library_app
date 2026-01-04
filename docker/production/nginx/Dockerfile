FROM nginx:alpine

# Copy custom nginx config
COPY docker/production/nginx/nginx.conf /etc/nginx/conf.d/default.conf

# Expose port 80
EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
