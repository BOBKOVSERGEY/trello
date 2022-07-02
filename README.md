https://www.youtube.com/watch?v=cgSnL20vHTQ&list=PLze7bMjv1CYtP6FSv6L60IpI3rynxAStr&index=13
0:0

# Внесение изменений в миграции
php artisan migrate:refresh

# Создание тестовых данных
php artisan db:seed

# Создание ресурного и API контроллера
php artisan make:controller Api/V1/DeskListController --resource --api

# Создание тестовых данных
php artisan make:seeder DeskSeeder

php artisan db:seed --class=TaskSeeder
