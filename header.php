<?php
// header.php
?>
<!doctype html>
<html lang="en" data-bs-theme="blue-theme">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>VYNE College</title>
  <!--favicon-->
  <link rel="icon" href="assets/images/logo1.png" type="image/png">
  <!-- loader-->
  <link href="assets/css/pace.min.css" rel="stylesheet">
  <script src="assets/js/pace.min.js"></script>
  <!--plugins-->
  <link href="assets/plugins/OwlCarousel/css/owl.carousel.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/plugins/lightbox/dist/css/glightbox.min.css">
  <!--bootstrap css-->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined" rel="stylesheet">
  <!--main css-->
  <link href="assets/css/bootstrap-extended.css" rel="stylesheet">
  <link href="sass/main.css" rel="stylesheet">
  <link href="assets/css/horizontal-menu.css" rel="stylesheet">
  <link href="sass/dark-theme.css" rel="stylesheet">
  <link href="sass/semi-dark.css" rel="stylesheet">
  <link href="sass/blue-theme.css" rel="stylesheet">
  <link href="sass/bordered-theme.css" rel="stylesheet">
</head>

<body>
 
  <!--start header-->
  <header class="top-header" id="Parent_Scroll_Div">
    <nav class="navbar navbar-expand-xl bg-transparent align-items-center gap-3 container px-4 px-lg-0">
      <div class="logo-header d-none d-xl-flex align-items-center gap-2">
        <div class="logo-icon">
          <img src="assets/images/logo-icon.png" class="logo-img" width="45" alt="">
        </div>
        <div class="logo-name">
          <h5 class="mb-0">VYNE College</h5>
        </div>
      </div>
      <div class="btn-toggle d-xl-none" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
        <a href="javascript:;"><i class="material-icons-outlined">menu</i></a>
      </div>

      <div class="offcanvas offcanvas-start w-260" tabindex="-1" id="offcanvasNavbar">
        <div class="offcanvas-header border-bottom h-70">
          <div class="d-flex align-items-center gap-2">
            <div class="">
              <img src="assets/images/logo-icon.png" class="logo-icon" width="45" alt="logo icon">
            </div>
            <div class="">
              <h4 class="logo-text">VYNE College</h4>
            </div>
          </div>
          <a href="javascript:;" class="primaery-menu-close" data-bs-dismiss="offcanvas">
            <i class="material-icons-outlined">close</i>
          </a>
        </div>
        <div class="offcanvas-body p-0 primary-menu">
          <ul class="navbar-nav align-items-center mx-auto gap-0 gap-xl-1">
            <li class="nav-item">
              <a class="nav-link" href="#home">
                <div class="parent-icon"><i class="material-icons-outlined">home</i>
                </div>
                <div class="menu-title">Ana Sayfa</div>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#About">
                <div class="parent-icon"><i class="material-icons-outlined">info</i>
                </div>
                <div class="menu-title">Hakkımızda</div>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#Portfolio">
                <div class="parent-icon"><i class="material-icons-outlined">photo_camera</i>
                </div>
                <div class="menu-title">Oyundan Kareler</div>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#Team">
                <div class="parent-icon"><i class="material-icons-outlined">people_alt</i>
                </div>
                <div class="menu-title">Geliştirme Ekibi</div>
              </a>
            </li>
          </ul>
        </div>
      </div>
      <div class="">
        <button class="btn btn-grd btn-grd-primary raised d-flex align-items-center rounded-5 gap-2 px-4" type="button">
          <i class="material-icons-outlined">account_circle</i> <a style="color: white;" href="auth-cover-login.php"> Giriş Yap</a>
        </button>
      </div>
    </nav>
  </header>
  <!--end top header-->