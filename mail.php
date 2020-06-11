<?php

include __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

function startsWith($haystack, $needle) {
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function loadEnv(string $file): void {
    $contents = file_get_contents($file);
    $lines = explode("\n", $contents);
    
    // Cleanup lines
    $lines = array_filter($lines, function(string $line): bool {
        return !empty($line) && !startsWith(trim($line), '#');
    });
    foreach ($lines as $line) {
        putenv(trim($line));
    }
}

$options = getopt('', ['env-file::']);
if (isset($options['env-file'])) {
    $envFile = $options['env-file'];
    if (file_exists($envFile)) {
        echo "Loading  file";
        loadEnv($envFile);
    } else {
        throw new Exception("Failed to find '$envFile'");
    }
}

function getBody(): string {
    $body = '';
    while (!feof(STDIN)) {
        $body .= fgetss(STDIN);
    }
    return $body;
}

$body = getBody();
if (empty($body)) {
    echo "Empty body";
    echo "\n";
    exit(1);
}

$mail = new PHPMailer(true);


$mail->isSMTP();
$mail->Host = getenv('SMTP_HOST');
$mail->Port = getenv('SMTP_PORT');

$mail->SMTPAuth = true;
$mail->Username = getenv('SMTP_USER');
$mail->Password = getenv('SMTP_PASS');
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
//Recipients
$mail->setFrom(getenv('MAIL_FROM'));
$mail->addAddress(getenv('MAIL_TO'));
// Content
$mail->isHTML(false);
$mail->Subject = getenv('MAIL_SUBJECT');
$mail->Body = $body;

$mail->send();
echo 'Message has been sent';
echo "\n";
