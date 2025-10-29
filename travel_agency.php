<?php
session_start();

// Database configuration
$host = 'localhost';
$dbname = 'travel_agency';
$username = 'root';
$password = '';

// Create database connection
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Initialize message variable
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Get current page
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Sample destinations data
$destinations = [
    [
        'id' => 1,
        'name' => 'Bali, Indonesia',
        'image' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=400&h=300&fit=crop',
        'price' => '$899',
        'duration' => '7 Days',
        'description' => 'Experience the magical island of Bali with pristine beaches and rich culture.'
    ],
    [
        'id' => 2,
        'name' => 'Paris, France',
        'image' => 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=400&h=300&fit=crop',
        'price' => '$1,299',
        'duration' => '5 Days',
        'description' => 'Discover the City of Light with iconic landmarks and exquisite cuisine.'
    ],
    [
        'id' => 3,
        'name' => 'Tokyo, Japan',
        'image' => 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=400&h=300&fit=crop',
        'price' => '$1,499',
        'duration' => '6 Days',
        'description' => 'Explore the perfect blend of traditional and modern Japanese culture.'
    ],
    [
        'id' => 4,
        'name' => 'Santorini, Greece',
        'image' => 'https://images.unsplash.com/photo-1613395877344-13d4a8e0d49e?w=400&h=300&fit=crop',
        'price' => '$1,199',
        'duration' => '5 Days',
        'description' => 'Enjoy stunning sunsets and white-washed buildings on this Greek paradise.'
    ]
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking'])) {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $destination = htmlspecialchars($_POST['destination']);
    $date = htmlspecialchars($_POST['date']);
    $guests = htmlspecialchars($_POST['guests']);
    
    // Insert into database
    try {
        $stmt = $conn->prepare("INSERT INTO bookings (name, email, destination, travel_date, guests, booking_date) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$name, $email, $destination, $date, $guests]);
        
        $_SESSION['message'] = "Thank you, $name! Your booking request for $destination has been received. We'll contact you at $email shortly.";
        header('Location: ' . $_SERVER['PHP_SELF'] . '?page=home');
        exit;
    } catch(PDOException $e) {
        $_SESSION['message'] = "Error: " . $e->getMessage();
    }
}

// Handle delete booking
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    try {
        $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['message'] = "Booking deleted successfully!";
        header('Location: ' . $_SERVER['PHP_SELF'] . '?page=admin');
        exit;
    } catch(PDOException $e) {
        $_SESSION['message'] = "Error deleting booking: " . $e->getMessage();
    }
}

