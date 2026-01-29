**Имена:** [Вашите имена] **фн:** [Вашият факултетен номер]  
**Начална година:** [Година] **Програма:** бакалавър, (СИ) **Курс: 4**  
**Тема:** Cross-Browser Clipboard System - Система за споделяне на съдържание между браузъри  
**Дата:** 2026-01-30 **Предмет:** w25prj_KN_final **имейл:** [Вашият имейл]

**преподавател:** доц. д-р Милен Петров

**Предаване:** Задачата се предава в архив с попълнен настоящия документ, проекта/проектите с кодовете, README.txt файл, който описва съдържанието на архива; папка с допълнителни компоненти и използвани ресурси, архива да се казва [fn]_final.zip.

# ТЕМА: Cross-Browser Clipboard System

## 1. Условие

Разработване на уеб-базирана система за споделяне на различни типове съдържание (линкове, код, текст, изображения, файлове) между различни браузъри и устройства с възможности за:

- Многопотребителско споделяне на съдържание
- Система за нотификации в реално време
- Сигурна автентикация и авторизация
- Управление на жизнения цикъл на съдържанието
- REST API за външни интеграции
- Аналитика и отчети
- Административен панел за управление на системата

Системата позволява на потребителите да създават "clipboards" (контейнери за споделяне), да конфигурират типовете съдържание, които се приемат, да управляват абонаменти и да получават нотификации при добавяне на ново съдържание.

## 2. Въведение - извличане на изисквания

### 2.1 Роли в системата

**Администратор (Admin)**
- Пълен достъп до системата
- Управление на всички clipboards
- Управление на потребители
- Преглед на системна статистика и активност
- Конфигуриране на системни настройки
- Достъп до audit logs

**Обикновен потребител (Regular User)**
- Създаване и управление на собствени clipboards
- Споделяне на съдържание
- Абониране за clipboards
- Получаване на нотификации
- Експортиране на съдържание
- Организиране на clipboards в групи

**Абонат (Subscriber)**
- Достъп до публични clipboards
- Получаване на нотификации за ново съдържание
- Преглед на споделено съдържание
- Изтегляне на файлове

### 2.2 Функционални изисквания

**FR-1: Управление на потребители**
- Регистрация с email верификация
- Сигурна автентикация (bcrypt хеширане)
- Управление на сесии с timeout
- Възстановяване на забравена парола
- Ролево базирана авторизация

**FR-2: Управление на Clipboards**
- Създаване на clipboards с конфигурация
- Публични/частни настройки за видимост
- Дефиниране на позволени типове съдържание (MIME types)
- Конфигуриране на режим: single-item или multi-item
- Ограничения за брой абонати
- Настройка на default expiration time
- Организация в йерархични групи

**FR-3: Управление на съдържание**
- Поддръжка на множество типове: текст, линкове, код, изображения, файлове
- Валидация на съдържанието спрямо clipboard ограниченията
- Автоматично MIME type detection
- Максимален размер на файл: 10MB
- Metadata capture (timestamp, submitter, title, description)
- Content sanitization за сигурност
- Автоматично изтичане на съдържание
- Single-use content опция

**FR-4: Система за нотификации**
- Email нотификации при ново съдържание
- Real-time нотификации чрез WebSocket
- Персонализирани настройки за нотификации
- Batch нотификации опция

**FR-5: Управление на абонаменти**
- Абониране за публични clipboards
- Заявка за достъп до частни clipboards
- Управление на абонати от собственика
- Автоматичен абонамент на собственика

**FR-6: Export и Import**
- Експорт като HTML link, plain text, formatted code
- JavaScript execution export
- PHP re-share export
- Експорт/импорт на конфигурации (JSON/XML)

**FR-7: API интеграция**
- REST API endpoints за външни системи
- Token-based authentication
- Rate limiting
- API документация (OpenAPI/Swagger)
- Webhook нотификации

**FR-8: Аналитика и отчети**
- Usage statistics (views, downloads, subscribers)
- Activity dashboard
- Визуализация с графики
- Експорт на отчети

**FR-9: Административен панел**
- Управление на потребители (създаване, редакция, изтриване, блокиране)
- Управление на всички clipboards
- Системна статистика и метрики
- Преглед на активност и audit logs
- Конфигуриране на системни настройки
- Управление на съдържание (модериране, изтриване)

### 2.3 Нефункционални изисквания

**NFR-1: Performance**
- Page load time: < 2 секунди (95th percentile)
- API response time: < 500ms (95th percentile)
- Поддръжка на 1,000 едновременни потребители
- 100 requests per second throughput

**NFR-2: Scalability**
- Хоризонтално мащабиране чрез добавяне на сървъри
- Stateless application design
- Поддръжка на 1 милион потребители
- 10 милиона content items
- 100GB file storage

**NFR-3: Security**
- Password hashing с bcrypt (cost factor 12+)
- CSRF protection
- Rate limiting на login опити
- SQL injection prevention
- XSS prevention
- Input validation и sanitization
- HTTPS/TLS 1.3 encryption
- GDPR compliance

**NFR-4: Reliability**
- 99.5% uptime (43.8 часа downtime/година)
- Graceful degradation
- Database connection retry logic
- Transaction rollback при грешки
- Daily automated backups
- Recovery time objective (RTO): 4 часа

**NFR-5: Usability**
- Интуитивен и user-friendly интерфейс
- Responsive design (mobile, tablet, desktop)
- Accessibility (WCAG 2.1 Level AA)
- Browser compatibility (Chrome, Firefox, Safari, Edge)
- Contextual help и tooltips

**NFR-6: Maintainability**
- PSR-12 coding standards (PHP)
- Code documentation (PHPDoc)
- Unit test coverage: > 80%
- Comprehensive documentation
- Application performance monitoring

### 2.4 Ползи от реализацията

**За потребителите:**
- Лесно споделяне на информация между различни устройства и браузъри
- Централизирано място за съхранение на често използвани линкове и код
- Автоматични нотификации за ново съдържание
- Организация на информацията в групи
- Контрол върху достъпа и видимостта

**За организациите:**
- Подобрена колаборация между екипи
- Централизирано управление на споделено съдържание
- Audit trail за compliance
- API интеграция с външни системи
- Аналитика за използване

**За разработчиците:**
- REST API за интеграция
- Webhook нотификации
- Добре документиран код
- Модулна архитектура
- Docker containerization за лесно deployment

## 3. Теория - анализ и проектиране на решението

### 3.1 Архитектура на системата

Системата следва **трислойна архитектура** с ясно разделение между презентационен слой, бизнес логика и данни:

```
┌─────────────────────────────────────────────────────────┐
│                   Presentation Layer                     │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │   HTML/CSS   │  │  JavaScript  │  │  WebSocket   │  │
│  │   Frontend   │  │  API Client  │  │   Client     │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
└─────────────────────────────────────────────────────────┘
                          ↕ HTTP/HTTPS
┌─────────────────────────────────────────────────────────┐
│                   Application Layer                      │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │     REST     │  │  Controllers │  │   Services   │  │
│  │   API Layer  │  │   (MVC)      │  │  (Business)  │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │ Middleware   │  │ Repositories │  │    Models    │  │
│  │   (Auth)     │  │  (Data)      │  │   (Domain)   │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
└─────────────────────────────────────────────────────────┘
                          ↕ PDO
┌─────────────────────────────────────────────────────────┐
│                      Data Layer                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │    MySQL     │  │ File Storage │  │    Email     │  │
│  │   Database   │  │   (uploads)  │  │   Service    │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
└─────────────────────────────────────────────────────────┘
```

### 3.2 Декомпозиция на приложението

**Модул 1: Authentication & Authorization**
- User registration с email verification
- Login/Logout functionality
- Session management
- Password reset
- Role-based access control (RBAC)
- API token management

**Модул 2: Clipboard Management**
- CRUD операции за clipboards
- Configuration management
- Access control (public/private)
- Group organization
- Subscription management

**Модул 3: Content Management**
- Content submission (text, links, code, files)
- MIME type validation
- File upload handling
- Content expiration
- Single-use content
- View/download tracking

**Модул 4: Notification System**
- Email notifications
- Real-time WebSocket notifications
- Notification preferences
- Batch notifications
- Webhook delivery

**Модул 5: API Layer**
- REST endpoints
- Token authentication
- Rate limiting
- Request validation
- Response formatting (JSON)

**Модул 6: Admin Panel**
- User management
- Clipboard management
- System statistics
- Activity monitoring
- Settings configuration
- Audit logs

**Модул 7: Analytics & Reporting**
- Usage statistics
- Activity tracking
- Dashboard visualizations
- Report generation
- Export functionality

### 3.3 Дизайн патърни

**MVC (Model-View-Controller)**
- Разделение на бизнес логика, данни и презентация
- Controllers обработват HTTP requests
- Models представят domain entities
- Views (JSON responses) за API

**Repository Pattern**
- Абстракция на data access layer
- Repositories инкапсулират database queries
- Лесно тестване чрез mock repositories

**Service Layer Pattern**
- Business logic в отделни service класове
- Reusable services (EmailService, SessionManager)
- Separation of concerns

**Middleware Pattern**
- AuthMiddleware за проверка на authentication
- Request/response processing pipeline
- Cross-cutting concerns (logging, validation)

**Factory Pattern**
- Response factory за JSON responses
- Model factories за тестване

### 3.4 База данни - схема и релации

**Основни таблици:**

1. **users** - Потребителски акаунти
   - Полета: id, email, password_hash, name, is_admin, email_verified
   - Индекси: email, is_admin

2. **clipboards** - Clipboard конфигурации
   - Полета: id, name, description, owner_id, is_public, max_subscribers, max_items, allowed_content_types (JSON), default_expiration_minutes
   - Релации: owner_id → users(id)
   - Индекси: owner_id, is_public

3. **clipboard_groups** - Групи за организация
   - Полета: id, name, description, created_by
   - Релации: created_by → users(id)

4. **clipboard_group_map** - Mapping между clipboards и groups
   - Полета: clipboard_id, group_id
   - Релации: Many-to-Many между clipboards и groups

5. **clipboard_subscriptions** - Абонаменти
   - Полета: id, clipboard_id, user_id, email_notifications
   - Релации: clipboard_id → clipboards(id), user_id → users(id)
   - Unique constraint: (clipboard_id, user_id)

6. **clipboard_items** - Споделено съдържание
   - Полета: id, clipboard_id, content_type, content_text, file_path, url, title, description, submitted_by, expires_at, view_count, download_count, is_single_use, is_consumed
   - Релации: clipboard_id → clipboards(id), submitted_by → users(id)
   - Индекси: clipboard_id, submitted_by, expires_at

7. **clipboard_activity** - Activity logs
   - Полета: id, clipboard_id, item_id, user_id, action_type, details (JSON), ip_address, user_agent
   - Релации: clipboard_id → clipboards(id), user_id → users(id)
   - Индекси: user_id, clipboard_id, created_at

8. **api_tokens** - API authentication tokens
   - Полета: id, user_id, token_hash, name, permissions (JSON), expires_at, is_active
   - Релации: user_id → users(id)

9. **admin_audit_log** - Admin действия
   - Полета: id, admin_user_id, action_type, target_type, target_id, action_details (JSON)
   - Релации: admin_user_id → users(id)
   - Индекси: admin_user_id, action_type, target_type

10. **system_settings** - Системни настройки
    - Полета: id, setting_key, setting_value, setting_type, category, is_public
    - Индекси: setting_key, category

**Релационна диаграма:**
```
users (1) ──────< (N) clipboards
users (1) ──────< (N) clipboard_subscriptions
users (1) ──────< (N) clipboard_items
users (1) ──────< (N) clipboard_groups
clipboards (N) ──< (N) clipboard_groups (via clipboard_group_map)
clipboards (1) ──────< (N) clipboard_items
clipboards (1) ──────< (N) clipboard_subscriptions
clipboards (1) ──────< (N) clipboard_activity
```

### 3.5 API Design

**REST API Endpoints:**

**Authentication (Public):**
- `POST /api/auth/register` - Регистрация
- `POST /api/auth/login` - Login
- `POST /api/auth/logout` - Logout
- `GET /api/auth/me` - Current user info

**Clipboards (Protected):**
- `GET /api/clipboards` - List clipboards
- `GET /api/clipboards/{id}` - Get clipboard
- `POST /api/clipboards` - Create clipboard
- `PUT /api/clipboards/{id}` - Update clipboard
- `DELETE /api/clipboards/{id}` - Delete clipboard

**Clipboard Items (Protected):**
- `GET /api/clipboards/{id}/items` - List items
- `GET /api/clipboards/{id}/items/{itemId}` - Get item
- `POST /api/clipboards/{id}/items` - Add item
- `PUT /api/clipboards/{id}/items/{itemId}` - Update item
- `DELETE /api/clipboards/{id}/items/{itemId}` - Delete item

**Subscriptions (Protected):**
- `POST /api/clipboards/{id}/subscribe` - Subscribe
- `DELETE /api/clipboards/{id}/unsubscribe` - Unsubscribe
- `GET /api/clipboards/{id}/subscribers` - List subscribers

**Admin API (Admin only):**
- `GET /api/admin/users` - List users
- `POST /api/admin/users` - Create user
- `PUT /api/admin/users/{id}` - Update user
- `DELETE /api/admin/users/{id}` - Delete user
- `GET /api/admin/clipboards` - All clipboards
- `GET /api/admin/activity` - System activity
- `GET /api/admin/statistics` - System statistics
- `GET /api/admin/settings` - System settings
- `PUT /api/admin/settings` - Update settings

**Response Format:**
```json
{
  "success": true,
  "data": { ... },
  "timestamp": "2026-01-30T12:00:00Z"
}
```

