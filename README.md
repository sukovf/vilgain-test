# Vilgain TEST

## Installation
Clone the repository.

Create the **.env.local** and **.env.test.local** files in the root of the project, both with the following content.
```dotenv
DATABASE_URL="mysql://root:test@db_server:3306/dev?serverVersion=10.11.2-MariaDB&charset=utf8mb4"
```

Build the images:
```bash
docker-compose build
```

Start the containers:
```bash
docker-compose up -d
```

The **web_server** container automatically downloads **PHP dependencies**, runs **Doctrine migrations** and
generates a **JWT keypair** on startup.

## Usage
The API is available at **http://localhost:8080/**.

You can make calls to the API via **Postman** or **Swagger UI** available at **http://localhost:8080/api/doc**

1. Register a new user via the **/api/auth/register** endpoint.
2. Log in via the **/api/auth/login** endpoint using the credentials you've chosen.
3. Copy the **JWT** token you've just received and paste it as a **Bearer Token** in the Postman **Authorization tab**
    or in the **Authorize modal** in the **Swagger UI**.
4. Have fun!

## Testing
Run the tests.
```bash
docker exec -it web_server vendor/bin/phpunit
```