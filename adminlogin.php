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
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if($user && password_verify($password, $user['password'])){
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_role'] = $user['role'];
            
            // Son aktif zamanını güncelle
            $updateLastActive = $pdo->prepare("UPDATE users SET last_active = CURRENT_TIMESTAMP WHERE id = ?");
            $updateLastActive->execute([$user['id']]);
            
            header("Location: admin_panel.php");
            exit;
        } else {
            $errors[] = "Kullanıcı bulunamadı, şifre yanlış veya admin yetkisi yok.";
        }
    }
}
?>
<!doctype html>
<html lang="tr" data-bs-theme="blue-theme">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VYNE Admin Girişi</title>
    <link rel="icon" href="assets/images/logo-icon.png" type="image/png">
    <link href="assets/css/pace.min.css" rel="stylesheet">
    <script src="assets/js/pace.min.js"></script>
    <link href="assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet">
    <link href="assets/plugins/metismenu/metisMenu.min.css" rel="stylesheet">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined" rel="stylesheet">
    <link href="assets/css/bootstrap-extended.css" rel="stylesheet">
    <link href="sass/main.css" rel="stylesheet">
    <link href="sass/dark-theme.css" rel="stylesheet">
    <link href="sass/blue-theme.css" rel="stylesheet">
    <link href="sass/responsive.css" rel="stylesheet">
    <style>
        .logo-container {
            text-align: center;
            padding: 2rem 0;
        }
        .logo-container img {
            max-width: 200px;
            height: auto;
        }
        .auth-cover-left {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-cover-right {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .auth-img-cover-login {
            max-width: 100%;
            height: auto;
        }
        @media (max-width: 1200px) {
            .auth-cover-left {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="section-authentication-cover">
    <div class="">
        <div class="row g-0">
            <!-- Sol bölüm (görsel) -->
            <div class="col-12 col-xl-7 col-xxl-8 auth-cover-left d-none d-xl-flex border-end bg-transparent">
                <div class="card rounded-0 border-0 bg-transparent">
                    <div class="card-body">
                        <div class="logo-container">
                            <img src="assets/images/logo1.png" alt="VYNE Admin Logo">
                        </div>
                        <img src="assets/images/auth/login1.png" class="img-fluid auth-img-cover-login" width="650" alt="">
                    </div>
                </div>
            </div>
            <!-- Sağ bölüm (form) -->
            <div class="col-12 col-xl-5 col-xxl-4 auth-cover-right border-top border-4 border-danger d-flex align-items-center justify-content-center">
                <div class="card rounded-0 m-3 border-0 bg-none">
                    <div class="card-body p-sm-5">
                        <h4 class="fw-bold">Admin Girişi</h4>
                        <p class="mb-0">Admin paneline erişmek için kimlik bilgilerinizi girin</p>
                        <?php
                        if(!empty($errors)){
                            foreach($errors as $error){
                                echo "<p style='color:red;'>$error</p>";
                            }
                        }
                        ?>
                        <div class="form-body mt-4">
                            <form class="row g-3" method="POST" action="">
                                <div class="col-12">
                                    <label for="inputEmailAddress" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="inputEmailAddress" name="email" placeholder="admin@example.com" required>
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
                                <div class="col-12">
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-grd-danger">Admin Girişi Yap</button>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="text-start">
                                        <p class="mb-0">Normal kullanıcı girişi için <a href="auth-cover-login.php">tıklayın</a></p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function () {
        $("#show_hide_password a").on('click', function (event) {
            event.preventDefault();
            if ($('#show_hide_password input').attr("type") == "text") {
                $('#show_hide_password input').attr('type', 'password');
                $('#show_hide_password i').addClass("bi-eye-slash-fill");
                $('#show_hide_password i').removeClass("bi-eye-fill");
            } else if ($('#show_hide_password input').attr("type") == "password") {
                $('#show_hide_password input').attr('type', 'text');
                $('#show_hide_password i').removeClass("bi-eye-slash-fill");
                $('#show_hide_password i').addClass("bi-eye-fill");
            }
        });
    });
</script>

</body>
</html> 