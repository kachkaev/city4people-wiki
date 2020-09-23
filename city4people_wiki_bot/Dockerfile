FROM node:12.18.4-alpine

RUN mkdir -p /app
WORKDIR /app

COPY package.json yarn.lock /app/

RUN YARN_CACHE_FOLDER=/dev/shm/yarn_cache yarn --production --frozen-lockfile

COPY src /app/src

USER node

CMD [ "yarn", "start" ]
