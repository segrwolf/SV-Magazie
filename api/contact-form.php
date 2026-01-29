<?php
error_reporting(0);
header('Content-Type: application/json; charset=utf-8');

$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validation
if (empty($name) || empty($email)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Name and email are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

// reCAPTCHA verification
$recaptchaSecret = '6LeI0FgsAAAAADW90cC8pMwEOwgJ_-Y7B9rNmKfU';
$recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

if (empty($recaptchaResponse)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please confirm you are not a robot.']);
    exit;
}

$recaptchaVerify = curl_init('https://www.google.com/recaptcha/api/siteverify');
curl_setopt($recaptchaVerify, CURLOPT_POST, true);
curl_setopt($recaptchaVerify, CURLOPT_POSTFIELDS, http_build_query([
    'secret'   => $recaptchaSecret,
    'response' => $recaptchaResponse
]));
curl_setopt($recaptchaVerify, CURLOPT_RETURNTRANSFER, true);
curl_setopt($recaptchaVerify, CURLOPT_TIMEOUT, 10);
$recaptchaResult = json_decode(curl_exec($recaptchaVerify), true);
curl_close($recaptchaVerify);

if (empty($recaptchaResult['success'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'reCAPTCHA verification failed. Please try again.']);
    exit;
}

// Telegram config
$telegramBotToken = '7474162554:AAED5JLvSNVF0cvKo6gaG1lvl_7cs_gdf5A';
$telegramChatId   = '-1003828812353';

// Build Telegram message (HTML parse mode — more reliable than Markdown)
$text  = "<b>New Contact Request</b>\n\n";
$text .= "<b>Name:</b> " . htmlspecialchars($name) . "\n";
$text .= "<b>Email:</b> " . htmlspecialchars($email) . "\n";
if (!empty($phone)) {
    $text .= "<b>Phone:</b> " . htmlspecialchars($phone) . "\n";
}
if (!empty($message)) {
    $text .= "\n<b>Message:</b>\n" . htmlspecialchars($message) . "\n";
}
$text .= "\n" . date('Y-m-d H:i:s');

// Send to Telegram
$telegramUrl = "https://api.telegram.org/bot{$telegramBotToken}/sendMessage";

$ch = curl_init($telegramUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'chat_id'    => $telegramChatId,
    'text'       => $text,
    'parse_mode' => 'HTML'
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Always save to log as backup
$logFile = __DIR__ . '/../data/leads.log';
$logEntry = date('Y-m-d H:i:s') . " | " . $name . " | " . $email . " | " . $phone . " | " . $message . "\n";
file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

echo json_encode(['success' => true, 'message' => 'Thank you! We will contact you soon.']);
