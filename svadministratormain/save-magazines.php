<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$magazinesFile = __DIR__ . '/../data/magazines.json';
$magazines = json_decode(file_get_contents($magazinesFile), true) ?: [];

$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $newIssue = [
        'id'    => count($magazines) > 0 ? max(array_column($magazines, 'id')) + 1 : 1,
        'title' => trim($_POST['title'] ?? ''),
        'year'  => trim($_POST['year'] ?? ''),
        'cover' => trim($_POST['cover'] ?? ''),
        'pdf'   => trim($_POST['pdf'] ?? '')
    ];
    // Add to beginning (newest first)
    array_unshift($magazines, $newIssue);
}

if ($action === 'delete') {
    $index = (int)($_POST['index'] ?? -1);
    if (isset($magazines[$index])) {
        array_splice($magazines, $index, 1);
    }
}

file_put_contents($magazinesFile, json_encode($magazines, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

header('Location: index.php?saved=1');
exit;
