<?php
session_start();
require_once 'config.php';

try {
    // Kullanıcı oturum kontrolü
    if (!isset($_SESSION['user_id'])) {
        header("Location: auth-cover-login.php");
        exit();
    }
    
    // Kullanıcı bilgilerini veritabanından çek
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->fetch();

    if (!$user) {
        // Kullanıcı bulunamadıysa oturumu sonlandır
        session_destroy();
        header("Location: auth-cover-login.php");
        exit();
    }
    
    // Profil güncelleme işlemi
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
        $email = $_POST['side-overlay-profile-email'];
        $new_password = $_POST['side-overlay-profile-password'];
        
        $updateQuery = "UPDATE users SET email = :email";
        $params = [':email' => $email];
        
        // Eğer yeni şifre girilmişse
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $updateQuery .= ", password = :password";
            $params[':password'] = $hashed_password;
        }
        
        $updateQuery .= " WHERE id = :user_id";
        $params[':user_id'] = $_SESSION['user_id'];
        
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->execute($params);
        
        // Son aktif zamanını güncelle
        $updateLastActive = $pdo->prepare("UPDATE users SET last_active = CURRENT_TIMESTAMP WHERE id = :user_id");
        $updateLastActive->execute([':user_id' => $_SESSION['user_id']]);
        
        // Başarılı güncelleme sonrası kullanıcı bilgilerini yeniden çek
        $stmt->execute();
        $user = $stmt->fetch();
    }
    
    // İstatistikleri çek
    $statsStmt = $pdo->query("SELECT 
        (SELECT COUNT(*) FROM whitelist_applications WHERE status = 'pending') as whitelist_count,
        (SELECT COUNT(*) FROM admin_complaints WHERE status = 'pending') as complaints_count,
        (SELECT COUNT(*) FROM users WHERE last_active > DATE_SUB(NOW(), INTERVAL 24 HOUR) AND role = 'user') as active_users
    ");
    $stats = $statsStmt->fetch();
    
} catch(PDOException $e) {
    echo "Bağlantı hatası: " . $e->getMessage();
    exit();
}

