<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Bogasari Asset Monitoring System

A Laravel-based web application for real-time monitoring and analysis of industrial assets, with features for tracking asset conditions, parameters, and generating analytics.

## Features

-   **Asset Hierarchy Management**: Organize assets in a three-tier structure (Area > Group > Asset)
-   **Real-time Monitoring**: Track asset parameters with warning and danger thresholds
-   **Data Analysis**:
    -   Individual asset parameter analysis
    -   Cross-asset analysis
    -   Historical data visualization
-   **Alert System**: Warning and danger state monitoring
-   **Dynamic Charts**: Real-time data visualization using ApexCharts
-   **Responsive Interface**: Built with Livewire for dynamic updates

## Requirements

-   PHP >= 8.1
-   PostgreSQL Database
-   Composer
-   Node.js & NPM
-   Laravel 10.x
-   Livewire 3.x

## Installation

1. **Clone the Repository**

    ```bash
    git clone <repository-url>
    cd bogasari
    ```

2. **Install PHP Dependencies**

    ```bash
    composer install
    ```

3. **Install JavaScript Dependencies**

    ```bash
    npm install
    ```

4. **Environment Setup**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5. **Configure Database**

    - Create a PostgreSQL database
    - Update .env with your database credentials:
        ```
        DB_CONNECTION=pgsql
        DB_HOST=127.0.0.1
        DB_PORT=5432
        DB_DATABASE=your_database_name
        DB_USERNAME=your_username
        DB_PASSWORD=your_password
        ```

6. **Run Migrations and Seeders**

    ```bash
    php artisan migrate
    php artisan db:seed
    ```

7. **Build Assets**

    ```bash
    npm run build
    ```

8. **Start the Development Server**
    ```bash
    php artisan serve
    ```

## Automatic Startup Configuration

You can configure the application to start automatically when Windows boots up:

1. **Create Startup Batch File**
   - Create a new file `serve-laravel.bat` with the following content:
     ```batch
     cd /d C:\laragon\www\bogasari
     php artisan serve
     ```
   - Save it to `C:\scripts\serve-laravel.bat`

2. **Create Hidden Execution Script**
   - Create a new file `serve-laravel.vbs` with the following content:
     ```vbscript
     Set WshShell = CreateObject("WScript.Shell")
     WshShell.Run chr(34) & "C:\scripts\serve-laravel.bat" & chr(34), 0
     Set WshShell = Nothing
     ```
   - Save it to `C:\scripts\serve-laravel.vbs`

3. **Add to Windows Startup**
   - Press `Windows + R` to open Run dialog
   - Type `shell:startup` and press Enter
   - Create a shortcut to `C:\scripts\serve-laravel.vbs` in the Startup folder

This configuration will automatically start the Laravel development server when Windows starts.

## Project Structure

-   `app/Models/`: Contains all model definitions (Area, Group, Asset, ListData, LogData)
-   `app/Livewire/`: Contains Livewire components for real-time features
-   `database/migrations/`: Database structure definitions
-   `database/seeders/`: Sample data seeders
-   `resources/views/`: Blade views and Livewire components templates

## Key Components

-   **Area Management**: Manage different areas of the facility
-   **Group Management**: Organize assets into logical groups
-   **Asset Management**: Track individual assets and their parameters
-   **Parameter Monitoring**: Monitor various parameters with warning and danger thresholds
-   **Data Logging**: Automatic logging of parameter values at regular intervals

## Usage

1. **Asset Organization**

    - Create Areas
    - Add Groups to Areas
    - Add Assets to Groups

2. **Parameter Configuration**

    - Set up parameters for each asset
    - Configure warning and danger thresholds

3. **Monitoring**

    - View real-time parameter values
    - Monitor warning and danger states
    - Analyze historical data

4. **Analysis**
    - Use the Asset Analysis feature for individual asset performance
    - Use Cross Asset Analysis for comparing multiple assets
    - Generate reports and visualizations

## Security

-   Ensure proper permissions are set on storage and bootstrap/cache directories
-   Configure your web server to prevent direct access to .env file
-   Regular updates of dependencies for security patches

## License

[Your License Information]

## Support

[Your Support Information]
