FROM node:20-alpine
WORKDIR /app
COPY package*.json ./
RUN npm install --only=production
COPY src ./src
COPY .env ./.env
EXPOSE 3000
CMD ["npm","start"]
