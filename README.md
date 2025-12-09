# EventHub - Event Management System

A modern, professional event management platform built with HTML, CSS, JavaScript, PHP, and SQLite.

## Features

### 🎯 Core Features
- **Unified Authentication**: Separate login flows for users and organizers
- **Event Management**: Create, edit, and manage events with full CRUD operations
- **Booking System**: Complete booking flow from event selection to payment
- **Order Management**: Track bookings and manage order statuses
- **Real-time Notifications**: Instant updates for order confirmations

### 👥 User Features
- **Event Discovery**: Browse and search through events with advanced filters
- **User Profile**: Manage personal information and view booking history
- **Booking Flow**: Step-by-step booking process with address confirmation
- **Payment Options**: Support for online payments and Cash on Delivery (COD)
- **Order Tracking**: View all bookings with real-time status updates

### 🎪 Organizer Features
- **Dashboard**: Comprehensive overview of events and statistics
- **Event Creation**: Easy-to-use interface for creating new events
- **Order Management**: Review and approve/reject pending bookings
- **Profile Management**: Update organizer information and add media
- **Analytics**: Track event performance and registration statistics

### 🎨 Design Features
- **Modern UI**: Clean, professional design with vibrant color palette
- **Responsive Layout**: Works seamlessly on all devices
- **Interactive Elements**: Smooth transitions and user-friendly interface
- **Accessibility**: Built with accessibility best practices

## Technology Stack

### Frontend
- **HTML5**: Semantic markup for structure
- **CSS3**: Modern styling with flexbox and grid
- **JavaScript**: Interactive functionality and DOM manipulation
- **Responsive Design**: Mobile-first approach

### Backend
- **PHP 7.4+**: Server-side logic and API handling
- **SQLite**: Lightweight database for data persistence
- **Session Management**: Secure user authentication
- **MVC Pattern**: Organized code structure

### Database Schema
- **Users**: User authentication and basic information
- **Organizers**: Extended organizer profiles
- **Events**: Event details and information
- **Orders**: Booking records and payment information

## Installation

### Prerequisites
- PHP 7.4 or higher
- Web server (Apache, Nginx, etc.)
- SQLite3 PHP extension

### Setup Instructions

1. **Clone or download the project**
   ```bash
   git clone <repository-url>
   cd eventhub
   ```

2. **Set up the web server**
   - Place the files in your web server's document root
   - Ensure the server has read/write permissions for the project directory

3. **Set up the database**
   - The SQLite database will be created automatically on first run
   - Make sure the web server has write permissions to create the database file

4. **Run the setup script (optional)**
   ```bash
   php setup-sample-data.php
   ```
   This will create sample users, events, and orders for testing.

5. **Access the application**
   - Open your web browser and navigate to the EventHub URL
   - Default login credentials (if you ran the setup script):
     - User: john@example.com / password123
     - Organizer: organizer@example.com / password123

## Project Structure

```
eventhub/
├── assets/
│   ├── css/
│   │   └── style.css          # Main stylesheet
│   ├── js/
│   │   └── script.js          # JavaScript functionality
│   └── images/                # Image assets
├── config/
│   ├── auth.php               # Authentication class
│   ├── database.php           # Database connection class
│   ├── eventmanager.php       # Event management class
│   └── ordermanager.php       # Order management class
├── user/
│   ├── dashboard.php          # User dashboard
│   ├── welcome.php            # User welcome screen
│   ├── profile.php            # User profile management
│   ├── orders.php             # User order history
│   ├── booking-detail.php     # Event details page
│   ├── booking-address.php   # Address confirmation
│   ├── booking-payment.php    # Payment selection
│   └── booking-confirmation.php # Booking confirmation
├── organizer/
│   ├── dashboard.php          # Organizer dashboard
│   └── profile.php            # Organizer profile management
├── api/
│   └── handler.php            # API endpoint for AJAX requests
├── includes/                  # Reusable components
├── database.sqlite            # SQLite database file (created automatically)
├── index.php                 # Main login page
└── setup-sample-data.php      # Sample data generator
```

## Usage Guide

### For Users

1. **Registration/Login**
   - Create an account as a user or organizer
   - Login with your credentials

2. **Browsing Events**
   - Use the search bar to find specific events
   - Apply filters by type, price range, or organizer
   - Click on events to view detailed information

3. **Booking Events**
   - Select an event and click "Book Now"
   - Choose your preferred date and time
   - Confirm your address details
   - Select payment method (Online/COD)
   - Complete the booking process

4. **Managing Orders**
   - View all your bookings in the "My Orders" section
   - Track order status (Pending, Confirmed, Cancelled)
   - View booking details and payment information

### For Organizers

1. **Profile Setup**
   - Complete your organizer profile with bio and information
   - Add video URL to showcase your work

2. **Creating Events**
   - Click "Create New Event" from the dashboard
   - Fill in event details (title, type, price, location, description)
   - Save and publish your event

3. **Managing Orders**
   - View pending orders in the dashboard
   - Review customer information and booking details
   - Accept or reject orders with one click
   - Track overall event statistics

## Customization

### Styling
- Edit `assets/css/style.css` to modify colors, fonts, and layout
- The design uses CSS variables for easy theming
- Responsive breakpoints are defined for mobile, tablet, and desktop

### Functionality
- Add new features by extending existing classes in the `config/` directory
- Create new pages following the existing structure
- Modify database schema by editing the `Database` class

### Database
- The SQLite database file is created automatically
- Schema is defined in `config/database.php`
- Use the `Database` class for all database operations

## Security Considerations

- **Password Hashing**: All passwords are hashed using PHP's password_hash()
- **Session Management**: Secure session handling with proper validation
- **Input Validation**: Form inputs are validated and sanitized
- **SQL Injection Prevention**: Prepared statements used for all database queries
- **CSRF Protection**: Basic CSRF protection implemented

## Browser Compatibility

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## Support

For support and questions:
- Check the documentation in this README
- Review the code comments for detailed explanations
- Test the sample data to understand the system flow

## License

This project is open source and available under the MIT License.

## Contributing

Contributions are welcome! Please feel free to submit issues and enhancement requests.

---

**EventHub** - Making event management simple, professional, and enjoyable.# event-mana
