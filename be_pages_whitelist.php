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

// Daha önce başvuru yapılmış mı kontrol et
$stmt = $pdo->prepare("SELECT * FROM whitelist_applications WHERE user_id = ? ORDER BY submit_date DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$existing_application = $stmt->fetch();

// Form gönderildi mi kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($existing_application) {
        $_SESSION['error'] = "Daha önce başvuru yapmışsınız. Birden fazla başvuru yapılamaz.";
    } else {
        try {
            // Formdan gelen verileri al ve güvenli hale getir
            $fullName = htmlspecialchars($_POST['fullName']);
            $age = intval($_POST['yas']);
            $discordID = htmlspecialchars($_POST['discordID']);
            $gmail = filter_var($_POST['gmail'], FILTER_SANITIZE_EMAIL);
            $streamPlatform = htmlspecialchars($_POST['yayinPlatform']);
            $greeting = htmlspecialchars($_POST['naber']);
            $characterStory = htmlspecialchars($_POST['karakterHikaye']);
            $characterPhobias = htmlspecialchars($_POST['karakterFobi']);
            $rpDefinition = htmlspecialchars($_POST['rpTanimi']);
            $collegeExperience = $_POST['collegeTecrube'];
            $collegeMeaning = htmlspecialchars($_POST['collegeAnlam']);
            $meWeDo = htmlspecialchars($_POST['meWeDo']);
            $professorScenario = htmlspecialchars($_POST['profSelam']);
            $schoolYardScenario = htmlspecialchars($_POST['okulBahcesi']);
            $referrer = htmlspecialchars($_POST['referans']);

            // Karakter hikayesi minimum 3000 kelime kontrolü
            if (str_word_count($characterStory) < 3000) {
                throw new Exception("Karakter hikayesi en az 3000 kelime olmalıdır.");
            }

            // Veritabanına kaydet
            $stmt = $pdo->prepare("INSERT INTO whitelist_applications (
                user_id, full_name, age, discord_id, email, stream_platform,
                greeting, character_story, character_phobias, rp_definition,
                college_experience, college_meaning, me_we_do_examples,
                professor_scenario, school_yard_scenario, reference_discord_id,
                status, submit_date
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', CURRENT_TIMESTAMP)");

            $stmt->execute([
                $_SESSION['user_id'], $fullName, $age, $discordID, $gmail,
                $streamPlatform, $greeting, $characterStory, $characterPhobias,
                $rpDefinition, $collegeExperience, $collegeMeaning, $meWeDo,
                $professorScenario, $schoolYardScenario, $referrer
            ]);

            // Kullanıcı bilgilerini güncelle
            $updateUser = $pdo->prepare("UPDATE users SET discord_id = ?, email = ? WHERE id = ?");
            $updateUser->execute([$discordID, $gmail, $_SESSION['user_id']]);

            $_SESSION['success'] = "Whitelist başvurunuz başarıyla alınmıştır. En kısa sürede incelenecektir.";
            header('Location: be_pages_whitelist.php');
            exit();

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
    }
}

// Son aktif zamanı güncelle
$stmt = $pdo->prepare("UPDATE users SET last_active = CURRENT_TIMESTAMP WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
?>
<!doctype html>
<html lang="tr" class="remember-theme">
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
        .whitelist-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 20px;
        }
        .baslikortala {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--bs-primary);
        }
        .alert {
            margin-bottom: 1rem;
            padding: 1rem;
            border-radius: 0.25rem;
        }
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        .form-control {
            margin-bottom: 1rem;
        }
        .character-count {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: -0.5rem;
            margin-bottom: 1rem;
        }
        .btn-primary {
            margin-top: 1rem;
            width: 100%;
        }
        textarea {
            min-height: 150px;
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
                  <a class="nav-main-link active" href="be_pages_whitelist.php">
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
                    <span>Profile</span>
                    <i class="fa fa-fw fa-user opacity-25"></i>
                  </a>
                  <div class="dropdown-divider"></div>

                  <!-- Toggle Side Overlay -->
                  <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                  <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1" href="javascript:void(0)" data-toggle="layout" data-action="side_overlay_toggle">
                    <span>Settings</span>
                    <i class="fa fa-fw fa-wrench opacity-25"></i>
                  </a>
                  <!-- END Side Overlay -->

                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1" href="op_auth_signin.html">
                    <span>Sign Out</span>
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
        <!-- Whitelist Başvuru Formu -->
        <div class="whitelist-container">
          <h2 class="baslikortala">Whitelist Başvuru Formu</h2>
          
          <?php if (isset($_SESSION['error'])): ?>
              <div class="alert alert-danger">
                  <?php 
                  echo $_SESSION['error'];
                  unset($_SESSION['error']);
                  ?>
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

          <?php if ($existing_application): ?>
              <div class="alert alert-info">
                  <h4>Mevcut Başvuru Durumu: <?php echo htmlspecialchars($existing_application['status']); ?></h4>
                  <p>Başvuru Tarihi: <?php echo date('d.m.Y H:i', strtotime($existing_application['submit_date'])); ?></p>
              </div>
          <?php else: ?>
              <form class="whitelist-form" action="" method="POST" onsubmit="return validateForm()">
                  <div class="mb-3">
                      <label for="fullName" class="form-label">Ad, Soyad</label>
                      <input type="text" class="form-control" id="fullName" name="fullName" required>
                  </div>

                  <div class="mb-3">
                      <label for="yas" class="form-label">Yaş</label>
                      <input type="number" class="form-control" id="yas" name="yas" min="18" required>
                  </div>

                  <div class="mb-3">
                      <label for="discordID" class="form-label">Discord ID</label>
                      <input type="text" class="form-control" id="discordID" name="discordID" value="<?php echo htmlspecialchars($user['discord_id'] ?? ''); ?>" required>
                  </div>

                  <div class="mb-3">
                      <label for="gmail" class="form-label">Gmail</label>
                      <input type="email" class="form-control" id="gmail" name="gmail" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                  </div>

                  <div class="mb-3">
                      <label for="yayinPlatform" class="form-label">Yayın yapıyor musun? (Hangi platformda?)</label>
                      <input type="text" class="form-control" id="yayinPlatform" name="yayinPlatform">
                  </div>

                  <div class="mb-3">
                      <label for="naber" class="form-label">Naber, Nasılsın?</label>
                      <input type="text" class="form-control" id="naber" name="naber" required>
                  </div>

                  <div class="mb-3">
                      <label for="karakterHikaye" class="form-label">Karakter Hikayesi (min. 3000 kelime)</label>
                      <textarea class="form-control" id="karakterHikaye" name="karakterHikaye" rows="10" required></textarea>
                      <div class="character-count">Kelime sayısı: <span id="wordCount">0</span>/3000</div>
                  </div>

                  <div class="mb-3">
                      <label for="karakterFobi" class="form-label">Karakterinizin fobileri</label>
                      <textarea class="form-control" id="karakterFobi" name="karakterFobi" rows="3" required></textarea>
                  </div>

                  <div class="mb-3">
                      <label for="rpTanimi" class="form-label">Roleplay tanımını detaylı anlatın</label>
                      <textarea class="form-control" id="rpTanimi" name="rpTanimi" rows="5" required></textarea>
                  </div>

                  <div class="mb-3">
                      <label for="collegeTecrube" class="form-label">Daha önce college temasında rp yaptınız mı?</label>
                      <select class="form-select" id="collegeTecrube" name="collegeTecrube" required>
                          <option value="Evet">Evet</option>
                          <option value="Hayır">Hayır</option>
                      </select>
                  </div>

                  <div class="mb-3">
                      <label for="collegeAnlam" class="form-label">College teması sizin için neyi ifade ediyor?</label>
                      <textarea class="form-control" id="collegeAnlam" name="collegeAnlam" rows="3" required></textarea>
                  </div>

                  <div class="mb-3">
                      <label for="meWeDo" class="form-label">3 adet "me we do" örneği veriniz</label>
                      <textarea class="form-control" id="meWeDo" name="meWeDo" rows="3" required></textarea>
                  </div>

                  <div class="mb-3">
                      <label for="profSelam" class="form-label">Ders içerisindesiniz, ön sıralarda oturup derse katılmaya çalışıyorsunuz ama profesör size neredeyse hiç fırsat vermiyor, dersin ilerleyen zamanlarında profesör sana doğru dönüp ses yaptığın için kızmaya başladı, fakat senin bir suçun yok arkanda bulunan bir grup dersi tamamiyle sabote etmesine rağmen suçlanan sensin. Profesörün emri ile dışarı doğru ilerlerken ses yapan grup ile profesörün birbirleri ile şakalaştığını fark ediyorsun. Dersin dışındasın ve ne yapacağını düşünmektesin?</label>
                      <textarea class="form-control" id="profSelam" name="profSelam" rows="3" required></textarea>
                  </div>

                  <div class="mb-3">
                      <label for="okulBahcesi" class="form-label">Okul içerisinde ders saatleri dışında bahçede oturuyorsun, etrafı gözetlerken bir köşede yüzünü tam olarak göremedeğin 2 çocuk yüzlerine maske geçirdi. Seni farkettiler fakat başka bir yöne doğru yöneldiler. Gün bitti ertesi sabah uyandın, okul gazetesine baktığında profesörlerden birinin okul içerisinde darp edildiğini öğrendin. Gördüğün şeyleri bildirdirdiğin senaryoda maskeli çocukların sana bulaşma ihtimalleri hayli yüksek.</label>
                      <textarea class="form-control" id="okulBahcesi" name="okulBahcesi" rows="3" required></textarea>
                  </div>

                  <div class="mb-3">
                      <label for="referans" class="form-label">Referans var mı? (Varsa Discord ID)</label>
                      <input type="text" class="form-control" id="referans" name="referans">
                  </div>

                  <button type="submit" class="btn btn-primary">Başvuru Gönder</button>
              </form>

              <script>
              document.getElementById('karakterHikaye').addEventListener('input', function() {
                  const wordCount = this.value.trim().split(/\s+/).length;
                  document.getElementById('wordCount').textContent = wordCount;
              });

              function validateForm() {
                  const story = document.getElementById('karakterHikaye').value;
                  const wordCount = story.trim().split(/\s+/).length;
                  
                  if (wordCount < 3000) {
                      alert('Karakter hikayesi en az 3000 kelime olmalıdır!');
                      return false;
                  }
                  return true;
              }
              </script>
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