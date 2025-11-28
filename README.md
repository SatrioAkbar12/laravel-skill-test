# Laravel Skill Test

### Overview
A Laravel Skill Test to implement RESTful routes for a Post model, completed by Satrio Akbar Sudigdo.

### Recommended environment
- PHP 8.3
- Node v22.15.0
- Database: SQLite
- Server: Built-in development server

### Setup
1. **Clone the repository**
```
git clone https://github.com/SatrioAkbar12/laravel-skill-test.git
cd laravel-skill-test
```
2. **Install dependencies**
```
composer install
```
3. **Configure .env**
```
cp .env.example .env
php artisan key:generate
```
4. **Run migrations and seed database**
```
php artisan migrate --seed
```
5. **Start the server**
```
php artisan serve
```

### Testing
```
# run all test
php artisan test

#run PostTest
php artisan test --filter=PostTest

#run SchedulingPostTest
php artisan test --filter=SchedulingPostTest
```
