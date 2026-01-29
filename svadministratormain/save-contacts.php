<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$contactsFile = __DIR__ . '/../data/contacts.json';

$contacts = [
    'phone'     => trim($_POST['phone'] ?? ''),
    'email'     => trim($_POST['email'] ?? ''),
    'instagram' => trim($_POST['instagram'] ?? ''),
    'address'   => trim($_POST['address'] ?? '')
];

file_put_contents($contactsFile, json_encode($contacts, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

header('Location: index.php?saved=1');
exit;
