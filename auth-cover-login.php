<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;
    $errors = [];

    if(empty($email) || empty($password)){
        $errors[] = "Email ve şifre gereklidir.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if($user && password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            if($remember){
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
                
                $stmt = $pdo->prepare("UPDATE users SET remember_token = ?, token_expires = ? WHERE id = ?");
                $stmt->execute([$token, $expires, $user['id']]);
                
                setcookie('remember_token', $token, time() + (86400 * 30), '/');
            }
            
            $updateLastActive = $pdo->prepare("UPDATE users SET last_active = CURRENT_TIMESTAMP WHERE id = ?");
            $updateLastActive->execute([$user['id']]);
            
            header("Location: be_pages_dashboard.php");
            exit;
        } else {
            $errors[] = "Kullanıcı bulunamadı veya şifre yanlış.";
        }
    }
}

if(!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])){
    $stmt = $pdo->prepare("SELECT * FROM users WHERE remember_token = ? AND token_expires > NOW()");
    $stmt->execute([$_COOKIE['remember_token']]);
    $user = $stmt->fetch();
    
    if($user){
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        header("Location: be_pages_dashboard.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap - VYNE College</title>
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
                <h1>VYNE College'a Hoş Geldiniz</h1>
                <p>Eğitim yolculuğunuza bizimle devam edin</p>
                <img src="assets/images/auth/login1.png" alt="Login İllüstrasyon" style="max-width: 400px; margin-top: 2rem;">
            </div>
        </div>
        <div class="auth-right">
            <div class="auth-form-container">
                <img src="assets/images/logo1.png" alt="Logo" style="max-width: 150px;">
                <h2>Giriş Yap</h2>
                <p>Hesabınıza giriş yapmak için kimlik bilgilerinizi girin</p>
                
                <?php if(!empty($errors)): ?>
                    <?php foreach($errors as $error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="ornek@email.com" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Şifre</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Şifrenizi girin" required>
                            <span class="input-group-text" id="togglePassword">
                                <i class="bi bi-eye-slash"></i>
                            </span>
                        </div>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Beni Hatırla</label>
                    </div>

                    <div class="form-group">
                        <a href="auth-cover-forgot-password.php" style="color: var(--primary-color); text-decoration: none;">Şifremi Unuttum</a>
                    </div>

                    <button type="submit" class="btn btn-primary">Giriş Yap</button>

                    <div class="form-group" style="text-align: center; margin-top: 1rem;">
                        <p>Hesabınız yok mu? <a href="auth-cover-register.php" style="color: var(--primary-color); text-decoration: none;">Kayıt Ol</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            } else {
                password.type = 'password';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }
        });
    </script>
</body>
</html>
