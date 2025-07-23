<?php
require "./db/data.php";
require './php_mailer/Exception.php';
require './php_mailer/PHPMailer.php';
require './php_mailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Email configuration
$smtp_host = 'smtp.gmail.com';
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

// Kajak ture
$kajak_tours = [
    'bihac_city_tour' => [
        'name' => 'Bihac City Tour',
        'price' => 35,
        'original_price' => null,
        'description' => 'Mirna vožnja kajaka po rijeci Uni + obilazak istorijskog centra Bihaća'
    ],
    'kostela' => [
        'name' => 'Kostela',
        'price' => 35,
        'original_price' => 40,
        'description' => 'Avantura kroz kanjon Kostela sa prekrasnim vodopadi'
    ]
];

// Handle success message from redirect
if (isset($_GET['success']) && $_GET['success'] == '1' && isset($_GET['id'])) {
    $reservation_id = intval($_GET['id']);
    $email_status = $_GET['email'] ?? 'sent';
    
    if ($email_status === 'sent') {
        $success_message = "🎉 Kajak rezervacija je uspješno potvrđena! Vaš ID rezervacije je: #" . $reservation_id . ". Email sa potvrdom je poslan na vašu adresu.";
    } else {
        $success_message = "✅ Kajak rezervacija je uspješno potvrđena! Vaš ID rezervacije je: #" . $reservation_id . ". Nismo mogli poslati email, molimo kontaktirajte nas.";
    }
}

// Function to validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Function to validate phone number
function validatePhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return strlen($phone) >= 10 && strlen($phone) <= 15;
}

