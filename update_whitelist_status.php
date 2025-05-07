<?php
session_start();
require 'config.php';

// Admin kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    
    // Geçerli durumları kontrol et
    $valid_statuses = ['pending', 'approved', 'rejected'];
    if (!in_array($status, $valid_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Geçersiz durum']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE whitelist_applications SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Veritabanı hatası']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek']);
} 