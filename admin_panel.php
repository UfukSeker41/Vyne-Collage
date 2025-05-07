<?php
session_start();
require 'config.php';

// Admin kontrolü
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: adminlogin.php');
    exit();
}

// Kullanıcı bilgilerini al
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$user = $stmt->fetch();

// Whitelist başvurularını al
$stmt = $pdo->prepare("
    SELECT wa.*, u.username, u.email 
    FROM whitelist_applications wa 
    LEFT JOIN users u ON wa.user_id = u.id 
    ORDER BY wa.created_at DESC
");
$stmt->execute();
$whitelist_applications = $stmt->fetchAll();

// Yetkili şikayetlerini al
$stmt = $pdo->prepare("
    SELECT ac.*, u.username, u.email 
    FROM admin_complaints ac 
    LEFT JOIN users u ON ac.user_id = u.id 
    ORDER BY ac.created_at DESC
");
$stmt->execute();
$admin_complaints = $stmt->fetchAll();

// Kullanıcıları al
$stmt = $pdo->prepare("SELECT * FROM users ORDER BY created_at DESC");
$stmt->execute();
$users = $stmt->fetchAll();
?>

<!doctype html>
<html lang="tr" class="remember-theme">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>VYNE College - Admin Paneli</title>
    <link rel="shortcut icon" href="dashboardassets/media/photos/logo1.png">
    <link rel="stylesheet" id="css-main" href="dashboardassets/css/codebase.css">
    <script src="dashboardassets/js/setTheme.js"></script>
    <style>
        .stats-card {
            transition: transform 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .application-card {
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        .application-card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 500;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
        .complaint-card {
            border-left: 4px solid #dc3545;
            margin-bottom: 1rem;
        }
        .user-card {
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .user-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>
    <div id="page-container" class="sidebar-o enable-page-overlay side-scroll page-header-modern main-content-boxed">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-content">
                <div class="content-header justify-content-lg-center">
                    <div>
                        <span class="smini-visible fw-bold tracking-wide fs-lg">
                            V<span class="text-primary">Y</span>
                        </span>
                        <a class="link-fx fw-bold tracking-wide mx-auto" href="be_pages_dashboard.php">
                            <span class="smini-hidden">
                                <span class="fs-4 text-dual">VYNE</span> <span class="fs-4 text-primary">Admin</span>
                            </span>
                        </a>
                    </div>
                </div>

                <div class="js-sidebar-scroll">
                    <div class="content-side content-side-user px-0 py-0">
                        <div class="smini-hidden text-center mx-auto">
                            <a class="img-link" href="be_pages_generic_profile.php">
                                <img class="img-avatar" src="dashboardassets/media/avatars/avatar15.jpg" alt="">
                            </a>
                            <ul class="list-inline mt-3 mb-0">
                                <li class="list-inline-item">
                                    <a class="link-fx text-dual fs-sm fw-semibold text-uppercase" href="be_pages_generic_profile.php">
                                        <?php echo htmlspecialchars($user['username']); ?>
                                    </a>
                                </li>
                                <li class="list-inline-item">
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
                    </div>

                    <div class="content-side content-side-full">
                        <ul class="nav-main">
                            <li class="nav-main-item">
                                <a class="nav-main-link active" href="admin_panel.php">
                                    <i class="nav-main-link-icon fa fa-home"></i>
                                    <span class="nav-main-link-name">Admin Panel</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link" href="be_pages_dashboard.php">
                                    <i class="nav-main-link-icon fa fa-tachometer-alt"></i>
                                    <span class="nav-main-link-name">Dashboard</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Header -->
        <header id="page-header">
            <div class="content-header">
                <div class="content-header-section">
                    <button type="button" class="btn btn-alt-secondary" data-toggle="layout" data-action="sidebar_toggle">
                        <i class="fa fa-bars"></i>
                    </button>
                </div>
                <div class="content-header-section">
                    <h1>Admin Paneli</h1>
                </div>
            </div>
        </header>

        <!-- Main Container -->
        <main id="main-container">
            <div class="content">
                <!-- İstatistikler -->
                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <div class="block block-rounded stats-card">
                            <div class="block-content block-content-full">
                                <div class="py-3 text-center">
                                    <div class="fs-2 fw-bold text-primary">
                                        <?php echo count($whitelist_applications); ?>
                                    </div>
                                    <div class="fs-sm fw-semibold text-muted">Toplam Whitelist Başvurusu</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="block block-rounded stats-card">
                            <div class="block-content block-content-full">
                                <div class="py-3 text-center">
                                    <div class="fs-2 fw-bold text-success">
                                        <?php echo count(array_filter($whitelist_applications, function($app) { return $app['status'] === 'approved'; })); ?>
                                    </div>
                                    <div class="fs-sm fw-semibold text-muted">Onaylanan Başvurular</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="block block-rounded stats-card">
                            <div class="block-content block-content-full">
                                <div class="py-3 text-center">
                                    <div class="fs-2 fw-bold text-danger">
                                        <?php echo count($admin_complaints); ?>
                                    </div>
                                    <div class="fs-sm fw-semibold text-muted">Yetkili Şikayetleri</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="block block-rounded stats-card">
                            <div class="block-content block-content-full">
                                <div class="py-3 text-center">
                                    <div class="fs-2 fw-bold text-info">
                                        <?php echo count($users); ?>
                                    </div>
                                    <div class="fs-sm fw-semibold text-muted">Toplam Kullanıcı</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Whitelist Başvuruları -->
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Whitelist Başvuruları</h3>
                    </div>
                    <div class="block-content">
                        <?php foreach ($whitelist_applications as $application): ?>
                        <div class="block block-rounded application-card">
                            <div class="block-content">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h4 class="mb-1"><?php echo htmlspecialchars($application['full_name']); ?></h4>
                                        <p class="text-muted mb-0">Discord: <?php echo htmlspecialchars($application['discord_id']); ?></p>
                                    </div>
                                    <span class="status-badge status-<?php echo $application['status']; ?>">
                                        <?php echo ucfirst($application['status']); ?>
                                    </span>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Yaş:</strong> <?php echo htmlspecialchars($application['age']); ?></p>
                                        <p><strong>Email:</strong> <?php echo htmlspecialchars($application['email']); ?></p>
                                        <p><strong>Yayın Platform:</strong> <?php echo htmlspecialchars($application['stream_platform']); ?></p>
                                        <p><strong>Selamlama:</strong> <?php echo htmlspecialchars($application['greeting']); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>College Tecrübesi:</strong> <?php echo htmlspecialchars($application['college_experience']); ?></p>
                                        <p><strong>Referans Discord ID:</strong> <?php echo htmlspecialchars($application['reference_discord_id']); ?></p>
                                        <p><strong>Başvuru Tarihi:</strong> <?php echo date('d.m.Y H:i', strtotime($application['submit_date'])); ?></p>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <h5>Karakter Hikayesi</h5>
                                    <p><?php echo nl2br(htmlspecialchars($application['character_story'])); ?></p>
                                </div>
                                <div class="mt-3">
                                    <h5>Karakter Fobileri</h5>
                                    <p><?php echo nl2br(htmlspecialchars($application['character_phobias'])); ?></p>
                                </div>
                                <div class="mt-3">
                                    <h5>Roleplay Tanımı</h5>
                                    <p><?php echo nl2br(htmlspecialchars($application['rp_definition'])); ?></p>
                                </div>
                                <div class="mt-3">
                                    <h5>College Anlamı</h5>
                                    <p><?php echo nl2br(htmlspecialchars($application['college_meaning'])); ?></p>
                                </div>
                                <div class="mt-3">
                                    <h5>Me We Do Örnekleri</h5>
                                    <p><?php echo nl2br(htmlspecialchars($application['me_we_do_examples'])); ?></p>
                                </div>
                                <div class="mt-3">
                                    <h5>Profesör Senaryosu</h5>
                                    <p><?php echo nl2br(htmlspecialchars($application['professor_scenario'])); ?></p>
                                </div>
                                <div class="mt-3">
                                    <h5>Okul Bahçesi Senaryosu</h5>
                                    <p><?php echo nl2br(htmlspecialchars($application['school_yard_scenario'])); ?></p>
                                </div>
                                <div class="mt-3">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-success" onclick="updateWhitelistStatus(<?php echo $application['id']; ?>, 'approved')">
                                            Onayla
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="updateWhitelistStatus(<?php echo $application['id']; ?>, 'rejected')">
                                            Reddet
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Yetkili Şikayetleri -->
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Yetkili Şikayetleri</h3>
                    </div>
                    <div class="block-content">
                        <?php foreach ($admin_complaints as $complaint): ?>
                        <div class="block block-rounded complaint-card">
                            <div class="block-content">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h4 class="mb-1"><?php echo htmlspecialchars($complaint['subject']); ?></h4>
                                        <p class="text-muted mb-0"><?php echo htmlspecialchars($complaint['username']); ?></p>
                                    </div>
                                    <span class="status-badge status-<?php echo $complaint['status']; ?>">
                                        <?php echo ucfirst($complaint['status']); ?>
                                    </span>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Şikayet Edilen Yetkili:</strong> <?php echo htmlspecialchars($complaint['admin_name']); ?></p>
                                        <p><strong>Şikayet Tarihi:</strong> <?php echo date('d.m.Y H:i', strtotime($complaint['created_at'])); ?></p>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <h5>Şikayet Detayı</h5>
                                    <p><?php echo nl2br(htmlspecialchars($complaint['content'])); ?></p>
                                </div>
                                <div class="mt-3">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-success" onclick="updateComplaintStatus(<?php echo $complaint['id']; ?>, 'resolved')">
                                            Çözüldü
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="updateComplaintStatus(<?php echo $complaint['id']; ?>, 'rejected')">
                                            Reddet
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Kullanıcı Listesi -->
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Kullanıcı Listesi</h3>
                    </div>
                    <div class="block-content">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-vcenter">
                                <thead>
                                    <tr>
                                        <th>Kullanıcı Adı</th>
                                        <th>Email</th>
                                        <th>Rol</th>
                                        <th>Kayıt Tarihi</th>
                                        <th>Son Aktif</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : 'primary'; ?>">
                                                <?php echo ucfirst($user['role']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d.m.Y H:i', strtotime($user['created_at'])); ?></td>
                                        <td><?php echo $user['last_active'] ? date('d.m.Y H:i', strtotime($user['last_active'])) : 'Hiç'; ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-alt-secondary" onclick="editUser(<?php echo $user['id']; ?>)">
                                                    <i class="fa fa-pencil-alt"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-alt-danger" onclick="deleteUser(<?php echo $user['id']; ?>)">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer id="page-footer">
            <div class="content py-3">
                <div class="row fs-sm">
                    <div class="col-sm-6 order-sm-2 py-1 text-center text-sm-end">
                        <a class="fw-semibold" href="https://discord.gg/vyne" target="_blank">VYNE College</a>
                    </div>
                    <div class="col-sm-6 order-sm-1 py-1 text-center text-sm-start">
                        &copy; <span data-toggle="year-copy"></span>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script src="dashboardassets/js/codebase.app.min.js"></script>
    <script>
        function updateWhitelistStatus(id, status) {
            if (confirm('Bu başvuruyu ' + status + ' olarak işaretlemek istediğinize emin misiniz?')) {
                fetch('update_whitelist_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + id + '&status=' + status
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Bir hata oluştu: ' + data.message);
                    }
                });
            }
        }

        function updateComplaintStatus(id, status) {
            if (confirm('Bu şikayeti ' + status + ' olarak işaretlemek istediğinize emin misiniz?')) {
                fetch('update_complaint_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + id + '&status=' + status
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Bir hata oluştu: ' + data.message);
                    }
                });
            }
        }

        function editUser(id) {
            // Kullanıcı düzenleme modalını aç
            window.location.href = 'edit_user.php?id=' + id;
        }

        function deleteUser(id) {
            if (confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?')) {
                fetch('delete_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + id
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Bir hata oluştu: ' + data.message);
                    }
                });
            }
        }
    </script>
</body>
</html> 