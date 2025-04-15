# Quality Control Tracker

<div align="center">

![Quality Control Tracker Logo](https://via.placeholder.com/200x80?text=QC+Tracker)

A comprehensive web-based application for tracking and managing quality control processes in manufacturing and production environments.

[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)](https://www.php.net/)
[![MySQL Version](https://img.shields.io/badge/MySQL-5.7%2B-orange.svg)](https://www.mysql.com/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

</div>

## 📋 Overview

Quality Control Tracker is a PHP-based web application designed to help organizations record, monitor, and analyze quality control inspections. The system provides a centralized platform for tracking product quality, identifying trends, and generating reports to support continuous improvement initiatives.

![Dashboard Preview](https://via.placeholder.com/800x400?text=Dashboard+Preview)

## ✨ Features

### 🔍 Quality Control Management
- **Inspection Records**: Create, view, edit, and manage detailed QC inspection records
- **Pass/Fail Tracking**: Record inspection results with comprehensive notes
- **Batch Management**: Track quality metrics by batch numbers
- **Advanced Filtering**: Find records by product, date range, status, and more

### 📊 Reporting & Analytics
- **Visual Dashboard**: Get an at-a-glance view of key quality metrics
- **Pass Rate Analysis**: Track pass/fail rates over time with visual charts
- **Product Performance**: Compare quality metrics across different products
- **Data Export**: Export records and reports to CSV format for further analysis

### 👥 User Management
- **Role-Based Access**: Three permission levels (Admin, Manager, Inspector)
- **User Administration**: Add, edit, and manage user accounts
- **Secure Authentication**: Password-protected access with encrypted storage

### 🏭 Product Management
- **Product Catalog**: Maintain a database of products subject to quality control
- **Categorization**: Organize products by category for easier management
- **Status Control**: Activate/deactivate products without losing historical data

### ⚙️ Administrative Tools
- **System Settings**: Configure application parameters
- **Activity Logging**: Track user actions for accountability
- **Data Backup**: Export system data for backup purposes

## 🛠️ Technologies Used

### Backend
- **PHP 7.4+**: Server-side scripting language
- **MySQL 5.7+**: Relational database management system
- **PDO/MySQLi**: Database abstraction and prepared statements
- **PHP Sessions**: User authentication and state management

### Frontend
- **HTML5**: Semantic markup structure
- **CSS3**: Custom styling with responsive design principles
- **JavaScript**: Client-side interactivity and validation
- **Pure Vanilla Implementation**: No frameworks for lightweight performance

### Security Features
- **Password Hashing**: Secure credential storage using PHP's password_hash()
- **Prepared Statements**: Protection against SQL injection attacks
- **Input Validation**: Comprehensive server-side validation
- **CSRF Protection**: Form security measures

## 📦 Installation

### Prerequisites
- Web server (Apache, Nginx, etc.)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- mod_rewrite enabled (for clean URLs)

### Step-by-Step Installation


1. **Git Clone**
```Bash
   - git clone name_of_the_repo(link)
   - cd name_of_the_repo
```

2. **Create a MySQL database**

```sql
CREATE DATABASE qc_tracker;
```


3. **Import the database schema**

```shellscript
mysql -u username -p qc_tracker &lt; database.sql
```


4. **Configure database connection**

1. Edit the `config.php` file with your database credentials:


```php
$db_host = 'localhost';
$db_user = 'your_username';
$db_pass = 'your_password';
$db_name = 'qc_tracker';
```


5. **Set up the admin user**

1. Access the setup script via browser: `http://yoursite.com/setup_admin.php`
2. This will create an admin user with credentials:

1. Username: admin
2. Password: admin



3. **Important**: Delete the setup_admin.php file after creating the admin user



6. **Set proper permissions**

```shellscript
chmod 755 -R /path/to/qc-tracker
chmod 777 -R /path/to/qc-tracker/uploads (if applicable)
```


7. **Access the application**

1. Navigate to `http://yoursite.com/qc-tracker/` in your web browser
2. Log in with the admin credentials





## 📘 Usage Guide

### Adding Products

1. Log in as an admin or manager
2. Navigate to Product Management
3. Click "Add New Product"
4. Fill in the product details and save


### Recording Quality Control Inspections

1. Navigate to "Add QC Record"
2. Select the product and batch number
3. Enter inspection details and set pass/fail status
4. Add any relevant notes
5. Save the record


### Generating Reports

1. Navigate to the Reports section
2. Set date range and other filters as needed
3. View the generated reports
4. Export to CSV if needed


### Managing Users

1. Log in as an admin
2. Navigate to User Management
3. Add, edit, or deactivate users as needed
4. Assign appropriate roles based on responsibilities


## 🔧 Troubleshooting

### Common Issues and Solutions

#### Login Problems

- **Issue**: Unable to login with correct credentials
- **Solution**: Run the setup_admin.php script to reset admin credentials
- **Prevention**: Ensure password_hash() and password_verify() functions are compatible with your PHP version


#### Database Connection Errors

- **Issue**: "Could not connect to database" error
- **Solution**: Verify database credentials in config.php and ensure MySQL service is running
- **Prevention**: Test database connection before deployment


#### Blank Page or 500 Error

- **Issue**: Page displays blank or server returns 500 error
- **Solution**: Check PHP error logs and enable error reporting temporarily:

```php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```


- **Prevention**: Implement proper error handling and logging


### PHP Configuration Requirements

- session.auto_start = 0
- memory_limit = 128M (minimum)
- max_execution_time = 30 (minimum)
- file_uploads = On (if using file uploads)


## 🎨 Customization

### Styling Modifications

The application uses a custom CSS file (styles.css) that can be easily modified to match your organization's branding:

1. **Colors**: Edit the CSS variables at the top of styles.css:

```css
:root {
    --primary-color: #2563eb;
    --primary-hover: #1d4ed8;
    /* other color variables */
}
```


2. **Logo**: Replace the logo image in the header section of layout files
3. **Typography**: Modify font-family properties in the CSS


### Adding Custom Fields

To add custom fields to quality control records:

1. Modify the database schema in database.sql
2. Update the form in add_record.php and edit_record.php
3. Adjust the display in view_record.php
4. Update the getAllRecords() function in functions.php


## 🔒 Security Considerations

### Production Environment Recommendations

- **HTTPS**: Always use SSL/TLS encryption in production
- **Environment Variables**: Store sensitive data in environment variables instead of config files
- **Regular Updates**: Keep PHP and MySQL updated to patch security vulnerabilities
- **File Permissions**: Set restrictive file permissions (644 for files, 755 for directories)


### Password Policies

Implement stronger password requirements by modifying the user creation and editing functions:

- Minimum length (8+ characters)
- Complexity requirements (uppercase, lowercase, numbers, special characters)
- Regular password rotation


## 🚀 Future Development

### Planned Features

- **Mobile Application**: Native mobile apps for on-the-floor inspections
- **API Integration**: RESTful API for integration with other systems
- **Barcode/QR Scanning**: Scan product codes directly into the system
- **Advanced Analytics**: Statistical process control and trend analysis
- **Document Management**: Attach technical specifications and procedures to products


## 📁 Project Structure

```
qc-tracker/
├── index.php                 # Main entry point and dashboard
├── login.php                 # User authentication
├── config.php                # Database and application configuration
├── functions.php             # Shared functions and utilities
├── add_record.php            # Create new QC records
├── records.php               # List and filter QC records
├── view_record.php           # Detailed view of a single record
├── edit_record.php           # Modify existing records
├── reports.php               # Generate and display reports
├── admin.php                 # Admin dashboard
├── user_management.php       # User administration
├── add_user.php              # Create new users
├── edit_user.php             # Modify user accounts
├── product_management.php    # Product administration
├── add_product.php           # Create new products
├── edit_product.php          # Modify product details
├── export.php                # Data export functionality
├── setup_admin.php           # Initial admin setup (delete after use)
├── styles.css                # Application styling
└── database.sql              # Database schema and initial data
```

## ❓ FAQ

### General Questions

**Q: Is this application suitable for regulated industries (e.g., pharmaceuticals, medical devices)?**A: The basic version provides fundamental QC tracking, but regulated industries may need additional validation and documentation features to meet compliance requirements.

**Q: Can multiple inspectors use the system simultaneously?**A: Yes, the application supports concurrent users with different permission levels.

**Q: Is there a limit to how many records can be stored?**A: The limit depends on your MySQL database configuration and server storage capacity.

### Technical Questions

**Q: Can I integrate this with our ERP system?**A: The current version doesn't have built-in API integration, but you could develop custom connectors using the database structure.

**Q: Does the application work on mobile devices?**A: The interface is responsive and works on tablets and smartphones, though a dedicated mobile app would provide a better experience for field use.

**Q: Can we host this in the cloud?**A: Yes, the application can be hosted on any server with PHP and MySQL support, including cloud providers.

## 📞 Support and Contact

For support inquiries, feature requests, or bug reports, please:

- Open an issue on the GitHub repository
- Contact the development team at [senior.dev.info@proton.me]



## 📜 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- [PHP](https://www.php.net/) - The backbone of our application
- [MySQL](https://www.mysql.com/) - Reliable database management
- All contributors who have helped improve this project


**Create a MySQL database**

```sql
CREATE DATABASE qc_tracker;
```


**Import the database schema**

```shellscript
mysql -u username -p qc_tracker &lt; database.sql
```


**Configure database connection**

- Edit the `config.php` file with your database credentials:


```php
$db_host = 'localhost';
$db_user = 'your_username';
$db_pass = 'your_password';
$db_name = 'qc_tracker';
```


**Set up the admin user**

- Access the setup script via browser: `http://yoursite.com/setup_admin.php`
- This will create an admin user with credentials:

- Username: admin
- Password: admin



- **Important**: Delete the setup_admin.php file after creating the admin user



**Set proper permissions**

```shellscript
chmod 755 -R /path/to/qc-tracker
chmod 777 -R /path/to/qc-tracker/uploads (if applicable)
```


**Access the application**

- Navigate to `http://yoursite.com/qc-tracker/` in your web browser
- Log in with the admin credentials