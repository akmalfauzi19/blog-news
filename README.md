
## Installation

1. Clone the repository:

```bash
git Clone https://github.com/akmalfauzi19/blog-news.git
cd blog-news
```

2. Install PHP dependencies:

```bash
composer install
```

3. Install JavaScript dependencies:

```bash
npm install
npm run dev
```
4. Copy the .env.example file to .env and set your environment variables:

```bash
cp .env.example .env
```

Update the .env file with your database credentials and other necessary configurations.

5. Generate the application key:

```bash
php artisan key:generate
```

6. Seed the database:

```bash
php artisan db:seed --class=PermissionTableSeeder
php artisan db:seed --class=CreateAdminUserSeeder
```

## Running the Application

1. Start the development server:

```bash
php artisan serve
```


## Additional Commands

```bash
php artisan cache:clear
php artisan config:clear

```
