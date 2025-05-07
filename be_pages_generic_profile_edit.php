<?php 
session_start();
require_once 'config.php';

// Kullanıcı giriş yapmış mı kontrol et
if (!isset($_SESSION['user_id'])) {
    header('Location: op_auth_signin.html');
    exit();
}

// Kullanıcı bilgilerini veritabanından al
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Profil güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $username = $_POST['profile-settings-username'];
    $email = $_POST['profile-settings-email'];
    $discord_id = $_POST['profile-settings-discord'];
    $fivem_hex = $_POST['profile-settings-fivem'];
    
    try {
        // Profil bilgilerini güncelle
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, discord_id = ?, fivem_hex = ?, last_active = CURRENT_TIMESTAMP WHERE id = ?");
        if ($stmt->execute([$username, $email, $discord_id, $fivem_hex, $_SESSION['user_id']])) {
            $_SESSION['success_message'] = "Profil başarıyla güncellendi!";
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Duplicate entry
            $_SESSION['error_message'] = "Bu kullanıcı adı veya email adresi zaten kullanımda!";
        } else {
            $_SESSION['error_message'] = "Profil güncellenirken bir hata oluştu!";
        }
    }
}

// Şifre değiştirme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['profile-settings-password'];
    $new_password = $_POST['profile-settings-password-new'];
    $confirm_password = $_POST['profile-settings-password-new-confirm'];
    
    // Mevcut şifreyi kontrol et
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $stored_password = $stmt->fetchColumn();
    
    if (password_verify($current_password, $stored_password)) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            
            if ($stmt->execute([$hashed_password, $_SESSION['user_id']])) {
                $_SESSION['success_message'] = "Şifreniz başarıyla güncellendi!";
            } else {
                $_SESSION['error_message'] = "Şifre güncellenirken bir hata oluştu!";
            }
        } else {
            $_SESSION['error_message'] = "Yeni şifreler eşleşmiyor!";
        }
    } else {
        $_SESSION['error_message'] = "Mevcut şifre yanlış!";
    }
}

