# DPM CRM Project

This is the global hrm project. Follow the steps below to set it up locally or on your server.

---

## Requirements

- PHP >= 8.1
- Composer
- MySQL
- Node.js & NPM (for front-end assets)

---

## Installation

1. Clone the repository:

```ssh terminal
git clone https://github.com/Rogers-James/ops_manager.git


2. Install PHP dependencies:

composer install

3. Copy .env.example to .env:

cp .env.example .env


4. Generate application key:

php artisan key:generate


5. Install Dependencies

npm install
npm run dev


6. Serve Application Locally

php artisan serve




=> For Updating Project

After cloning, to pull latest changes:

git pull origin main

=> Then run:

composer install
php artisan migrate
npm install
npm run dev
