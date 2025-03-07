<?php
header('Content-Type: application/json');

// Include PHPMailer autoload (if using Composer, adjust the path if necessary)
require 'vendor/autoload.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Initialize PHPMailer
$mail = new PHPMailer();
$emailTO = [];

// Site information
$sitename = 'Numilex';
$emailTO[] = ['email' => 'bengherbisarah@gmail.com', 'name' => 'Numilex team'];
$subject = "Nouvelle inscription à la newsletter - " . $sitename;
$msg_success = "Vous avez <strong>réussi</strong> à vous inscrire à notre newsletter. Merci de votre intérêt !";

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    if (!empty($_POST["s_email"])) {
        $s_email = $_POST["s_email"];
        $honeypot = $_POST["form-anti-honeypot"] ?? '';

        // Proceed if honeypot is empty
        if ($honeypot === '' && !empty($emailTO)) {
            try {
                // SMTP Configuration
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'bengherbisarah@gmail.com'; // Your SMTP username
                $mail->Password = 'uaze nlxr xhvu wfeo'; // Your SMTP password (use app password for Gmail)
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use STARTTLS
                $mail->Port = 587; // Use 587 for STARTTLS

                // Email settings
                $mail->setFrom($s_email, 'Nouvelle Inscription - ' . $sitename);
                $mail->addReplyTo($s_email);
                $mail->Subject = $subject;

                // Add recipient addresses
                foreach ($emailTO as $to) {
                    $mail->addAddress($to['email'], $to['name']);
                }

                // Build the email body
                $bodymsg = "Nouvelle inscription à la newsletter:<br>";
                $bodymsg .= "Email: $s_email<br>";
                $bodymsg .= $_SERVER['HTTP_REFERER'] ? '<br>---<br><br>Ce email a été envoyé par: ' . $_SERVER['HTTP_REFERER'] : '';

                // Set email format to HTML and character set
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Body = $bodymsg;

                // Send the email
                if ($mail->send()) {
                    $response = ['result' => "success", 'message' => $msg_success];
                } else {
                    $response = ['result' => "error", 'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"];
                }
                echo json_encode($response);

            } catch (Exception $e) {
                echo json_encode(['result' => "error", 'message' => "An error occurred: " . $e->getMessage()]);
            }
        } else {
            echo json_encode(['result' => "error", 'message' => "Bot <strong>Detected</strong>."]);
        }
    } else {
        echo json_encode(['result' => "error", 'message' => "Veuillez <strong>entrer</strong> une adresse e-mail valide."]);
    }
}
