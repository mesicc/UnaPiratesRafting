<?php
$valid_credentials = [
    'unapirat' => '$2y$10$Psx5cfl93p4VLpAEsJv2tO7U0me/L8evQuK.sSY8SfF8vHzpWfaN.', // Generated with phpsandbox i password_hash(), Sifra je tatahadi
];

$realm = 'Secure Area';

if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="' . $realm . '"');
    header('HTTP/1.0 401 Unauthorized');
    die('Please enter your username and password');
}

$username_auth = $_SERVER['PHP_AUTH_USER'];
$password_auth = $_SERVER['PHP_AUTH_PW'] ?? '';

if (!array_key_exists($username_auth, $valid_credentials) || 
    !password_verify($password_auth, $valid_credentials[$username_auth])) {
    header('WWW-Authenticate: Basic realm="' . $realm . '"');
    header('HTTP/1.0 401 Unauthorized');
    die('Invalid credentials');
}

// Database configuration
require "./db/data.php";
require './php_mailer/Exception.php';
require './php_mailer/PHPMailer.php';
require './php_mailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Email configuration - Update these with your SMTP settings
$smtp_host = 'smtp.gmail.com'; // or your SMTP server
$smtp_port = 587;
$smtp_username = 'unapirates@gmail.com';
$smtp_password = 'abks dhpj qslr fttb';
$from_email = 'unapirates@gmail.com';
$from_name = 'Una Pirates';

// Initialize variables
$message = '';
$filter = $_GET['filter'] ?? 'all'; // all, confirmed, cancelled
$activity_filter = $_GET['activity'] ?? 'all'; // all, rafting, kajak
$sort_order = $_GET['sort'] ?? 'asc'; // asc, desc

// Handle reservation cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_reservation'])) {
    $reservation_id = intval($_POST['reservation_id']);
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Get reservation details before cancelling
        $stmt = $pdo->prepare("SELECT * FROM Reservations WHERE reservation_id = :id AND is_confirmed = TRUE");
        $stmt->bindParam(':id', $reservation_id);
        $stmt->execute();
        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($reservation) {
            // Update reservation status to cancelled
            $update_stmt = $pdo->prepare("UPDATE Reservations SET is_confirmed = FALSE WHERE reservation_id = :id");
            $update_stmt->bindParam(':id', $reservation_id);
            
            if ($update_stmt->execute()) {
                // Send cancellation email
                if (sendCancellationEmail($reservation)) {
                    header("Location: " . $_SERVER['PHP_SELF']);
                } else {
                    $message = "<div class='message warning'>⚠️ Reservation #{$reservation_id} cancelled but email could not be sent.</div>";
                }
            }
        } else {
            $message = "<div class='message error'>❌ Reservation not found or already cancelled.</div>";
        }
    } catch (PDOException $e) {
        $message = "<div class='message error'>❌ Database error: " . $e->getMessage() . "</div>";
    }
}

// Handle reservation deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_reservation'])) {
    $reservation_id = intval($_POST['reservation_id']);
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Get reservation details before deleting (for logging/confirmation)
        $stmt = $pdo->prepare("SELECT * FROM Reservations WHERE reservation_id = :id");
        $stmt->bindParam(':id', $reservation_id);
        $stmt->execute();
        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($reservation) {
            // Delete the reservation
            $delete_stmt = $pdo->prepare("DELETE FROM Reservations WHERE reservation_id = :id");
            $delete_stmt->bindParam(':id', $reservation_id);
            
            if ($delete_stmt->execute()) {
                header("Location: " . $_SERVER['PHP_SELF']);
            } else {
                $message = "<div class='message error'>❌ Failed to delete reservation #{$reservation_id}.</div>";
            }
        } else {
            $message = "<div class='message error'>❌ Reservation not found.</div>";
        }
    } catch (PDOException $e) {
        $message = "<div class='message error'>❌ Database error: " . $e->getMessage() . "</div>";
    }
}

