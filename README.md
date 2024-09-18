# Install docker & docker-compose

https://www.docker.com/

# Change env

- Copy .env.example -> .env and change here

    - APP_PORT=8085 #Port run website
    - DB_PORT=3306 #Port DB
    - DB_DATABASE=database
    - DB_USERNAME=user
    - DB_PASSWORD=xxxx
    - DB_PASSWORD_ROOT=xxxx
    - PMA_PORT=8080 # Port run phpMyadmin

# Run source

- docker-compose up -d --build
- copy .env.example -> .env in folder src
- Access to container now_app: docker exec -it now_app bash
- Setup for laravel
    - Generate key: php artisan key:generate
    - Install vender: composer install
    - Run migration: php artisan migrate
    - Run seeder: php artisan db:seed
- Access browser http://localhost:${APP_PORT}
