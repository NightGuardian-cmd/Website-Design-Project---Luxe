<?php
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true) ?? [];
$action    = $data['action']     ?? '';
$productId = intval($data['product_id'] ?? 0);
$qty       = intval($data['qty'] ?? 1);

if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

$db = getDB();

switch ($action) {
    case 'add':
        $p = $db->prepare("SELECT id, stock FROM products WHERE id = ?");
        $p->execute([$productId]);
        $product = $p->fetch();
        if (!$product) { echo json_encode(['success'=>false,'message'=>'Product not found']); exit; }

        $_SESSION['cart'][$productId] = ($_SESSION['cart'][$productId] ?? 0) + $qty;
        break;

    case 'remove':
        unset($_SESSION['cart'][$productId]);
        break;

    case 'increase':
        if (isset($_SESSION['cart'][$productId])) $_SESSION['cart'][$productId]++;
        break;

    case 'decrease':
        if (isset($_SESSION['cart'][$productId]) && $_SESSION['cart'][$productId] > 1) {
            $_SESSION['cart'][$productId]--;
        }
        break;

    case 'clear':
        $_SESSION['cart'] = [];
        break;
}

$cartCount = array_sum($_SESSION['cart']);
echo json_encode(['success' => true, 'cart_count' => $cartCount, 'cart' => $_SESSION['cart']]);