// Fetch all bookings for admin page
if ($page === 'admin') {
    try {
        $stmt = $conn->query("SELECT * FROM bookings ORDER BY booking_date DESC");
        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $bookings = [];
        $message = "Error fetching bookings: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wanderlust Travel Agency</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-menu a {
            color: white;
            text-decoration: none;
            transition: opacity 0.3s;
        }

        .nav-menu a:hover {
            opacity: 0.8;
        }

        .hero {
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), 
                        url('https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=1200&h=600&fit=crop') center/cover;
            color: white;
            text-align: center;
            padding: 8rem 2rem;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .hero p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
        }

        .btn {
            display: inline-block;
            padding: 0.8rem 2rem;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: transform 0.3s, background 0.3s;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            background: #5568d3;
        }

        .btn-danger {
            background: #e74c3c;
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        .btn-small {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 4rem 2rem;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 3rem;
            color: #333;
        }

        .destinations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .destination-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .destination-card:hover {
            transform: translateY(-10px);
        }

        .destination-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .card-content {
            padding: 1.5rem;
        }

        .card-content h3 {
            margin-bottom: 0.5rem;
            color: #667eea;
        }

        .card-info {
            display: flex;
            justify-content: space-between;
            margin: 1rem 0;
            font-weight: bold;
            color: #666;
        }

        .booking-form {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #333;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }

        .success-message {
            background: #4caf50;
            color: white;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 2rem;
            text-align: center;
        }

        .error-message {
            background: #e74c3c;
            color: white;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 2rem;
            text-align: center;
        }

        .footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 2rem;
            margin-top: 4rem;
        }

        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
        }

        .about-text {
            font-size: 1.1rem;
            line-height: 1.8;
        }

        .about-image {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        /* Admin Table Styles */
        .admin-table {
            width: 100%;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .admin-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .admin-table th {
            background: #667eea;
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: bold;
        }

        .admin-table td {
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }

        .admin-table tr:hover {
            background: #f8f9fa;
        }

        .admin-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-card h3 {
            font-size: 2.5rem;
            color: #667eea;
            margin-bottom: 0.5rem;
        }

        .stat-card p {
            color: #666;
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .about-content {
                grid-template-columns: 1fr;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .nav-menu {
                gap: 1rem;
                font-size: 0.9rem;
            }

            .admin-table {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="nav-container">
            <div class="logo">✈️ Wanderlust Travel</div>
            <ul class="nav-menu">
                <li><a href="?page=home">Home</a></li>
                <li><a href="?page=destinations">Destinations</a></li>
                <li><a href="?page=booking">Book Now</a></li>
                <li><a href="?page=about">About</a></li>
                <li><a href="?page=admin">📊 Admin</a></li>
            </ul>
        </nav>
    </header>

    <?php if ($page === 'home'): ?>
        <section class="hero">
            <h1>Explore the World with Us</h1>
            <p>Discover amazing destinations and create unforgettable memories</p>
            <a href="?page=destinations" class="btn">View Destinations</a>
        </section>

        <?php if ($message): ?>
            <div class="container">
                <div class="success-message"><?php echo $message; ?></div>
            </div>
        <?php endif; ?>

        <div class="container">
            <h2 class="section-title">Popular Destinations</h2>
            <div class="destinations-grid">
                <?php foreach (array_slice($destinations, 0, 4) as $dest): ?>
                    <div class="destination-card">
                        <img src="<?php echo $dest['image']; ?>" alt="<?php echo $dest['name']; ?>">
                        <div class="card-content">
                            <h3><?php echo $dest['name']; ?></h3>
                            <p><?php echo $dest['description']; ?></p>
                            <div class="card-info">
                                <span>⏱️ <?php echo $dest['duration']; ?></span>
                                <span>💰 <?php echo $dest['price']; ?></span>
                            </div>
                            <a href="?page=booking" class="btn">Book Now</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    <?php elseif ($page === 'destinations'): ?>
        <div class="container">
            <h2 class="section-title">All Destinations</h2>
            <div class="destinations-grid">
                <?php foreach ($destinations as $dest): ?>
                    <div class="destination-card">
                        <img src="<?php echo $dest['image']; ?>" alt="<?php echo $dest['name']; ?>">
                        <div class="card-content">
                            <h3><?php echo $dest['name']; ?></h3>
                            <p><?php echo $dest['description']; ?></p>
                            <div class="card-info">
                                <span>⏱️ <?php echo $dest['duration']; ?></span>
                                <span>💰 <?php echo $dest['price']; ?></span>
                            </div>
                            <a href="?page=booking" class="btn">Book Now</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    <?php elseif ($page === 'booking'): ?>
        <div class="container">
            <h2 class="section-title">Book Your Dream Vacation</h2>
            <form method="POST" class="booking-form">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="destination">Select Destination</label>
                    <select id="destination" name="destination" required>
                        <option value="">Choose a destination...</option>
                        <?php foreach ($destinations as $dest): ?>
                            <option value="<?php echo $dest['name']; ?>"><?php echo $dest['name']; ?> - <?php echo $dest['price']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="date">Travel Date</label>
                    <input type="date" id="date" name="date" required>
                </div>
                <div class="form-group">
                    <label for="guests">Number of Guests</label>
                    <input type="number" id="guests" name="guests" min="1" max="10" required>
                </div>
                <button type="submit" name="booking" class="btn">Submit Booking Request</button>
            </form>
        </div>

    <?php elseif ($page === 'admin'): ?>
        <div class="container">
            <h2 class="section-title">📊 Bookings Dashboard</h2>
            
            <?php if ($message): ?>
                <div class="<?php echo strpos($message, 'Error') !== false ? 'error-message' : 'success-message'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="admin-stats">
                <div class="stat-card">
                    <h3><?php echo isset($bookings) ? count($bookings) : 0; ?></h3>
                    <p>Total Bookings</p>
                </div>
                <div class="stat-card">
                    <h3><?php 
                        if (isset($bookings)) {
                            echo array_sum(array_column($bookings, 'guests'));
                        } else {
                            echo 0;
                        }
                    ?></h3>
                    <p>Total Guests</p>
                </div>
            </div>

            <div class="admin-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Destination</th>
                            <th>Travel Date</th>
                            <th>Guests</th>
                            <th>Booking Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($bookings) && count($bookings) > 0): ?>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td><?php echo $booking['id']; ?></td>
                                    <td><?php echo htmlspecialchars($booking['name']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['email']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['destination']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($booking['travel_date'])); ?></td>
                                    <td><?php echo $booking['guests']; ?></td>
                                    <td><?php echo date('M d, Y H:i', strtotime($booking['booking_date'])); ?></td>
                                    <td>
                                        <a href="?page=admin&delete=<?php echo $booking['id']; ?>" 
                                           class="btn btn-danger btn-small" 
                                           onclick="return confirm('Are you sure you want to delete this booking?')">
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 2rem;">
                                    No bookings found. Start by creating some bookings!
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php elseif ($page === 'about'): ?>
        <div class="container">
            <h2 class="section-title">About Wanderlust Travel</h2>
            <div class="about-content">
                <div class="about-text">
                    <p>Welcome to Wanderlust Travel Agency, your trusted partner in creating unforgettable travel experiences since 2010. We specialize in curating personalized vacation packages that cater to every traveler's unique preferences and dreams.</p>
                    <br>
                    <p>Our team of experienced travel experts has explored the globe to handpick the finest destinations, accommodations, and experiences. Whether you're seeking adventure, relaxation, cultural immersion, or romantic getaways, we're here to turn your travel dreams into reality.</p>
                    <br>
                    <p>With partnerships across the world and a commitment to exceptional customer service, we ensure every journey with us is seamless, memorable, and extraordinary.</p>
                </div>
                <img src="https://images.unsplash.com/photo-1436491865332-7a61a109cc05?w=600&h=400&fit=crop" alt="Travel" class="about-image">
            </div>
        </div>
    <?php endif; ?>

    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> Wanderlust Travel Agency. All rights reserved.</p>
        <p>📧 info@wanderlusttravel.com | 📞 +1 (555) 123-4567</p>
    </footer>
</body>
</html>
