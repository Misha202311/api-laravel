Laravel Version  9.52.16
PHP Version	8.1.9
//////////////////////////
composer install
Create .env
Connect Database
php artisan migrate
php artisan db:seed --class=DatabaseSeeder
php artisan db:seed --class=TaskSeeder
php artisan passport:install
php artisan l5-swagger:generate
php artisan serve
