# Notification
Application responsible for send users notification.

# Requirements
- Docker and Docker-Compose

# Stack
- PHP + [Symfony](https://symfony.com/what-is-symfony)
- MSQL

# Patterns
- Factory
- Adapter

# Steps to Run Project

1. Create a docker network to works!
```
docker network create notification-network
```

2. Up all containers
```
docker-compose up -d
```

3. Enter to terminal on container
```
docker exec -it api.notification.dev sh
```

4. Install dependencies
```
composer install
```

5. Running migration
```
php bin/console doctrine:migrations:migrate
```

# Running Unit Tests

1. Running 
```
docker exec -it api.notification.dev php bin/phpunit
```

# Running API

## Swagger
You can access OpenAPI through this link [Swagger](http://localhost:8080/api/doc)

## Running with CURL
```
curl --location 'http://localhost:8080/notification' \
--header 'Content-Type: application/json' \
--data '{
    "userId": 1,
    "context": "status",
    "channel": "console",
    "title": "Hello",
    "message": "You are welcome!"
}'
```

## Payload Documentation

| field                 | Size            | Description                                                                           |
| :----------------:    | :------:        | :----                                                                                 |
| userId                |   int           | User identify                                                                         |
| context               |   string        | Message context e.g. marketing notification. Possible values (status, news, marketing) |
| channel               |   string        | Channel that will used to send a message e.g. SMS, Whatsapp. Today is developed only channel console. Possible values (console) |
| title                 |   string        | Message title |
| message               |   string        | Message content |

## Rules (Sending limits by context)
- status: not more than 2 per minute for each recipient
- news: not more than 1 per day for each recipient
- marketing: not more than 3 per hour for each recipient


## API Return Documentation
| Status                 | Description                                                                           |
| :----------------:     | :------------------------------       |
| 200                    |   Message sent with success            |
| 404                    |   Context or Channel not found         |
| 429                    |   You have exceeded your rate limit    |
| 500                    |   Internal Server Error                |