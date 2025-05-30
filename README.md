﻿# Laravel 12 Project Setup Guide (Using Laravel Herd)

Follow these steps to properly set up the project.

## Install Laravel Herd

First, install Laravel Herd from [https://herd.laravel.com](https://herd.laravel.com) for your system (Mac or Windows).
Herd includes PHP, Composer, MySQL, Redis, and more out of the box.

## Clone the Project into Herd's Directory

Open your Herd sites directory (default is `~/Sites` on Mac or `C:\Users\{YourName}\Sites` on Windows).

Open a terminal in that folder, then run:

```bash
git clone https://github.com/aandrewjuan1/sortieasy.git
cd sortieasy
```

## Install PHP Dependencies

In the project folder, run:

```bash
composer install
```

This will install all Laravel backend dependencies listed in `composer.json`.

## Set Up Environment Variables

Copy the `.env.example` file to a new `.env` file:

```bash
cp .env.example .env
```

Then generate the application key:

```bash
php artisan key:generate
```

## Configure the Database (Using SQLite)

Edit your `.env` file to use SQLite:

```dotenv
DB_CONNECTION=sqlite
```

Create the `database` folder and the SQLite database file:

```bash
mkdir database
touch database/database.sqlite
```

> **Note:** Adjust the `DB_DATABASE` path depending on your operating system.

## Run Migrations and Seeders

Run the database migrations and seed the database:

```bash
php artisan migrate --seed
```

## Install Frontend Dependencies

Install Node.js packages:

```bash
npm install
```

Then compile the frontend assets:

```bash
npm run dev
```

## Start the Server in Herd

Open the Herd app.

Locate your project in the Herd dashboard.

Click **Start Server**.

Run these in terminal as well:
php artisan queue:work
php artisan schedule:work

Visit your project at: [https://sortieasy.test](https://sortieasy.test)

If Herd does not automatically detect your project, you can manually add it through the Herd UI.
