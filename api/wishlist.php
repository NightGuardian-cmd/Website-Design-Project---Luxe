<?php
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success'=>false,'login'=>true,'message'=>'Please log in to use your wishlist.']);
    exit;
}

$data      = json_decode(file_get_contents('php://input'), true) ?? [];
$productId = intval($data['product_id'] ?? 0);

if (!$productId) {
    echo json_encode(['success'=>false,'message'=>'Invalid product.']);
    exit;
}

$db     = getDB();
$userId = $_SESSION['user_id'];

// Toggle: check if exists
$check = $db->prepare("SELECT id FROM wishlist WHERE user_id=? AND product_id=?");
$check->execute([$userId, $productId]);

if ($check->fetch()) {
    // Remove
    $db->prepare("DELETE FROM wishlist WHERE user_id=? AND product_id=?")->execute([$userId, $productId]);
    echo json_encode(['success'=>true,'added'=>false,'message'=>'Removed from wishlist.']);
} else {
    // Add
    $db->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?,?)")->execute([$userId, $productId]);
    echo json_encode(['success'=>true,'added'=>true,'message'=>'Added to wishlist!']);
}