// Function to send confirmation email
function sendConfirmationEmail($reservation, $tour_info) {
    global $smtp_host, $smtp_port, $smtp_username, $smtp_password, $from_email, $from_name, $owner_mail, $owner_name;
    
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = $smtp_host;
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_username;
        $mail->Password = $smtp_password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $smtp_port;
        
        $mail->setFrom($from_email, $from_name);
        $mail->addAddress($reservation['email'], $reservation['name_on_reservation']);
        $mail->addCC($owner_mail, $owner_name);
        
        $mail->isHTML(true);
        $mail->Subject = 'Potvrda rezervacije - ' . $tour_info['name'] . ' Kajak avantura';
        
        $price_display = $tour_info['original_price'] ? 
            "<span style='text-decoration: line-through; color: #999;'>{$tour_info['original_price']}€</span> <strong style='color: #7b1fa2;'>{$tour_info['price']}€</strong> (SNIŽENJE!)" : 
            "<strong>{$tour_info['price']}€</strong>";
        
        $email_body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #7b1fa2 0%, #9c27b0 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
                .detail-row { display: flex; justify-content: space-between; margin: 10px 0; padding: 8px 0; border-bottom: 1px solid #eee; }
                .label { font-weight: bold; color: #555; }
                .value { color: #333; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
                .highlight { background: #f3e5f5; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #7b1fa2; }
                .price-highlight { background: #e8f5e8; padding: 10px; border-radius: 5px; text-align: center; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎉 Kajak rezervacija potvrđena!</h1>
                    <p>Vaša {$tour_info['name']} avantura je spremna!</p>
                </div>
                <div class='content'>
                    <p>Poštovani/a {$reservation['name_on_reservation']},</p>
                    <div class='highlight'>
                        <strong>🎊 Odlične vijesti!</strong> Vaša kajak rezervacija je <strong>automatski potvrđena</strong>. Spremni ste za nevjerovatnu avanturu!
                    </div>
                    <div class='details'>
                        <h3>📋 Detalji vaše rezervacije</h3>
                        <div class='detail-row'>
                            <span class='label'>ID rezervacije:</span>
                            <span class='value'>#{$reservation['reservation_id']}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='label'>Tip aktivnosti:</span>
                            <span class='value'>🛶 Kajak</span>
                        </div>
                        <div class='detail-row'>
                            <span class='label'>Tura:</span>
                            <span class='value'>{$tour_info['name']}</span>
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
                            <span class='value' style='font-size: 1.2em; font-weight: bold; color: #7b1fa2;'>" . ($reservation['amount_of_people'] * $tour_info['price']) . "€</span>
                        </div>
                        <div class='detail-row'>
                            <span class='label'>Status:</span>
                            <span class='value' style='color: #7b1fa2; font-weight: bold;'>✅ POTVRĐENO</span>
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
                        <li>📱 Telefon: <strong>(555) 123-KAJAK</strong></li>
                        <li>🆔 Referenca: <strong>#{$reservation['reservation_id']}</strong></li>
                    </ul>
                    <div class='footer'>
                        <p>Srdačno,<br><strong>Una Pirates Kajak Tim</strong></p>
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
    $email = trim($_POST['email']);
    $name_on_reservation = trim($_POST['name_on_reservation']);
    $phone_number = trim($_POST['phone_number']);
    $selected_tour = $_POST['selected_tour'] ?? 'bihac_city_tour';
    $reservation_date = $_POST['reservation_date'];
    $amount_of_people = intval($_POST['amount_of_people']);
    $activity_type = 'Kajak';
    
    // Validacija izabrane ture
    if (!array_key_exists($selected_tour, $kajak_tours)) {
        $selected_tour = 'bihac_city_tour';
    }
    
    $tour_info = $kajak_tours[$selected_tour];
    $rafting_route = $tour_info['name'];
    
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
    
    if (empty($reservation_date)) {
        $errors[] = "Datum rezervacije je obavezan.";
    } elseif (strtotime($reservation_date) < strtotime('today')) {
        $errors[] = "Datum ne može biti u prošlosti.";
    }
    
    if ($amount_of_people < 1 || $amount_of_people > 60) {
        $errors[] = "Broj osoba mora biti između 1 i 60.";
    }
    
    if (empty($errors)) {
        try {
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]);
            
            $sql = "INSERT INTO Reservations (email, name_on_reservation, phone_number, activity_type, rafting_route, reservation_date, amount_of_people, is_confirmed) 
                    VALUES (:email, :name_on_reservation, :phone_number, :activity_type, :rafting_route, :reservation_date, :amount_of_people, TRUE)";
            
            $stmt = $pdo->prepare($sql);
            
            $success = $stmt->execute([
                ':email' => $email,
                ':name_on_reservation' => $name_on_reservation,
                ':phone_number' => $phone_number,
                ':activity_type' => $activity_type,
                ':rafting_route' => $rafting_route,
                ':reservation_date' => $reservation_date,
                ':amount_of_people' => $amount_of_people
            ]);
            
            if ($success) {
                $reservation_id = $pdo->lastInsertId();
                
                $reservation_data = [
                    'reservation_id' => $reservation_id,
                    'email' => $email,
                    'name_on_reservation' => $name_on_reservation,
                    'phone_number' => $phone_number,
                    'rafting_route' => $rafting_route,
                    'reservation_date' => $reservation_date,
                    'amount_of_people' => $amount_of_people
                ];
                
                $email_sent = sendConfirmationEmail($reservation_data, $tour_info);
                $email_param = $email_sent ? 'sent' : 'failed';
                
                header("Location: " . $_SERVER['PHP_SELF'] . "?success=1&id=" . $reservation_id . "&email=" . $email_param);
                exit();
            } else {
                $error_message = "Greška pri čuvanju rezervacije. Molimo pokušajte ponovo.";
            }
        } catch (PDOException $e) {
            $error_message = "Greška baze podataka: " . $e->getMessage();
            error_log("Database error in kayak-reservation.php: " . $e->getMessage());
        } catch (Exception $e) {
            $error_message = "Sistemska greška: " . $e->getMessage();
            error_log("General error in kayak-reservation.php: " . $e->getMessage());
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
    <title>UNA PIRATES - Kajak Rezervacija</title>
    <link rel="icon" href="materijali/logo.webp" type="image/x-icon" />
    <link rel="stylesheet" href="./css/kayakreservation.css"/>
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
                <p>🛶 Rezervišite svoju kajak avanturu danas!</p>
            </div>
        </div>

        <div class="instant-confirmation">
            ⚡ Trenutna potvrda - Vaša kajak rezervacija će biti odmah potvrđena!
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
                    <strong>🛶 Kajak rezervacija:</strong> Kada pošaljete ovaj formular, vaša kajak rezervacija će biti automatski potvrđena i odmah ćete dobiti email sa svim detaljima!
                </div>

                <div class="tour-selection">
                    <h3>🛶 Dostupne Kajak Avanture</h3>
                    <div class="tour-options">
                        <div class="tour-option">
                            <h4>🏙️ Bihac City Tour</h4>
                            <div class="tour-price">35€ po osobi</div>
                            <ul>
                                <li>Mirna vožnja kajaka po rijeci Uni</li>
                                <li>Prijevoz od agencije do željene destinacije</li>
                                <li>Osnovna kayak oprema</li>
                                <li>Licencirani vodič/skiper na rijeci</li>
                            </ul>
                        </div>
                        <div class="tour-option">
                            <h4>🏔️ Kostela</h4>
                            <div class="tour-price">
                                <span class="original-price">40€</span>
                                <span class="sale-price">35€</span>
                                <span class="sale-badge">SNIŽENJE!</span>
                            </div>
                            <ul>
                                <li>Avantura kroz kanjon Kostela</li>
                                <li>Prijevoz od agencije do željene destinacije</li>
                                <li>Osnovna kayak oprema</li>
                                <li>Licencirani vodič/skiper na rijeci</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <h2 class="form-section-title">Kajak rezervacija</h2>

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
                        <label for="selected_tour">Izaberite turu <span class="required">*</span></label>
                        <select id="selected_tour" name="selected_tour" required>
                            <option value="bihac_city_tour" <?php echo ($selected_tour ?? 'bihac_city_tour') === 'bihac_city_tour' ? 'selected' : ''; ?>>
                                🏙️ Bihac City Tour - 35€ po osobi
                            </option>
                            <option value="kostela" <?php echo ($selected_tour ?? '') === 'kostela' ? 'selected' : ''; ?>>
                                🏔️ Kostela - 35€ po osobi (sniženo sa 40€) ⭐
                            </option>
                        </select>
                        <div class="route-info" id="tour-description">
                            Izaberite željenu kajak turu
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
                        <div class="total-price" id="total-price" style="margin-top: 10px; font-weight: bold; color: #7b1fa2;">
                            Ukupna cijena: <span id="price-amount">35€</span>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn" id="submitBtn">
                        <i class="fas fa-water"></i> Rezerviši kajak - Trenutna potvrda!
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Tour selection and price calculation
        const tourSelect = document.getElementById('selected_tour');
        const amountInput = document.getElementById('amount_of_people');
        const tourDescription = document.getElementById('tour-description');
        const totalPriceElement = document.getElementById('price-amount');

        const tourDescriptions = {
            'bihac_city_tour': 'Mirna vožnja kajaka po rijeci Uni + obilazak istorijskog centra Bihaća',
            'kostela': 'Avantura kroz kanjon Kostela sa prekrasnim vodopadi - SNIŽENJE!'
        };

        function updateTourInfo() {
            const selectedTour = tourSelect.value;
            const amount = parseInt(amountInput.value) || 1;
            const pricePerPerson = 35;
            
            tourDescription.textContent = tourDescriptions[selectedTour] || 'Izaberite željenu kajak turu';
            totalPriceElement.textContent = (amount * pricePerPerson) + '€';
        }

        if (tourSelect) {
            tourSelect.addEventListener('change', updateTourInfo);
        }

        if (amountInput) {
            amountInput.addEventListener('input', updateTourInfo);
        }

        // Initialize on page load
        updateTourInfo();

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('reservationForm');
            const submitBtn = document.getElementById('submitBtn');
            const phoneInput = document.getElementById('phone_number');

            // Format phone number
            if (phoneInput) {
                phoneInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length >= 10) {
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
                    updateTourInfo();
                });

                amountInput.addEventListener('blur', function(e) {
                    const value = parseInt(e.target.value);
                    if (isNaN(value) || value < 1) {
                        e.target.value = 1;
                    } else if (value > 60) {
                        e.target.value = 60;
                    }
                    updateTourInfo();
                });
            }

            // Form validation
            if (form) {
                form.addEventListener('submit', function(e) {
                    const email = document.getElementById('email').value;
                    const phone = document.getElementById('phone_number').value;

                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(email)) {
                        alert('Molimo unesite validnu email adresu.');
                        e.preventDefault();
                        return;
                    }

                    const phoneDigits = phone.replace(/\D/g, '');
                    if (phoneDigits.length < 10 || phoneDigits.length > 15) {
                        alert('Molimo unesite valjan broj telefona (10-15 cifara).');
                        e.preventDefault();
                        return;
                    }

                    document.body.classList.add('loading');
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Šalje se...';
                    submitBtn.disabled = true;
                });
            }

            // Auto-hide success message
            const successBox = document.querySelector('.success-box');
            if (successBox) {
                setTimeout(() => {
                    successBox.style.opacity = '0';
                    setTimeout(() => {
                        successBox.style.display = 'none';
                    }, 500);
                }, 10000);
            }

            // Scroll to error
            const errorBox = document.querySelector('.error-box');
            if (errorBox) {
                errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    </script>
</body>
</html>
