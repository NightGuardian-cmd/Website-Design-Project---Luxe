<?php
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: application/json');

$data  = json_decode(file_get_contents('php://input'), true) ?? [];
$email = trim($data['email'] ?? ($_POST['email'] ?? ''));

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success'=>false,'message'=>'Please enter a valid email address.']);
    exit;
}

$db = getDB();
$check = $db->prepare("SELECT id FROM newsletter WHERE email=?");
$check->execute([$email]);
if ($check->fetch()) {
    echo json_encode(['success'=>false,'message'=>"You're already subscribed! 🎉"]);
    exit;
}

$db->prepare("INSERT INTO newsletter (email) VALUES (?)")->execute([$email]);
echo json_encode(['success'=>true,'message'=>'Welcome! Check your inbox for a special offer.']);