// Kullanıcı adını güvenli bir şekilde al
$username = htmlspecialchars($user['username'] ?? 'Kullanıcı');
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
            <?php echo $username; ?>
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
                <i class="fa fa-fw fa-pencil-alt opacity-50 me-1"></i> Profile
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
                    <input type="text" class="form-control" id="side-overlay-profile-name" name="side-overlay-profile-name" placeholder="Kullanıcı adınız.." value="<?php echo $username; ?>">
                    <span class="input-group-text">
                      <i class="fa fa-user"></i>
                    </span>
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="side-overlay-profile-email">E-posta</label>
                  <div class="input-group">
                    <input type="email" class="form-control" id="side-overlay-profile-email" name="side-overlay-profile-email" placeholder="E-posta adresiniz.." value="<?php echo htmlspecialchars($user['email']); ?>">
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
                <img class="img-avatar img-avatar32" src="dashboardassets/images/logo1.png" alt="">
              </div>
              <!-- END Visible only in mini mode -->

              <!-- Visible only in normal mode -->
              <div class="smini-hidden text-center mx-auto">
                <a class="img-link" href="be_pages_generic_profile.php">
                  <img class="img-avatar" src="dashboardassets/images/logo1.png" alt="">
                </a>
                <ul class="list-inline mt-3 mb-0">
                  <li class="list-inline-item">
                    <a class="link-fx text-dual fs-sm fw-semibold text-uppercase" href="be_pages_generic_profile.php"><?php echo $username; ?></a>
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
                  <a class="nav-main-link active" href="be_pages_dashboard.php">
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
            </div>
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
            <button type="button" class="btn btn-sm btn-alt-secondary" data-toggle="layout" data-action="sidebar_toggle">
              <i class="fa fa-fw fa-bars"></i>
            </button>
            <!-- END Toggle Sidebar -->

            <!-- Color Themes -->
            <div class="dropdown d-inline-block">
              <button type="button" class="btn btn-sm btn-alt-secondary" id="page-header-themes-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-fw fa-brush"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-lg p-0" aria-labelledby="page-header-themes-dropdown">
                <div class="px-3 py-2 bg-body-light rounded-top">
                  <h5 class="fs-sm text-center mb-0">
                    Tema Renkleri
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
                    Karanlık Mod
                  </h5>
                </div>
                <div class="px-2 py-3">
                  <div class="row g-1 text-center">
                    <div class="col-4">
                      <button type="button" class="dropdown-item mb-0 d-flex align-items-center gap-2" data-toggle="layout" data-action="dark_mode_off" data-dark-mode="off">
                        <i class="far fa-sun fa-fw opacity-50"></i>
                        <span class="fs-sm fw-medium">Açık</span>
                      </button>
                    </div>
                    <div class="col-4">
                      <button type="button" class="dropdown-item mb-0 d-flex align-items-center gap-2" data-toggle="layout" data-action="dark_mode_on" data-dark-mode="on">
                        <i class="fa fa-moon fa-fw opacity-50"></i>
                        <span class="fs-sm fw-medium">Koyu</span>
                      </button>
                    </div>
                    <div class="col-4">
                      <button type="button" class="dropdown-item mb-0 d-flex align-items-center gap-2" data-toggle="layout" data-action="dark_mode_system" data-dark-mode="system">
                        <i class="fa fa-desktop fa-fw opacity-50"></i>
                        <span class="fs-sm fw-medium">Sistem</span>
                      </button>
                    </div>
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
                <span class="d-none d-sm-inline-block fw-semibold"><?php echo $username; ?></span>
                <i class="fa fa-angle-down opacity-50 ms-1"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-md dropdown-menu-end p-0" aria-labelledby="page-header-user-dropdown">
                <div class="px-2 py-3 bg-body-light rounded-top">
                  <h5 class="h6 text-center mb-0">
                    <?php echo $username; ?>
                  </h5>
                </div>
                <div class="p-2">
                  <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1" href="be_pages_generic_profile.php">
                    <span>Profil</span>
                    <i class="fa fa-fw fa-user opacity-25"></i>
                  </a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1" href="javascript:void(0)" data-toggle="layout" data-action="side_overlay_toggle">
                    <span>Ayarlar</span>
                    <i class="fa fa-fw fa-wrench opacity-25"></i>
                  </a>
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
            <button type="button" class="btn btn-sm btn-alt-secondary" data-toggle="layout" data-action="side_overlay_toggle">
              <i class="fa fa-fw fa-stream"></i>
            </button>
            <!-- END Toggle Side Overlay -->
          </div>
          <!-- END Right Section -->
        </div>
        <!-- END Header Content -->
      </header>
      <!-- END Header -->

      <!-- Main Container -->
      <main id="main-container">
        <!-- Page Content -->
        <div class="content">
          <div class="row">
            <!-- Row #1 -->
            <div class="col-6 col-xl-3">
              <a class="block block-rounded block-link-shadow text-end" href="javascript:void(0)">
                <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                  <div class="d-none d-sm-block">
                    <i class="fa fa-clipboard-list fa-2x opacity-25"></i>
                  </div>
                  <div>
                    <div class="fs-3 fw-semibold"><?php echo $stats['whitelist_count']; ?></div>
                    <div class="fs-sm fw-semibold text-uppercase text-muted">Bekleyen Whitelist</div>
                  </div>
                </div>
              </a>
            </div>
            <div class="col-6 col-xl-3">
              <a class="block block-rounded block-link-shadow text-end" href="javascript:void(0)">
                <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                  <div class="d-none d-sm-block">
                    <i class="fa fa-envelope-open fa-2x opacity-25"></i>
                  </div>
                  <div>
                    <div class="fs-3 fw-semibold"><?php echo $stats['complaints_count']; ?></div>
                    <div class="fs-sm fw-semibold text-uppercase text-muted">Bekleyen Şikayet</div>
                  </div>
                </div>
              </a>
            </div>
            <div class="col-6 col-xl-3">
              <a class="block block-rounded block-link-shadow text-end" href="javascript:void(0)">
                <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                  <div class="d-none d-sm-block">
                    <i class="fa fa-users fa-2x opacity-25"></i>
                  </div>
                  <div>
                    <div class="fs-3 fw-semibold"><?php echo $stats['active_users']; ?></div>
                    <div class="fs-sm fw-semibold text-uppercase text-muted">Aktif Üye</div>
                  </div>
                </div>
              </a>
            </div>
            <!-- END Row #1 -->

           
          </div>
        </div>
        <!-- END Page Content -->
      </main>
      <!-- END Main Container -->
    </div>
    <!-- END Page Container -->

    <script src="dashboardassets/js/codebase.app.min.js"></script>
    <script src="dashboardassets/js/plugins/chart.js/chart.umd.js"></script>
    <script src="dashboardassets/js/pages/be_pages_dashboard.min.js"></script>
  </body>
</html>