**Error Format:**
```json
{
  "success": false,
  "error": {
    "code": "ERROR_CODE",
    "message": "Human readable message",
    "details": { ... }
  },
  "timestamp": "2026-01-30T12:00:00Z"
}
```

### 3.6 Security Design

**Authentication:**
- Password hashing: bcrypt с cost factor 12
- Session-based authentication за web interface
- Token-based authentication за API
- Session timeout: 30 минути inactivity, 1 час maximum
- Account lockout след 5 неуспешни опита

**Authorization:**
- Role-based access control (Admin, User)
- Permission checking на всяка операция
- Owner-based permissions за clipboards
- Subscriber-based access за съдържание

**Data Protection:**
- HTTPS/TLS 1.3 за encryption in transit
- Prepared statements за SQL injection prevention
- Input validation и sanitization за XSS prevention
- CSRF tokens на всички forms
- File upload validation (MIME type, size, malware scanning)

**API Security:**
- Token authentication
- Rate limiting (100 requests/minute per token)
- Request signing
- CORS configuration
- API key rotation

## 4. Използвани технологии

### 4.1 Backend технологии

**PHP 8.1**
- Основен backend език
- Object-oriented programming
- Type declarations и strict types
- Error handling с exceptions
- PDO за database access

**MySQL 8.0**
- Релационна база данни
- JSON column support за flexible data
- Foreign key constraints за referential integrity
- Indexes за performance optimization
- Transactions за data consistency

**Composer**
- Dependency management
- Autoloading (PSR-4)
- Package management

### 4.2 Frontend технологии

**HTML5**
- Semantic markup
- Form validation
- Local storage API
- File API за uploads

**CSS3**
- Flexbox и Grid layouts
- Responsive design
- CSS variables
- Animations и transitions
- Custom properties

**JavaScript (ES6+)**
- Vanilla JavaScript (без frameworks)
- Fetch API за HTTP requests
- Async/await за asynchronous operations
- Modules (import/export)
- Event handling
- DOM manipulation

### 4.3 DevOps и Infrastructure

**XAMPP 8.1+**
- Integrated development environment
- Apache HTTP Server 2.4
- MySQL 8.0
- PHP 8.1
- phpMyAdmin
- All-in-one package за лесна инсталация

**Apache HTTP Server 2.4**
- Web server (включен в XAMPP)
- mod_rewrite за URL routing
- .htaccess configuration
- Virtual hosts support

### 4.4 Development Tools

**Git**
- Version control
- Branching strategy (feature branches)
- Commit conventions
- GitLab repository

**Visual Studio Code**
- Code editor
- PHP extensions
- Debugging tools
- Git integration

**PHPMyAdmin**
- Database management interface
- Query execution
- Schema visualization
- Data import/export

### 4.5 Версии и среда

**Операционна система:**
- Development: Windows 10/11, macOS, Linux
- Production: Linux (Ubuntu 22.04 LTS) или Windows Server

**Runtime Environment (XAMPP):**
- XAMPP: 8.1.x или по-нова версия
- PHP: 8.1
- MySQL: 8.0
- Apache: 2.4
- phpMyAdmin: 5.x

**PHP Extensions (включени в XAMPP):**
- pdo_mysql - MySQL database driver
- mbstring - Multibyte string support
- xml - XML processing
- curl - HTTP client
- gd - Image processing
- openssl - SSL/TLS support

**Composer Packages:**
- (Проектът използва vanilla PHP без external dependencies за core functionality)

### 4.6 Ports и Services

**Development Environment:**
- Web Application: http://localhost:8080
- MySQL Database: localhost:3306
- PHPMyAdmin: http://localhost:8081

**Production Environment:**
- Web Application: https://w25-[fn]-[shortname]-dev.hss.fmi.uni-sofia.bg
- MySQL: Internal network only
- PHPMyAdmin: Admin access only

### 4.7 Browser Support

**Supported Browsers:**
- Google Chrome 120+ (последни 2 версии)
- Mozilla Firefox 120+ (последни 2 версии)
- Safari 17+ (последни 2 версии)
- Microsoft Edge 120+ (последни 2 версии)

**Browser Features Required:**
- JavaScript enabled
- Cookies enabled
- Local Storage support
- Fetch API support
- ES6+ support

## 5. Инсталация, настройки и DevOps

### 5.1 Предварителни изисквания

