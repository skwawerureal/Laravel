# Bulk Email System

A comprehensive Laravel-based bulk email marketing system with campaign management, subscriber lists, email templates, and analytics tracking.

## Features

### 🚀 Core Functionality
- **Campaign Management** - Create, schedule, send, pause, and monitor email campaigns
- **Email List Management** - Organize subscribers into targeted groups
- **Subscriber Management** - Individual subscriber management with custom fields
- **Email Templates** - Reusable email templates with personalization variables
- **Excel Import** - Bulk import subscribers from Excel/CSV files
- **Analytics & Tracking** - Real-time open rates, click tracking, and engagement metrics
- **Queue-Based Sending** - Background email processing for performance
- **Unsubscribe System** - Automated opt-out handling

### 📊 Analytics & Reporting
- Real-time campaign performance metrics
- Open rate and click rate tracking
- Bounce and unsubscribe monitoring
- Dashboard with comprehensive statistics
- Individual email delivery status

### 🎨 User Interface
- Modern responsive design with Tailwind CSS
- Intuitive campaign creation wizard
- Drag-and-drop template editor
- Real-time status updates
- Mobile-friendly interface

## Installation

### Prerequisites
- PHP 8.2+
- Composer
- SQLite (default) or MySQL/MariaDB
- Web server (Apache/Nginx)

### Setup Instructions

1. **Clone and Install Dependencies**
   ```bash
   git clone <repository-url>
   cd bulk-email-system
   composer install
   ```

2. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database Setup**
   
   **Option A: SQLite (Recommended for development)**
   ```bash
   touch database/database.sqlite
   php artisan migrate
   ```
   
   **Option B: MySQL/MariaDB**
   ```bash
   # Update .env with your database credentials
   php artisan migrate
   ```

4. **Start Development Server**
   ```bash
   php artisan serve
   ```

5. **Queue Worker (for email sending)**
   ```bash
   php artisan queue:work
   ```

## Usage

### Creating Your First Campaign

1. **Create Email List**
   - Navigate to Email Lists → Create Email List
   - Add subscribers manually or import from Excel

2. **Design Email Template**
   - Go to Templates → Create Template
   - Use variables like `{{first_name}}`, `{{email}}`, etc.

3. **Launch Campaign**
   - Campaigns → Create Campaign
   - Select email lists and template
   - Schedule or send immediately

### Email Personalization Variables

Available variables for templates and campaigns:
- `{{first_name}}` - Subscriber's first name
- `{{last_name}}` - Subscriber's last name
- `{{full_name}}` - Subscriber's full name
- `{{email}}` - Subscriber's email address
- `{{unsubscribe_url}}` - Unsubscribe link

### Excel Import Format

When importing subscribers, use these column headers:
- `email` (required) - Email address
- `first_name` - First name
- `last_name` - Last name
- Any additional columns will be stored as custom fields

## API Endpoints

### Campaign Management
- `GET /campaigns` - List all campaigns
- `POST /campaigns` - Create new campaign
- `GET /campaigns/{id}` - Show campaign details
- `PUT /campaigns/{id}` - Update campaign
- `DELETE /campaigns/{id}` - Delete campaign
- `POST /campaigns/{id}/send` - Send campaign
- `POST /campaigns/{id}/pause` - Pause campaign
- `POST /campaigns/{id}/resume` - Resume campaign

### Email List Management
- `GET /email-lists` - List all email lists
- `POST /email-lists` - Create new email list
- `POST /email-lists/{id}/import` - Import subscribers from Excel
- `POST /email-lists/{id}/add-subscriber` - Add single subscriber

### Template Management
- `GET /email-templates` - List all templates
- `POST /email-templates` - Create new template
- `GET /email-templates/{id}/preview` - Preview template

### Tracking & Analytics
- `GET /email/open/{subscriber}` - Track email opens
- `GET /email/click/{sentEmail}` - Track link clicks
- `GET /unsubscribe/{token}` - Handle unsubscribes

## Database Schema

### Core Tables
- `campaigns` - Email campaign data
- `email_lists` - Subscriber groups
- `subscribers` - Individual subscriber records
- `email_templates` - Reusable email templates
- `sent_emails` - Individual email delivery records
- `email_analytics` - Open/click tracking data

### Relationships
- Campaigns → Email Lists (Many-to-Many)
- Email Lists → Subscribers (Many-to-Many)
- Campaigns → Sent Emails (One-to-Many)
- Sent Emails → Analytics (One-to-Many)

## Configuration

### Email Settings (.env)
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-server.com
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@domain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Queue Configuration
```env
QUEUE_CONNECTION=database
# or
QUEUE_CONNECTION=redis
```

## Security Features

- **CSRF Protection** - All forms protected
- **Input Validation** - Comprehensive validation rules
- **SQL Injection Prevention** - Eloquent ORM usage
- **XSS Protection** - Proper output escaping
- **Unsubscribe Tokens** - Secure opt-out links

## Performance Optimization

- **Queue System** - Background email processing
- **Database Indexing** - Optimized queries
- **Caching** - Template and data caching
- **Rate Limiting** - Prevent abuse
- **Batch Processing** - Efficient bulk operations

## Monitoring & Logging

- **Application Logs** - Comprehensive error logging
- **Email Delivery Logs** - Track sending status
- **Performance Metrics** - Campaign analytics
- **Queue Monitoring** - Job processing status

## Testing

### Running Tests
```bash
php artisan test
```

### Test Coverage
- Unit tests for models and services
- Feature tests for all endpoints
- Email sending functionality tests
- Import/export functionality tests

## Deployment

### Production Setup
1. Set `APP_ENV=production` in `.env`
2. Configure production database
3. Set up proper email credentials
4. Configure queue worker (Supervisor recommended)
5. Set up SSL certificate
6. Configure web server (Nginx/Apache)

### Queue Worker Setup
```bash
# Using Supervisor
sudo apt-get install supervisor
# Create supervisor config file
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

## Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -am 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

## Support

### Common Issues
- **Emails not sending**: Check queue worker status and email configuration
- **Import failures**: Verify Excel file format and required columns
- **Slow performance**: Ensure proper database indexing and queue setup

### Debug Mode
Enable debug mode in `.env`:
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Changelog

### v1.0.0
- Initial release with core functionality
- Campaign management system
- Email list and subscriber management
- Template system with personalization
- Excel import functionality
- Analytics and tracking
- Queue-based email sending
- Modern responsive UI

---

**Built with ❤️ using Laravel 12**
