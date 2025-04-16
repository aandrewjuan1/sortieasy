# Clone Guide for Laravel 12 Livewire Starter Kit

This guide will help you quickly set up the project using the Tall Stack with Laravel 12 and Livewire, all through the Laravel Herd tool.

## Prerequisites

Before you start, make sure you have the following installed:

- **Laravel Herd**: Simplifies setting up the environment for Laravel projects.
- **Git**: For cloning the repository.

## Steps to Clone the Project

1. **Clone the Repository**

   First, clone the repository to your local machine using Git:

   ```bash
   git clone https://github.com/aandrewjuan1/sortieasy.git
   cd your-repository
   
2. **Install Laravel Herd**
    
    Download and install Laravel Herd from the official website: Laravel Herd
   - After installation, open your terminal and confirm that Herd is installed properly by checking the version:

    ```bash
    herd --version

3. **Install Dependencies**

   Run the following commands to install all necessary dependencies for the project:
   - Install PHP dependencies:

    ```bash
   composer install
    
4. **Set Up Environment**

   Next, set up your environment by copying the .env.example file to create your .env file:

   ```bash
   cp .env.example .env
   php artisan key:generate

5. **Run Migrations and Seed the Database**

   Set up your database and run migrations:

   ```bash
   php artisan migrate --seed

6. **Start the Development Server in Laravel Herd**

   Now that your environment is set up, you can start the development server directly within Laravel Herd. Open Laravel       Herd, and it will automatically manage the necessary services and start the server for you.


