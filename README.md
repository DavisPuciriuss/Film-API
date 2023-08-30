# Film-API

# Used versions
PHP: 8.1

Sail: 1.18

Pint: 1.0


# Set-up Commands
Rename .env.example to .env

If you don't have a sail alias use this instead for the next commands: "./vendor/bin/sail up"

If you wish to create an alias: https://laravel.com/docs/10.x/sail#configuring-a-shell-alias

sail up -d

sail composer install

sail artisan migrate:fresh --seed

sail artisan optimize

sail npm i

sail npm run build

# Running Tests (https://laravel.com/docs/10.x/testing#running-tests)
sail artisan test

# Running Pint (https://laravel.com/docs/10.x/pint)
./vendor/bin/pint

# Running Larastan (https://github.com/nunomaduro/larastan)
./vendor/bin/phpstan analyse

# Seeder User
Username:
test@test.lv

Password:
password
