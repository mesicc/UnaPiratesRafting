<?php
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
$smtp_username = 'test@gmail.com';
$smtp_password = 'password-goes-here';
$from_email = 'test@gmail.com';
$from_name = 'Una Pirates';
$owner_mail = "owner@gmail.com";
$owner_name = "Owner";

// Initialize variables
$error_message = '';
$success_message = '';

// Rafting routes with pricing
$rafting_routes = [
    'Kostela' => [
        'name' => 'Kostela',
        'price' => 35,
        'original_price' => null,
        'description' => 'Lakša ruta idealna za početnike i porodice sa djecom'
    ],
    'Štrbački Buk' => [
        'name' => 'Štrbački Buk', 
        'price' => 50,
        'original_price' => null,
        'description' => 'Avanturistička ruta sa više uzbuđenja i adrenalina'
    ]
];

// Handle success message from redirect
if (isset($_GET['success']) && $_GET['success'] == '1' && isset($_GET['id'])) {
    $reservation_id = intval($_GET['id']);
    $email_status = $_GET['email'] ?? 'sent';
    
    if ($email_status === 'sent') {
        $success_message = "🎉 Rafting rezervacija je uspješno potvrđena! Vaš ID rezervacije je: #" . $reservation_id . ". Email sa potvrdom je poslan na vašu adresu.";
    } else {
        $success_message = "✅ Rafting rezervacija je uspješno potvrđena! Vaš ID rezervacije je: #" . $reservation_id . ". Nismo mogli poslati email, molimo kontaktirajte nas.";
    }
}

// Function to validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Function to validate phone number (basic validation for international formats)
function validatePhone($phone) {
    // Remove all non-digit characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    // Check if phone number has 10-15 digits
    return strlen($phone) >= 10 && strlen($phone) <= 15;
}