// Function to send cancellation email
function sendCancellationEmail($reservation) {
    global $smtp_host, $smtp_port, $smtp_username, $smtp_password, $from_email, $from_name;
    
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = $smtp_host;
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_username;
        $mail->Password = $smtp_password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $smtp_port;
        
        // Recipients
        $mail->setFrom($from_email, $from_name);
        $mail->addAddress($reservation['email'], $reservation['name_on_reservation']);
        
        // Content
        $mail->isHTML(true);
        $activity_type = $reservation['activity_type'] ?? 'Adventure';
        $mail->Subject = 'Reservation Cancelled - ' . $activity_type . ' Adventure';
        
        $email_body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #dc3545, #c82333); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
                .detail-row { display: flex; justify-content: space-between; margin: 10px 0; padding: 8px 0; border-bottom: 1px solid #eee; }
                .label { font-weight: bold; color: #555; }
                .value { color: #333; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
                .alert-box { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 8px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>❌ Reservation Cancelled</h1>
                    <p>Important notification about your booking</p>
                </div>
                <div class='content'>
                    <p>Dear {$reservation['name_on_reservation']},</p>
                    <div class='alert-box'>
                        <strong>📢 Important Notice:</strong> We regret to inform you that your " . strtolower($activity_type) . " reservation has been <strong>cancelled</strong> by our team.
                    </div>
                    <div class='details'>
                        <h3>📋 Cancelled Reservation Details</h3>
                        <div class='detail-row'>
                            <span class='label'>Reservation ID:</span>
                            <span class='value'>#{$reservation['reservation_id']}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='label'>Activity Type:</span>
                            <span class='value'>{$activity_type}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='label'>Name:</span>
                            <span class='value'>{$reservation['name_on_reservation']}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='label'>Email:</span>
                            <span class='value'>{$reservation['email']}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='label'>Phone:</span>
                            <span class='value'>{$reservation['phone_number']}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='label'>Route:</span>
                            <span class='value'>{$reservation['rafting_route']}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='label'>Original Date:</span>
                            <span class='value'>" . date('F j, Y', strtotime($reservation['reservation_date'])) . "</span>
                        </div>
                        <div class='detail-row'>
                            <span class='label'>Number of People:</span>
                            <span class='value'>{$reservation['amount_of_people']}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='label'>Status:</span>
                            <span class='value' style='color: #dc3545; font-weight: bold;'>❌ CANCELLED</span>
                        </div>
                    </div>
                    <h3>📞 Contact Us Immediately</h3>
                    <p>If you have any questions or concerns, please don't hesitate to contact us:</p>
                    <ul>
                        <li>📧 Email: <strong>{$from_email}</strong></li>
                        <li>📱 Phone: <strong>+387 61 782 339 - Una Pirates</strong></li>
                        <li>🆔 Reference: <strong>#{$reservation['reservation_id']}</strong></li>
                    </ul>
                    <div class='footer'>
                        <p>Sincerely,<br><strong>The Adventure Team</strong></p>
                        <p><em>This cancellation notice was sent automatically.</em></p>
                    </div>
                </div>
            </div>
        </body>
        </html>";
        
        $mail->Body = $email_body;
        $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $email_body));
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

// Fetch reservations based on filter and sort
function getReservations($filter, $activity_filter, $sort_order) {
    global $host, $dbname, $username, $password;
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Ensure activity_type column exists
        ensureActivityTypeColumn($pdo);
        
        $where_conditions = [];
        $params = [];
        
        // Status filter
        if ($filter === 'confirmed') {
            $where_conditions[] = 'is_confirmed = :confirmed';
            $params[':confirmed'] = true;
        } elseif ($filter === 'cancelled') {
            $where_conditions[] = 'is_confirmed = :cancelled';
            $params[':cancelled'] = false;
        }
        
        // Activity filter
        if ($activity_filter !== 'all') {
            if ($activity_filter === 'rafting') {
                $where_conditions[] = "(activity_type = :rafting OR activity_type IS NULL)";
                $params[':rafting'] = 'Rafting';
            } elseif ($activity_filter === 'kajak') {
                $where_conditions[] = "activity_type = :kajak";
                $params[':kajak'] = 'Kajak';
            }
        }
        
        $where_clause = '';
        if (!empty($where_conditions)) {
            $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
        }
        
        $order_clause = "ORDER BY reservation_date " . ($sort_order === 'desc' ? 'DESC' : 'ASC');
        
        $sql = "SELECT * FROM Reservations $where_clause $order_clause";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getReservations: " . $e->getMessage());
        return [];
    }
}

// Get statistics
function getStatistics() {
    global $host, $dbname, $username, $password;
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Ensure activity_type column exists
        ensureActivityTypeColumn($pdo);
        
        $stats = [
            'total' => 0,
            'confirmed' => 0,
            'cancelled' => 0,
            'rafting' => 0,
            'kajak' => 0
        ];
        
        // Basic stats
        $total = $pdo->query("SELECT COUNT(*) FROM Reservations")->fetchColumn();
        $confirmed = $pdo->query("SELECT COUNT(*) FROM Reservations WHERE is_confirmed = TRUE")->fetchColumn();
        $cancelled = $pdo->query("SELECT COUNT(*) FROM Reservations WHERE is_confirmed = FALSE")->fetchColumn();
        
        $stats['total'] = $total;
        $stats['confirmed'] = $confirmed;
        $stats['cancelled'] = $cancelled;
        
        // Activity stats
        $rafting = $pdo->query("SELECT COUNT(*) FROM Reservations WHERE activity_type = 'Rafting' OR activity_type IS NULL")->fetchColumn();
        $kajak = $pdo->query("SELECT COUNT(*) FROM Reservations WHERE activity_type = 'Kajak'")->fetchColumn();
        
        $stats['rafting'] = $rafting;
        $stats['kajak'] = $kajak;
        
        return $stats;
    } catch (PDOException $e) {
        error_log("Database error in getStatistics: " . $e->getMessage());
        return [
            'total' => 0,
            'confirmed' => 0,
            'cancelled' => 0,
            'rafting' => 0,
            'kajak' => 0
        ];
    }
}

// Function to ensure activity_type column exists
function ensureActivityTypeColumn($pdo) {
    try {
        // Check if column exists
        $checkColumn = $pdo->query("SHOW COLUMNS FROM Reservations LIKE 'activity_type'");
        if ($checkColumn->rowCount() == 0) {
            // Add column if it doesn't exist
            $pdo->exec("ALTER TABLE Reservations ADD COLUMN activity_type VARCHAR(50) DEFAULT 'Rafting'");
            // Update existing records
            $pdo->exec("UPDATE Reservations SET activity_type = 'Rafting' WHERE activity_type IS NULL");
        }
    } catch (Exception $e) {
        error_log("Error ensuring activity_type column: " . $e->getMessage());
    }
}

$reservations = getReservations($filter, $activity_filter, $sort_order);
$statistics = getStatistics();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UNA PIRATES - Admin Panel</title>
    <link rel="icon" href="materijali/logo.webp" type="image/x-icon" />
     <link rel="stylesheet" href="./css/admin.css" />
    <script src="https://kit.fontawesome.com/53f832df41.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="header-title">
                <img src="materijali/logo.webp" alt="Una Pirates Logo" class="logo-header">
                <h1>UNA PIRATES</h1>
            </div>
            <div class="admin-badge">Admin Panel</div>
        </div>
    </div>

    <div class="container">
        <?php echo $message; ?>

        <!-- Statistics -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-card-content">
                    <div class="stat-number"><?php echo $statistics['total']; ?></div>
                    <div class="stat-label">Ukupno rezervacija</div>
                </div>
            </div>
            <div class="stat-card confirmed">
                <div class="stat-card-content">
                    <div class="stat-number"><?php echo $statistics['confirmed']; ?></div>
                    <div class="stat-label">Aktivne</div>
                </div>
            </div>
            <div class="stat-card cancelled">
                <div class="stat-card-content">
                    <div class="stat-number"><?php echo $statistics['cancelled']; ?></div>
                    <div class="stat-label">Otkazane</div>
                </div>
            </div>
            <div class="stat-card rafting">
                <div class="stat-card-content">
                    <div class="stat-number"><?php echo $statistics['rafting']; ?></div>
                    <div class="stat-label">🚣 Rafting</div>
                </div>
            </div>
            <div class="stat-card kajak">
                <div class="stat-card-content">
                    <div class="stat-number"><?php echo $statistics['kajak']; ?></div>
                    <div class="stat-label">🛶 Kajak</div>
                </div>
            </div>
        </div>

        <!-- Controls -->
        <div class="controls">
            <div class="filter-group">
                <label for="filter">Status:</label>
                <select id="filter" onchange="updateFilter()">
                    <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>Sve rezervacije</option>
                    <option value="confirmed" <?php echo $filter === 'confirmed' ? 'selected' : ''; ?>>Samo aktivne</option>
                    <option value="cancelled" <?php echo $filter === 'cancelled' ? 'selected' : ''; ?>>Samo otkazane</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="activity">Aktivnost:</label>
                <select id="activity" onchange="updateFilter()">
                    <option value="all" <?php echo $activity_filter === 'all' ? 'selected' : ''; ?>>Sve aktivnosti</option>
                    <option value="rafting" <?php echo $activity_filter === 'rafting' ? 'selected' : ''; ?>>🚣 Rafting</option>
                    <option value="kajak" <?php echo $activity_filter === 'kajak' ? 'selected' : ''; ?>>🛶 Kajak</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="sort">Sortiraj po datumu:</label>
                <select id="sort" onchange="updateFilter()">
                    <option value="asc" <?php echo $sort_order === 'asc' ? 'selected' : ''; ?>>Najstarije prvo</option>
                    <option value="desc" <?php echo $sort_order === 'desc' ? 'selected' : ''; ?>>Najnovije prvo</option>
                </select>
            </div>
        </div>

        <!-- Reservations Table -->
        <div class="reservations-table">
            <div class="table-header">
                <h2>Rezervacije (<?php echo count($reservations); ?> pronađeno)</h2>
            </div>

            <?php if (empty($reservations)): ?>
                <div class="no-data">
                    <i class="fas fa-clipboard-list"></i>
                    <p>Nema rezervacija koje odgovaraju vašim kriterijumima.</p>
                </div>
            <?php else: ?>
                <!-- Desktop Table -->
                <table class="desktop-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Aktivnost</th>
                            <th>Ime</th>
                            <th>Email</th>
                            <th>Telefon</th>
                            <th>Ruta</th>
                            <th>Datum</th>
                            <th>Osobe</th>
                            <th>Status</th>
                            <th>Akcije</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $reservation): ?>
                            <tr>
                                <td>#<?php echo $reservation['reservation_id']; ?></td>
                                <td>
                                    <?php 
                                    $activity_type = $reservation['activity_type'] ?? 'Rafting';
                                    echo htmlspecialchars($activity_type);
                                    ?>
                                    <span class="activity-badge <?php echo strtolower($activity_type) === 'kajak' ? 'activity-kajak' : 'activity-rafting'; ?>">
                                        <?php echo strtolower($activity_type) === 'kajak' ? '🛶' : '🚣'; ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($reservation['name_on_reservation']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['email']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['phone_number']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['rafting_route']); ?></td>
                                <td><?php echo date('M j, Y', strtotime($reservation['reservation_date'])); ?></td>
                                <td><?php echo $reservation['amount_of_people']; ?></td>
                                <td>
                                    <span class="status-badge <?php echo $reservation['is_confirmed'] ? 'status-confirmed' : 'status-cancelled'; ?>">
                                        <?php echo $reservation['is_confirmed'] ? 'Aktivna' : 'Otkazana'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <?php if ($reservation['is_confirmed']): ?>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Da li ste sigurni da želite da OTKAŽETE ovu rezervaciju? Klijent će biti obavešten putem email-a.\n\nReservacija: #<?php echo $reservation['reservation_id']; ?>\nKlijent: <?php echo htmlspecialchars($reservation['name_on_reservation']); ?>\nDatum: <?php echo date('M j, Y', strtotime($reservation['reservation_date'])); ?>')">
                                                <input type="hidden" name="reservation_id" value="<?php echo $reservation['reservation_id']; ?>">
                                                <button type="submit" name="cancel_reservation" class="cancel-btn">
                                                    ⚠️ Otkaži
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <button class="cancel-btn" disabled>
                                                ❌ Otkazana
                                            </button>
                                        <?php endif; ?>
                                        
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Da li ste sigurni da želite da OBRIŠETE ovu rezervaciju? Ova akcija se ne može poništiti!\n\nReservacija: #<?php echo $reservation['reservation_id']; ?>\nKlijent: <?php echo htmlspecialchars($reservation['name_on_reservation']); ?>\nEmail: <?php echo htmlspecialchars($reservation['email']); ?>')">
                                            <input type="hidden" name="reservation_id" value="<?php echo $reservation['reservation_id']; ?>">
                                            <button type="submit" name="delete_reservation" class="delete-btn">
                                                🗑️ Obriši
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Mobile Cards -->
                <div class="mobile-cards">
                    <?php foreach ($reservations as $reservation): ?>
                        <div class="reservation-card">
                            <div class="card-header">
                                <div class="card-id">#<?php echo $reservation['reservation_id']; ?></div>
                                <div>
                                    <span class="status-badge <?php echo $reservation['is_confirmed'] ? 'status-confirmed' : 'status-cancelled'; ?>">
                                        <?php echo $reservation['is_confirmed'] ? 'Aktivna' : 'Otkazana'; ?>
                                    </span>
                                    <?php $activity_type = $reservation['activity_type'] ?? 'Rafting'; ?>
                                    <span class="activity-badge <?php echo strtolower($activity_type) === 'kajak' ? 'activity-kajak' : 'activity-rafting'; ?>">
                                        <?php echo strtolower($activity_type) === 'kajak' ? '🛶 Kajak' : '🚣 Rafting'; ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="card-row">
                                <div class="card-label">Ime:</div>
                                <div class="card-value"><?php echo htmlspecialchars($reservation['name_on_reservation']); ?></div>
                            </div>
                            <div class="card-row">
                                <div class="card-label">Email:</div>
                                <div class="card-value"><?php echo htmlspecialchars($reservation['email']); ?></div>
                            </div>
                            <div class="card-row">
                                <div class="card-label">Telefon:</div>
                                <div class="card-value"><?php echo htmlspecialchars($reservation['phone_number']); ?></div>
                            </div>
                            <div class="card-row">
                                <div class="card-label">Ruta:</div>
                                <div class="card-value"><?php echo htmlspecialchars($reservation['rafting_route']); ?></div>
                            </div>
                            <div class="card-row">
                                <div class="card-label">Datum:</div>
                                <div class="card-value"><?php echo date('M j, Y', strtotime($reservation['reservation_date'])); ?></div>
                            </div>
                            <div class="card-row">
                                <div class="card-label">Osobe:</div>
                                <div class="card-value"><?php echo $reservation['amount_of_people']; ?></div>
                            </div>
                            
                            <div class="card-actions">
                                <div class="action-buttons">
                                    <?php if ($reservation['is_confirmed']): ?>
                                        <form method="POST" onsubmit="return confirm('Da li ste sigurni da želite da OTKAŽETE ovu rezervaciju? Klijent će biti obavešten putem email-a.\n\nReservacija: #<?php echo $reservation['reservation_id']; ?>\nKlijent: <?php echo htmlspecialchars($reservation['name_on_reservation']); ?>\nDatum: <?php echo date('M j, Y', strtotime($reservation['reservation_date'])); ?>')">
                                            <input type="hidden" name="reservation_id" value="<?php echo $reservation['reservation_id']; ?>">
                                            <button type="submit" name="cancel_reservation" class="cancel-btn">
                                                ⚠️ Otkaži rezervaciju
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button class="cancel-btn" disabled>
                                            ❌ Već otkazana
                                        </button>
                                    <?php endif; ?>
                                    
                                    <form method="POST" onsubmit="return confirm('Da li ste sigurni da želite da OBRIŠETE ovu rezervaciju? Ova akcija se ne može poništiti!\n\nReservacija: #<?php echo $reservation['reservation_id']; ?>\nKlijent: <?php echo htmlspecialchars($reservation['name_on_reservation']); ?>\nEmail: <?php echo htmlspecialchars($reservation['email']); ?>')">
                                        <input type="hidden" name="reservation_id" value="<?php echo $reservation['reservation_id']; ?>">
                                        <button type="submit" name="delete_reservation" class="delete-btn">
                                            🗑️ Obriši rezervaciju
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function updateFilter() {
            const filter = document.getElementById('filter').value;
            const activity = document.getElementById('activity').value;
            const sort = document.getElementById('sort').value;
            
            const url = new URL(window.location);
            url.searchParams.set('filter', filter);
            url.searchParams.set('activity', activity);
            url.searchParams.set('sort', sort);
            
            window.location.href = url.toString();
        }

        //Auto-refresh every 500 seconds to show new reservations
        setTimeout(() => {
            window.location.reload();
        }, 500000); //ne treba ovaj kurac da radi

        // Add loading states for buttons
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const button = this.querySelector('button[type="submit"]');
                if (button && !button.disabled) {
                    button.style.opacity = '0.7';
                    button.style.pointerEvents = 'none';
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + button.textContent;
                }
            });
        });
    </script>
</body>
</html>

