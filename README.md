# SweetLife Hotel Management System

A comprehensive hotel management web application built with PHP, MySQL, and vanilla JavaScript, using the SweetLife template for a modern, responsive frontend.

## Features

### User Features
- **Room Booking**: Browse and book hotel rooms and short-let apartments
- **Real-time Availability**: Check room availability with calendar integration
- **Secure Payments**: Integrated Paystack payment gateway
- **Booking Management**: View booking history and status
- **Responsive Design**: Mobile-friendly interface

### Admin Features
- **Dashboard**: Overview of bookings, revenue, and occupancy
- **Room Management**: Add, edit, and delete rooms with image uploads
- **Booking Management**: View and manage all bookings
- **Customer Management**: View customer details and booking history
- **Payment Tracking**: Monitor all payments and transactions
- **Reports**: Generate revenue and occupancy reports

## Tech Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: Vanilla JavaScript, Bootstrap 4
- **Payment**: Paystack API
- **Template**: SweetLife Hotel Template

## Installation

1. **Clone/Download** the project files to your web server directory

2. **Database Setup**:
   ```sql
   -- Import the database schema
   mysql -u root -p < database/schema.sql
   ```

3. **Configuration**:
   - Update `config/config.php` with your database credentials
   - Add your Paystack API keys
   - Configure email settings for notifications

4. **File Permissions**:
   ```bash
   chmod 755 uploads/
   chmod 755 uploads/rooms/
   chmod 755 admin/uploads/
   ```

5. **Default Admin Login**:
   - Username: `admin`
   - Password: `admin123`

## Directory Structure

```
├── admin/                  # Admin panel files
├── api/                    # API endpoints
├── auth/                   # Authentication handlers
├── classes/                # PHP classes
├── config/                 # Configuration files
├── css/                    # Stylesheets
├── database/               # Database schema
├── images/                 # Template images
├── includes/               # Reusable PHP includes
├── js/                     # JavaScript files
├── uploads/                # File uploads directory
├── index.php               # Homepage
├── rooms.php               # Hotel rooms page
├── apartments.php          # Short-let apartments page
└── booking-confirmation.php # Booking confirmation
```

## API Endpoints

- `GET /api/rooms.php` - Get all rooms
- `GET /api/rooms.php?type=hotel_room` - Get hotel rooms only
- `GET /api/rooms.php?type=short_let` - Get apartments only
- `POST /api/rooms.php` - Check room availability
- `POST /api/bookings.php` - Create new booking
- `POST /api/payments.php` - Handle payments

## Security Features

- Password hashing with PHP's `password_hash()`
- SQL injection prevention with prepared statements
- XSS protection with `htmlspecialchars()`
- CSRF protection for forms
- File upload validation
- Session security

## Payment Integration

The system uses Paystack for secure payment processing:

1. User selects room and fills booking form
2. System creates booking record
3. Payment is initialized with Paystack
4. User completes payment on Paystack
5. Payment verification and booking confirmation
6. Email receipt sent to customer

## Customization

### Adding New Room Categories
```sql
INSERT INTO room_categories (name, description) VALUES ('Premium Suite', 'Luxury suite with premium amenities');
```

### Modifying Email Templates
Edit the email templates in the `includes/email-templates/` directory.

### Styling Changes
Modify `css/style.css` or add custom CSS to maintain the SweetLife theme.

## Support

For support and customization requests, please contact the development team.

## License

This project is proprietary software. All rights reserved.