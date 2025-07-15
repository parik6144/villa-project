## Project Name
Brief description of the project.

### Prerequisites
This project requires:\
	•	PHP ^8.2\
	•	Composer\
	•	Node.js\
	•	A database system - MySQL

### Getting Started
Follow these instructions to set up your environment and start development.
1. Clone the Repository

Clone this repository to your local machine. Use the command:

`git clone git@github.com:dits-agency/laravel-booking.git`

`cd laravel-booking`

2. Install Dependencies

Run the following command to install the necessary PHP and JavaScript dependencies.

`composer install`

3. Set Up Environment Variables

Copy the example environment file and make the necessary changes according to your environment.

`cp .env.example .env`

Then, edit the ⁠.env file with your database connection details and any other configurations.
4. Generate Application Key

Generate a new unique key for your Laravel application.

`php artisan key:generate`

5. Run Migrations

Migrate the database schemas into your database.

`php artisan migrate`

6. Create new Filament admin user

`php artisan make:filament-user`

Admin url is `/backend`

7. Seed the Database (optional)

If you have any seeders, run them to seed your database with initial data.

`php artisan db:seed`

8. Serve Application

Finally, you can serve your application using Laravel’s built-in server:
`php artisan serve` or use your local web software.
This command will start a server at http://localhost:8000

### Coolify
Post-deployment commands:
```
php artisan migrate --force
&& php artisan icons:cache
&& php artisan view:cache
&& php artisan config:cache
&& php artisan route:cache
&& php artisan event:cache
&& php artisan filament:assets
```
