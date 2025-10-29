# travel_agency

Great question! Currently, the bookings are **not saved anywhere** - they just show a success message and then disappear. To actually see and store your bookings, you need to save them to a database.

Let me create an updated version that saves bookings to a MySQL database and adds an admin page to view all bookings:Now I need to provide you with the database setup instructions. Here's what you need to do:

## Step 1: Create the Database

1. **Open phpMyAdmin** in your browser:
   - Go to: `http://localhost/phpmyadmin`

2. **Create a new database:**
   - Click on "New" in the left sidebar
   - Database name: `travel_agency`
   - Click "Create"

## Step 2: Create the Bookings Table

1. Click on your `travel_agency` database in the left sidebar
2. Click on the "SQL" tab at the top
3. Copy and paste this SQL code:

```sql
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    destination VARCHAR(255) NOT NULL,
    travel_date DATE NOT NULL,
    guests INT NOT NULL,
    booking_date DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

4. Click "Go" to execute

## Step 3: Access Your Website

Now you can access:
- **Main website**: `http://localhost/travel_agency.php`
- **Admin Dashboard**: `http://localhost/travel_agency.php?page=admin`

## Features Added:

✅ **Admin Dashboard** - Click "Admin" in the navigation menu to see:
- Total number of bookings
- Total number of guests
- Complete list of all bookings in a table
- Delete bookings option

✅ **Database Storage** - All bookings are now saved to MySQL database

✅ **View All Bookings** - See customer name, email, destination, travel date, number of guests, and when they booked

The bookings are now permanently stored and you can view them anytime in the Admin section!