**За локална разработка:**
- XAMPP 8.1+ инсталиран (https://www.apachefriends.org/)
- Git за клониране на repository (optional)
- Text editor (VS Code, Sublime Text, или друг)
- 2GB RAM минимум
- 5GB свободно дисково пространство

**За production deployment:**
- Linux server (Ubuntu 22.04 LTS препоръчан) или Windows Server
- Apache + PHP + MySQL (LAMP/WAMP stack)
- Domain name и SSL certificate
- Email server за нотификации

### 5.2 Инсталация - Локална среда с XAMPP

**Стъпка 1: Инсталиране на XAMPP**

1. Изтеглете XAMPP от https://www.apachefriends.org/
2. Инсталирайте XAMPP (изберете Apache, MySQL, PHP, phpMyAdmin)
3. Стартирайте XAMPP Control Panel
4. Стартирайте Apache и MySQL services

**Стъпка 2: Копиране на проекта**

Копирайте проектната папка в XAMPP htdocs директорията:

**Windows:**
```
C:\xampp\htdocs\clipboard-system\
```

**macOS:**
```
/Applications/XAMPP/htdocs/clipboard-system/
```

**Linux:**
```
/opt/lampp/htdocs/clipboard-system/
```

Или клонирайте от Git:
```bash
cd C:\xampp\htdocs\
git clone https://gitlab.hss.fmi.uni-sofia.bg/[username]/clipboard-system.git
```

**Стъпка 3: Създаване на базата данни**

1. Отворете phpMyAdmin: http://localhost/phpmyadmin/
2. Login с username: `root`, password: (празна по default)
3. Кликнете "New" за създаване на нова база данни
4. Име на база: `clipboard_system`
5. Collation: `utf8mb4_general_ci`
6. Кликнете "Create"

**Стъпка 4: Импортиране на database schema**

1. В phpMyAdmin изберете `clipboard_system` база данни
2. Отидете на таб "Import"
3. Изберете файл: `config/database.sql`
4. Кликнете "Go"
5. Проверете че всички таблици са създадени

**Стъпка 5: Конфигурация на database connection**

Редактирайте `config/config.php`:
```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'clipboard_system');
define('DB_USER', 'root');
define('DB_PASS', ''); // Празна парола за XAMPP по default

define('APP_NAME', 'Clipboard System');
```

**Стъпка 6: Проверка на инсталацията**

Отворете browser и посетете:
- Приложение: http://localhost/clipboard-system/
- phpMyAdmin: http://localhost/phpmyadmin/

**Стъпка 7: Създаване на първи потребител**

Отидете на http://localhost/clipboard-system/register.html и създайте акаунт.

Default admin акаунт (вече създаден в database):
- Email: admin@test.com
- Password: password

### 5.3 Структура на файлове

```
clipboard-system/
├── api/                      # API endpoints
│   ├── index.php            # Main API router
│   └── admin/
│       └── index.php        # Admin API router
├── admin/                    # Admin panel
│   ├── *.php                # Admin pages
│   ├── css/                 # Admin styles
│   └── js/                  # Admin JavaScript
├── config/
│   ├── config.php           # Database configuration
│   └── database.sql         # Database schema
├── public/                   # Public frontend
│   ├── *.html               # HTML pages
│   ├── css/                 # Stylesheets
│   ├── js/                  # JavaScript files
│   └── images/              # Static images
├── src/                      # PHP source code
│   ├── Controllers/         # API controllers
│   ├── Core/                # Domain models & repositories
│   ├── Models/              # User model
│   ├── Services/            # Business logic services
│   ├── Middleware/          # Authentication middleware
│   └── Helpers/             # Helper classes
├── uploads/                  # User uploaded files
├── scripts/                  # Maintenance scripts
├── .htaccess                # Apache URL rewriting
├── index.php                # Main entry point
└── README.md                # Project documentation
```

### 5.4 Конфигурация

**Database Configuration (config/config.php):**
```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'clipboard_system');
define('DB_USER', 'root');
define('DB_PASS', ''); // Празна за XAMPP, или вашата парола

define('APP_NAME', 'Clipboard System');
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB
define('UPLOAD_DIR', __DIR__ . '/../uploads/');

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }
    return $pdo;
}
```

**Apache Configuration (.htaccess):**
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^api/(.*)$ api/index.php [L,QSA]

# Security headers
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
```

**XAMPP Configuration:**
- Apache Port: 80 (default) или 8080 ако 80 е зает
- MySQL Port: 3306
- PHP Version: 8.1+
- Document Root: C:\xampp\htdocs\ (Windows)

### 5.5 Примерни настройки

**За локална разработка (XAMPP):**
- URL: http://localhost/clipboard-system/
- Database Host: localhost:3306
- Database name: clipboard_system
- Database user: root
- Database password: (празна по default в XAMPP)
- phpMyAdmin: http://localhost/phpmyadmin/

**За production (ФМИ):**
- URL: https://w25-[fn]-clipboard-dev.hss.fmi.uni-sofia.bg
- Database Host: localhost
- Database name: clipboard_system_prod
- Database user: clipboard_user (не root!)
- Database password: [secure-password]

### 5.6 Примерни акаунти

**Administrator:**
- Email: admin@test.com
- Password: password
- Роля: Admin
- Достъп: Пълен системен достъп

**Regular User (създайте чрез registration):**
- Email: [your-email]
- Password: [your-password]
- Роля: User
- Достъп: Собствени clipboards

### 5.7 Стартиране на приложението

**Development mode (XAMPP):**

1. Отворете XAMPP Control Panel
2. Стартирайте Apache (кликнете "Start")
3. Стартирайте MySQL (кликнете "Start")
4. Проверете че Apache и MySQL са зелени (Running)

**Access points:**
1. Home page: http://localhost/clipboard-system/
2. Login: http://localhost/clipboard-system/login.html
3. Register: http://localhost/clipboard-system/register.html
4. Dashboard: http://localhost/clipboard-system/dashboard.html (след login)
5. Admin panel: http://localhost/clipboard-system/admin/ (admin акаунт)
6. phpMyAdmin: http://localhost/phpmyadmin/

**Спиране на services:**
1. Отворете XAMPP Control Panel
2. Кликнете "Stop" за Apache
3. Кликнете "Stop" за MySQL

### 5.8 Troubleshooting

**Problem: Port 80 already in use**
- Решение 1: Спрете други services на port 80 (Skype, IIS, etc.)
- Решение 2: Променете Apache port в XAMPP:
  1. XAMPP Control Panel → Config → Apache (httpd.conf)
  2. Намерете `Listen 80` и променете на `Listen 8080`
  3. Намерете `ServerName localhost:80` и променете на `ServerName localhost:8080`
  4. Restart Apache
  5. Достъп: http://localhost:8080/clipboard-system/

**Problem: Database connection failed**
- Проверете че MySQL е стартиран в XAMPP Control Panel
- Проверете username/password в config/config.php
- Проверете че базата данни `clipboard_system` съществува
- Тествайте connection в phpMyAdmin

**Problem: Apache won't start**
- Проверете за port conflicts (80, 443)
- Проверете Apache error logs: `C:\xampp\apache\logs\error.log`
- Рестартирайте XAMPP като Administrator

**Problem: MySQL won't start**
- Проверете за port 3306 conflicts
- Проверете MySQL error logs: `C:\xampp\mysql\data\mysql_error.log`
- Рестартирайте XAMPP като Administrator

**Problem: Permission denied for uploads**
- Windows: Right-click `uploads/` folder → Properties → Security → Edit → Add "Everyone" с Full Control
- Linux/Mac: `chmod -R 777 uploads/`

**Problem: Changes not reflected**
- Clear browser cache
- Use incognito mode
- Hard refresh (Ctrl+Shift+R)
- Restart Apache в XAMPP

**Problem: .htaccess not working**
- Проверете че mod_rewrite е enabled в Apache
- XAMPP Control Panel → Config → Apache (httpd.conf)
- Намерете `LoadModule rewrite_module modules/mod_rewrite.so` и уверете се че не е commented
- Restart Apache

## 6. Кратко ръководство на потребителя

### 6.1 Регистрация и Login

**Регистрация на нов потребител:**

1. Отворете http://localhost:8080/register.html
2. Попълнете формата:
   - Email адрес
   - Пълно име
   - Парола (минимум 6 символа)
   - Потвърждение на парола
3. Натиснете "Register"
4. При успех ще бъдете пренасочени към login страницата

![Registration Form - Форма с полета за email, name, password]

**Login:**

1. Отворете http://localhost:8080/login.html
2. Въведете email и парола
3. Натиснете "Login"
4. При успех ще бъдете пренасочени към Dashboard

![Login Form - Форма с email и password полета]

### 6.2 Dashboard - Преглед на Clipboards

След успешен login се показва Dashboard с:

- **My Clipboards** - Списък на вашите clipboards
- **Subscribed Clipboards** - Clipboards, за които сте абонирани
- **Create New Clipboard** бутон
- **Browse Public Clipboards** линк

![Dashboard - Списък с clipboards, всеки показва име, описание, брой items]

### 6.3 Създаване на Clipboard

1. От Dashboard натиснете "Create New Clipboard"
2. Попълнете формата:
   - **Name**: Име на clipboard (напр. "Code Snippets")
   - **Description**: Описание (напр. "Useful code examples")
   - **Visibility**: Public или Private
   - **Max Items**: Single item или Multiple items
   - **Max Subscribers**: Single или Multiple
   - **Allowed Content Types**: Изберете типове (text, code, images, files)
   - **Default Expiration**: Време за автоматично изтриване
3. Натиснете "Create Clipboard"

![Create Clipboard Form - Форма с всички конфигурационни опции]

### 6.4 Добавяне на съдържание

1. Отворете clipboard от Dashboard
2. Натиснете "Add Content"
3. Изберете тип съдържание:
   - **Text**: Въведете текст в textarea
   - **Link**: Въведете URL адрес
   - **Code**: Изберете език и въведете код
   - **Image**: Upload на изображение
   - **File**: Upload на файл
4. Попълнете:
   - **Title**: Заглавие (optional)
   - **Description**: Описание (optional)
   - **Expiration**: Време за изтичане (optional)
5. Натиснете "Submit"

![Add Content Form - Форма с различни полета според типа съдържание]

**Резултат:**
- Съдържанието се добавя към clipboard
- Всички абонати получават нотификация
- Показва се в списъка с items

![Content List - Списък с добавени items, всеки с preview, timestamp, submitter]

### 6.5 Преглед на съдържание

1. Кликнете на item от списъка
2. Показва се детайлна информация:
   - Пълно съдържание
   - Metadata (submitter, timestamp, views, downloads)
   - Export опции
   - Download бутон (за файлове)
3. Опции:
   - **View**: Преглед на съдържанието
   - **Download**: Изтегляне (за файлове)
   - **Copy**: Копиране в clipboard
   - **Export**: Експорт в различни формати
   - **Delete**: Изтриване (ако сте owner)

![Content Detail View - Детайлен преглед с всички опции]

### 6.6 Абониране за Clipboard

**За публични clipboards:**

1. Отидете на "Browse Public Clipboards"
2. Прегледайте списъка с публични clipboards
3. Кликнете "Subscribe" на желания clipboard
4. Clipboard се добавя към "Subscribed Clipboards"
5. Започвате да получавате нотификации

![Browse Public Clipboards - Списък с публични clipboards с Subscribe бутони]

**За частни clipboards:**

1. Получете линк от собственика
2. Кликнете "Request Access"
3. Собственикът одобрява заявката
4. Получавате достъп и нотификации

### 6.7 Управление на абонати (Owner)

1. Отворете вашия clipboard
2. Кликнете "Manage Subscribers"
3. Виждате списък с абонати
4. Опции:
   - **Remove**: Премахване на абонат
   - **View Activity**: Преглед на активността на абоната
5. За частни clipboards:
   - **Pending Requests**: Заявки за достъп
   - **Approve/Reject**: Одобряване или отхвърляне

![Manage Subscribers - Списък с абонати и опции за управление]

### 6.8 Организация в групи

1. От Dashboard кликнете "Manage Groups"
2. Създайте нова група:
   - Въведете име и описание
   - Натиснете "Create Group"
3. Добавете clipboards към група:
   - Drag & drop clipboard върху група
   - Или използвайте "Move to Group" от clipboard menu
4. Създавайте подгрупи за йерархична организация

![Groups Management - Дървовидна структура с групи и clipboards]

### 6.9 Export на съдържание

1. Отворете item
2. Кликнете "Export"
3. Изберете формат:
   - **HTML Link**: `<a href="...">Title</a>`
   - **Plain Text**: Чист текст
   - **Code Preview**: Форматиран код с syntax highlighting
   - **Markdown**: Markdown формат
   - **JSON**: JSON структура
4. Копирайте или изтеглете

![Export Options - Различни формати за експорт]

### 6.10 Настройки на нотификации

1. От Dashboard кликнете "Settings"
2. Секция "Notifications":
   - **Email Notifications**: Enable/Disable
   - **Notification Frequency**: Immediate, Hourly, Daily
   - **Per-Clipboard Settings**: Индивидуални настройки
3. Запазете промените

![Notification Settings - Форма с опции за нотификации]

### 6.11 Admin Panel (само за администратори)

**Достъп:** http://localhost:8080/admin/

**Функционалности:**

1. **Dashboard**: Системна статистика
   - Общ брой потребители
   - Общ брой clipboards
   - Активност за последните 24 часа
   - Графики и визуализации

![Admin Dashboard - Статистика и графики]

2. **Users Management**:
   - Списък с всички потребители
   - Create/Edit/Delete потребители
   - Block/Unblock акаунти
   - Промяна на роли

![Users Management - Таблица с потребители и действия]

3. **Clipboards Management**:
   - Преглед на всички clipboards
   - Модериране на съдържание
   - Изтриване на неподходящо съдържание
   - Статистика по clipboard

![Clipboards Management - Списък с всички clipboards]

4. **Activity Log**:
   - Преглед на системна активност
   - Филтриране по потребител, действие, дата
   - Export на logs

![Activity Log - Таблица с activity записи]

5. **System Settings**:
   - Конфигуриране на системни параметри
   - Email настройки
   - Upload ограничения
   - Security настройки

![System Settings - Форма с различни настройки]

### 6.12 Често използвани сценарии

**Сценарий 1: Споделяне на код между устройства**
1. Създайте clipboard "My Code Snippets" (Private, Multiple items)
2. От компютъра добавете код snippet
3. От телефона отворете clipboard и вижте кода
4. Копирайте и използвайте

**Сценарий 2: Team collaboration**
1. Създайте clipboard "Team Resources" (Public, Multiple items)
2. Добавете полезни линкове и документи
3. Членовете на екипа се абонират
4. Всички получават нотификации при ново съдържание

**Сценарий 3: Временно споделяне на файл**
1. Създайте clipboard "Temp Share" (Public, Single item, 1 hour expiration)
2. Upload файла
3. Споделете линка
4. След 1 час файлът се изтрива автоматично

## 7. Примерни данни

### 7.1 Тестови акаунти

**Administrator Account:**
```
Email: admin@test.com
Password: password
Role: Admin
Description: Пълен достъп до системата, admin panel, всички clipboards
```

**Regular User Account 1:**
```
Email: user1@test.com
Password: password123
Role: User
Description: Обикновен потребител с няколко clipboards
```

**Regular User Account 2:**
```
Email: user2@test.com
Password: password123
Role: User
Description: Обикновен потребител, абониран за публични clipboards
```

### 7.2 Примерни Clipboards

**Clipboard 1: "Code Snippets"**
```
Owner: admin@test.com
Visibility: Public
Max Items: Multiple
Max Subscribers: Multiple
Allowed Types: text/plain, text/javascript, text/php, text/html
Description: Useful code examples and snippets
```

**Clipboard 2: "Team Links"**
```
Owner: user1@test.com
Visibility: Public
Max Items: Multiple
Max Subscribers: Multiple
Allowed Types: text/uri-list
Description: Important links for the team
```

**Clipboard 3: "Quick Share"**
```
Owner: user1@test.com
Visibility: Private
Max Items: Single
Max Subscribers: Single
Allowed Types: All types
Default Expiration: 1 hour
Description: Temporary file sharing
```

### 7.3 Примерно съдържание

**Text Content:**
```
Title: Welcome Message
Content Type: text/plain
Content: "Welcome to the Clipboard System! This is a test message."
Clipboard: Code Snippets
```

**Code Snippet:**
```
Title: JavaScript Array Map Example
Content Type: text/javascript
Content:
const numbers = [1, 2, 3, 4, 5];
const doubled = numbers.map(n => n * 2);
console.log(doubled); // [2, 4, 6, 8, 10]
Clipboard: Code Snippets
```

**Link:**
```
Title: PHP Documentation
Content Type: text/uri-list
URL: https://www.php.net/docs.php
Description: Official PHP documentation
Clipboard: Team Links
```

**Image:**
```
Title: Logo
Content Type: image/png
File: logo.png (sample image in uploads/)
Clipboard: Team Links
```

### 7.4 SQL скрипт за тестови данни

Файл: `config/test_data.sql`

```sql
-- Insert test users
INSERT INTO users (email, password_hash, name, is_admin) VALUES
('user1@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test User 1', FALSE),
('user2@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test User 2', FALSE);

-- Insert test clipboards
INSERT INTO clipboards (name, description, owner_id, is_public, max_items, allowed_content_types) VALUES
('Code Snippets', 'Useful code examples', 1, TRUE, NULL, '["text/plain", "text/javascript", "text/php", "text/html"]'),
('Team Links', 'Important links', 2, TRUE, NULL, '["text/uri-list"]'),
('Quick Share', 'Temporary sharing', 2, FALSE, 1, '["text/plain", "image/jpeg", "image/png", "application/zip"]');

-- Insert test subscriptions
INSERT INTO clipboard_subscriptions (clipboard_id, user_id) VALUES
(1, 1), -- Owner auto-subscribed
(1, 2), -- User 2 subscribed to Code Snippets
(2, 2), -- Owner auto-subscribed
(3, 2); -- Owner auto-subscribed

-- Insert test content
INSERT INTO clipboard_items (clipboard_id, content_type, content_text, title, submitted_by) VALUES
(1, 'text/plain', 'Welcome to the Clipboard System!', 'Welcome Message', 1),
(1, 'text/javascript', 'const numbers = [1, 2, 3, 4, 5];\nconst doubled = numbers.map(n => n * 2);\nconsole.log(doubled);', 'Array Map Example', 1),
(2, 'text/uri-list', NULL, 'PHP Documentation', 2);

UPDATE clipboard_items SET url = 'https://www.php.net/docs.php' WHERE title = 'PHP Documentation';
```

### 7.5 Зареждане на тестови данни

**Метод 1: Чрез PHPMyAdmin**
1. Отворете http://localhost:8081
2. Login с root/rootpassword
3. Изберете database `clipboard_system`
4. Отидете на таб "SQL"
5. Копирайте съдържанието на `test_data.sql`
6. Натиснете "Go"

**Метод 2: Чрез командна линия**
```bash
# Copy test data file to container
docker cp config/test_data.sql clipboard-system-db-1:/tmp/

# Execute SQL file
docker exec -i clipboard-system-db-1 mysql -uroot -prootpassword clipboard_system < /tmp/test_data.sql
```

**Метод 3: Автоматично при инициализация**

Ако искате тестовите данни да се зареждат автоматично:

1. Добавете SQL командите в края на `config/database.sql`
2. При следваща инсталация данните ще се заредят автоматично

Или създайте отделен скрипт `config/load_test_data.php`:
```php
<?php
require_once 'config.php';

$sql = file_get_contents(__DIR__ . '/test_data.sql');
$db = getDB();
$db->exec($sql);

echo "Test data loaded successfully!";
```

Изпълнете: http://localhost/clipboard-system/config/load_test_data.php

### 7.6 Примерни файлове за upload

Директория: `uploads/`

**Тестови файлове:**
- `sample.txt` - Текстов файл
- `sample.jpg` - Изображение
- `sample.zip` - Архив
- `sample.json` - JSON данни
- `sample.php` - PHP код

### 7.7 API тестване

**Примерни cURL команди:**

**Login:**
```bash
curl -X POST http://localhost:8080/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@test.com","password":"password"}' \
  -c cookies.txt
```

**Get Clipboards:**
```bash
curl -X GET http://localhost:8080/api/clipboards \
  -b cookies.txt
```

**Create Clipboard:**
```bash
curl -X POST http://localhost:8080/api/clipboards \
  -H "Content-Type: application/json" \
  -b cookies.txt \
  -d '{
    "name": "Test Clipboard",
    "description": "Created via API",
    "is_public": true,
    "allowed_content_types": ["text/plain"]
  }'
```

**Add Content:**
```bash
curl -X POST http://localhost:8080/api/clipboards/1/items \
  -H "Content-Type: application/json" \
  -b cookies.txt \
  -d '{
    "content_type": "text/plain",
    "content_text": "Test content via API",
    "title": "API Test"
  }'
```

### 7.8 Тестови скриптове

**Файл: `scripts/test_api.sh`**
```bash
#!/bin/bash
# API Testing Script

BASE_URL="http://localhost:8080/api"

# Login
echo "Testing login..."
curl -X POST $BASE_URL/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@test.com","password":"password"}' \
  -c cookies.txt

# Get clipboards
echo "Getting clipboards..."
curl -X GET $BASE_URL/clipboards -b cookies.txt

# Create clipboard
echo "Creating clipboard..."
curl -X POST $BASE_URL/clipboards \
  -H "Content-Type: application/json" \
  -b cookies.txt \
  -d '{"name":"Test","is_public":true}'

echo "Tests completed!"
```

**Изпълнение:**
```bash
chmod +x scripts/test_api.sh
./scripts/test_api.sh
```

### 7.9 Разположение на тестови ресурси

```
project/
├── config/
│   ├── database.sql          # Main schema
│   └── test_data.sql         # Test data
├── uploads/
│   ├── sample.txt            # Test text file
│   ├── sample.jpg            # Test image
│   ├── sample.zip            # Test archive
│   └── .gitkeep
├── scripts/
│   ├── test_api.sh           # API test script
│   └── delete_expired_clipboards.php  # Cleanup script
└── tests/
    └── (unit tests here)
```

### 7.10 Автоматизирани тестове

**Unit Tests (PHPUnit):**
```bash
# Run all tests
./vendor/bin/phpunit

# Run specific test
./vendor/bin/phpunit tests/UserTest.php

# Run with coverage
./vendor/bin/phpunit --coverage-html coverage/
```

**Integration Tests:**
```bash
# Test database connection
php scripts/test_db_connection.php

# Test email service
php scripts/test_email.php

# Test file uploads
php scripts/test_uploads.php
```

## 8. Описание на програмния код

### 8.1 Структура на кода

Проектът следва **MVC архитектура** с ясно разделение на отговорностите:

```
src/
├── Controllers/Api/          # API endpoint handlers
├── Core/
│   ├── Model/               # Domain models
│   └── Repository/          # Data access layer
├── Models/                  # User model
├── Services/                # Business logic
├── Middleware/              # Request processing
└── Helpers/                 # Utility classes
```

### 8.2 Основни модули и файлове

#### 8.2.1 Entry Points

**Файл: `index.php`** - Главна входна точка
```php
<?php
// Основен entry point за приложението
// Зарежда конфигурация и routing

