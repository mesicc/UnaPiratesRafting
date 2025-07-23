<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$name = isset($_POST['ime']) ? htmlspecialchars($_POST['ime']) : '';
$email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
$subject = isset($_POST['tip']) ? htmlspecialchars($_POST['tip']) : '';
$message = isset($_POST['poruka']) ? htmlspecialchars($_POST['poruka']) : '';

$to = "info@bobbabubble.ba";
$subjectEmail = "Bobba Bubble - Nova poruka od $name";

$messageEmail = "
<html>
<head>
  <style>
    body { font-family: sans-serif; background-color: #fff5fa; color: #333; padding: 2em; }
    h2 { color: #b399c8; }
  </style>
</head>
<body>
  <h2>Nova poruka sa BobbaBubble stranice</h2>
  <p><strong>Ime i prezime:</strong> $name</p>
  <p><strong>Email:</strong> <a href='mailto:$email'>$email</a></p>
  <p><strong>Tip upita:</strong> $subject</p>
  <hr>
  <p><strong>Poruka:</strong><br>$message</p>
  <br><br>
  <p style='font-size: 0.9em; color: gray;'>
    Poruka poslana putem kontakt forme na <a href='https://bobbabubble.ba'>bobbabubble.ba</a><br>
    Ne odgovarajte direktno na ovu poruku.
  </p>
  <p style='font-size: 80%; color: gray;'>
  Developed by: <a href='https://msehic.com/' target='_blank'>Muhammed Šehić</a> in 2025.>
  </p>
</body>
</html>
";

$headers = "From: noreply@bobbabubble.ba\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

if (mail($to, $subjectEmail, $messageEmail, $headers)) {
    echo "success";
} else {
    echo "error";
}
?>