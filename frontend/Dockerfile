# Use the official Node.js image as the base image
FROM node:20

# Set the working directory in the container
WORKDIR /app

# Copy package.json and package-lock.json to the container
COPY ["package.json", "package-lock.json","./"]

# Install dependencies
RUN npm i --save --legacy-peer-deps

# Copy the rest of the application code to the container
COPY . ./

ENV NEXT_TELEMETRY_DISABLED 1
# Copy the environment file to the container
COPY .env.production .env

# delete the other env files
RUN rm -rf .env.production
RUN rm -rf .env.development
RUN rm -rf .env.staging
RUN rm -rf .env.preproduction

# Build the Next.js app
RUN npm run build

ENV NODE_ENV production

RUN addgroup --system --gid 1001 nodejs
RUN adduser --system --uid 1001 nextjs

# Expose the port that the app will run on
EXPOSE 3000


# Start the app
CMD ["npm", "start"]
