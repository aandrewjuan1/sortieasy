Clone Guide for Laravel 12 Livewire Starter Kit
This guide will help you quickly set up the project using the Tall Stack with Laravel 12 and Livewire, all through the Laravel Herd tool.
Prerequisites
Before you start, make sure you have the following installed:

Laravel Herd: Simplifies setting up the environment for Laravel projects.
Git: For cloning the repository.

Steps to Clone the Project

Clone the Repository
First, clone the repository to your local machine using Git:
git clone https://github.com/your-username/your-repository.git
cd your-repository


Install Laravel Herd
Laravel Herd is a development environment that helps you set up PHP, Composer, Node, and other tools. It ensures compatibility with the versions required for this project.
Download and install Laravel Herd from the official website: Laravel Herd
After installation, open your terminal and confirm that Herd is installed properly by checking the version:
herd --version


Install Dependencies
Run the following commands to install all necessary dependencies for the project:
Install PHP dependencies:
composer install

Install Node.js dependencies:
npm install


Set Up Environment
Next, set up your environment by copying the .env.example file to create your .env file:
cp .env.example .env

Then, generate your application key:
php artisan key:generate


Run Migrations and Seed the Database
Set up your database and run migrations:
php artisan migrate --seed


Start the Development Server in Laravel Herd
Now that your environment is set up, you can start the development server directly within Laravel Herd. Open Laravel Herd, and it will automatically manage the necessary services and start the server for you.