// Başarı ve hata mesajlarını göster
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
    unset($_SESSION['error_message']);
}
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

    <!-- You can include a specific file from css/themes/ folder to alter the default color theme of the template. eg: -->
    <!-- <link rel="stylesheet" id="css-theme" href="dashboardassets/css/themes/flat.min.css"> -->
    <!-- END Stylesheets -->

    <!-- Load and set color theme + dark mode preference (blocking script to prevent flashing) -->
    <script src="dashboardassets/js/setTheme.js"></script>
  </head>

  <body>
    <div id="page-container" class="sidebar-o enable-page-overlay side-scroll page-header-modern main-content-boxed">
      <!-- Side Overlay-->
      <aside id="side-overlay">
        <!-- Side Header -->
        <div class="content-header">
          <!-- User Avatar -->
          <a class="img-link me-2" href="be_pages_generic_profile.php">
            <img class="img-avatar img-avatar32" src="dashboardassets/images/logo1.png" alt="">
          </a>
          <!-- END User Avatar -->

          <!-- User Info -->
          <a class="link-fx text-body-color-dark fw-semibold fs-sm" href="be_pages_generic_profile.php">
            <?php echo htmlspecialchars($user['username']); ?>
          </a>
          <!-- END User Info -->

          <!-- Close Side Overlay -->
          
        </div>
        <!-- END Side Header -->

        <!-- Side Content -->
        <div class="content-side">
          <!-- Profile -->
          <div class="block pull-x">
            <div class="block-header bg-body-light">
              <h3 class="block-title">
                <i class="fa fa-fw fa-pencil-alt opacity-50 me-1"></i> Profile
              </h3>
              <div class="block-options">
                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="content_toggle"></button>
              </div>
            </div>
            <div class="block-content block-content-full">
              <form action="" method="POST" enctype="multipart/form-data">
                <div class="row items-push">
                  <div class="col-lg-3">
                    <p class="text-muted">
                      Hesap bilgileriniz. Kullanıcı adınız ve diğer bilgileriniz herkese görünür olacaktır.
                    </p>
                  </div>
                  <div class="col-lg-7 offset-lg-1">
                    <div class="mb-4">
                      <label class="form-label" for="profile-settings-username">Kullanıcı Adı</label>
                      <input type="text" class="form-control form-control-lg" id="profile-settings-username" name="profile-settings-username" placeholder="Kullanıcı adınızı girin.." value="<?php echo htmlspecialchars($user['username']); ?>">
                    </div>
                    <div class="mb-4">
                      <label class="form-label" for="profile-settings-email">E-posta Adresi</label>
                      <input type="email" class="form-control form-control-lg" id="profile-settings-email" name="profile-settings-email" placeholder="E-posta adresinizi girin.." value="<?php echo htmlspecialchars($user['email']); ?>">
                    </div>
                    <div class="mb-4">
                      <label class="form-label" for="profile-settings-discord">Discord ID</label>
                      <input type="text" class="form-control form-control-lg" id="profile-settings-discord" name="profile-settings-discord" placeholder="Discord ID'nizi girin.." value="<?php echo htmlspecialchars($user['discord_id']); ?>">
                    </div>
                    <div class="mb-4">
                      <label class="form-label" for="profile-settings-fivem">FiveM Hex</label>
                      <input type="text" class="form-control form-control-lg" id="profile-settings-fivem" name="profile-settings-fivem" placeholder="FiveM Hex kodunuzu girin.." value="<?php echo htmlspecialchars($user['fivem_hex']); ?>">
                    </div>
                    <div class="mb-4">
                      <label class="form-label">Son Aktif Olma</label>
                      <p class="form-control-plaintext"><?php echo $user['last_active']; ?></p>
                    </div>
                    <div class="mb-4">
                      <label class="form-label">Hesap Oluşturma Tarihi</label>
                      <p class="form-control-plaintext"><?php echo $user['created_at']; ?></p>
                </div>
                    <div class="mb-4">
                      <label class="form-label">Kullanıcı Rolü</label>
                      <p class="form-control-plaintext"><?php echo ucfirst($user['role'] ?: 'Kullanıcı'); ?></p>
                  </div>
                    <div class="row mb-4">
                      <div class="col-md-10 col-xl-6">
                        <div class="push">
                          <img class="img-avatar" src="dashboardassets/images/logo1.png">
                        </div>
                        <div class="mb-4">
                          <label class="form-label" for="profile-settings-avatar">Yeni avatar seçin</label>
                          <input class="form-control" type="file" id="profile-settings-avatar" name="profile-settings-avatar">
                        </div>
                      </div>
                    </div>
                    <div class="mb-4">
                      <button type="submit" name="update_profile" class="btn btn-alt-primary">Güncelle</button>
                </div>
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
                    <a class="link-fx text-dual fs-sm fw-semibold text-uppercase" href="be_pages_generic_profile.php"><?php echo htmlspecialchars($user['username']); ?></a>
                  </li>
                  <li class="list-inline-item">
                    <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                    <a class="link-fx text-dual" data-toggle="layout" data-action="dark_mode_toggle" href="javascript:void(0)">
                      <i class="far fa-fw fa-moon" data-dark-mode-icon></i>
                    </a>
                  </li>
                  <li class="list-inline-item">
                    <a class="link-fx text-dual" href="logout.php">
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
                  <a class="nav-main-link" href="be_pages_yetkilisikayet.php">
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

      <!-- Main Container -->
      <main id="main-container">
        <!-- Page Content -->
        <!-- User Info -->
        <div class="bg-image bg-image-bottom" style="background-image: url('dashboardassets/media/photos/photo13@2x.jpg');">
          <div class="bg-black-75 py-4">
            <div class="content content-full text-center">
              <!-- Avatar -->
              <div class="mb-3">
                <a class="img-link" href="be_pages_generic_profile.php">
                  <img class="img-avatar img-avatar96 img-avatar-thumb" src="assets/images/logo1.png" alt="">
                </a>
              </div>
              <!-- END Avatar -->

              <!-- Personal -->
              <h1 class="h3 text-white fw-bold mb-2"><?php echo htmlspecialchars($user['username']); ?></h1>
              <h2 class="h5 text-white-75">
                Üye<a class="text-primary-light" href="javascript:void(0)"></a>
              </h2>
              <!-- END Personal -->

              <!-- Actions -->
              <a href="be_pages_generic_profile.php" class="btn btn-primary">
                <i class="fa fa-arrow-left opacity-50 me-1"></i> Back to Profile
              </a>
              <!-- END Actions -->
            </div>
          </div>
        </div>
        <!-- END User Info -->

        <!-- Main Content -->
        <div class="content">
          <!-- User Profile -->
          <div class="block block-rounded">
            <div class="block-header block-header-default">
              <h3 class="block-title">
                <i class="fa fa-user-circle me-1 text-muted"></i> Kullanıcı Profili
              </h3>
            </div>
            <div class="block-content">
              <form action="" method="POST">
                <div class="row items-push">
                  <div class="col-lg-3">
                    <p class="text-muted">
                      Giriş şifrenizi değiştirmek hesabınızı güvende tutmanın kolay bir yoludur.
                    </p>
                  </div>
                  <div class="col-lg-7 offset-lg-1">
                    <div class="mb-4">
                      <label class="form-label" for="profile-settings-password">Mevcut Şifre</label>
                      <input type="password" class="form-control form-control-lg" id="profile-settings-password" name="profile-settings-password">
                    </div>
                    <div class="mb-4">
                      <label class="form-label" for="profile-settings-password-new">Yeni Şifre</label>
                      <input type="password" class="form-control form-control-lg" id="profile-settings-password-new" name="profile-settings-password-new">
                    </div>
                    <div class="mb-4">
                      <label class="form-label" for="profile-settings-password-new-confirm">Yeni Şifre Tekrar</label>
                      <input type="password" class="form-control form-control-lg" id="profile-settings-password-new-confirm" name="profile-settings-password-new-confirm">
                    </div>
                    <div class="mb-4">
                      <button type="submit" name="change_password" class="btn btn-alt-primary">Güncelle</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <!-- END User Profile -->
        </div>
        <!-- END Main Content -->
        <!-- END Page Content -->
      </main>
      <!-- END Main Container -->

    </div>
    <!-- END Page Container -->
    <script src="dashboardassets/js/codebase.app.min.js"></script>
  </body>
</html>