// Function to send confirmation email
function sendConfirmationEmail($reservation, $route_info) {
    global $smtp_host, $smtp_port, $smtp_username, $smtp_password, $from_email, $from_name, $owner_mail, $owner_name;
    
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
        $mail->addCC($owner_mail, $owner_name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Potvrda rezervacije - ' . $route_info['name'] . ' Rafting avantura';
        
        $price_display = $route_info['original_price'] ? 
            "<span style='text-decoration: line-through; color: #999;'>{$route_info['original_price']}€</span> <strong style='color: #4f785e;'>{$route_info['price']}€</strong> (SNIŽENJE!)" : 
            "<strong>{$route_info['price']}€</strong>";

        $email_body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #4f785e 0%, #34624d 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
                .detail-row { display: flex; justify-content: space-between; margin: 10px 0; padding: 8px 0; border-bottom: 1px solid #eee; }
                .label { font-weight: bold; color: #555; }
                .value { color: #333; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
                .highlight { background: #e8f5e8; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #4f785e; }
                .price-highlight { background: #e8f5e8; padding: 10px; border-radius: 5px; text-align: center; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎉 Rafting rezervacija potvrđena!</h1>
                    <p>Vaša {$route_info['name']} avantura je spremna!</p>
                </div>
                <div class='content'>
                    <p>Poštovani/a {$reservation['name_on_reservation']},</p>
                    <div class='highlight'>
                        <strong>🎊 Odlične vijesti!</strong> Vaša rafting rezervacija je <strong>automatski potvrđena</strong>. Spremni ste za nevjerovatnu avanturu!
                    </div>
                    <div class='details'>
                        <h3>📋 Detalji vaše rezervacije</h3>
                        <div class='detail-row'>
                            <span class='label'>ID rezervacije:</span>
                            <span class='value'>#{$reservation['reservation_id']}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='label'>Tip aktivnosti:</span>
                            <span class='value'>🚣 Rafting</span>
                        </div>
                        <div class='detail-row'>
                            <span class='label'>Ruta:</span>
                            <span class='value'>{$route_info['name']}</span>
                        </div>
                        <div class='price-highlight'>
                            <strong>Cena po osobi: $price_display</strong>
                        </div>
                        <div class='detail-row'>
                            <span class='label'>Ime:</span>
                            <span class='value'>{$reservation['name_on_reservation']}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='label'>Email:</span>
                            <span class='value'>{$reservation['email']}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='label'>Telefon:</span>
                            <span class='value'>{$reservation['phone_number']}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='label'>Datum:</span>
                            <span class='value'>" . date('d.m.Y', strtotime($reservation['reservation_date'])) . "</span>
                        </div>
                        <div class='detail-row'>
                            <span class='label'>Broj osoba:</span>
                            <span class='value'>{$reservation['amount_of_people']}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='label'>Ukupna cena:</span>
                            <span class='value' style='font-size: 1.2em; font-weight: bold; color: #4f785e;'>" . ($reservation['amount_of_people'] * $route_info['price']) . "€</span>
                        </div>
                        <div class='detail-row'>
                            <span class='label'>Status:</span>
                            <span class='value' style='color: #4f785e; font-weight: bold;'>✅ POTVRĐENO</span>
                        </div>
                    </div>
                    
                    <h3>📝 Šta ponijeti</h3>
                    <ul>
                        <li><strong>Dolazak:</strong> Dođite 30 minuta prije zakazanog vremena</li>
                        <li><strong>Odjeća:</strong> Brzo-sušeća odjeća i sigurna obuća</li>
                        <li><strong>Zaštita:</strong> Krema za sunce, kapa i naočare</li>
                        <li><strong>Voda:</strong> Flaša za vodu (mi obezbijedimo užinu)</li>
                        <li><strong>Lične stvari:</strong> Vodootporna torba za vrijednosti</li>
                    </ul>
                    
                    <h3>📞 Kontakt</h3>
                    <p>Za izmjene ili otkazivanje kontaktirajte nas najmanje 24h unaprijed:</p>
                    <ul>
                        <li>📧 Email: <strong>{$from_email}</strong></li>
                        <li>📱 Telefon: <strong>(555) 123-RAFT</strong></li>
                        <li>🆔 Referenca: <strong>#{$reservation['reservation_id']}</strong></li>
                    </ul>
                    
                    <div class='footer'>
                        <p>Srdačno,<br><strong>Una Pirates Rafting Tim</strong></p>
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
        error_log("Email greška: {$mail->ErrorInfo}");
        return false;
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = trim($_POST['email']);
    $name_on_reservation = trim($_POST['name_on_reservation']);
    $phone_number = trim($_POST['phone_number']);
    $rafting_route = trim($_POST['rafting_route']);
    $reservation_date = $_POST['reservation_date'];
    $amount_of_people = intval($_POST['amount_of_people']);

    // Validation
    $errors = [];

    if (empty($email)) {
        $errors[] = "Email je obavezan.";
    } elseif (!validateEmail($email)) {
        $errors[] = "Unesite valjan email.";
    }

    if (empty($name_on_reservation)) {
        $errors[] = "Ime na rezervaciji je obavezno.";
    }

    if (empty($phone_number)) {
        $errors[] = "Broj telefona je obavezan.";
    } elseif (!validatePhone($phone_number)) {
        $errors[] = "Unesite valjan broj telefona (10-15 cifara).";
    }

    if (empty($rafting_route)) {
        $errors[] = "Molimo izaberite rafting rutu.";
    }

    if (empty($reservation_date)) {
        $errors[] = "Datum rezervacije je obavezan.";
    } elseif (strtotime($reservation_date) < strtotime('today')) {
        $errors[] = "Datum ne može biti u prošlosti.";
    }

    if ($amount_of_people < 1 || $amount_of_people > 60) {
        $errors[] = "Broj osoba mora biti između 1 i 60.";
    }

    // If no validation errors, proceed with database insertion
    if (empty($errors)) {
        try {
            // Create PDO connection
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]);

            // Prepare SQL statement - NOTE: is_confirmed is now set to TRUE by default
            $sql = "INSERT INTO Reservations (email, name_on_reservation, phone_number, rafting_route, reservation_date, amount_of_people, is_confirmed) 
                    VALUES (:email, :name_on_reservation, :phone_number, :rafting_route, :reservation_date, :amount_of_people, TRUE)";
            
            $stmt = $pdo->prepare($sql);

            // Execute the statement
            $success = $stmt->execute([
                ':email' => $email,
                ':name_on_reservation' => $name_on_reservation,
                ':phone_number' => $phone_number,
                ':rafting_route' => $rafting_route,
                ':reservation_date' => $reservation_date,
                ':amount_of_people' => $amount_of_people
            ]);

            if ($success) {
                $reservation_id = $pdo->lastInsertId();

                // Prepare reservation data for email
                $reservation_data = [
                    'reservation_id' => $reservation_id,
                    'email' => $email,
                    'name_on_reservation' => $name_on_reservation,
                    'phone_number' => $phone_number,
                    'rafting_route' => $rafting_route,
                    'reservation_date' => $reservation_date,
                    'amount_of_people' => $amount_of_people
                ];

                // Get route info for email
                $route_info = $rafting_routes[$rafting_route] ?? $rafting_routes['Kostela'];

                // Send confirmation email
                $email_sent = sendConfirmationEmail($reservation_data, $route_info);

                // Redirect with success status and email status
                $email_param = $email_sent ? 'sent' : 'failed';
                header("Location: " . $_SERVER['PHP_SELF'] . "?success=1&id=" . $reservation_id . "&email=" . $email_param);
                exit();
            } else {
                $error_message = "Greška pri čuvanju rezervacije. Molimo pokušajte ponovo.";
            }
        } catch (PDOException $e) {
            $error_message = "Greška baze podataka: " . $e->getMessage();
            error_log("Database error in reservation.php: " . $e->getMessage());
        } catch (Exception $e) {
            $error_message = "Sistemska greška: " . $e->getMessage();
            error_log("General error in reservation.php: " . $e->getMessage());
        }
    } else {
        $error_message = implode("<br>", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UNA PIRATES - Rafting Rezervacija</title>
    <link rel="icon" href="materijali/logo.webp" type="image/x-icon" />
    <link rel="stylesheet" href="./css/reservation.css"/>
    <script src="https://kit.fontawesome.com/53f832df41.js" crossorigin="anonymous"></script>
</head>
<body>
    <a href="index.html" class="back-to-home">
        <i class="fas fa-arrow-left"></i> Nazad na početnu
    </a>

    <div class="container">
        <div class="header">
            <div class="header-content">
                <img src="materijali/logo.webp" alt="Una Pirates Logo" class="logo-header">
                <h1>UNA PIRATES</h1>
                <p>🚣 Rezervišite svoju rafting avanturu danas!</p>
            </div>
        </div>

        <div class="instant-confirmation">
            ⚡ Trenutna potvrda - Vaša rafting rezervacija će biti odmah potvrđena!
        </div>

        <div class="form-container">
            <?php if (!empty($error_message)): ?>
                <div class="message-box error-box">
                    <strong>⚠️ Greška:</strong><br>
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <div class="message-box success-box">
                    <strong>🎉 Potvrđeno!</strong><br>
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <?php if (empty($success_message)): ?>
                <div class="info-box">
                    <strong>🚣 Rafting rezervacija:</strong> Kada pošaljete ovaj formular, vaša rafting rezervacija će biti automatski potvrđena i odmah ćete dobiti email sa svim detaljima!
                </div>

                <div class="route-selection">
                    <h3>🚣 Dostupne Rafting Rute</h3>
                    <div class="route-options">
                        <div class="route-option">
                            <h4>⚡ Štrbački Buk <span class="badge-popular">POPULARNO</span></h4>
                            <div class="route-price">50€ po osobi + 8€ Ulaz NP Una</div>
                            <ul>
                                <li>Osnovna rafting oprema</li>
                                <li>Spektakularni vodopadi</li>
                                <li>Adrenalin i uzbuđenje</li>
                                <li>Prekrasni prirodni krajolici</li>
                            </ul>
                        </div>
                        <div class="route-option">
                            <h4>🌊 Kostela</h4>
                            <div class="route-price">35€ po osobi</div>
                            <ul>
                                <li>Osnovna rafting oprema</li>
                                <li>Lakša ruta - idealna za početnike</li>
                                <li>Porodična avantura</li>
                                <li>Prekrasni prirodni krajolici</li>
                                <li>Odlične fotografije i uspomene</li>
                            </ul>
                        </div>
                        
                    </div>
                </div>

                <h2 class="form-section-title">Rafting rezervacija</h2>
                <form method="POST" action="" id="reservationForm">
                    <div class="form-group">
                        <label for="email">Email adresa <span class="required">*</span></label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="name_on_reservation">Ime na rezervaciji <span class="required">*</span></label>
                        <input type="text" id="name_on_reservation" name="name_on_reservation" value="<?php echo htmlspecialchars($name_on_reservation ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="phone_number">Broj telefona <span class="required">*</span></label>
                        <input type="tel" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($phone_number ?? ''); ?>" placeholder="npr. +387-61-123-456" required>
                    </div>

                    <div class="form-group">
                        <label for="rafting_route">Izaberite rutu <span class="required">*</span></label>
                        <select id="rafting_route" name="rafting_route" required>
                            <option value="">Izaberite rutu...</option>
                            <option value="Štrbački Buk" <?php echo (isset($rafting_route) && $rafting_route === 'Štrbački Buk') ? 'selected' : ''; ?>>
                                ⚡ Štrbački Buk - 50€ po osobi + 8€ NP Una 
                            </option>
                            <option value="Kostela" <?php echo (isset($rafting_route) && $rafting_route === 'Kostela') ? 'selected' : ''; ?>>
                                🌊 Kostela - 35€ po osobi
                            </option>
                        </select>
                        <div class="route-info" id="route-description">
                            Izaberite željenu rafting rutu
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="reservation_date">Datum rezervacije <span class="required">*</span></label>
                        <input type="date" id="reservation_date" name="reservation_date" value="<?php echo htmlspecialchars($reservation_date ?? ''); ?>" min="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="amount_of_people">Broj osoba <span class="required">*</span></label>
                        <input type="number" id="amount_of_people" name="amount_of_people" value="<?php echo htmlspecialchars($amount_of_people ?? ''); ?>" min="1" max="60" required>
                        <div class="route-info">
                            Minimum 1 osoba, maksimum 60 osoba po rezervaciji
                        </div>
                        <div class="total-price" id="total-price" style="margin-top: 10px; font-weight: bold; color: #4f785e;">
                            Ukupna cijena: <span id="price-amount">35€</span>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn" id="submitBtn">
                        <i class="fas fa-water"></i> Rezerviši rafting - Trenutna potvrda!
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Route selection and price calculation
        const routeSelect = document.getElementById('rafting_route');
        const amountInput = document.getElementById('amount_of_people');
        const routeDescription = document.getElementById('route-description');
        const totalPriceElement = document.getElementById('price-amount');

        const routeDescriptions = {
            'Kostela': 'Lakša ruta idealna za početnike i porodice sa djecom',
            'Štrbački Buk': 'Avanturistička ruta sa više uzbuđenja i adrenalina'
        };

        const routePrices = {
            'Kostela': 35,
            'Štrbački Buk': 58
        };

        function updateRouteInfo() {
            const selectedRoute = routeSelect.value;
            const amount = parseInt(amountInput.value) || 1;
            const pricePerPerson = routePrices[selectedRoute] || 35;

            routeDescription.textContent = routeDescriptions[selectedRoute] || 'Izaberite željenu rafting rutu';
            totalPriceElement.textContent = (amount * pricePerPerson) + '€';
        }

        if (routeSelect) {
            routeSelect.addEventListener('change', updateRouteInfo);
        }

        if (amountInput) {
            amountInput.addEventListener('input', updateRouteInfo);
        }

        // Initialize on page load
        updateRouteInfo();

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('reservationForm');
            const submitBtn = document.getElementById('submitBtn');
            const phoneInput = document.getElementById('phone_number');

            // Format phone number as user types
            if (phoneInput) {
                phoneInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length >= 10) {
                        // Format for Bosnia and Herzegovina numbers
                        if (value.startsWith('387')) {
                            value = value.replace(/(\d{3})(\d{2})(\d{3})(\d{3})/, '+$1-$2-$3-$4');
                        } else {
                            value = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
                        }
                    }
                    e.target.value = value;
                });
            }

            // Validate number of people
            if (amountInput) {
                amountInput.addEventListener('input', function(e) {
                    const value = parseInt(e.target.value);
                    if (value > 60) {
                        alert('Maksimalan broj osoba po rezervaciji je 60.');
                        e.target.value = 60;
                    } else if (value < 1 && e.target.value !== '') {
                        alert('Minimum broj osoba je 1.');
                        e.target.value = 1;
                    }
                    updateRouteInfo();
                });

                amountInput.addEventListener('blur', function(e) {
                    const value = parseInt(e.target.value);
                    if (isNaN(value) || value < 1) {
                        e.target.value = 1;
                    } else if (value > 60) {
                        e.target.value = 60;
                    }
                    updateRouteInfo();
                });
            }

            // Form validation and loading state
            if (form) {
                form.addEventListener('submit', function(e) {
                    const email = document.getElementById('email').value;
                    const phone = document.getElementById('phone_number').value;

                    // Email validation
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(email)) {
                        alert('Molimo unesite validnu email adresu.');
                        e.preventDefault();
                        return;
                    }

                    // Phone validation
                    const phoneDigits = phone.replace(/\D/g, '');
                    if (phoneDigits.length < 10 || phoneDigits.length > 15) {
                        alert('Molimo unesite valjan broj telefona (10-15 cifara).');
                        e.preventDefault();
                        return;
                    }

                    // Add loading state
                    document.body.classList.add('loading');
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Šalje se...';
                    submitBtn.disabled = true;
                });
            }

            // Auto-hide success message after 10 seconds
            const successBox = document.querySelector('.success-box');
            if (successBox) {
                setTimeout(() => {
                    successBox.style.opacity = '0';
                    setTimeout(() => {
                        successBox.style.display = 'none';
                    }, 500);
                }, 10000);
            }

            // Smooth scroll to form if there are errors
            const errorBox = document.querySelector('.error-box');
            if (errorBox) {
                errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    </script>
</body>
</html>
