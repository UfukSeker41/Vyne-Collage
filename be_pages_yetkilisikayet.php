<?php
session_start();
require_once 'config.php';

// Kullanıcı giriş yapmış mı kontrol et
if (!isset($_SESSION['user_id'])) {
    header('Location: auth-cover-login.php');
    exit();
}

// Kullanıcı bilgilerini veritabanından al
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Kullanıcı bulunamadıysa
if (!$user) {
    header('Location: auth-cover-login.php');
    exit();
}

// Son aktif zamanı güncelle
$stmt = $pdo->prepare("UPDATE users SET last_active = CURRENT_TIMESTAMP WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);

// Form gönderildi mi kontrol et
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $admin_name = trim($_POST['admin_name']);
    $subject = trim($_POST['subject']);
    $content = trim($_POST['content']);
    $errors = [];

    if(empty($admin_name)){
        $errors[] = "Yetkili ismi gereklidir.";
    }
    if(empty($subject)){
        $errors[] = "Konu başlığı gereklidir.";
    }
    if(empty($content)){
        $errors[] = "Şikayet içeriği gereklidir.";
    }

    if(empty($errors)){
        try {
            $stmt = $pdo->prepare("INSERT INTO admin_complaints (admin_name, subject, content, user_id, status, created_at) VALUES (?, ?, ?, ?, 'Beklemede', CURRENT_TIMESTAMP)");
            if($stmt->execute([$admin_name, $subject, $content, $_SESSION['user_id']])){
                $_SESSION['success'] = "Şikayetiniz başarıyla gönderildi.";
                header("Location: be_pages_yetkilisikayet.php");
                exit;
            } else {
                $errors[] = "Şikayet gönderilirken bir hata oluştu.";
            }
        } catch(PDOException $e) {
            $errors[] = "Veritabanı hatası: " . $e->getMessage();
        }
    }
}

