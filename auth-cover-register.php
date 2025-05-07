<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username         = trim($_POST['username']);
    $email            = trim($_POST['email']);
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $fivem_hex        = trim($_POST['fivem_hex']);
    $discord_id       = trim($_POST['discord_id']);
    $terms_accepted   = isset($_POST['terms_accepted']) ? true : false;
    $errors = [];

    if(!$terms_accepted){
        $errors[] = "Şartlar ve koşulları kabul etmelisiniz.";
    }

    if(empty($username)){
        $errors[] = "Kullanıcı adı gereklidir.";
    }
    if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors[] = "Geçerli bir email girin.";
    }
    if(empty($password)){
        $errors[] = "Şifre gereklidir.";
    }
    if($password !== $confirm_password){
        $errors[] = "Şifreler eşleşmiyor.";
    }
    
    if(empty($errors)){
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if($stmt->fetch()){
            $errors[] = "Bu email adresi zaten kayıtlı.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $created_at = date('Y-m-d H:i:s');
            $role = 'user';
            
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, fivem_hex, discord_id, created_at, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if($stmt->execute([$username, $email, $hashed_password, $fivem_hex, $discord_id, $created_at, $role])){
                $_SESSION['message'] = "Kayıt başarılı. Lütfen giriş yapın.";
                header("Location: auth-cover-login.php");
                exit;
            } else {
                $errors[] = "Kayıt sırasında bir hata oluştu.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol - VYNE College</title>
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
                <h1>VYNE College'a Katılın</h1>
                <p>Eğitim yolculuğunuza bizimle başlayın</p>
                <img src="assets/images/auth/register1.png" alt="Kayıt İllüstrasyon" style="max-width: 400px; margin-top: 2rem;">
            </div>
        </div>
        <div class="auth-right">
            <div class="auth-form-container">
                <img src="assets/images/logo1.png" alt="Logo" style="max-width: 150px;">
                <h2>Kayıt Ol</h2>
                <p>Hesabınızı oluşturmak için bilgilerinizi girin</p>
                
                <?php if(!empty($errors)): ?>
                    <?php foreach($errors as $error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label class="form-label" for="username">Kullanıcı Adı</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Kullanıcı adınızı girin" required>
                    </div>

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

                    <div class="form-group">
                        <label class="form-label" for="confirm_password">Şifre Tekrar</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Şifrenizi tekrar girin" required>
                            <span class="input-group-text" id="toggleConfirmPassword">
                                <i class="bi bi-eye-slash"></i>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="fivem_hex">FiveM HEX</label>
                        <input type="text" class="form-control" id="fivem_hex" name="fivem_hex" placeholder="FiveM HEX'nizi girin">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="discord_id">Discord ID</label>
                        <input type="text" class="form-control" id="discord_id" name="discord_id" placeholder="Discord ID'nizi girin">
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="terms_accepted" name="terms_accepted" required>
                        <label class="form-check-label" for="terms_accepted">
                            <a href="KVKK.php">Şartlar ve Koşulları</a> okudum ve kabul ediyorum
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary">Kayıt Ol</button>

                    <div class="form-group" style="text-align: center; margin-top: 1rem;">
                        <p>Zaten hesabınız var mı? <a href="auth-cover-login.php" style="color: var(--primary-color); text-decoration: none;">Giriş Yap</a></p>
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