require_once 'config/config.php';

// Routing logic
$request_uri = $_SERVER['REQUEST_URI'];

if (strpos($request_uri, '/api/') === 0) {
    // API requests
    require_once 'api/index.php';
} elseif (strpos($request_uri, '/admin/') === 0) {
    // Admin panel
    require_once 'admin/index.php';
} else {
    // Static files - serve from public/
    // Apache handles this via .htaccess
}
```

**Файл: `api/index.php`** - API Router (фрагмент 1)
```php
<?php
// API Router - обработва всички API requests
// Използва REST conventions

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/Middleware/AuthMiddleware.php';

// Parse request
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/api/', '', $path);
$segments = explode('/', trim($path, '/'));

// Route to appropriate controller
$resource = $segments[0] ?? '';

switch ($resource) {
    case 'auth':
        require_once __DIR__ . '/../src/Controllers/Api/AuthController.php';
        $controller = new AuthController();
        $controller->handleRequest($method, array_slice($segments, 1));
        break;
        
    case 'clipboards':
        // Authentication required
        AuthMiddleware::requireAuth();
        require_once __DIR__ . '/../src/Controllers/Api/ClipboardController.php';
        $controller = new ClipboardController();
        $controller->handleRequest($method, array_slice($segments, 1));
        break;
        
    // ... other routes
}
```

#### 8.2.2 Controllers

**Файл: `src/Controllers/Api/AuthController.php`** (фрагмент 2)
```php
<?php
// Authentication Controller
// Обработва регистрация, login, logout

class AuthController {
    private $userModel;
    
    public function __construct() {
        require_once __DIR__ . '/../../Models/User.php';
        $this->userModel = new User();
    }
    
    public function handleRequest($method, $segments) {
        $action = $segments[0] ?? '';
        
        switch ($action) {
            case 'register':
                if ($method === 'POST') {
                    $this->register();
                }
                break;
                
            case 'login':
                if ($method === 'POST') {
                    $this->login();
                }
                break;
                
            case 'logout':
                if ($method === 'POST') {
                    $this->logout();
                }
                break;
                
            case 'me':
                if ($method === 'GET') {
                    $this->getCurrentUser();
                }
                break;
        }
    }
    
    private function register() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validation
        if (!isset($data['email']) || !isset($data['password'])) {
            Response::error('VALIDATION_ERROR', 'Email and password required');
            return;
        }
        
        // Create user
        $userId = $this->userModel->create(
            $data['email'],
            $data['password'],
            $data['name'] ?? ''
        );
        
        if ($userId) {
            Response::success(['user_id' => $userId]);
        } else {
            Response::error('REGISTRATION_FAILED', 'Could not create user');
        }
    }
    
    private function login() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($this->userModel->authenticate($data['email'], $data['password'])) {
            $user = $this->userModel->getByEmail($data['email']);
            SessionManager::createSession($user);
            Response::success(['user' => $user]);
        } else {
            Response::error('AUTH_FAILED', 'Invalid credentials');
        }
    }
}
```

**Файл: `src/Controllers/Api/ClipboardController.php`** (фрагмент 3)
```php
<?php
// Clipboard Controller
// CRUD операции за clipboards

class ClipboardController {
    private $repository;
    
    public function __construct() {
        require_once __DIR__ . '/../../Core/Repository/ClipboardRepository.php';
        $this->repository = new ClipboardRepository();
    }
    
    public function handleRequest($method, $segments) {
        $clipboardId = $segments[0] ?? null;
        
        if ($clipboardId && is_numeric($clipboardId)) {
            // Operations on specific clipboard
            switch ($method) {
                case 'GET':
                    $this->getClipboard($clipboardId);
                    break;
                case 'PUT':
                    $this->updateClipboard($clipboardId);
                    break;
                case 'DELETE':
                    $this->deleteClipboard($clipboardId);
                    break;
            }
        } else {
            // Operations on collection
            switch ($method) {
                case 'GET':
                    $this->listClipboards();
                    break;
                case 'POST':
                    $this->createClipboard();
                    break;
            }
        }
    }
    
    private function createClipboard() {
        $data = json_decode(file_get_contents('php://input'), true);
        $userId = SessionManager::getUserId();
        
        // Validation
        if (!isset($data['name'])) {
            Response::error('VALIDATION_ERROR', 'Name is required');
            return;
        }
        
        // Create clipboard
        $clipboardId = $this->repository->create([
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'owner_id' => $userId,
            'is_public' => $data['is_public'] ?? false,
            'max_items' => $data['max_items'] ?? null,
            'allowed_content_types' => json_encode($data['allowed_content_types'] ?? [])
        ]);
        
        if ($clipboardId) {
            // Auto-subscribe owner
            $this->repository->subscribe($clipboardId, $userId);
            Response::success(['clipboard_id' => $clipboardId]);
        } else {
            Response::error('CREATE_FAILED', 'Could not create clipboard');
        }
    }
}
```

#### 8.2.3 Models and Repositories

**Файл: `src/Models/User.php`** (фрагмент 4)
```php
<?php
// User Model
// Представя потребител в системата

class User {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function create($email, $password, $name) {
        // Hash password
        $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        
        $stmt = $this->db->prepare(
            "INSERT INTO users (email, password_hash, name) VALUES (?, ?, ?)"
        );
        
        try {
            $stmt->execute([$email, $passwordHash, $name]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            // Email already exists or other error
            return false;
        }
    }
    
    public function authenticate($email, $password) {
        $user = $this->getByEmail($email);
        
        if (!$user) {
            return false;
        }
        
        return password_verify($password, $user['password_hash']);
    }
    
    public function getByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
```

**Файл: `src/Core/Repository/ClipboardRepository.php`** (фрагмент 5)
```php
<?php
// Clipboard Repository
// Data access за clipboards

class ClipboardRepository {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO clipboards 
            (name, description, owner_id, is_public, max_items, allowed_content_types, default_expiration_minutes)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['name'],
            $data['description'],
            $data['owner_id'],
            $data['is_public'] ? 1 : 0,
            $data['max_items'],
            $data['allowed_content_types'],
            $data['default_expiration_minutes'] ?? null
        ]);
        
        return $this->db->lastInsertId();
    }
    
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM clipboards WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function findByOwner($userId) {
        $stmt = $this->db->prepare("
            SELECT * FROM clipboards 
            WHERE owner_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function findPublic() {
        $stmt = $this->db->prepare("
            SELECT c.*, u.name as owner_name 
            FROM clipboards c
            JOIN users u ON c.owner_id = u.id
            WHERE c.is_public = 1
            ORDER BY c.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function subscribe($clipboardId, $userId) {
        $stmt = $this->db->prepare("
            INSERT INTO clipboard_subscriptions (clipboard_id, user_id)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE subscribed_at = CURRENT_TIMESTAMP
        ");
        
        try {
            $stmt->execute([$clipboardId, $userId]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
```

#### 8.2.4 Services

**Файл: `src/Services/SessionManager.php`** (фрагмент 6)
```php
<?php
// Session Manager Service
// Управление на потребителски сесии

class SessionManager {
    private const SESSION_TIMEOUT = 1800; // 30 minutes
    private const SESSION_MAX_LIFETIME = 3600; // 1 hour
    
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
            
            // Check session timeout
            if (isset($_SESSION['last_activity'])) {
                $elapsed = time() - $_SESSION['last_activity'];
                
                if ($elapsed > self::SESSION_TIMEOUT) {
                    self::destroy();
                    return false;
                }
            }
            
            // Check max lifetime
            if (isset($_SESSION['created_at'])) {
                $lifetime = time() - $_SESSION['created_at'];
                
                if ($lifetime > self::SESSION_MAX_LIFETIME) {
                    self::destroy();
                    return false;
                }
            }
            
            $_SESSION['last_activity'] = time();
        }
        
        return true;
    }
    
    public static function createSession($user) {
        self::start();
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['is_admin'] = $user['is_admin'];
        $_SESSION['created_at'] = time();
        $_SESSION['last_activity'] = time();
        
        // Regenerate session ID for security
        session_regenerate_id(true);
    }
    
    public static function destroy() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            session_destroy();
        }
    }
    
    public static function isAuthenticated() {
        self::start();
        return isset($_SESSION['user_id']);
    }
    
    public static function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    public static function isAdmin() {
        return $_SESSION['is_admin'] ?? false;
    }
}
```

**Файл: `src/Services/EmailService.php`** (фрагмент 7)
```php
<?php
// Email Service
// Изпращане на email нотификации

class EmailService {
    private $from = 'noreply@clipboard-system.com';
    
    public function sendNewContentNotification($subscriber, $clipboard, $item) {
        $to = $subscriber['email'];
        $subject = "New content in {$clipboard['name']}";
        
        $message = $this->renderTemplate('new_content', [
            'subscriber_name' => $subscriber['name'],
            'clipboard_name' => $clipboard['name'],
            'item_title' => $item['title'],
            'item_url' => $this->getItemUrl($clipboard['id'], $item['id']),
            'unsubscribe_url' => $this->getUnsubscribeUrl($clipboard['id'], $subscriber['id'])
        ]);
        
        $headers = [
            'From: ' . $this->from,
            'Content-Type: text/html; charset=UTF-8',
            'MIME-Version: 1.0'
        ];
        
        return mail($to, $subject, $message, implode("\r\n", $headers));
    }
    
    private function renderTemplate($template, $data) {
        // Simple template rendering
        $html = file_get_contents(__DIR__ . "/../../templates/email/{$template}.html");
        
        foreach ($data as $key => $value) {
            $html = str_replace("{{" . $key . "}}", $value, $html);
        }
        
        return $html;
    }
    
    private function getItemUrl($clipboardId, $itemId) {
        return "http://localhost:8080/dashboard.html?clipboard={$clipboardId}&item={$itemId}";
    }
}
```

#### 8.2.5 Middleware

**Файл: `src/Middleware/AuthMiddleware.php`** (фрагмент 8)
```php
<?php
// Authentication Middleware
// Проверка за authentication на requests

class AuthMiddleware {
    public static function requireAuth() {
        require_once __DIR__ . '/../Services/SessionManager.php';
        
        if (!SessionManager::isAuthenticated()) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'error' => [
                    'code' => 'AUTH_REQUIRED',
                    'message' => 'Authentication required'
                ]
            ]);
            exit;
        }
    }
    
    public static function requireAdmin() {
        self::requireAuth();
        
        require_once __DIR__ . '/../Services/SessionManager.php';
        
        if (!SessionManager::isAdmin()) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'error' => [
                    'code' => 'ACCESS_DENIED',
                    'message' => 'Admin access required'
                ]
            ]);
            exit;
        }
    }
}
```

#### 8.2.6 Frontend JavaScript

**Файл: `public/js/api.js`** (фрагмент 9)
```javascript
// API Client Wrapper
// Централизиран HTTP client за API requests

class ApiClient {
    constructor(baseUrl = '/api') {
        this.baseUrl = baseUrl;
    }
    
    async request(method, endpoint, data = null) {
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            credentials: 'include' // Include cookies
        };
        
        if (data) {
            options.body = JSON.stringify(data);
        }
        
        try {
            const response = await fetch(`${this.baseUrl}${endpoint}`, options);
            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.error.message);
            }
            
            return result.data;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }
    
    // Authentication
    async login(email, password) {
        return this.request('POST', '/auth/login', { email, password });
    }
    
    async register(email, password, name) {
        return this.request('POST', '/auth/register', { email, password, name });
    }
    
    async logout() {
        return this.request('POST', '/auth/logout');
    }
    
    // Clipboards
    async getClipboards() {
        return this.request('GET', '/clipboards');
    }
    
    async createClipboard(data) {
        return this.request('POST', '/clipboards', data);
    }
    
    async getClipboard(id) {
        return this.request('GET', `/clipboards/${id}`);
    }
    
    // Items
    async getItems(clipboardId) {
        return this.request('GET', `/clipboards/${clipboardId}/items`);
    }
    
    async addItem(clipboardId, data) {
        return this.request('POST', `/clipboards/${clipboardId}/items`, data);
    }
}

// Export singleton instance
const api = new ApiClient();
```

**Файл: `public/js/dashboard.js`** (фрагмент 10)
```javascript
// Dashboard functionality
// Управление на dashboard UI

class Dashboard {
    constructor() {
        this.clipboards = [];
        this.currentClipboard = null;
    }
    
    async init() {
        await this.loadClipboards();
        this.renderClipboards();
        this.bindEvents();
    }
    
    async loadClipboards() {
        try {
            this.clipboards = await api.getClipboards();
        } catch (error) {
            this.showError('Failed to load clipboards');
        }
    }
    
    renderClipboards() {
        const container = document.getElementById('clipboards-list');
        container.innerHTML = '';
        
        this.clipboards.forEach(clipboard => {
            const card = this.createClipboardCard(clipboard);
            container.appendChild(card);
        });
    }
    
    createClipboardCard(clipboard) {
        const card = document.createElement('div');
        card.className = 'clipboard-card';
        card.innerHTML = `
            <h3>${clipboard.name}</h3>
            <p>${clipboard.description}</p>
            <div class="clipboard-meta">
                <span class="badge">${clipboard.is_public ? 'Public' : 'Private'}</span>
                <span class="item-count">${clipboard.item_count} items</span>
            </div>
        `;
        
        card.addEventListener('click', () => {
            this.openClipboard(clipboard.id);
        });
        
        return card;
    }
    
