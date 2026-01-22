# BON - Natural Beverage E-commerce Store

This is a complete, pure PHP e-commerce application for "BON", featuring a customer-facing shop and a full admin panel.

## Features
- **Frontend**: Responsive design, Product filtering, Cart & Checkout, User Accounts, Animations.
- **Admin**: Dashboard stats, Product/Category/Order management.
- **Tech**: PHP 8, MySQL, HTML, CSS Vanilla JS.

## Prerequisites
- XAMPP, php.
- MySQL

## Setup Instructions

1.  **Database Setup**:
    - Open phpMyAdmin (usually `http://127.0.0.1/phpmyadmin`).
    - Create a new database named `cans_db`.
    - Import the file `database/schema.sql` into this database.
    - *Note: This will create tables and insert sample data (products, admin user).*

2.  **Configuration**:
    - Open `config.php`.
    - Check the database credentials (Default: `root` / empty password). Update if necessary.
    - Update `APP_URL` if your project path is different from `http://127.0.0.1/bonGravity`.

3.  **Run**:
    - Open your browser to `http://localhost/127.0.0.1/index.php`.

## Credentials

**Admin Account**:
- Email: `admin@bon.com`
- Password: `password`

**Customer Account (Sample)**:
- Email: `user@example.com`
- Password: `password`
