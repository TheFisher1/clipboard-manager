#!/bin/bash

# Deployment script for Azure VM

echo "Starting deployment..."

# Pull latest changes (if using git)
# git pull origin main

# Stop existing containers
echo "Stopping existing containers..."
sudo docker-compose down

# Build and start containers
echo "Building and starting containers..."
sudo docker-compose up -d --build

# Show running containers
echo "Running containers:"
sudo docker-compose ps

# Show logs
echo "Recent logs:"
sudo docker-compose logs --tail=50

echo "Deployment complete!"
echo "Access your app at: http://$(curl -s ifconfig.me)"