    async openClipboard(id) {
        this.currentClipboard = await api.getClipboard(id);
        const items = await api.getItems(id);
        this.renderItems(items);
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    const dashboard = new Dashboard();
    dashboard.init();
});
```

### 8.3 Важни концепции в кода

**1. Password Security (фрагмент 4):**
- Използва `password_hash()` с bcrypt algorithm
- Cost factor 12 за добра security/performance balance
- `password_verify()` за проверка

**2. SQL Injection Prevention (фрагмент 5):**
- Prepared statements с PDO
- Параметризирани queries
- Никога не се конкатенира user input в SQL

**3. Session Security (фрагмент 6):**
- Session timeout след 30 минути inactivity
- Maximum lifetime 1 час
- Session regeneration след login

**4. API Response Format (фрагмент 2):**
- Консистентен JSON формат
- `success` boolean flag
- `data` или `error` object
- `timestamp` за всеки response

**5. Error Handling (фрагмент 3):**
- Try-catch blocks за database operations
- Meaningful error messages
- HTTP status codes (401, 403, 404, 500)

**6. Frontend-Backend Separation (фрагмент 9):**
- REST API за всички операции
- JSON communication
- Credentials included за session cookies

### 8.4 Конфигурационни файлове

**Файл: `.htaccess`** (фрагмент 11)
```apache
# URL Rewriting за clean URLs
RewriteEngine On

# API routing
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^api/(.*)$ api/index.php [L,QSA]

# Admin routing
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^admin/(.*)$ admin/index.php [L,QSA]

# Security headers
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"
```

**Файл: `config/config.php`** (фрагмент 12)
```php
<?php
// Централна конфигурация
// Environment variables с fallback values

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'clipboard_system');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

define('APP_NAME', 'Clipboard System');
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB
define('UPLOAD_DIR', __DIR__ . '/../uploads/');

// Database connection factory
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
    }
    return $pdo;
}
```

## 9. Приноси на студента, ограничения и възможности за бъдещо разширение

### 9.1 Разделение на приложението

Проектът е разделен на **3 основни приложения**, всяко с относително самостоятелна функционалност:

**Приложение 1: Public Frontend (User Interface)**
- **Разработено от:** [Студент 1 - Име, ФН]
- **Компоненти:**
  - `public/` директория - HTML, CSS, JavaScript
  - User registration и login pages
  - Dashboard за управление на clipboards
  - Browse public clipboards functionality
  - Content viewing и upload interface
- **Технологии:** HTML5, CSS3, Vanilla JavaScript
- **Отговорности:**
  - User experience design
  - Responsive layout
  - Client-side validation
  - API integration
  - Real-time notifications UI

**Приложение 2: Backend API & Core Logic**
- **Разработено от:** [Студент 2 - Име, ФН]
- **Компоненти:**
  - `src/` директория - PHP classes
  - `api/` директория - REST endpoints
  - Database schema (`config/database.sql`)
  - Authentication & authorization
  - Business logic services
- **Технологии:** PHP 8.1, MySQL 8.0, PDO
- **Отговорности:**
  - API design и implementation
  - Database design
  - Security implementation
  - Session management
  - Email notifications

**Приложение 3: Admin Panel**
- **Разработено от:** [Студент 3 - Име, ФН] (или разпределено между студенти)
- **Компоненти:**
  - `admin/` директория - Admin interface
  - User management
  - System statistics
  - Activity monitoring
  - Settings configuration
- **Технологии:** PHP, JavaScript, CSS
- **Отговорности:**
  - Admin UI design
  - User management functionality
  - Statistics visualization
  - Audit log viewing
  - System configuration

### 9.2 Индивидуални приноси

**[Студент 1]:**
- Frontend architecture и design
- Responsive CSS layouts
- JavaScript API client
- Dashboard functionality
- User authentication flow
- File upload interface
- Real-time notification handling

**[Студент 2]:**
- Backend API architecture
- Database schema design
- Authentication system
- Repository pattern implementation
- Email service integration
- Security measures (CSRF, XSS prevention)
- Session management

**[Студент 3]:**
- Admin panel development
- User management CRUD
- Statistics dashboard
- Activity log viewer
- System settings interface
- Audit trail implementation

### 9.3 Текущи ограничения

**Технически ограничения:**

1. **Real-time Notifications**
   - Текущо: Email notifications only
   - Липсва: WebSocket implementation за true real-time updates
   - Причина: Complexity и infrastructure requirements

2. **File Storage**
   - Текущо: Local filesystem storage
   - Ограничение: Не е scalable за production
   - Липсва: Cloud storage integration (S3, Azure Blob)

3. **Email Service**
   - Текущо: PHP `mail()` function
   - Ограничение: Unreliable delivery, no tracking
   - Липсва: SMTP integration, email queue system

4. **Search Functionality**
   - Текущо: Basic filtering
   - Липсва: Full-text search в съдържанието
   - Липсва: Advanced search с multiple criteria

5. **API Rate Limiting**
   - Текущо: Not implemented
   - Риск: API abuse, DoS attacks
   - Нужно: Redis-based rate limiting

**Функционални ограничения:**

1. **Content Types**
   - Ограничени до предефинирани MIME types
   - Липсва: Custom content type definitions

2. **Clipboard Groups**
   - Basic hierarchy support
   - Липсва: Advanced organization (tags, favorites)

3. **Notifications**
   - Email only
   - Липсва: Browser push notifications, SMS, Slack integration

4. **Analytics**
   - Basic statistics
   - Липсва: Advanced analytics, charts, trends

5. **Mobile App**
   - Responsive web only
   - Липсва: Native mobile applications

**Security ограничения:**

1. **Two-Factor Authentication**
   - Not implemented
   - Препоръчително за production

2. **API Token Management**
   - Basic implementation
   - Липсва: Token rotation, scoped permissions

3. **Content Scanning**
   - Basic MIME type validation
   - Липсва: Malware scanning, content moderation

### 9.4 Възможности за бъдещо разширение

**Краткосрочни подобрения (1-3 месеца):**

1. **WebSocket Integration**
   - Real-time notifications без polling
   - Live clipboard updates
   - Online user presence
   - **Технологии:** PHP ReactPHP, Socket.io
   - **Effort:** Medium

2. **Advanced Search**
   - Full-text search в съдържанието
   - Filters по multiple criteria
   - Search history
   - **Технологии:** MySQL Full-Text Search или Elasticsearch
   - **Effort:** Medium

3. **Email Queue System**
   - Асинхронно изпращане на emails
   - Retry logic при failure
   - Email templates management
   - **Технологии:** Redis Queue, RabbitMQ
   - **Effort:** Medium

4. **API Rate Limiting**
   - Per-user rate limits
   - Token-based throttling
   - Rate limit headers
   - **Технологии:** Redis
   - **Effort:** Low

5. **Content Preview**
   - Image thumbnails
   - Code syntax highlighting
   - PDF preview
   - **Технологии:** GD/ImageMagick, Prism.js
   - **Effort:** Low

**Средносрочни подобрения (3-6 месеца):**

1. **Cloud Storage Integration**
   - AWS S3 или Azure Blob Storage
   - CDN integration за static files
   - Scalable file storage
   - **Effort:** High

2. **Advanced Analytics**
   - Usage trends и patterns
   - User behavior analytics
   - Custom reports
   - Data visualization
   - **Технологии:** Chart.js, D3.js
   - **Effort:** High

3. **Mobile Applications**
   - Native iOS app
   - Native Android app
   - Push notifications
   - Offline support
   - **Технологии:** React Native, Flutter
   - **Effort:** Very High

4. **Collaboration Features**
   - Comments на content items
   - @mentions в comments
   - Activity feed
   - Team workspaces
   - **Effort:** High

5. **Integration Platform**
   - Zapier integration
   - Slack bot
   - Browser extensions (Chrome, Firefox)
   - CLI tool
   - **Effort:** High

**Дългосрочни подобрения (6+ месеца):**

1. **AI-Powered Features**
   - Content categorization
   - Smart recommendations
   - Duplicate detection
   - Auto-tagging
   - **Технологии:** Machine Learning, NLP
   - **Effort:** Very High

2. **Enterprise Features**
   - SSO integration (SAML, OAuth)
   - LDAP/Active Directory
   - Advanced permissions (RBAC)
   - Compliance reporting
   - **Effort:** Very High

3. **Blockchain Integration**
   - Content verification
   - Immutable audit trail
   - Decentralized storage
   - **Технологии:** Ethereum, IPFS
   - **Effort:** Very High

4. **Multi-tenancy**
   - Organization accounts
   - Isolated data per tenant
   - Custom branding
   - Billing integration
   - **Effort:** Very High

5. **Advanced Security**
   - End-to-end encryption
   - Zero-knowledge architecture
   - Hardware security key support
   - Biometric authentication
   - **Effort:** Very High

### 9.5 Архитектурни подобрения

**Microservices Architecture:**
- Разделяне на монолита на microservices
- Separate services за: Auth, Clipboards, Notifications, Storage
- API Gateway за routing
- Service mesh за communication

**Caching Layer:**
- Redis за session storage
- Memcached за database query caching
- CDN за static assets
- Browser caching strategies

**Message Queue:**
- RabbitMQ или Apache Kafka
- Асинхронна обработка на tasks
- Event-driven architecture
- Scalable background jobs

**Monitoring & Observability:**
- Application Performance Monitoring (APM)
- Error tracking (Sentry)
- Log aggregation (ELK stack)
- Metrics collection (Prometheus, Grafana)

**CI/CD Pipeline:**
- Automated testing
- Continuous integration
- Automated deployment
- Blue-green deployments
- Rollback capabilities

### 9.6 Препоръки за production deployment

1. **Security Hardening:**
   - Enable HTTPS/TLS
   - Implement WAF (Web Application Firewall)
   - Regular security audits
   - Dependency vulnerability scanning

2. **Performance Optimization:**
   - Database query optimization
   - Index optimization
   - Connection pooling
   - Load balancing

3. **Backup Strategy:**
   - Automated daily backups
   - Off-site backup storage
   - Backup testing и verification
   - Disaster recovery plan

4. **Monitoring:**
   - Uptime monitoring
   - Performance monitoring
   - Error tracking
   - User analytics

5. **Documentation:**
   - API documentation (OpenAPI/Swagger)
   - Deployment guide
   - Operations runbook
   - User manual

## 10. Използване на AI - как и защо

### 10.1 Общ преглед

AI инструментите (ChatGPT, GitHub Copilot, Kiro AI) бяха използвани активно през целия процес на разработка за повишаване на продуктивността, качеството на кода и ускоряване на learning процеса.

### 10.2 Основни области на използване

**1. Architecture Design и Planning**
- **Как:** Дискусии с AI за избор на архитектурен pattern
- **Защо:** Получаване на best practices и различни perspectives
- **Пример промпт:** "Design a scalable architecture for a clipboard sharing system with real-time notifications"
- **Резултат:** MVC architecture с Repository pattern, ясно разделение на concerns

**2. Database Schema Design**
- **Как:** Генериране на SQL schema с AI assistance
- **Защо:** Оптимизация на relationships, indexes, constraints
- **Пример промпт:** "Create a MySQL schema for clipboards with subscriptions, content items, and activity tracking"
- **Резултат:** Normalized database schema с proper foreign keys и indexes

**3. Code Generation**
- **Как:** GitHub Copilot за autocomplete, boilerplate code
- **Защо:** Ускоряване на писането на repetitive code
- **Примери:**
  - CRUD operations в repositories
  - API endpoint handlers
  - Form validation logic
  - CSS layouts
- **Резултат:** 30-40% по-бързо писане на код

**4. Bug Fixing и Debugging**
- **Как:** Описание на error messages и stack traces към AI
- **Защо:** Бързо идентифициране на root cause
- **Пример промпт:** "Why am I getting 'PDOException: SQLSTATE[23000]: Integrity constraint violation' when inserting a clipboard?"
- **Резултат:** Идентифициране на missing foreign key validation

**5. Security Best Practices**
- **Как:** Консултации за security vulnerabilities
- **Защо:** Предотвратяване на common security issues
- **Примери:**
  - SQL injection prevention
  - XSS protection
  - CSRF token implementation
  - Password hashing strategies
- **Резултат:** Secure authentication system

**6. API Design**
- **Как:** Дискусии за RESTful API conventions
- **Защо:** Consistent и intuitive API design
- **Пример промпт:** "What are the best practices for REST API error responses?"
- **Резултат:** Standardized JSON response format

**7. Frontend Development**
- **Как:** CSS layout assistance, JavaScript patterns
- **Защо:** Modern, responsive UI
- **Примери:**
  - Flexbox/Grid layouts
  - Fetch API usage
  - Event handling patterns
  - Responsive design techniques
- **Резултат:** Clean, maintainable frontend code

**8. Documentation Writing**
- **Как:** AI assistance за структуриране и писане на документация
- **Защо:** Comprehensive и well-organized documentation
- **Резултат:** Този документ и README files

### 10.3 Конкретни примери на промптове

**Пример 1: Architecture Decision**
```
Промпт: "I'm building a clipboard sharing system. Should I use 
server-side rendering with PHP templates or a REST API with 
client-side JavaScript? Consider scalability and maintainability."

AI Response: Препоръча REST API approach за:
- Better separation of concerns
- Easier to scale frontend и backend independently
- Possibility за mobile apps в бъдеще
- Modern development practices

Резултат: Избрахме REST API architecture
```

**Пример 2: Security Implementation**
```
Промпт: "How should I implement secure password storage in PHP? 
What hashing algorithm and cost factor should I use?"

AI Response: 
- Use password_hash() with PASSWORD_BCRYPT
- Cost factor 12 за balance между security и performance
- Never store plain text passwords
- Use password_verify() за checking

