<?php
session_start();
require 'config.php';

if(!isset($_GET['token'])){
    header("Location: auth-cover-login.php");
    exit;
}

$token = $_GET['token'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()");
$stmt->execute([$token]);
$user = $stmt->fetch();

if(!$user){
    $_SESSION['error'] = "Geçersiz veya süresi dolmuş token.";
    header("Location: auth-cover-login.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $errors = [];
    
    if(empty($password)){
        $errors[] = "Şifre gereklidir.";
    } elseif(strlen($password) < 6){
        $errors[] = "Şifre en az 6 karakter olmalıdır.";
    } elseif($password != $confirm_password){
        $errors[] = "Şifreler eşleşmiyor.";
    }
    
    if(empty($errors)){
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
        if($stmt->execute([$hashed_password, $user['id']])){
            $_SESSION['message'] = "Şifreniz başarıyla güncellendi.";
            header("Location: auth-cover-login.php");
            exit;
        } else {
            $errors[] = "Hata oluştu.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifre Sıfırlama - VYNE College</title>
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
                <h1>Yeni Şifre Belirleyin</h1>
                <p>Güvenli bir şifre seçin</p>
                <img src="assets/images/auth/forgot-password1.png" alt="Şifre Sıfırlama İllüstrasyon" style="max-width: 400px; margin-top: 2rem;">
            </div>
        </div>
        <div class="auth-right">
            <div class="auth-form-container">
                <img src="assets/images/logo1.png" alt="Logo" style="max-width: 150px;">
                <h2>Şifre Sıfırlama</h2>
                <p>Yeni şifrenizi belirleyin</p>
                
                <?php if(!empty($errors)): ?>
                    <?php foreach($errors as $error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label class="form-label" for="password">Yeni Şifre</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Yeni şifrenizi girin" required>
                            <span class="input-group-text" id="togglePassword">
                                <i class="bi bi-eye-slash"></i>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="confirm_password">Şifre Tekrar</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Şifrenizi tekrar girin" required>
                            <span class="input-group-text" id="toggleConfirmPassword">
                                <i class="bi bi-eye-slash"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Şifreyi Güncelle</button>

                    <div class="form-group" style="text-align: center; margin-top: 1rem;">
                        <a href="auth-cover-login.php" style="color: var(--primary-color); text-decoration: none;">
                            <i class="bi bi-arrow-left"></i> Giriş Sayfasına Dön
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, toggleId) {
            const input = document.getElementById(inputId);
            const toggle = document.getElementById(toggleId);
            const icon = toggle.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }
        }

        document.getElementById('togglePassword').addEventListener('click', function() {
            togglePassword('password', 'togglePassword');
        });

        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            togglePassword('confirm_password', 'toggleConfirmPassword');
        });
    </script>
</body>
</html>
