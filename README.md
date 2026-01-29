# Cross-Browser Clipboard System

A client-server clipboard sharing application built with PHP REST API backend and vanilla JavaScript frontend.

> **🚀 XAMPP Ready**: This application is configured to work with XAMPP out of the box. See [XAMPP_SETUP.md](XAMPP_SETUP.md) for setup instructions or run `check_config.php` to verify your configuration.

## Architecture

### Server (PHP)
- **API Endpoint**: `/api/`
- **REST Controllers**: Handle clipboard and item operations
- **Models & Repositories**: Domain logic and database access
- **JSON Responses**: All API responses in JSON format
- **CORS Enabled**: Cross-origin requests supported

### Client (Browser)
- **Static Files**: HTML, CSS, JavaScript in `/public/`
- **API Client**: JavaScript fetch-based API wrapper
- **Responsive UI**: Modern, clean interface
- **No Server-Side Rendering**: Pure client-side rendering

## API Endpoints

### Authentication (Public)
- `POST /api/auth/register` - Create new account
- `POST /api/auth/login` - Login with email/password
- `POST /api/auth/logout` - Logout current user
- `GET /api/auth/me` - Get current user info

### Clipboards (Protected)
- `GET /api/clipboards` - List all clipboards
- `GET /api/clipboards/{id}` - Get clipboard details
- `POST /api/clipboards` - Create new clipboard
- `PUT /api/clipboards/{id}` - Update clipboard
- `DELETE /api/clipboards/{id}` - Delete clipboard

### Clipboard Items (Protected)
- `GET /api/clipboards/{id}/items` - List items in clipboard
- `GET /api/clipboards/{id}/items/{itemId}` - Get item details
- `POST /api/clipboards/{id}/items` - Add item to clipboard
- `PUT /api/clipboards/{id}/items/{itemId}` - Update item
- `DELETE /api/clipboards/{id}/items/{itemId}` - Delete item

## Project Structure

```
├── api/
│   └── index.php              # API entry point & routing
├── config/
│   ├── config.php             # Database & app configuration
│   └── database.sql           # Database schema
├── public/                    # Client-side files
│   ├── index.html             # Home page
│   ├── login.html             # Login page
│   ├── register.html          # Registration page
│   ├── dashboard.html         # Dashboard page
│   ├── css/
│   │   └── style.css          # Styles
│   └── js/
│       ├── api.js             # API client wrapper
│       ├── auth.js            # Authentication logic
│       └── dashboard.js       # Dashboard functionality
├── src/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── AuthController.php
│   │       ├── ClipboardController.php
│   │       └── ClipboardItemController.php
│   ├── Core/
│   │   ├── Model/
│   │   │   ├── Clipboard.php
│   │   │   └── ClipboardItem.php
│   │   └── Repository/
│   │       ├── ClipboardRepository.php
│   │       └── ClipboardItemRepository.php
│   ├── Models/
│   │   └── User.php
│   ├── Services/
│   │   └── SessionManager.php
│   └── Middleware/
│       └── AuthMiddleware.php
├── .htaccess                  # URL rewriting
└── index.php                  # Main entry point
```

## Setup

### Quick Start with XAMPP

1. **Install XAMPP** with Apache and MySQL
2. **Enable mod_rewrite** in Apache (see [XAMPP_SETUP.md](XAMPP_SETUP.md) for details)
3. **Copy project** to `C:\xampp\htdocs\clipboard\` (or your htdocs folder)
4. **Create database** named `clipboard_system` in phpMyAdmin
5. **Import schema**: Import `config/database.sql` into the database
6. **Check configuration**: Visit `http://localhost/clipboard/check_config.php`
7. **Run installer**: Visit `http://localhost/clipboard/install.php` to create admin account
8. **Access app**: Go to `http://localhost/clipboard/`

For detailed XAMPP setup instructions, see [XAMPP_SETUP.md](XAMPP_SETUP.md)

### Manual Setup (Other Servers)

1. **Database**: Import `config/database.sql`
2. **Configuration**: Update `config/config.php` with your database credentials
3. **Web Server**: Point to project root, ensure mod_rewrite is enabled
4. **Permissions**: Make `uploads/` directory writable
5. **Create Account**: Navigate to `/register.html` to create your first user
6. **Login**: Use `/login.html` to access the dashboard

## Authentication

The system uses session-based authentication:
- Users must register and login to access clipboards
- Sessions are secure with HTTP-only cookies
- Rate limiting on login attempts
- CSRF protection on forms
- Session timeout after inactivity

## Development

The application is fully separated:
- **Backend**: PHP handles all data operations via REST API
- **Frontend**: Browser handles all UI rendering and user interactions
- **Communication**: JSON over HTTP

No server-side templating or mixed PHP/HTML in the client interface.
