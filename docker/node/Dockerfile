ARG NODE_VERSION
FROM node:${NODE_VERSION}

WORKDIR /var/www

COPY package*.json ./

RUN npm install

COPY . .

EXPOSE 5173
