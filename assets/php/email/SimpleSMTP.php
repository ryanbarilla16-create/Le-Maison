<?php
// SimpleSMTP.php - Gmail SMTP Email Sender for XAMPP
// Now strictly powered by PHPMailer to ensure TLS compatibility with Google

require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class SimpleSMTP {
    private $username;
    private $password;
    private $debugLog = [];
    private $lastError = '';

    public function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

    public function getDebugLog() {
        return implode("\n", $this->debugLog);
    }

    public function getLastError() {
        return $this->lastError;
    }

    public function send($to, $subject, $body) {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->SMTPDebug = 0; // Set to 2 for detailed output
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->username;
            $mail->Password   = $this->password;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Port 465
            $mail->Port       = 465;

            // Optional: Bypass strict SSL checks if local XAMPP has no certificates
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Recipients
            $mail->setFrom($this->username, 'Le Maison de Yelo Lane');
            $mail->addAddress($to);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags($body);

            $mail->send();
            $this->log("SUCCESS: Email sent to $to via PHPMailer SMTP");
            return true;
            
        } catch (Exception $e) {
            $this->lastError = "PHPMailer Error: {$mail->ErrorInfo}";
            $this->log("SMTP method failed: " . $this->lastError);
            throw new Exception($this->lastError);
        }
    }

    private function log($msg) {
        $this->debugLog[] = date('H:i:s') . ' ' . trim($msg);
        error_log("[SMTP] " . trim($msg));
    }
}
?>
