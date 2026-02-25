<?php
require 'assets/php/email/SimpleSMTP.php';
$mail = new SimpleSMTP('ryanbarilla254@gmail.com', 'smqnvgtyfgwzipqr');
try {
    $mail->send('ryanbarilla254@gmail.com', 'Test Subject', '<h1>Test</h1>');
    echo 'SUCCESSFUL LOGIN AND EMAIL SEND';
} catch(Exception $e) {
    echo 'FAILED: ' . $e->getMessage();
}
