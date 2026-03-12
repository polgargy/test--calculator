ARG NODE_VERSION
FROM node:${NODE_VERSION}-alpine

# Set working directory
WORKDIR /var/www/client

# Configure host
ENV HOST=0.0.0.0

# Install dependencies and run dev server
CMD ["sh", "-c", "npm install && npm run dev"]
