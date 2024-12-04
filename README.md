# Clinic Management System

![Clinic Hero Image](image.png)

## Description

Cilnic is revolutionizing healthcare accessibility by providing a seamless platform where anyone,
regardless of their age or location, can connect with healthcare professionals. Our platform enables
users to search for doctors, book appointments, and complete payments online, all in one place.
We believe that quality healthcare should be easily accessible to everyone, and our platform is
designed to bridge the gap between patients and healthcare providers through technology.

## Setup Instructions

1. Ensure MySQL is running on port 3306 with no password
   - To modify MySQL connection settings, edit `includes/dbh.inc.php`
2. Create a database named 'clinic'
   ```sql
   CREATE DATABASE clinic;
   USE clinic;
   ```
3. Import the database schema by executing the `db.sql` file in MySQL server

## Installation Requirements

1. WAMP or XAMPP server
2. An IDE (VSCode, Netbeans, PHPStorm, etc.)

3. Docker Setup

## How to Use

### For Patients

1. Initial access is limited to guest privileges (no appointment booking or profile access)
2. To create an account:
   - Click "Register" in the top right corner
   - Select "Create Account"
   - Fill in your details
3. Login with your credentials
4. After logging in, you can:
   - Book appointments
   - Access your profile page
   - Apply to become a doctor

### For Doctors

- Doctors can apply through the platform to join the medical community
- Once approved, they can manage appointments and patient records

## Key Features

1. Easy Connection with Local Doctors
   - Find and book appointments with doctors in your area
2. Medical History Access
   - View your diagnosis history
   - Access prescribed treatments
3. Doctor Registration
   - Medical professionals can join the platform
   - Help serve the local community

## Future Features

Check our GitHub issues page for upcoming features and enhancements. Stay tuned!

## Database Configuration

The default MySQL connection settings are:

- Port: 3306
- Password: none
- Configuration file location: `includes/dbh.inc.php`

## Contributing

Feel free to contribute to this project. Check our GitHub issues for areas where help is needed.

If you encounter any issues or have questions, please open an issue on our GitHub repository.