// Kullanıcının önceki şikayetlerini getir
$stmt = $pdo->prepare("SELECT * FROM admin_complaints WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$previous_complaints = $stmt->fetchAll();

?>
<!doctype html>
<html lang="en" class="remember-theme">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">

    <title>VYNE College Dashboard</title>

    <meta name="description" content="VYNE College Dashboard created by pixelcave">
    <meta name="author" content="pixelcave">
    <meta name="robots" content="index, follow">

    <!-- Open Graph Meta -->
    <meta property="og:title" content="VYNE College Dashboard">
    <meta property="og:site_name" content="Codebase">
    <meta property="og:description" content="VYNE College Dashboard created by pixelcave">
    <meta property="og:type" content="website">
    <meta property="og:url" content="">
    <meta property="og:image" content="">

    <!-- Icons -->
    <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
    <link rel="shortcut icon" href="dashboardassets/media/photos/logo1.png">
    <link rel="icon" type="image/png" sizes="192x192" href="dashboardassets/media/photos/logo1.png">
    <link rel="apple-touch-icon" sizes="180x180" href="dashboardassets/media/photos/logo1.png">
    <!-- END Icons -->

    <!-- Stylesheets -->

    <!-- Codebase framework -->
    <link rel="stylesheet" id="css-main" href="dashboardassets/css/codebase.css">
    
    <script src="dashboardassets/js/setTheme.js"></script>

    <style>
          /* Form Container */
    /* Form kapsayıcı */
    .form-container {
      max-width: 400px;
      margin: 50px auto;
      padding: 20px;
      background-color: var(--light-bg);
      border: 1px solid var(--light-border);
      border-radius: 5px;
    }
    @media (prefers-color-scheme: dark) {
      .form-container {
        background-color: var(--dark-bg);
        border: 1px solid var(--dark-border);
      }
    }
    .form-container h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    .form-container label {
      display: block;
      margin-bottom: 5px;
    }
    .form-container input,
    .form-container textarea {
      width: 100%;
      padding: 8px;
      margin-bottom: 15px;
      border: 1px solid var(--light-border);
      border-radius: 3px;
      background-color: var(--light-bg);
      color: var(--light-text);
    }
    @media (prefers-color-scheme: dark) {
      .form-container input,
      .form-container textarea {
        background-color: var(--dark-input-bg);
        border: 1px solid var(--dark-border);
        color: var(--dark-text);
      }
    }
    .form-container button {
      width: 100%;
      padding: 10px;
      background-color: #007bff;
      border: none;
      color: #fff;
      font-size: 16px;
      border-radius: 3px;
      cursor: pointer;
    }
    .form-container button:hover {
      background-color: #0056b3;
    }
        /* Temel stil: input ve textarea */
    input[type="text"],
    input[type="number"],
    textarea {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
      background-color: #ffffff;
      color: #333333;
    }

    /* Odaklanma durumu */
    input[type="text"]:focus,
    input[type="number"]:focus,
    textarea:focus {
      border-color: #4e73df;
      outline: none;
    }

    </style>
  </head>

  <body>
    <div id="page-container" class="sidebar-o enable-page-overlay side-scroll page-header-modern main-content-boxed">
      <!-- Side Overlay-->
      <aside id="side-overlay">
        <!-- Side Header -->
        <div class="content-header">
          <!-- User Avatar -->
          <a class="img-link me-2" href="be_pages_generic_profile.php">
            <img class="img-avatar img-avatar32" src="dashboardassets/media/avatars/avatar15.jpg" alt="">
          </a>
          <!-- END User Avatar -->

          <!-- User Info -->
          <a class="link-fx text-body-color-dark fw-semibold fs-sm" href="be_pages_generic_profile.php">
            <?php echo htmlspecialchars($user['username']); ?>
          </a>
          <!-- END User Info -->

          <!-- Close Side Overlay -->
          <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
          <button type="button" class="btn btn-sm btn-alt-danger ms-auto" data-toggle="layout" data-action="side_overlay_close">
            <i class="fa fa-fw fa-times"></i>
          </button>
          <!-- END Close Side Overlay -->
        </div>
        <!-- END Side Header -->

        <!-- Side Content -->
        <div class="content-side">
          <!-- Profile -->
          <div class="block pull-x">
            <div class="block-header bg-body-light">
              <h3 class="block-title">
                <i class="fa fa-fw fa-pencil-alt opacity-50 me-1"></i> Profil
              </h3>
              <div class="block-options">
                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="content_toggle"></button>
              </div>
            </div>
            <div class="block-content block-content-full">
              <form action="be_pages_dashboard.php" method="POST">
                <div class="mb-3">
                  <label class="form-label" for="side-overlay-profile-name">Kullanıcı Adı</label>
                  <div class="input-group">
                    <input type="text" class="form-control" id="side-overlay-profile-name" name="side-overlay-profile-name" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                    <span class="input-group-text">
                      <i class="fa fa-user"></i>
                    </span>
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="side-overlay-profile-email">E-posta</label>
                  <div class="input-group">
                    <input type="email" class="form-control" id="side-overlay-profile-email" name="side-overlay-profile-email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                    <span class="input-group-text">
                      <i class="fa fa-envelope"></i>
                    </span>
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="side-overlay-profile-password">Yeni Şifre</label>
                  <div class="input-group">
                    <input type="password" class="form-control" id="side-overlay-profile-password" name="side-overlay-profile-password" placeholder="Yeni şifre..">
                    <span class="input-group-text">
                      <i class="fa fa-asterisk"></i>
                    </span>
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="side-overlay-profile-password-confirm">Yeni Şifre Tekrar</label>
                  <div class="input-group">
                    <input type="password" class="form-control" id="side-overlay-profile-password-confirm" name="side-overlay-profile-password-confirm" placeholder="Yeni şifre tekrar..">
                    <span class="input-group-text">
                      <i class="fa fa-asterisk"></i>
                    </span>
                  </div>
                </div>
                <div class="row">
                  <div class="col-6">
                    <button type="submit" name="update_profile" class="btn btn-alt-primary">
                      <i class="fa fa-sync opacity-50 me-1"></i> Güncelle
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <!-- END Profile -->

         
        </div>
        <!-- END Side Content -->
      </aside>
      <!-- END Side Overlay -->

      <!-- Sidebar -->
      <!--
        Helper classes

        Adding .smini-hide to an element will make it invisible (opacity: 0) when the sidebar is in mini mode
        Adding .smini-show to an element will make it visible (opacity: 1) when the sidebar is in mini mode
          If you would like to disable the transition, just add the .no-transition along with one of the previous 2 classes

        Adding .smini-hidden to an element will hide it when the sidebar is in mini mode
        Adding .smini-visible to an element will show it only when the sidebar is in mini mode
        Adding 'smini-visible-block' to an element will show it (display: block) only when the sidebar is in mini mode
      -->
      <nav id="sidebar">
        <!-- Sidebar Content -->
        <div class="sidebar-content">
          <!-- Side Header -->
          <div class="content-header justify-content-lg-center">
            <!-- Logo -->
            <div>
              <span class="smini-visible fw-bold tracking-wide fs-lg">
                V<span class="text-primary">Y</span>
              </span>
              <a class="link-fx fw-bold tracking-wide mx-auto" href="be_pages_dashboard.php">
                <span class="smini-hidden">
                  <span class="fs-4 text-dual">VYNE</span> <span class="fs-4 text-primary">College</span>
                </span>
              </a>
            </div>
            <!-- END Logo -->

            <!-- Options -->
            <div>
              <!-- Close Sidebar, Visible only on mobile screens -->
              <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
              <button type="button" class="btn btn-sm btn-alt-danger d-lg-none" data-toggle="layout" data-action="sidebar_close">
                <i class="fa fa-fw fa-times"></i>
              </button>
              <!-- END Close Sidebar -->
            </div>
            <!-- END Options -->
          </div>
          <!-- END Side Header -->

          <!-- Sidebar Scrolling -->
          <div class="js-sidebar-scroll">
            <!-- Side User -->
            <div class="content-side content-side-user px-0 py-0">
              <!-- Visible only in mini mode -->
              <div class="smini-visible-block animated fadeIn px-3">
                <img class="img-avatar img-avatar32" src="assets/images/logo1.png" alt="">
              </div>
              <!-- END Visible only in mini mode -->

              <!-- Visible only in normal mode -->
              <div class="smini-hidden text-center mx-auto">
                <a class="img-link" href="be_pages_generic_profile.php">
                  <img class="img-avatar" src="assets/images/logo1.png" alt="">
                </a>
                <ul class="list-inline mt-3 mb-0">
                  <li class="list-inline-item">
                    <a class="link-fx text-dual fs-sm fw-semibold text-uppercase" href="be_pages_generic_profile.php">
                      <?php echo htmlspecialchars($user['username']); ?>
                    </a>
                  </li>
                  <li class="list-inline-item">
                    <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                    <a class="link-fx text-dual" data-toggle="layout" data-action="dark_mode_toggle" href="javascript:void(0)">
                      <i class="far fa-fw fa-moon" data-dark-mode-icon></i>
                    </a>
                  </li>
                  <li class="list-inline-item">
                    <a class="link-fx text-dual" href="op_auth_signin.html">
                      <i class="fa fa-sign-out-alt"></i>
                    </a>
                  </li>
                </ul>
              </div>
              <!-- END Visible only in normal mode -->
            </div>
            <!-- END Side User -->

            <!-- Side Navigation -->
            <div class="content-side content-side-full">
              <ul class="nav-main">
                <li class="nav-main-item">
                  <a class="nav-main-link" href="be_pages_dashboard.php">
                    <i class="nav-main-link-icon fa fa-home"></i>
                    <span class="nav-main-link-name">Ana Sayfa</span>
                  </a>
                </li>
                <li class="nav-main-item">
                  <a class="nav-main-link" href="https://whimsical.com/vyne-college-JLhM2HQh1YG6HEqsMVidMq">
                    <i class="nav-main-link-icon fa fa-project-diagram"></i>
                    <span class="nav-main-link-name">Whimsical</span>
                  </a>
                </li>
                <li class="nav-main-item">
                  <a class="nav-main-link" href="be_pages_rules.php">
                    <i class="nav-main-link-icon fa fa-gavel"></i>
                    <span class="nav-main-link-name">VYNE Kurallar</span>
                  </a>
                </li>
                <li class="nav-main-item">
                  <a class="nav-main-link" href="be_pages_okulkurallari.php">
                    <i class="nav-main-link-icon fa fa-book"></i>
                    <span class="nav-main-link-name">Okul Kuralları</span>
                  </a>
                </li>
                <li class="nav-main-item">
                  <a class="nav-main-link" href="be_pages_whitelist.php">
                    <i class="nav-main-link-icon fa fa-clipboard-list"></i>
                    <span class="nav-main-link-name">Whitelist Başvuru</span>
                  </a>
                </li>
                <li class="nav-main-item">
                  <a class="nav-main-link active" href="be_pages_yetkilisikayet.php">
                    <i class="nav-main-link-icon fa fa-flag"></i>
                    <span class="nav-main-link-name">Yetkili Şikayet</span>
                  </a>
                </li>
              </ul>
            
            <!-- END Side Navigation -->
          </div>
          <!-- END Sidebar Scrolling -->
        </div>
        <!-- Sidebar Content -->
      </nav>
      <!-- END Sidebar -->

      <!-- Header -->
      <header id="page-header">
        <!-- Header Content -->
        <div class="content-header">
          <!-- Left Section -->
          <div class="space-x-1">
            <!-- Toggle Sidebar -->
            <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
            <button type="button" class="btn btn-sm btn-alt-secondary" data-toggle="layout" data-action="sidebar_toggle">
              <i class="fa fa-fw fa-bars"></i>
            </button>
            <!-- END Toggle Sidebar -->

            <!-- Open Search Section -->
            
            <!-- END Open Search Section -->

            <!-- Color Themes -->
            <div class="dropdown d-inline-block">
              <button type="button" class="btn btn-sm btn-alt-secondary" id="page-header-themes-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-fw fa-brush"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-lg p-0" aria-labelledby="page-header-themes-dropdown">
                <div class="px-3 py-2 bg-body-light rounded-top">
                  <h5 class="fs-sm text-center mb-0">
                    Color Themes
                  </h5>
                </div>
                <div class="p-3">
                  <div class="row g-0 text-center">
                    <div class="col-2">
                      <a class="text-default" data-toggle="theme" data-theme="default" href="javascript:void(0)">
                        <i class="fa fa-2x fa-circle"></i>
                      </a>
                    </div>
                    <div class="col-2">
                      <a class="text-elegance" data-toggle="theme" data-theme="dashboardassets/css/themes/elegance.min.css" href="javascript:void(0)">
                        <i class="fa fa-2x fa-circle"></i>
                      </a>
                    </div>
                    <div class="col-2">
                      <a class="text-pulse" data-toggle="theme" data-theme="dashboardassets/css/themes/pulse.min.css" href="javascript:void(0)">
                        <i class="fa fa-2x fa-circle"></i>
                      </a>
                    </div>
                    <div class="col-2">
                      <a class="text-flat" data-toggle="theme" data-theme="dashboardassets/css/themes/flat.min.css" href="javascript:void(0)">
                        <i class="fa fa-2x fa-circle"></i>
                      </a>
                    </div>
                    <div class="col-2">
                      <a class="text-corporate" data-toggle="theme" data-theme="dashboardassets/css/themes/corporate.min.css" href="javascript:void(0)">
                        <i class="fa fa-2x fa-circle"></i>
                      </a>
                    </div>
                    <div class="col-2">
                      <a class="text-earth" data-toggle="theme" data-theme="dashboardassets/css/themes/earth.min.css" href="javascript:void(0)">
                        <i class="fa fa-2x fa-circle"></i>
                      </a>
                    </div>
                  </div>
                </div>
                <div class="px-3 py-2 bg-body-light rounded-top">
                  <h5 class="fs-sm text-center mb-0">
                    Dark Mode
                  </h5>
                </div>
                <div class="px-2 py-3">
                  <div class="row g-1 text-center">
                    <div class="col-4">
                      <button type="button" class="dropdown-item mb-0 d-flex align-items-center gap-2" data-toggle="layout" data-action="dark_mode_off" data-dark-mode="off">
                        <i class="far fa-sun fa-fw opacity-50"></i>
                        <span class="fs-sm fw-medium">Light</span>
                      </button>
                    </div>
                    <div class="col-4">
                      <button type="button" class="dropdown-item mb-0 d-flex align-items-center gap-2" data-toggle="layout" data-action="dark_mode_on" data-dark-mode="on">
                        <i class="fa fa-moon fa-fw opacity-50"></i>
                        <span class="fs-sm fw-medium">Dark</span>
                      </button>
                    </div>
                    <div class="col-4">
                      <button type="button" class="dropdown-item mb-0 d-flex align-items-center gap-2" data-toggle="layout" data-action="dark_mode_system" data-dark-mode="system">
                        <i class="fa fa-desktop fa-fw opacity-50"></i>
                        <span class="fs-sm fw-medium">System</span>
                      </button>
                    </div>
                  </div>
                </div>
                <div class="p-3 bg-body-light rounded-bottom">
                  <div class="row g-sm text-center">
                  </div>
                </div>
              </div>
            </div>
            <!-- END Color Themes -->
          </div>
          <!-- END Left Section -->

          <!-- Right Section -->
          <div class="space-x-1">
            <!-- User Dropdown -->
            <div class="dropdown d-inline-block">
              <button type="button" class="btn btn-sm btn-alt-secondary" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-user d-sm-none"></i>
                <span class="d-none d-sm-inline-block fw-semibold"><?php echo htmlspecialchars($user['username']); ?></span>
                <i class="fa fa-angle-down opacity-50 ms-1"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-md dropdown-menu-end p-0" aria-labelledby="page-header-user-dropdown">
                <div class="px-2 py-3 bg-body-light rounded-top">
                  <h5 class="h6 text-center mb-0">
                    <?php echo htmlspecialchars($user['username']); ?>
                  </h5>
                </div>
                <div class="p-2">
                  <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1" href="be_pages_generic_profile.php">
                    <span>Profil</span>
                    <i class="fa fa-fw fa-user opacity-25"></i>
                  </a>
                  <div class="dropdown-divider"></div>

                  <!-- Toggle Side Overlay -->
                  <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                  <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1" href="javascript:void(0)" data-toggle="layout" data-action="side_overlay_toggle">
                    <span>Ayarlar</span>
                    <i class="fa fa-fw fa-wrench opacity-25"></i>
                  </a>
                  <!-- END Side Overlay -->

                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1" href="logout.php">
                    <span>Çıkış Yap</span>
                    <i class="fa fa-fw fa-sign-out-alt opacity-25"></i>
                  </a>
                </div>
              </div>
            </div>
            <!-- END User Dropdown -->

            <!-- Notifications -->
            <div class="dropdown d-inline-block">
              <button type="button" class="btn btn-sm btn-alt-secondary" id="page-header-notifications" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-bell"></i>
                <span class="text-primary">&bull;</span>
              </button>
              <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-notifications">
                <div class="px-2 py-3 bg-body-light rounded-top">
                  <h5 class="h6 text-center mb-0">Bildirimler</h5>
                </div>
                <ul class="nav-items my-2 fs-sm">
                  <li>
                    <a class="text-dark d-flex py-2" href="be_pages_whitelist.php">
                      <div class="flex-shrink-0 me-2 ms-3">
                        <i class="fa fa-fw fa-envelope text-primary"></i>
                      </div>
                      <div class="flex-grow-1 pe-2">
                        <p class="fw-medium mb-1">Vyne College: Sunucu whitelist başvuru atmadın mı? Ne bekliyorsun?</p>
                        <div class="text-muted">Hemen başvur!</div>
                      </div>
                    </a>
                  </li>
                </ul>
                <div class="p-2 bg-body-light rounded-bottom">
                  <a class="dropdown-item text-center mb-0">
                    Kapat
                  </a>
                </div>
              </div>
            </div>
            <!-- END Notifications -->

            <!-- Toggle Side Overlay -->
            <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
            <button type="button" class="btn btn-sm btn-alt-secondary" data-toggle="layout" data-action="side_overlay_toggle">
              <i class="fa fa-fw fa-stream"></i>
            </button>
            <!-- END Toggle Side Overlay -->
          </div>
          <!-- END Right Section -->
        </div>
        <!-- END Header Content -->

        <!-- Header Search -->
        <div id="page-header-search" class="overlay-header bg-body-extra-light">
          <div class="content-header">
            <form class="w-100" action="#" method="POST">
              <div class="input-group">
                <!-- Close Search Section -->
                <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                <button type="button" class="btn btn-secondary" data-toggle="layout" data-action="header_search_off">
                  <i class="fa fa-fw fa-times"></i>
                </button>
                <!-- END Close Search Section -->
                <input type="text" class="form-control" placeholder="Search or hit ESC.." id="page-header-search-input" name="page-header-search-input">
                <button type="submit" class="btn btn-secondary">
                  <i class="fa fa-fw fa-search"></i>
                </button>
              </div>
            </form>
          </div>
        </div>
        <!-- END Header Search -->

        <!-- Header Loader -->
        <!-- Please check out the Activity page under Elements category to see examples of showing/hiding it -->
        <div id="page-header-loader" class="overlay-header bg-primary">
          <div class="content-header">
            <div class="w-100 text-center">
              <i class="far fa-sun fa-spin text-white"></i>
            </div>
          </div>
        </div>
        <!-- END Header Loader -->
      </header>
      <!-- END Header -->

           <!-- Ana İçerik -->
     <main id="main-container">
        <div class="content">
          <!-- Yetkili Şikayet Formu -->
          <div class="whitelist-container">
            <h2 class="baslikortala">Yetkili Şikayet Formu</h2>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach($errors as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <!-- Şikayet Formu -->
            <div class="form-body mt-4">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="admin_name" class="form-label">Yetkili İsmi</label>
                        <input type="text" class="form-control" id="admin_name" name="admin_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="subject" class="form-label">Konu Başlığı</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Şikayet İçeriği</label>
                        <textarea class="form-control" id="content" name="content" rows="6" required></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Şikayeti Gönder</button>
                    </div>
                </form>
            </div>

            <!-- Önceki Şikayetler -->
            <?php if (!empty($previous_complaints)): ?>
              <div class="mt-5">
                <h3 class="baslikortala">Önceki Şikayetleriniz</h3>
                <div class="table-responsive">
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Tarih</th>
                        <th>Yetkili</th>
                        <th>Konu</th>
                        <th>Durum</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($previous_complaints as $complaint): ?>
                        <tr>
                          <td><?php echo date('d.m.Y H:i', strtotime($complaint['created_at'])); ?></td>
                          <td><?php echo htmlspecialchars($complaint['admin_name']); ?></td>
                          <td><?php echo htmlspecialchars($complaint['subject']); ?></td>
                          <td>
                            <span class="badge <?php echo $complaint['status'] === 'Beklemede' ? 'bg-warning' : ($complaint['status'] === 'İncelendi' ? 'bg-success' : 'bg-danger'); ?>">
                              <?php echo htmlspecialchars($complaint['status']); ?>
                            </span>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            <?php endif; ?>

          </div>
        </div>
      </main>

    <!--
        Codebase JS

        Core libraries and functionality
        webpack is putting everything together at dashboardassets/_js/main/app.js
    -->
    <script src="dashboardassets/js/codebase.app.min.js"></script>

    <!-- Page JS Plugins -->
    <script src="dashboardassets/js/plugins/chart.js/chart.umd.js"></script>

    <!-- Page JS Code -->
    <script src="dashboardassets/js/pages/be_pages_dashboard.min.js"></script>

  
    
  </body>
</html>