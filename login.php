<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $errors = [];

    if(empty($email) || empty($password)){
        $errors[] = "Email ve şifre gereklidir.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if($user){
            if(password_verify($password, $user['password'])){
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: index.php");
                exit;
            } else {
                $errors[] = "Şifre yanlış.";
            }
        } else {
            $errors[] = "Kullanıcı bulunamadı.";
        }
    }
}
?>
<!doctype html>
<html lang="en" data-bs-theme="blue-theme">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>VYNE College - Giriş Yap</title>
  <!-- Şablonunuzdaki CSS dosyalarını ekleyin -->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="sass/main.css" rel="stylesheet">
  <link href="sass/dark-theme.css" rel="stylesheet">
  <link href="sass/blue-theme.css" rel="stylesheet">
  <link href="sass/responsive.css" rel="stylesheet">
</head>
<body>
  <!-- Giriş için attığınız HTML şablonu -->
  <div class="section-authentication-cover">
    <div class="">
      <div class="row g-0">
        <!-- Sol bölüm: görsel -->
        <div class="col-12 col-xl-7 col-xxl-8 auth-cover-left align-items-center justify-content-center d-none d-xl-flex border-end bg-transparent">
          <div class="card rounded-0 mb-0 border-0 shadow-none bg-transparent bg-none">
            <div class="card-body">
              <img src="assets/images/auth/login1.png" class="img-fluid auth-img-cover-login" width="650" alt="">
            </div>
          </div>
        </div>
        <!-- Sağ bölüm: form -->
        <div class="col-12 col-xl-5 col-xxl-4 auth-cover-right align-items-center justify-content-center border-top border-4 border-primary">
          <div class="card rounded-0 m-3 mb-0 border-0 shadow-none bg-none">
            <div class="card-body p-sm-5">
              <h4 class="fw-bold">Şimdi Başlayın</h4>
              <p class="mb-0">Hesabınıza giriş yapmak için kimlik bilgilerinizi girin</p>
              <!-- Hata mesajlarını burada gösterin -->
              <?php 
              if(!empty($errors)){
                  foreach($errors as $error){
                      echo "<p style='color:red;'>$error</p>";
                  }
              }
              if(isset($_SESSION['message'])){
                  echo "<p style='color:green;'>".$_SESSION['message']."</p>";
                  unset($_SESSION['message']);
              }
              ?>
              <div class="form-body mt-4">
                <form class="row g-3" method="POST" action="">
                  <div class="col-12">
                    <label for="inputEmailAddress" class="form-label">Email</label>
                    <input type="email" class="form-control" id="inputEmailAddress" name="email" placeholder="jhon@example.com" required>
                  </div>
                  <div class="col-12">
                    <label for="inputChoosePassword" class="form-label">Şifre</label>
                    <div class="input-group" id="show_hide_password">
                      <input type="password" class="form-control" id="inputChoosePassword" name="password" placeholder="Şifre Girin" required> 
                      <a href="javascript:;" class="input-group-text bg-transparent">
                        <i class="bi bi-eye-slash-fill"></i>
                      </a>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked">
                      <label class="form-check-label" for="flexSwitchCheckChecked">Beni Hatırla</label>
                    </div>
                  </div>
                  <div class="col-md-6 text-end">
                    <a href="forgot_password.php">Şifrenimi Unuttun ?</a>
                  </div>
                  <div class="col-12">
                    <div class="d-grid">
                      <button type="submit" class="btn btn-grd-primary">Giriş Yap</button>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="text-start">
                      <p class="mb-0">Henüz bir hesabınız yok mu? <a href="register.php">Buradan kayıt olun</a></p>
                    </div>
                  </div>
                </form>
              </div><!-- form-body -->
            </div>
          </div>
        </div>
      </div>
      <!-- end row -->
    </div>
  </div>
  <!-- Gerekli JS dosyalarını ekleyin -->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
