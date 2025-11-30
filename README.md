# IP Geolocation API Backend

Laravel API backend for IP geolocation lookup application with user authentication and search history management.

## Features

- **User Authentication** - Login with email and password using Laravel Sanctum
- **IP Geolocation Proxy** - Proxy endpoint for ipinfo.io API with caching to avoid rate limits
- **Search History Management** - Store and manage IP search history per user
- **RESTful API** - Clean API endpoints for frontend integration

## Tech Stack

- **Laravel 10** - PHP web framework
- **Laravel Sanctum** - API authentication
- **MySQL/PostgreSQL** - Database
- **Guzzle HTTP** - For external API calls

## Prerequisites

- PHP >= 8.1
- Composer
- MySQL/PostgreSQL
- Node.js & NPM (for frontend assets)

## Installation

### 1. Clone the Repository

```bash
git clone <your-repo-url>
cd api-backend
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Database

Edit `.env` file and set your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Run Migrations

```bash
php artisan migrate
```

### 6. Seed Database

Create a test user for login:

```bash
php artisan db:seed --class=UserSeeder
```

Default test user credentials (from UserSeeder):
- **Email**: `welj@dev.com`
- **Password**: `jlabs123`

### 7. Start the Server

```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

## API Endpoints

### Authentication

- **POST** `/api/login`
  - Body: `{ "email": "welj@dev.com", "password": "jlabs123" }`
  - Returns: `{ "success": true, "user": {...}, "token": "..." }`

- **POST** `/api/logout` (Requires Authentication)
  - Headers: `Authorization: Bearer {token}`
  - Revokes the current access token

### IP Information

- **GET** `/api/ip-info` (Public)
  - Returns current user's IP geolocation information
  - Cached for 5 minutes to reduce API calls

- **GET** `/api/ip-info/{ip}` (Public)
  - Returns geolocation information for specific IP address
  - Cached for 5 minutes

### IP Search History (Requires Authentication)

- **GET** `/api/ip-history`
  - Headers: `Authorization: Bearer {token}`
  - Returns user's IP search history (last 50 entries)

- **POST** `/api/ip-history`
  - Headers: `Authorization: Bearer {token}`
  - Body: `{ "ip": "8.8.8.8", "city": "...", "region": "...", "country": "...", "loc": "..." }`
  - Creates or updates IP search history entry

- **DELETE** `/api/ip-history`
  - Headers: `Authorization: Bearer {token}`
  - Body: `{ "ids": [1, 2, 3] }`
  - Deletes selected history entries

## Database Structure

### Users Table
- Standard Laravel users table with email and password authentication

### ip_search_history Table
- `id` - Primary key
- `user_id` - Foreign key to users table
- `ip` - IP address
- `city` - City name
- `region` - Region/State
- `country` - Country code
- `loc` - Latitude,Longitude coordinates
- `created_at` - Timestamp
- `updated_at` - Timestamp

## CORS Configuration

The API is configured to accept requests from `http://localhost:3000` (React frontend). CORS settings are in `config/cors.php`.

## External Dependencies

- **ipinfo.io API** - Used for IP geolocation data
  - Free tier available (with rate limits)
  - API calls are proxied through this backend to avoid CORS issues
  - Responses are cached for 5 minutes

## Project Structure

```
api-backend/
├── app/
│   └── Http/
│       └── Controllers/
│           └── Api/
│               ├── AuthController.php      # Login/Logout
│               ├── IpInfoController.php    # IP geolocation proxy
│               └── IpHistoryController.php # History management
├── database/
│   ├── migrations/
│   │   └── 2024_01_01_000000_create_ip_search_history_table.php
│   └── seeders/
│       └── UserSeeder.php                 # Test user seeder
├── routes/
│   └── api.php                            # API routes
└── config/
    └── cors.php                            # CORS configuration
```

## Testing

Run the test suite:

```bash
php artisan test
```

## Important Notes

### Frontend Repository

This is the **API Backend** repository. The React frontend is in a separate repository:

**Frontend Repository**: [https://github.com/wchesedh/react-frontend.git](https://github.com/wchesedh/react-frontend.git)

**To run the complete application:**
1. Start this Laravel API backend on `http://localhost:8000`
2. Start the React frontend (see frontend repository README)
3. The frontend will connect to the API automatically

### Environment Variables

Make sure to configure these in your `.env` file:
- Database credentials
- `APP_URL` - Your application URL
- `SANCTUM_STATEFUL_DOMAINS` - If using stateful authentication

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
