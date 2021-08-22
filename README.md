1) Set  database Configuration in .Env file
2) set  Email   Configuration in .env file
3) composer update
4) php artisan key:generate
5) php artisan migrate
6) php artisan db:seed --class=UserTableSeeder
7) php artisan db:seed --class=ProductTableSeeder
8) php artisan db:seed --class=EmailTableSeeder
9) php artisan serve