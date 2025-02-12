# Agriget E-commerce Backend

## Getting Started

Follow these steps to pull and install the project.

### Prerequisites

Make sure you have the following installed on your machine:
- Git
- PHP
- Composer
- MySQL

### Installation

1. **Clone the repository:**
    ```sh
    git clone https://github.com/yourusername/ag-ecommerce-backend.git
    ```

2. **Navigate to the project directory:**
    ```sh
    cd ag-ecommerce-backend
    ```

3. **Install the dependencies:**
    ```sh
    composer install
    ```

4. **Set up environment variables:**
    Copy the `.env.example` file to `.env` and update the necessary environment variables.
    ```sh
    cp .env.example .env
    ```

5. **Generate an application key:**
    ```sh
    php artisan key:generate
    ```

6. **Run database migrations:**
    ```sh
    php artisan migrate
    ```

7. **Start the development server:**
    ```sh
    php artisan serve
    ```

### Running Tests

To run the tests, use the following command:
```sh
php artisan test
```

### Additional Scripts

- **Clear application cache:**
    ```sh
    php artisan cache:clear
    ```

- **Optimize the application:**
    ```sh
    php artisan optimize
    ```

### Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

### License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