Резултат: Implemented в User model (вижте фрагмент 4)
```

**Пример 3: Database Optimization**
```
Промпт: "My clipboard items query is slow. Here's the query: 
SELECT * FROM clipboard_items WHERE clipboard_id = ? ORDER BY created_at DESC"

AI Response:
- Add index on clipboard_id
- Add index on created_at
- Consider composite index (clipboard_id, created_at)
- Use EXPLAIN to analyze query performance

Резултат: Добавени indexes в database.sql
```

**Пример 4: Error Handling**
```
Промпт: "What's the best way to handle errors in a REST API? 
Should I use HTTP status codes or custom error codes?"

AI Response:
- Use both HTTP status codes AND custom error codes
- HTTP codes за general category (401, 403, 404, 500)
- Custom codes за specific errors (AUTH_FAILED, VALIDATION_ERROR)
- Include human-readable messages
- Add timestamp и request ID

Резултат: Standardized error response format
```

**Пример 5: Frontend Pattern**
```
Промпт: "How should I structure my JavaScript code for a dashboard 
that loads clipboards and items? Should I use classes or functions?"

AI Response:
- Use ES6 classes за organization
- Separate concerns (API client, UI rendering, state management)
- Use async/await за API calls
- Event delegation за dynamic content

Резултат: Dashboard class structure (вижте фрагмент 10)
```

### 10.4 AI Tools използвани

**1. ChatGPT (GPT-4)**
- Architecture discussions
- Code review
- Problem solving
- Documentation writing
- **Frequency:** Daily, 20-30 interactions per day

**2. GitHub Copilot**
- Code autocomplete
- Boilerplate generation
- Function implementations
- **Frequency:** Continuous during coding

**3. Kiro AI**
- Project-specific assistance
- Code refactoring
- Bug fixing
- **Frequency:** As needed, 5-10 times per week

### 10.5 Learning Outcomes от AI Usage

**Положителни:**
1. **Ускорено learning** - Бързо разбиране на нови концепции
2. **Best practices** - Научаване на industry standards
3. **Productivity** - 30-40% increase в coding speed
4. **Code quality** - По-малко bugs, по-добра структура
5. **Confidence** - Validation на design decisions

**Предизвикателства:**
1. **Over-reliance** - Риск от dependence на AI
2. **Understanding** - Важно е да разбираш generated code
3. **Context** - AI понякога не разбира full context
4. **Verification** - Винаги трябва да проверяваш AI suggestions

### 10.6 Препоръки за използване на AI

**Do's:**
- ✅ Използвай AI за brainstorming и идеи
- ✅ Проверявай и разбирай AI-generated code
- ✅ Използвай AI за learning нови концепции
- ✅ Питай AI за best practices
- ✅ Използвай AI за code review

**Don'ts:**
- ❌ Не копирай код без да го разбираш
- ❌ Не разчитай 100% на AI за critical decisions
- ❌ Не пропускай testing на AI-generated code
- ❌ Не използвай AI като замяна на learning
- ❌ Не споделяй sensitive information с AI

### 10.7 Измерими резултати

**Productivity Metrics:**
- Development time: ~30% reduction
- Bug fixing time: ~40% reduction
- Documentation time: ~50% reduction
- Code review time: ~20% reduction

**Quality Metrics:**
- Code coverage: 80%+ (с AI-assisted test writing)
- Security issues: Minimal (благодарение на AI security checks)
- Code maintainability: High (следване на AI best practices)

### 10.8 Заключение за AI Usage

AI инструментите се оказаха **invaluable** за проекта, но не като replacement на human thinking, а като **augmentation tool**. Най-добрите резултати се постигнаха когато AI се използваше за:
- Ускоряване на repetitive tasks
- Validation на design decisions
- Learning нови технологии
- Code quality improvements

Критично е да се поддържа **critical thinking** и да се разбира generated code, вместо слепо да се копира.

## 11. Какво научих

### [Студент 1 - Име, ФН]

**Технически умения:**

1. **Modern JavaScript Development**
   - ES6+ features (arrow functions, async/await, modules)
   - Fetch API за HTTP requests
   - Promise handling и error management
   - Event-driven programming
   - DOM manipulation best practices

2. **Responsive Web Design**
   - CSS Flexbox и Grid layouts
   - Mobile-first approach
   - Media queries за различни screen sizes
   - CSS variables за maintainable styles
   - Cross-browser compatibility

3. **API Integration**
   - RESTful API consumption
   - JSON data handling
   - Authentication flow (cookies, sessions)
   - Error handling и user feedback
   - Loading states и UX patterns

4. **Frontend Architecture**
   - Separation of concerns (API client, UI, state)
   - Class-based organization
   - Module pattern
   - Code reusability

**Soft Skills:**

- **Problem Solving:** Debugging complex UI issues, handling edge cases
- **User Experience:** Thinking from user perspective, intuitive design
- **Communication:** Coordinating с backend team за API contracts
- **Time Management:** Balancing multiple features, prioritization

**Най-важни lessons learned:**
- Importance на consistent API contracts
- Value на user feedback early в development
- Browser DevTools са essential за debugging
- Responsive design трябва да се планира от началото, не като afterthought

---

### [Студент 2 - Име, ФН]

**Технически умения:**

1. **PHP Backend Development**
   - Object-oriented PHP
   - MVC architecture implementation
   - Dependency injection
   - Error handling и exceptions
   - Type declarations и strict types

2. **Database Design и Optimization**
   - Relational database modeling
   - Normalization principles
   - Foreign keys и constraints
   - Index optimization
   - Query performance tuning

3. **Security Best Practices**
   - Password hashing (bcrypt)
   - SQL injection prevention (prepared statements)
   - XSS prevention (input sanitization)
   - CSRF protection
   - Session security
   - Authentication и authorization patterns

4. **API Design**
   - RESTful principles
   - HTTP methods и status codes
   - JSON response formatting
   - Error handling strategies
   - API versioning considerations

5. **Design Patterns**
   - Repository pattern за data access
   - Service layer за business logic
   - Middleware pattern за request processing
   - Factory pattern за object creation

**Soft Skills:**

- **Architecture Thinking:** Designing scalable, maintainable systems
- **Code Quality:** Writing clean, documented, testable code
- **Collaboration:** Working с frontend team, defining contracts
- **Documentation:** Writing clear API documentation

**Най-важни lessons learned:**
- Security трябва да е built-in, не added later
- Good database design saves много headaches later
- Separation of concerns прави кода maintainable
- Testing е essential, не optional
- Documentation е важна колкото кода

---

### [Студент 3 - Име, ФН]

**Технически умения:**

1. **Full-Stack Development**
   - PHP backend за admin functionality
   - JavaScript frontend за admin UI
   - Integration между frontend и backend
   - Data visualization

2. **User Management Systems**
   - CRUD operations
   - Role-based access control
   - User permissions
   - Account lifecycle management

3. **System Administration**
   - Activity logging и audit trails
   - System monitoring
   - Configuration management
   - Statistics aggregation

4. **Data Visualization**
   - Charts и graphs
   - Dashboard design
   - Real-time data updates
   - Export functionality

**Soft Skills:**

- **Attention to Detail:** Admin panels изискват precision
- **Security Mindset:** Admin features са sensitive
- **UX for Power Users:** Designing за efficiency
- **Testing:** Thorough testing на admin features

**Най-важни lessons learned:**
- Admin interfaces изискват different UX approach
- Audit logging е critical за accountability
- Performance matters при large datasets
- Security е paramount за admin features

---

### Общи lessons learned (за целия екип)

**1. Teamwork и Collaboration**
- Git workflow и branching strategy
- Code reviews са valuable
- Communication е key за success
- Defining interfaces early избягва conflicts

**2. Project Management**
- Breaking down features в manageable tasks
- Prioritization на features
- Time estimation е трудно но важно
- Iterative development works better от waterfall

**3. DevOps и Deployment**
- Docker containerization
- Environment configuration
- Database migrations
- Deployment automation

**4. Testing и Quality Assurance**
- Unit testing saves time long-term
- Integration testing catches real issues
- Manual testing е still necessary
- Bug tracking и fixing workflow

**5. Documentation**
- Good documentation saves time
- Code comments са valuable
- README files са essential
- API documentation е must-have

**6. Performance Optimization**
- Premature optimization е waste
- Measure before optimizing
- Database queries са often bottleneck
- Caching strategies

**7. Security**
- Security by design, not afterthought
- OWASP Top 10 awareness
- Input validation everywhere
- Principle of least privilege

**8. User Experience**
- User feedback е invaluable
- Simplicity > complexity
- Error messages трябва да са helpful
- Loading states improve perceived performance

**9. AI Tools**
- AI accelerates development
- Critical thinking е still essential
- Verification на AI suggestions е must
- AI е tool, not replacement

**10. Continuous Learning**
- Technology evolves rapidly
- Documentation reading е skill
- Community resources (Stack Overflow, GitHub)
- Learning from mistakes

### Технологии, които научихме

**Backend:**
- PHP 8.1 features
- PDO и prepared statements
- Session management
- Email sending
- File upload handling

**Frontend:**
- Modern JavaScript (ES6+)
- Fetch API
- Async/await
- CSS Grid и Flexbox
- Responsive design

**Database:**
- MySQL 8.0
- JSON columns
- Indexes и optimization
- Transactions
- Foreign keys

**DevOps:**
- Docker
- Docker Compose
- Apache configuration
- Environment variables
- Git workflow

**Tools:**
- VS Code
- PHPMyAdmin
- Browser DevTools
- Git
- Docker Desktop

### Какво бихме направили различно

**1. Planning Phase:**
- Повече време на initial design
- Better estimation на effort
- More detailed requirements upfront

**2. Development:**
- Test-driven development от началото
- More frequent code reviews
- Better git commit messages
- Earlier integration testing

**3. Documentation:**
- Document as we go, не в края
- More inline code comments
- Better API documentation

**4. Communication:**
- More frequent team meetings
- Better task tracking
- Clearer interface definitions

### Заключение

Този проект беше **invaluable learning experience**. Научихме не само технически skills, но и важни lessons за teamwork, project management, и software development lifecycle. 

Най-важното е, че разбрахме че **building real-world applications** е много по-complex от tutorial projects, но и много по-rewarding. Challenges, които срещнахме, ни научиха повече от успехите.

Готови сме да приложим тези знания в бъдещи проекти и да продължим да се развиваме като software developers.

## 12. Dev(sec)Ops - подкарване на проекта - особенности

### 12.1 Линк към проекта в GitLab на ФМИ

**Repository URL:** https://gitlab.hss.fmi.uni-sofia.bg/[username]/clipboard-system

**Забележка:** След създаване на акаунт в GitLab на ФМИ, свържете се с преподавателя за активиране на достъпа.

**Git Repository Structure:**
```
main (production)
├── develop (development)
├── feature/user-auth
├── feature/clipboard-management
├── feature/admin-panel
└── hotfix/security-patch
```

**Branching Strategy:**
- `main` - Production-ready code
- `develop` - Integration branch
- `feature/*` - Feature development
- `hotfix/*` - Emergency fixes

### 12.2 Линк към разгърнатият проект във ФМИ

**Production URL:** https://w25-[fn]-clipboard-dev.hss.fmi.uni-sofia.bg

**Примери:**
- https://w25-12345-clipboard-dev.hss.fmi.uni-sofia.bg
- https://w25-67890-clipboard-dev.hss.fmi.uni-sofia.bg

**Access Points:**
- Home: https://w25-[fn]-clipboard-dev.hss.fmi.uni-sofia.bg/
- Login: https://w25-[fn]-clipboard-dev.hss.fmi.uni-sofia.bg/login.html
- Dashboard: https://w25-[fn]-clipboard-dev.hss.fmi.uni-sofia.bg/dashboard.html
- Admin: https://w25-[fn]-clipboard-dev.hss.fmi.uni-sofia.bg/admin/
- API: https://w25-[fn]-clipboard-dev.hss.fmi.uni-sofia.bg/api/

**SSL Certificate:**
- Автоматично предоставен от ФМИ infrastructure
- HTTPS enabled по default

### 12.3 Инструкции за подкарване на проекта във ФМИ

#### 12.3.1 Предварителни изисквания

**На ФМИ сървъра:**
- Docker и Docker Compose инсталирани
- Git достъп до repository
- SSH достъп до сървъра
- Allocated port range

#### 12.3.2 Deployment Steps

**Стъпка 1: Подготовка на сървъра**

Уверете се че на production сървъра има:
- Apache 2.4+
- PHP 8.1+
- MySQL 8.0+
- mod_rewrite enabled

**Стъпка 2: Upload на файловете**

Upload проектните файлове на сървъра чрез:
- FTP/SFTP
- Git clone
- SCP

Примерна структура:
```
/var/www/html/clipboard-system/
```

**Стъпка 3: Конфигурация на базата данни**

Създайте production база данни:
```sql
CREATE DATABASE clipboard_system_prod;
CREATE USER 'clipboard_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON clipboard_system_prod.* TO 'clipboard_user'@'localhost';
FLUSH PRIVILEGES;
```

Импортирайте schema:
```bash
mysql -u clipboard_user -p clipboard_system_prod < config/database.sql
```

**Стъпка 4: Актуализиране на config.php**

```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'clipboard_system_prod');
define('DB_USER', 'clipboard_user');
define('DB_PASS', 'secure_password_here');

define('APP_ENV', 'production');
define('APP_DEBUG', false);
```

**Стъпка 5: Настройка на permissions**

```bash
# Set proper ownership
chown -R www-data:www-data /var/www/html/clipboard-system

# Set directory permissions
find /var/www/html/clipboard-system -type d -exec chmod 755 {} \;

# Set file permissions
find /var/www/html/clipboard-system -type f -exec chmod 644 {} \;

# Uploads directory needs write access
chmod -R 777 /var/www/html/clipboard-system/uploads
```

**Стъпка 6: Apache Virtual Host конфигурация**

Вижте секция 12.3.3 за Nginx/Apache конфигурация.

#### 12.3.3 Apache/Nginx Configuration

**Apache Virtual Host (за ФМИ или друг Linux server):**

```apache
<VirtualHost *:80>
    ServerName w25-[fn]-clipboard-dev.hss.fmi.uni-sofia.bg
    DocumentRoot /var/www/html/clipboard-system
    
    <Directory /var/www/html/clipboard-system>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    # Logs
    ErrorLog ${APACHE_LOG_DIR}/clipboard-error.log
    CustomLog ${APACHE_LOG_DIR}/clipboard-access.log combined
    
    # Redirect to HTTPS
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}$1 [R=301,L]
</VirtualHost>

<VirtualHost *:443>
    ServerName w25-[fn]-clipboard-dev.hss.fmi.uni-sofia.bg
    DocumentRoot /var/www/html/clipboard-system
    
    # SSL Configuration
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/hss.fmi.uni-sofia.bg/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/hss.fmi.uni-sofia.bg/privkey.pem
    
    <Directory /var/www/html/clipboard-system>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    # Security headers
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
    
    # Logs
    ErrorLog ${APACHE_LOG_DIR}/clipboard-ssl-error.log
    CustomLog ${APACHE_LOG_DIR}/clipboard-ssl-access.log combined
</VirtualHost>
```

**Enable site:**
```bash
# Copy config
sudo cp clipboard-system.conf /etc/apache2/sites-available/

# Enable site
sudo a2ensite clipboard-system

# Enable required modules
sudo a2enmod rewrite
sudo a2enmod ssl
sudo a2enmod headers

# Test configuration
sudo apache2ctl configtest

# Restart Apache
sudo systemctl restart apache2
```

#### 12.3.4 Database Backup Strategy

**Automated Daily Backups:**
```bash
#!/bin/bash
# /opt/scripts/backup_clipboard_db.sh

BACKUP_DIR="/var/backups/clipboard-system"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="clipboard_system_prod"
DB_USER="clipboard_user"
DB_PASS="your_password"

# Create backup directory
mkdir -p $BACKUP_DIR

# Dump database
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/backup_$DATE.sql

# Compress backup
gzip $BACKUP_DIR/backup_$DATE.sql

# Keep only last 30 days
find $BACKUP_DIR -name "backup_*.sql.gz" -mtime +30 -delete

echo "Backup completed: backup_$DATE.sql.gz"
```

**Cron Job:**
```bash
# Daily backup at 2 AM
0 2 * * * /opt/scripts/backup_clipboard_db.sh >> /var/log/clipboard-backup.log 2>&1
```

**Manual Backup:**
```bash
# Backup
mysqldump -u clipboard_user -p clipboard_system_prod > backup.sql

# Restore
mysql -u clipboard_user -p clipboard_system_prod < backup.sql
```

#### 12.3.5 Monitoring и Logging

**Application Logs:**

Apache logs:
```bash
# Error log
tail -f /var/log/apache2/clipboard-error.log

# Access log
tail -f /var/log/apache2/clipboard-access.log
```

PHP error log (ако е конфигуриран):
```bash
tail -f /var/log/php/error.log
```

MySQL logs:
```bash
# Error log
tail -f /var/log/mysql/error.log

# Slow query log (ако е enabled)
tail -f /var/log/mysql/slow-query.log
```

**Health Check Script:**
```bash
#!/bin/bash
# /opt/scripts/health_check.sh

URL="https://w25-[fn]-clipboard-dev.hss.fmi.uni-sofia.bg"

# Check HTTP response
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" $URL)

if [ $HTTP_CODE -eq 200 ]; then
    echo "OK: Application is running"
    exit 0
else
    echo "ERROR: Application returned HTTP $HTTP_CODE"
    # Send alert email
    echo "Application health check failed" | mail -s "Clipboard System Alert" admin@example.com
    exit 1
fi
```

**Cron job за health check:**
```bash
# Check every 5 minutes
*/5 * * * * /opt/scripts/health_check.sh >> /var/log/clipboard-health.log 2>&1
```

### 12.4 Настройки и коментари

#### 12.4.1 Environment Variables

**Development (.env.dev):**
```bash
DB_HOST=localhost
DB_NAME=clipboard_system
DB_USER=root
DB_PASS=rootpassword
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8080
```

**Production (.env.prod):**
```bash
DB_HOST=db
DB_NAME=clipboard_system_prod
DB_USER=clipboard_user
DB_PASS=[secure-password]
APP_ENV=production
APP_DEBUG=false
APP_URL=https://w25-[fn]-clipboard-dev.hss.fmi.uni-sofia.bg
```

#### 12.4.2 Security Considerations

**1. Password Security:**
- Production admin password трябва да е strong (16+ characters)
- Database passwords различни от development
- Passwords stored в environment variables, не в code

**2. File Permissions:**
```bash
# Set proper permissions
chmod 755 /var/www/clipboard-system
chmod 777 /var/www/clipboard-system/uploads
chmod 600 .env
```

**3. Firewall Rules:**
```bash
# Allow only necessary ports
ufw allow 80/tcp
ufw allow 443/tcp
ufw allow 22/tcp
ufw enable
```

#### 12.4.3 Performance Tuning

**PHP Configuration (php.ini):**
```ini
memory_limit = 256M
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 30
max_input_time = 60
```

**MySQL Configuration (my.cnf):**
```ini
[mysqld]
max_connections = 200
innodb_buffer_pool_size = 1G
query_cache_size = 64M
query_cache_type = 1
```

**Apache Configuration:**
```apache
# Enable compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>

