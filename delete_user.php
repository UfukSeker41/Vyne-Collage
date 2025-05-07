<?php
session_start();
require 'config.php';

// Admin kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    
    // Kendini silmeye çalışıyorsa engelle
    if ($id == $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'Kendinizi silemezsiniz']);
        exit;
    }
    
    try {
        // Önce kullanıcıya ait whitelist başvurularını sil
        $stmt = $pdo->prepare("DELETE FROM whitelist_applications WHERE user_id = ?");
        $stmt->execute([$id]);
        
        // Kullanıcıya ait şikayetleri sil
        $stmt = $pdo->prepare("DELETE FROM admin_complaints WHERE user_id = ?");
        $stmt->execute([$id]);
        
        // Kullanıcıyı sil
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Veritabanı hatası']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek']);
} 