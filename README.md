# Solar Energy Management System

This project is a web-based application designed to manage and monitor solar energy systems efficiently. It provides tools for tracking energy production, consumption, and storage, enabling users to optimize their solar energy usage.

## Features

- **Energy Monitoring**: Real-time tracking of solar energy production and consumption.
- **Data Visualization**: Graphs and charts for analyzing energy trends.
- **User Management**: Role-based access control for administrators and users.
- **System Alerts**: Notifications for system performance and maintenance.
- **Reports**: Generate detailed reports on energy usage and savings.

## Technologies Used

- **Backend**: Laravel PHP Framework {Filament V3}
- **Frontend**: HTML, CSS, JavaScript
- **Database**: MySQL

## Installation

1. Clone the repository to your local machine:

    ```bash
    git clone https://github.com/dyahunter35/FilamentPOS.git & cd vendor-management
    cp .env.example .env
    php artisan key:generate

    ```

2. Install dependency

    ```bash
    composer install
    npm  install & npm run dev
    ```

3. Seed database and shield

    ```bash
    php artisan migrate
    php artisan shield:setup --fresh
    php artisan shield:generate --all --panel vendors
    php artisan db:seed
    ```

4. Start Queue Services

    ```bash
    php artisan queue:work
    ```


## Usage

Access the application in your browser at `http://localhost:8000`. Log in or register to start managing your solar energy system.

## Contribution

Contributions are welcome! Please fork the repository and submit a pull request.

## License

This project is licensed under the [MIT License](LICENSE).