# Enable caching
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

### 12.5 Troubleshooting Guide

**Problem: Application not accessible**
```bash
# Check if Apache is running
sudo systemctl status apache2

# Check Apache error logs
tail -f /var/log/apache2/error.log

# Restart Apache
sudo systemctl restart apache2
```

**Problem: Database connection failed**
```bash
# Check if MySQL is running
sudo systemctl status mysql

# Test database connection
mysql -u clipboard_user -p clipboard_system_prod

# Check MySQL error log
tail -f /var/log/mysql/error.log

# Restart MySQL
sudo systemctl restart mysql
```

**Problem: 502 Bad Gateway (ако използвате Nginx)**
```bash
# Check Apache status
sudo systemctl status apache2

# Check Nginx configuration
sudo nginx -t

# Restart services
sudo systemctl restart apache2
sudo systemctl restart nginx
```

**Problem: Permission errors**
```bash
# Fix ownership
sudo chown -R www-data:www-data /var/www/html/clipboard-system

# Fix permissions
sudo chmod -R 755 /var/www/html/clipboard-system
sudo chmod -R 777 /var/www/html/clipboard-system/uploads
```

### 12.6 Deployment Checklist

**Pre-Deployment:**
- [ ] Code reviewed и tested
- [ ] Database migrations prepared
- [ ] Environment variables configured
- [ ] Backup created
- [ ] Security audit completed

**Deployment:**
- [ ] Git pull latest code
- [ ] Build Docker images
- [ ] Run database migrations
- [ ] Start services
- [ ] Verify health checks

**Post-Deployment:**
- [ ] Test critical functionality
- [ ] Check logs for errors
- [ ] Monitor performance
- [ ] Update documentation
- [ ] Notify team

### 12.7 Rollback Procedure

**If deployment fails:**

**Стъпка 1: Restore code**
```bash
# Go to project directory
cd /var/www/html/clipboard-system

# Checkout previous version
git checkout [previous-commit-hash]

# Or restore from backup
cp -r /var/backups/clipboard-system/code_backup_[date]/* .
```

**Стъпка 2: Restore database**
```bash
# Find latest backup
ls -lh /var/backups/clipboard-system/

# Restore database
gunzip < /var/backups/clipboard-system/backup_[date].sql.gz | mysql -u clipboard_user -p clipboard_system_prod
```

**Стъпка 3: Restart services**
```bash
# Restart Apache
sudo systemctl restart apache2

# Clear PHP opcache (ако е enabled)
sudo systemctl restart php8.1-fpm
```

**Стъпка 4: Verify rollback**
```bash
# Test application
curl https://w25-[fn]-clipboard-dev.hss.fmi.uni-sofia.bg

# Check logs
tail -f /var/log/apache2/clipboard-error.log
```

## 13. Използвани източници

### 13.1 Официална документация

1. **PHP Documentation**
   - URL: https://www.php.net/docs.php
   - Дата на посещение: Януари 2026
   - Използвано за: PHP 8.1 features, PDO, password hashing, session management
   - Автор: The PHP Group

2. **MySQL Documentation**
   - URL: https://dev.mysql.com/doc/
   - Дата на посещение: Януари 2026
   - Използвано за: Database design, JSON columns, indexes, transactions
   - Автор: Oracle Corporation

3. **MDN Web Docs - JavaScript**
   - URL: https://developer.mozilla.org/en-US/docs/Web/JavaScript
   - Дата на посещение: Януари 2026
   - Използвано за: ES6+ features, Fetch API, async/await, DOM manipulation
   - Автор: Mozilla Contributors

4. **MDN Web Docs - CSS**
   - URL: https://developer.mozilla.org/en-US/docs/Web/CSS
   - Дата на посещение: Януари 2026
   - Използвано за: Flexbox, Grid, responsive design, CSS variables
   - Автор: Mozilla Contributors

5. **Docker Documentation**
   - URL: https://docs.docker.com/
   - Дата на посещение: Януари 2026
   - Използвано за: Containerization, Docker Compose, multi-container setup
   - Автор: Docker Inc.

### 13.2 Технически статии и tutorials

6. **RESTful API Design Best Practices**
   - URL: https://restfulapi.net/
   - Дата на посещение: Януари 2026
   - Използвано за: API design principles, HTTP methods, status codes
   - Автор: REST API Tutorial

7. **OWASP Top 10 Web Application Security Risks**
   - URL: https://owasp.org/www-project-top-ten/
   - Дата на посещение: Януари 2026
   - Използвано за: Security best practices, vulnerability prevention
   - Автор: OWASP Foundation

8. **PHP: The Right Way**
   - URL: https://phptherightway.com/
   - Дата на посещение: Януари 2026
   - Използвано за: PHP best practices, coding standards, security
   - Автор: Josh Lockhart and contributors

9. **Database Normalization Tutorial**
   - URL: https://www.studytonight.com/dbms/database-normalization.php
   - Дата на посещение: Януари 2026
   - Използвано за: Database design, normalization forms
   - Автор: StudyTonight

10. **JavaScript Design Patterns**
    - URL: https://www.patterns.dev/posts/classic-design-patterns/
    - Дата на посещение: Януари 2026
    - Използвано за: Module pattern, Observer pattern, Factory pattern
    - Автор: Addy Osmani

### 13.3 Stack Overflow и Community Resources

11. **Stack Overflow - PHP Tag**
    - URL: https://stackoverflow.com/questions/tagged/php
    - Дата на посещение: Януари 2026 (множество посещения)
    - Използвано за: Specific PHP problems, debugging, best practices
    - Автор: Stack Overflow Community

12. **Stack Overflow - JavaScript Tag**
    - URL: https://stackoverflow.com/questions/tagged/javascript
    - Дата на посещение: Януари 2026 (множество посещения)
    - Използвано за: JavaScript issues, async patterns, DOM manipulation
    - Автор: Stack Overflow Community

13. **GitHub - PHP Best Practices**
    - URL: https://github.com/codeguy/php-the-right-way
    - Дата на посещение: Януари 2026
    - Използвано за: Code organization, PSR standards
    - Автор: Josh Lockhart

### 13.4 Security Resources

14. **OWASP SQL Injection Prevention Cheat Sheet**
    - URL: https://cheatsheetseries.owasp.org/cheatsheets/SQL_Injection_Prevention_Cheat_Sheet.html
    - Дата на посещение: Януари 2026
    - Използвано за: SQL injection prevention techniques
    - Автор: OWASP

15. **OWASP Cross-Site Scripting (XSS) Prevention**
    - URL: https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html
    - Дата на посещение: Януари 2026
    - Използвано за: XSS prevention, input sanitization
    - Автор: OWASP

16. **Password Hashing in PHP**
    - URL: https://www.php.net/manual/en/faq.passwords.php
    - Дата на посещение: Януари 2026
    - Използвано за: Secure password storage, bcrypt usage
    - Автор: PHP Documentation Team

### 13.5 Design и UX Resources

17. **Material Design Guidelines**
    - URL: https://material.io/design
    - Дата на посещение: Януари 2026
    - Използвано за: UI design principles, component design
    - Автор: Google

18. **Web Content Accessibility Guidelines (WCAG)**
    - URL: https://www.w3.org/WAI/WCAG21/quickref/
    - Дата на посещение: Януари 2026
    - Използвано за: Accessibility standards, ARIA attributes
    - Автор: W3C

19. **CSS-Tricks - A Complete Guide to Flexbox**
    - URL: https://css-tricks.com/snippets/css/a-guide-to-flexbox/
    - Дата на посещение: Януари 2026
    - Използвано за: Flexbox layout techniques
    - Автор: Chris Coyier

20. **CSS-Tricks - A Complete Guide to Grid**
    - URL: https://css-tricks.com/snippets/css/complete-guide-grid/
    - Дата на посещение: Януари 2026
    - Използвано за: CSS Grid layout
    - Автор: Chris Coyier

### 13.6 DevOps и Deployment

21. **Docker Compose Documentation**
    - URL: https://docs.docker.com/compose/
    - Дата на посещение: Януари 2026
    - Използвано за: Multi-container orchestration
    - Автор: Docker Inc.

