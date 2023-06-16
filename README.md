<p align="center">
	<a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a>
	<img src="https://storage.googleapis.com/ecocrafters_bucket/EcoCrafters.jpg" width="400">
</p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# EcoCrafters Laravel API

This repository contains the source code for the EcoCrafters API, a Laravel-based API for the EcoCrafters project. The API provides endpoints to interact with the EcoCrafters platform and perform various operations related to crafts, users, and detection system.

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
- [API Endpoints](#api-endpoints)
- [Contributing](#contributing)
- [License](#license)

## Installation

1. Clone the repository:
git clone ...

2. Navigate to the project directory:
cd ecocrafters-api

3. Install the dependencies:
composer install

4. Create a copy of the `.env.example` file and rename it to `.env`:
cp .env.example .env

5. Generate the application key:
php artisan key:generate

6. Configure the database connection in the `.env` file.

7. Run the database migrations:
php artisan migrate

8. Start the development server:
php artisan serve

9. Open your browser and visit `http://localhost:8000` to access the API.

## Usage

The EcoCrafters API provides various endpoints to interact with the platform. You can use tools like Postman or cURL to make HTTP requests to these endpoints.

Make sure to authenticate your requests by including the appropriate authentication headers. Refer to the [API documentation](#api-endpoints) for details on each endpoint and their required authentication.

## API Endpoints

The EcoCrafters API provides the following endpoints by the documentation link below :

https://documenter.getpostman.com/view/11927320/2s93m1Z44Y

Refer to the for more details on each endpoint, their request/response formats, and required authentication.

## Contributing

Contributions to the EcoCrafters Laravel API project are welcome! If you find any issues or have suggestions for improvements, please open an issue or submit a pull request.

Before making any contributions, please review our [contribution guidelines](CONTRIBUTING.md).

## License

This project is only for Capstone Project from C23-PR551 Team [MIT License](LICENSE).