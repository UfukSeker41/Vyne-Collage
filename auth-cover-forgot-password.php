<?php
session_start();
require 'config.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $email = trim($_POST['email']);
    $errors = [];
    
    if(empty($email)){
        $errors[] = "Email gereklidir.";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors[] = "Geçerli bir email girin.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if($user){
            // Reset token üretimi
            $token = bin2hex(random_bytes(16));
            $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));
            
            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
            if($stmt->execute([$token, $expires, $email])){
                // Gerçek ortamda bu link email ile gönderilmeli
                $resetLink = "http://vynecollege.com/auth-cover-reset-password.php?token=" . $token;
                $_SESSION['message'] = "Şifre sıfırlama linkiniz: <a href='$resetLink'>$resetLink</a>";
                header("Location: auth-cover-forgot-password.php");
                exit;
            } else {
                $errors[] = "Hata oluştu.";
            }
        } else {
            $errors[] = "Bu email ile kayıtlı kullanıcı bulunamadı.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifremi Unuttum - VYNE College</title>
    <link rel="icon" href="assets/images/logo-icon.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-left">
            <div class="auth-left-content">
                <img src="assets/images/logo1.png" alt="VYNE College Logo">
                <h1>Şifrenizi mi Unuttunuz?</h1>
                <p>Endişelenmeyin, size yardımcı olacağız</p>
                <img src="assets/images/auth/reset-password1.png" alt="Şifre Sıfırlama İllüstrasyon" style="max-width: 400px; margin-top: 2rem;">
            </div>
        </div>
        <div class="auth-right">
            <div class="auth-form-container">
                <img src="assets/images/logo1.png" alt="Logo" style="max-width: 150px;">
                <h2>Şifre Sıfırlama</h2>
                <p>Şifrenizi sıfırlamak için email adresinizi girin</p>
                
                <?php if(!empty($errors)): ?>
                    <?php foreach($errors as $error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if(isset($_SESSION['message'])): ?>
                    <div class="alert alert-success">
                        <?php 
                        echo $_SESSION['message'];
                        unset($_SESSION['message']);
                        ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="ornek@email.com" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Şifre Sıfırlama Linki Gönder</button>

                    <div class="form-group" style="text-align: center; margin-top: 1rem;">
                        <a href="auth-cover-login.php" style="color: var(--primary-color); text-decoration: none;">
                            <i class="bi bi-arrow-left"></i> Giriş Sayfasına Dön
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
