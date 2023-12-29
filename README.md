## How to setup local environment via docker and sail
1. docker run --rm --interactive --tty \
   --volume $PWD:/app \
   --user $(id -u):$(id -g) \
   --workdir /app \
   laravelsail/php83-composer:latest composer install

2. copy .env.example to .env
3. vendor/bin/sail up
4. sail artisan migrate:fresh
5. sail artisan db:seed
6. sail artisan key:generate
7. sail npm i
9. see localhost. Test user credentials: test@example.com:password
