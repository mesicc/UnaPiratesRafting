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
$from_name = 'Una Rafting';
$owner_mail = "owner@gmail.com";
$owner_name = "Owner";

// Initialize variables
$error_message = '';
$success_message = '';

// Handle success message from redirect
if (isset($_GET['success']) && $_GET['success'] == '1' && isset($_GET['id'])) {
    $reservation_id = intval($_GET['id']);
    $email_status = $_GET['email'] ?? 'sent';
    
    if ($email_status === 'sent') {
        $success_message = "🎉 Reservation confirmed successfully! Your reservation ID is: #" . $reservation_id . ". A confirmation email has been sent to your email address with all the details.";
    } else {
        $success_message = "✅ Reservation confirmed successfully! Your reservation ID is: #" . $reservation_id . ". However, we couldn't send the confirmation email. Please contact us if you need a copy.";
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
function sendConfirmationEmail($reservation) {
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
        $mail->Subject = 'Reservation Confirmed - Rafting Adventure';
        
        $email_body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
                .detail-row { display: flex; justify-content: space-between; margin: 10px 0; padding: 8px 0; border-bottom: 1px solid #eee; }
                .label { font-weight: bold; color: #555; }
                .value { color: #333; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
                .highlight { background: #e8f5e8; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #28a745; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎉 Reservation Confirmed!</h1>
                    <p>Your rafting adventure is all set!</p>
                </div>
                <div class='content'>
                    <p>Dear {$reservation['name_on_reservation']},</p>
                    <div class='highlight'>
                        <strong>🎊 Great news!</strong> Your rafting reservation has been <strong>automatically confirmed</strong>. No waiting required - you're all set for an amazing adventure!
                    </div>
                    <div class='details'>
                        <h3>📋 Your Reservation Details</h3>
                        <div class='detail-row'>
                            <span class='label'>Reservation ID:</span>
                            <span class='value'>#{$reservation['reservation_id']}</span>
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
                            <span class='label'>Date:</span>
                            <span class='value'>" . date('F j, Y', strtotime($reservation['reservation_date'])) . "</span>
                        </div>
                        <div class='detail-row'>
                            <span class='label'>Number of People:</span>
                            <span class='value'>{$reservation['amount_of_people']}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='label'>Status:</span>
                            <span class='value' style='color: #28a745; font-weight: bold;'>✅ CONFIRMED</span>
                        </div>
                    </div>
                    <h3>📝 What to Bring & Expect</h3>
                    <ul>
                        <li><strong>Arrival:</strong> Please arrive 30 minutes before your scheduled time</li>
                        <li><strong>Clothing:</strong> Quick-dry clothing and secure footwear (no flip-flops)</li>
                        <li><strong>Sun Protection:</strong> Sunscreen, hat, and sunglasses</li>
                        <li><strong>Hydration:</strong> Water bottle (we provide snacks)</li>
                        <li><strong>Personal Items:</strong> Waterproof bag for valuables</li>
                    </ul>
                    <h3>📞 Need to Make Changes?</h3>
                    <p>If you need to modify or cancel your reservation, please contact us at least 24 hours in advance:</p>
                    <ul>
                        <li>📧 Email: <strong>{$from_email}</strong></li>
                        <li>📱 Phone: <strong>(555) 123-RAFT</strong></li>
                        <li>🆔 Reference: <strong>#{$reservation['reservation_id']}</strong></li>
                    </ul>
                    <div class='highlight'>
                        <p><strong>🌊 Get Ready for Adventure!</strong><br>
                        We can't wait to see you on the water. Our experienced guides will ensure you have a safe and thrilling experience!</p>
                    </div>
                    <div class='footer'>
                        <p>Best regards,<br><strong>The Rafting Adventure Team</strong></p>
                        <p><em>This confirmation was sent automatically upon booking.</em></p>
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
        $errors[] = "Email is required.";
    } elseif (!validateEmail($email)) {
        $errors[] = "Please enter a valid email address.";
    }
    
    if (empty($name_on_reservation)) {
        $errors[] = "Name on reservation is required.";
    }
    
    if (empty($phone_number)) {
        $errors[] = "Phone number is required.";
    } elseif (!validatePhone($phone_number)) {
        $errors[] = "Please enter a valid phone number (10-15 digits).";
    }
    
    if (empty($rafting_route)) {
        $errors[] = "Please select a rafting route.";
    }
    
    if (empty($reservation_date)) {
        $errors[] = "Reservation date is required.";
    } elseif (strtotime($reservation_date) < strtotime('today')) {
        $errors[] = "Reservation date cannot be in the past.";
    }
    
    if ($amount_of_people < 1 || $amount_of_people > 60) {
        $errors[] = "Number of people must be between 1 and 60.";
    }
    
    // If no validation errors, proceed with database insertion
    if (empty($errors)) {
        try {
            // Create PDO connection
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Prepare SQL statement - NOTE: is_confirmed is now set to TRUE by default
            $sql = "INSERT INTO Reservations (email, name_on_reservation, phone_number, rafting_route, reservation_date, amount_of_people, is_confirmed) 
                    VALUES (:email, :name_on_reservation, :phone_number, :rafting_route, :reservation_date, :amount_of_people, TRUE)";
            
            $stmt = $pdo->prepare($sql);
            
            // Bind parameters
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':name_on_reservation', $name_on_reservation);
            $stmt->bindParam(':phone_number', $phone_number);
            $stmt->bindParam(':rafting_route', $rafting_route);
            $stmt->bindParam(':reservation_date', $reservation_date);
            $stmt->bindParam(':amount_of_people', $amount_of_people);
            
            // Execute the statement
            if ($stmt->execute()) {
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
                
                // Send confirmation email
                $email_sent = sendConfirmationEmail($reservation_data);
                
                // Redirect with success status and email status
                $email_param = $email_sent ? 'sent' : 'failed';
                header("Location: " . $_SERVER['PHP_SELF'] . "?success=1&id=" . $reservation_id . "&email=" . $email_param);
                exit();
            }
        } catch (PDOException $e) {
            $error_message = "Database error: Unable to submit reservation. Please try again later.";
            // Log the actual error for debugging (don't show to user)
            error_log("Database error: " . $e->getMessage());
        }
    } else {
        $error_message = implode("<br>", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UNA PIRATES - Rezervacija</title>
    <link rel="icon" href="materijali/logo.webp" type="image/x-icon" />
    <script src="https://kit.fontawesome.com/53f832df41.js" crossorigin="anonymous"></script>
    <style>
        @font-face {
            font-family: "EDO";
            src: url("/materijali/edo.ttf");
        }

        @font-face {
            font-family: "FUTURA";
            src: url("/materijali/Futura\ Medium.otf");
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Force desktop layout for larger screens */
        @media (min-width: 769px) {
            .container {
                max-width: 700px;
            }
            
            .header {
                padding: 50px 40px;
            }
            
            .form-container {
                padding: 50px 40px;
            }
            
            .header h1 {
                font-size: 3em;
            }
            
            input[type="text"],
            input[type="email"],
            input[type="tel"],
            input[type="date"],
            input[type="number"],
            select {
                padding: 18px;
                font-size: 16px;
            }
            
            .submit-btn {
                padding: 20px;
                font-size: 18px;
            }
        }

        body {
            font-family: "FUTURA", fallback-fonts, sans-serif;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.616), transparent),
                        url('materijali/raftingsl.webp');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            padding: 20px;
            color: white;
            line-height: 1.6;
        }

        /* Desktop specific background */
        @media (min-width: 769px) {
            body {
                background-attachment: fixed;
            }
        }

        /* Mobile specific background */
        @media (max-width: 768px) {
            body {
                background-attachment: scroll;
                padding: 10px;
            }
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .header {
            background: linear-gradient(135deg, #4f785e 0%, #34624d 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('materijali/4931029.webp');
            background-size: 500px;
            opacity: 0.1;
            z-index: 0;
        }

        .header-content {
            position: relative;
            z-index: 1;
        }

        .header h1 {
            font-family: "EDO", fallback-fonts, sans-serif;
            font-size: 2.5em;
            margin-bottom: 10px;
            color: #fff500;
            filter: drop-shadow(0 0 0.35rem rgb(0, 0, 0));
        }

        .header p {
            font-size: 1.1em;
            opacity: 0.9;
            margin-bottom: 0;
        }

        .logo-header {
            width: 60px;
            height: 60px;
            margin-bottom: 15px;
            filter: drop-shadow(0 0 0.35rem rgb(0, 0, 0));
        }

        .instant-confirmation {
            background: linear-gradient(135deg, #fff500, #ffed4e);
            color: #333;
            padding: 15px;
            text-align: center;
            font-weight: 600;
            font-size: 1.1em;
            border-bottom: 3px solid #34624d;
        }

        .form-container {
            padding: 40px;
            color: #333;
            background: white;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 1rem;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"],
        input[type="number"],
        select {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            font-family: "FUTURA", fallback-fonts, sans-serif;
            transition: all 0.3s ease;
            background: white;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #4f785e;
            box-shadow: 0 0 0 3px rgba(79, 120, 94, 0.1);
            transform: translateY(-1px);
        }

        select {
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 12px center;
            background-repeat: no-repeat;
            background-size: 16px;
            padding-right: 40px;
            appearance: none;
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, #4f785e, #34624d);
            color: white;
            padding: 18px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            font-family: "FUTURA", fallback-fonts, sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .submit-btn:hover {
            background: linear-gradient(135deg, #34624d, #2a4f3d);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 120, 94, 0.3);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .message-box {
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-weight: 500;
            border-left: 4px solid;
        }

        .error-box {
            background-color: #fee;
            border-left-color: #dc3545;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .success-box {
            background-color: #e8f5e8;
            border-left-color: #4f785e;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .required {
            color: #dc3545;
        }

        .info-box {
            background: linear-gradient(135deg, #e3f2fd, #f0f8ff);
            border: 1px solid #4f785e;
            color: #2c5530;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #4f785e;
        }

        .back-to-home {
            position: fixed;
            top: 20px;
            left: 20px;
            background: rgba(79, 120, 94, 0.9);
            color: white;
            padding: 12px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            z-index: 1000;
        }

        .back-to-home:hover {
            background: rgba(52, 98, 77, 0.95);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .route-info {
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
            font-style: italic;
        }

        .form-section-title {
            font-family: "EDO", fallback-fonts, sans-serif;
            color: #4f785e;
            font-size: 1.5em;
            margin-bottom: 20px;
            text-align: center;
            position: relative;
        }

        .form-section-title::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: #fff500;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding: 10px;
                background-attachment: scroll;
            }

            .container {
                margin: 10px auto;
                border-radius: 10px;
            }

            .form-container {
                padding: 25px 20px;
            }

            .header {
                padding: 30px 20px;
            }

            .header h1 {
                font-size: 2em;
            }

            .back-to-home {
                position: static;
                display: block;
                margin: 0 auto 20px;
                text-align: center;
                width: fit-content;
            }

            input[type="text"],
            input[type="email"],
            input[type="tel"],
            input[type="date"],
            input[type="number"],
            select {
                padding: 12px;
                font-size: 16px;
            }

            .submit-btn {
                padding: 15px;
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            .header h1 {
                font-size: 1.8em;
            }

            .form-container {
                padding: 20px 15px;
            }

            .message-box {
                padding: 15px;
            }

            .info-box {
                padding: 15px;
            }
        }

        /* Loading animation */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .loading .submit-btn {
            background: #6c757d;
            cursor: not-allowed;
        }

        /* Focus improvements for accessibility */
        input:focus,
        select:focus,
        .submit-btn:focus {
            outline: 2px solid #4f785e;
            outline-offset: 2px;
        }

        /* Smooth transitions */
        .form-group {
            transition: all 0.3s ease;
        }

        .form-group:hover {
            transform: translateY(-1px);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #4f785e;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #34624d;
        }
    </style>
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
                <p>Rezervišite svoju rafting avanturu danas!</p>
            </div>
        </div>

        <div class="instant-confirmation">
            ⚡ Trenutna potvrda - Vaša rezervacija će biti odmah potvrđena!
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
                    <strong>📧 Trenutni proces:</strong> Kada pošaljete ovaj formular, vaša rezervacija će biti automatski potvrđena i odmah ćete dobiti email sa svim detaljima!
                </div>

                <h2 class="form-section-title">Podaci za rezervaciju</h2>

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
                        <label for="rafting_route">Rafting ruta <span class="required">*</span></label>
                        <select id="rafting_route" name="rafting_route" required>
                            <option value="">Izaberite rutu...</option>
                            <option value="Kostela" <?php echo (isset($rafting_route) && $rafting_route === 'Kostela') ? 'selected' : ''; ?>>
                                Kostela - 35€ po osobi
                            </option>
                            <option value="Štrbački Buk" <?php echo (isset($rafting_route) && $rafting_route === 'Štrbački Buk') ? 'selected' : ''; ?>>
                                Štrbački Buk - 50€ po osobi
                            </option>
                        </select>
                        <div class="route-info">
                            Kostela: Lakša ruta, idealna za početnike | Štrbački Buk: Avanturistička ruta sa više uzbuđenja
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
                    </div>

                    <button type="submit" class="submit-btn" id="submitBtn">
                        <i class="fas fa-water"></i> Rezerviši sada - Trenutna potvrda!
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Add some client-side validation and UX improvements
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

        // Add some nice hover effects
        document.querySelectorAll('.form-group').forEach(group => {
            group.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });
            
            group.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Validacija broja osoba
        const amountInput = document.getElementById('amount_of_people');
        if (amountInput) {
            amountInput.addEventListener('input', function(e) {
                const value = parseInt(e.target.value);
                if (value > 60) {
                    alert('Maksimalan broj osoba po rezervaciji je 60. Molimo kontaktirajte nas direktno za veće grupe.');
                    e.target.value = 60;
                } else if (value < 1 && e.target.value !== '') {
                    alert('Minimum broj osoba je 1.');
                    e.target.value = 1;
                }
            });

            amountInput.addEventListener('blur', function(e) {
                const value = parseInt(e.target.value);
                if (isNaN(value) || value < 1) {
                    e.target.value = 1;
                } else if (value > 60) {
                    e.target.value = 60;
                }
            });
        }
    </script>
</body>
</html>