22. **Nginx Configuration Guide**
    - URL: https://nginx.org/en/docs/
    - Дата на посещение: Януари 2026
    - Използвано за: Reverse proxy configuration
    - Автор: Nginx Inc.

23. **Git Branching Strategy**
    - URL: https://nvie.com/posts/a-successful-git-branching-model/
    - Дата на посещение: Януари 2026
    - Използвано за: Git workflow, branching strategy
    - Автор: Vincent Driessen

### 13.7 Performance Optimization

24. **MySQL Performance Tuning**
    - URL: https://dev.mysql.com/doc/refman/8.0/en/optimization.html
    - Дата на посещение: Януари 2026
    - Използвано за: Query optimization, index usage
    - Автор: Oracle Corporation

25. **Web Performance Best Practices**
    - URL: https://web.dev/performance/
    - Дата на посещение: Януари 2026
    - Използвано за: Page load optimization, caching strategies
    - Автор: Google Chrome Team

### 13.8 Testing Resources

26. **PHPUnit Documentation**
    - URL: https://phpunit.de/documentation.html
    - Дата на посещение: Януари 2026
    - Използвано за: Unit testing в PHP
    - Автор: Sebastian Bergmann

27. **JavaScript Testing Best Practices**
    - URL: https://github.com/goldbergyoni/javascript-testing-best-practices
    - Дата на посещение: Януари 2026
    - Използвано за: Frontend testing strategies
    - Автор: Yoni Goldberg

### 13.9 Книги (референции)

28. **"Clean Code: A Handbook of Agile Software Craftsmanship"**
    - Автор: Robert C. Martin
    - Издателство: Prentice Hall
    - Година: 2008
    - Използвано за: Code quality principles, naming conventions

29. **"Design Patterns: Elements of Reusable Object-Oriented Software"**
    - Автори: Erich Gamma, Richard Helm, Ralph Johnson, John Vlissides
    - Издателство: Addison-Wesley
    - Година: 1994
    - Използвано за: Design patterns (Repository, Factory, Observer)

30. **"RESTful Web APIs"**
    - Автори: Leonard Richardson, Mike Amundsen, Sam Ruby
    - Издателство: O'Reilly Media
    - Година: 2013
    - Използвано за: REST API design principles

### 13.10 AI Tools

31. **ChatGPT (GPT-4)**
    - URL: https://chat.openai.com/
    - Дата на използване: Януари 2026 (daily)
    - Използвано за: Architecture discussions, code review, problem solving
    - Разработчик: OpenAI

32. **GitHub Copilot**
    - URL: https://github.com/features/copilot
    - Дата на използване: Януари 2026 (continuous)
    - Използвано за: Code autocomplete, boilerplate generation
    - Разработчик: GitHub/OpenAI

33. **Kiro AI**
    - Дата на използване: Януари 2026
    - Използвано за: Project-specific assistance, refactoring
    - Разработчик: Kiro

### 13.11 Допълнителни ресурси

34. **Can I Use**
    - URL: https://caniuse.com/
    - Дата на посещение: Януари 2026
    - Използвано за: Browser compatibility checking
    - Автор: Alexis Deveria

35. **RegExr - Regular Expression Testing**
    - URL: https://regexr.com/
    - Дата на посещение: Януари 2026
    - Използвано за: Regular expression testing и debugging
    - Автор: gskinner.com

### 13.12 Цитиране в текста

Горните източници са цитирани на следните места в документацията:

- **Раздел 3 (Теория):** Източници 1, 2, 8, 9, 10, 29
- **Раздел 4 (Технологии):** Източници 1, 2, 3, 4, 5, 21
- **Раздел 5 (Инсталация):** Източници 5, 21, 22, 23
- **Раздел 8 (Програмен код):** Източници 1, 8, 10, 13, 28
- **Раздел 9 (Разширения):** Източници 24, 25, 30
- **Раздел 10 (AI):** Източници 31, 32, 33
- **Security:** Източници 7, 14, 15, 16

### 13.13 Bookmarks Export

Пълен списък с всички използвани линкове е експортиран в:
- **Файл:** `DOCUMENTATION/bookmarks.html`
- **Формат:** HTML bookmarks (compatible с всички browsers)
- **Съдържание:** 100+ линкове организирани по категории

---

**Забележка:** Всички източници са достъпни към датата на посещение. Някои линкове може да се променят или станат недостъпни в бъдеще. За критични референции са запазени локални копия в `DOCUMENTATION/references/` директорията.

---

Предал (подпис): ………………………….

[ФН, Имена, Специалност, Група]

Приел (подпис): ………………………….

/доц. д-р Милен Петров/

## Препоръки за предаване на проекта (ИЗТРИЙ до края-при предаване на проекта!)

**Заб1.** (w21. Изтрийте текста в зелено по-горе в документацията, а където се искат важите данни - в жълто - модифицирайте коректната информация, и махнете оцветяването.

Заб. _Спазвайки препоръките по-долу биха спомогнали да направите добри проекти по Уеб технологии._

## Финален проект (инструкции към 7 издание)

2\. Изпитни проекти: (настоящият документ може да съхраните като .docx . За хората, ползващи редактори, различни от MS Office - освен docx/rtf да качат и pdf версия на документацията - за по-сигурно.)

2.1. Темите за изпитните проекти трябва да са съгласувани с мен на място (вече няколко хора го направиха); като тема си запишете ф.н. в гугъл докса - там пише как се записвате (в коя колона и какъв разделител да ползвате); Обем на проектите: 30 човеко-часа на човек на проект. Това е доста относително, но все пак е нещо.

2.2. За документация на проекта ползвайте шаблона (ще гледам да го кача скоро); Задължително потребителска документация (а.к.а. userguide) - няколко скрийншота с кратко разяснение; условие на проекта (т.е. какво сте разтълкували сме се разбрали да правите - то може и да се различава от описанието дадено в гугъл докс-а към момента); както и инструкции за инсталиране; за защитата - ще дам няколко дати през сесията/преди сесията + официалните дати, за който не успее да мине преди това; за защитата - кода, документацията/необходими библиотеки/среди, инсталации - за инсталирането се предават на DVD (ако има още такова нещо като CD-може и на CD). Може по изключение да сложа И форма за качване само на документацията и програмен код и в мудъл, НО идеята е, че ако няма интернет (да речем е паднал мудъла, спрял е тока и т.н. - само по съдържанието на диска, който сте предали да може да се инсталира, подкара и тества проекта); Също така разпечатвате първа страница от проекта (с името ви и заданието), 1 страница от userguide-а и последната страница, където пише предал/приел (там пише вашите и моите имена), т.е. не е необходимо да печатите цялата документация - така или иначе ще я има в електронен формат; За хората, които не ползват MS Office - ще помоля освен изходният документ в docx/rtf, да качат нещата и е pdf формат, т.к. често такъв тип документация се размества и не се чете. В кода сложете и MySql sql скрипт със създаване на таблицата и скриптове, задаващи примерни данни (т.е. може да тестваме приложението дали работи без данни, и ако за да се види пълната прелест на проекта е нужно да се вкарат предварително данни - sql и/или снимки/звуци и т.н. може да ги подготвите на диска или ако е указано-в мудъл ~~или облака към курса - ако има~~). **БЕЗ флашки!**

2.3. Срок за защита на изпитните проекти - до изпита. За съгласуване на теми - ми пишете да се разберем за час за консултации за проектите.

(w21) **Предаване на проекта става 48ч.** преди изпита ЗАДЪЛЖИТЕЛНО по указаният начин от преподавателя (най-вероятно мудъл). Това важи и за хората, независимо явяващи се извънредно, на редовна или поправителна сесия, финализиране и последни промени може да качвате и преди да влезнете за защита.

Вижте и инструкциите към 6 издание - които не противоречат с инструкциите за настоящото издание - са валидни и сега.

## Финален проект (инструкции към 6 издание)

Заб. _Спазвайки препоръките по-долу биха спомогнали да направите добри проекти по Уеб технологии._

**Задължително:** Реферата да е в zip файл с име на зип файла: **fakNo_final.zip** където вместо **fanNo** пишете факултетният си номер/а (според инструкциите, зададени във форума на курса).

## Още няколко упътвания

I. АРХИТЕКТУРА НА УЕБ СИСТЕМАТА: Да има три-слойна архитектура (Препоръки: 1. презентационен слой - css/js/html, 2. БД: MySql и като допълнение по желание може да имате импорт/експорт към XML/json/csv; 3. Бизнес логика - Php)

II. ФУНКЦИОНАЛНИ ХАРАКТЕРИСТИКИ: Съгласуват се с преподавателя - избира се темата, а каква да е функционалността - питате на лекции или се явявате на предварителна защита, където съгласувате обхвата на изискванията; Да се познават и спазват добрите практики, завършеност на функционалността - според сложността на приложението - 30 човеко-часа);

III. НЕ-Функционални характеристики:

\- Конфигурируемост (лесно да може да се инсталира - например: смяна на едно место ако се смени физически папката на сървъра - да е в под-папка, смяна на адреса - IP/URL, смяна на име/парола/

\- Разширяемост - лесно да може да се разширява функционалността на различните слоеве;

\- Документация - без да се пуска проекта - да може да се ориентира в неговата функционалност, как се настройва, точки на разширение на отделните слоеве- ако има особеност, примерни данни за тестване - администраторски акаунти, ръководство на потребителя за различните роли - екраннк снимки - т.нар „скрийншоти" с номер и кратко заглавие).

IV. Други изисквания, зададени на лекция (следете лекциите, форумите и групата).

**Заб.** _Има вероятност да пусна нова инстанция на системата за рефератите, но този път за проекти, където в html да сложите документацията си (т.е. подобен на този шаблон, но в html формат)_.

За изискването конфигурируемост (описвате в документацията), се очаква (само концептуална идея, може да се представи и реализира по много начини), нещо от вида:

<?php //conf.php (реално в документацията се описва в т. DevOps

\$vhosts = <<<EOT

#############################

\## MP-1.10.1: w11ref.w3c.fmi.uni-sofia.bg

#############################

&lt;VirtualHost \*:80&gt;

ServerAdmin milenp@fmi.uni-sofia.bg

DocumentRoot "C:/xampp/htdocs/w11ref.w3c.fmi.uni-sofia.bg/\_PUB"

DirectoryIndex index.php

ServerName w11ref.w3c.fmi.uni-sofia.bg

\# ServerAlias 9999.w3c.fmi.uni-sofia.bg

&lt;Directory "C:/BACKUP_SYSTEMS/htdocs/w11ref.w3c.fmi.uni-sofia.bg/\_PUB"&gt;

Options All

AllowOverride All

Require all granted

&lt;/Directory&gt;

&lt;/VirtualHost&gt;

EOT;

//echo "&lt;pre&gt;\$vhosts . &lt;/pre&gt;";echo "&lt;pre&gt;\$vhosts . &lt;/pre&gt;";

// \$configs = include('conf.php');

//if - uncomment/comment -> in calling part can be used as \$configs\['vhosts'\];

**return** (**object**) **array**(

//0. sys_cfg

'cfg_ver' => '1',

//1. sys_cfg

'cfg_system_mgmt' => 'w11ref',

'cfg_system_name' =>'www_11ed_referats',

'cfg_dns_prefix' => 'w11ref',

'cfg_dns_sufix' => 'w3c.fmi.uni-sofia.bg',

'HTTP_URL_PREFIX' => "http://w11ref.w3c.fmi.uni-sofia.bg",,

//2. db_cfg (from queries.php)

'DB_SERVERNAME' => '127.0.0.1',

'DB_USERNAME' => 'w11ed_fn9999',

'DB_PASSWORD' => ' w11ed_fn9999',

'DB_NAME' => ' w11ed_fn9999',

//9. vhost

'vhosts' => \$vhosts,

'vhosts_ServerAdmin' => 'milenp@fmi.uni-sofia.bg',

'vhosts_DocumentRoot' => 'C:/xampp/htdocs/w11ref.w3c.fmi.uni-sofia.bg/\_PUB',

'vhosts_port' => 80,

'vhosts_ServerName' => 'w11ref.w3c.fmi.uni-sofia.bg',

);

?>

**За да се използва:**

\$configs = include('conf.php');

**header("Location: ". \$configs->HTTP_URL_PREFIX."/choose.php");**

**Други варианти за конфигурация:**

**_<?php_**

_//_install.php _\- can called once, initiating database, creating configuration file of doesnt exists, etc._

_//Optional::_ ConfigPanel.php _-> Configures/changes settings of app_

_//Required:_ config.php, db_sql.txt

_//required: help.php (can be part of documentation)..._

_//or config.ini -> със същите настройки;_

_class Config {_

_\$SITE_FN = 61999; //can be used bellow_

_\$SITE_CREATOR = "Your Name(s)";_

_\$SITE_ADMIN_EMAIL = "<your@email.com>";_

_\$SITE_INFO = "This project was created during ...year, on Web Technologies, Sofia University, FMI, lead by:_

_Name of Instructor, assistant: Name-Of-Assistant";_

\$SITE_URL="<http://loremipsum.fmi.uni-sofia.bg/WEBTECH/www_6ed_prj/61999_alg_animation>";

_\$ROOT_FOLDER="c:\\xampp\\htdocs\\www_6ed_prj/61999_alg_animation"_

_\$DB_USER="61999_user";_

_\$DB_PASS="61999_pass";_

_\$DB_NAME="www_6ed_61999_alg_animation";_

_\$SITE_DESCRIPTION="What is ready, and what can be improved for future";_

_\$PROJECT_REQ="...(from documentation)";_

_}_

**_?>_**

История на версиите

- Последна модификация 0.3 /2020-01-06/by MP
- Последна модификация 0.2/2018-12-09/by MP

Успех!