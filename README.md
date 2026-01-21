# Cross-Browser Clipboard System

A client-server clipboard sharing application built with PHP REST API backend and vanilla JavaScript frontend.

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

### Clipboards
- `GET /api/clipboards` - List all clipboards
- `GET /api/clipboards/{id}` - Get clipboard details
- `POST /api/clipboards` - Create new clipboard
- `PUT /api/clipboards/{id}` - Update clipboard
- `DELETE /api/clipboards/{id}` - Delete clipboard

### Clipboard Items
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
│   ├── dashboard.html         # Dashboard page
│   ├── css/
│   │   └── style.css          # Styles
│   └── js/
│       ├── api.js             # API client wrapper
│       └── dashboard.js       # Dashboard functionality
├── src/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── ClipboardController.php
│   │       └── ClipboardItemController.php
│   └── Core/
│       ├── Model/
│       │   ├── Clipboard.php
│       │   └── ClipboardItem.php
│       └── Repository/
│           ├── ClipboardRepository.php
│           └── ClipboardItemRepository.php
├── .htaccess                  # URL rewriting
└── index.php                  # Main entry point
```

## Setup

1. **Database**: Import `config/database.sql`
2. **Configuration**: Update `config/config.php` with your database credentials
3. **Web Server**: Point to project root, ensure mod_rewrite is enabled
4. **Access**: Open browser to `http://localhost/` (or your server URL)

## Development

The application is fully separated:
- **Backend**: PHP handles all data operations via REST API
- **Frontend**: Browser handles all UI rendering and user interactions
- **Communication**: JSON over HTTP

No server-side templating or mixed PHP/HTML in the client interface.
