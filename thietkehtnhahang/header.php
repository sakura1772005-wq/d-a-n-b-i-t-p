<?php
// header.php - Dùng chung cho các màn hình nội bộ & Phân quyền truy cập chuyên nghiệp
require_once __DIR__ . '/config/db.php';

// 1. Kiểm tra session đăng nhập
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$currentUser = $_SESSION['user'];
$userRole = $currentUser['role'] ?? 'waiter';

// 2. Ma trận phân quyền truy cập theo từng vai trò (Role-based Access Control - RBAC)
// - reception: reservations.php, booking.php, tables.php
// - waiter: pos.php, tables.php
// - kitchen: kds.php
// - cashier: tables.php, checkout.php
// - admin: Toàn quyền truy cập tất cả
$rolePermissions = [
    'admin' => ['dashboard.php', 'tables.php', 'pos.php', 'kds.php', 'checkout.php', 'reservations.php', 'booking.php'],
    'reception' => ['reservations.php', 'booking.php', 'tables.php'],
    'waiter' => ['tables.php', 'pos.php'],
    'kitchen' => ['kds.php'],
    'cashier' => ['tables.php', 'checkout.php']
];

$allowedPages = $rolePermissions[$userRole] ?? ['tables.php'];
$currentPage = basename($_SERVER['PHP_SELF']);

// Kiểm tra quyền truy cập của trang hiện tại (nếu trang nằm ngoài quyền thì chặn & chuyển hướng)
if (!in_array($currentPage, $allowedPages)) {
    // Tìm trang mặc định an toàn cho vai trò này
    $defaultRedirect = $allowedPages[0] ?? 'login.php';
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
      <meta charset="UTF-8">
      <title>Không Có Quyền Truy Cập</title>
      <link rel="stylesheet" href="assets/css/app.css">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body style="background: #0f172a; display: flex; align-items: center; justify-content: center; height: 100vh; font-family: 'Plus Jakarta Sans', sans-serif;">
      <script>
        Swal.fire({
          icon: 'warning',
          title: 'Truy cập bị giới hạn!',
          text: 'Vai trò "<?= htmlspecialchars($currentUser['sub_role']) ?>" không có quyền truy cập trang này.',
          confirmButtonText: 'Quay lại khu vực của bạn',
          confirmButtonColor: '#d97706',
          background: '#1e293b',
          color: '#fff',
          allowOutsideClick: false
        }).then(() => {
          window.location.href = '<?= $defaultRedirect ?>';
        });
      </script>
    </body>
    </html>
    <?php
    exit;
}

// Xác định trang chủ logo chuyển đến theo vai trò
$homeUrl = ($userRole === 'admin') ? 'dashboard.php' : (($userRole === 'kitchen') ? 'kds.php' : (($userRole === 'reception') ? 'reservations.php' : 'tables.php'));

// Badge màu cho từng vai trò
$roleBadgeColors = [
    'admin' => ['bg' => 'rgba(239, 68, 68, 0.2)', 'text' => '#ef4444', 'icon' => 'fa-crown', 'title' => 'Quản Trị'],
    'reception' => ['bg' => 'rgba(16, 185, 129, 0.2)', 'text' => '#10b981', 'icon' => 'fa-bell-concierge', 'title' => 'Lễ Tân'],
    'waiter' => ['bg' => 'rgba(59, 130, 246, 0.2)', 'text' => '#3b82f6', 'icon' => 'fa-utensils', 'title' => 'Phục Vụ'],
    'cashier' => ['bg' => 'rgba(6, 182, 212, 0.2)', 'text' => '#06b6d4', 'icon' => 'fa-cash-register', 'title' => 'Thu Ngân'],
    'kitchen' => ['bg' => 'rgba(245, 158, 11, 0.2)', 'text' => '#f59e0b', 'icon' => 'fa-fire-burner', 'title' => 'Bếp / Bar']
];
$currentBadge = $roleBadgeColors[$userRole] ?? $roleBadgeColors['waiter'];
?>
<header class="app-topbar">
  <div style="display: flex; align-items: center; gap: 24px;">
    <a href="<?= $homeUrl ?>" class="brand-badge">
      <div class="logo-icon">
        <i class="fa-solid fa-crown"></i>
      </div>
      <div>
        <div class="brand-name">HOÀNG KIM RESTAURANT</div>
        <div class="brand-tagline">POS & Management System</div>
      </div>
    </a>

    <!-- Main Navigation Module Switcher (Tự động lọc menu chỉ hiển thị trang có quyền) -->
    <nav class="view-switcher">
      <?php if (in_array('tables.php', $allowedPages)): ?>
        <a href="tables.php" class="view-btn <?= $currentPage == 'tables.php' ? 'active' : '' ?>">
          <i class="fa-solid fa-border-all"></i> Sơ Đồ Bàn
        </a>
      <?php endif; ?>

      <?php if (in_array('pos.php', $allowedPages)): ?>
        <a href="pos.php?table_id=1" class="view-btn <?= $currentPage == 'pos.php' ? 'active' : '' ?>">
          <i class="fa-solid fa-tablet-screen-button"></i> Gọi Món (POS)
        </a>
      <?php endif; ?>

      <?php if (in_array('kds.php', $allowedPages)): ?>
        <a href="kds.php" class="view-btn <?= $currentPage == 'kds.php' ? 'active' : '' ?>">
          <i class="fa-solid fa-fire-burner"></i> Bếp (KDS)
        </a>
      <?php endif; ?>

      <?php if (in_array('checkout.php', $allowedPages)): ?>
        <a href="checkout.php?table_id=1" class="view-btn <?= $currentPage == 'checkout.php' ? 'active' : '' ?>">
          <i class="fa-solid fa-receipt"></i> Thu Ngân
        </a>
      <?php endif; ?>

      <?php if (in_array('reservations.php', $allowedPages)): ?>
        <a href="reservations.php" class="view-btn noti-bell-badge <?= $currentPage == 'reservations.php' ? 'active' : '' ?>" id="reservationNavBtn">
          <i class="fa-solid fa-bell"></i> Đặt Bàn Online
          <span class="noti-pulse" id="navNotiDot"></span>
        </a>
      <?php endif; ?>

      <?php if (in_array('dashboard.php', $allowedPages)): ?>
        <a href="dashboard.php" class="view-btn <?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">
          <i class="fa-solid fa-chart-line"></i> Quản Trị
        </a>
      <?php endif; ?>
    </nav>
  </div>

  <!-- Right Actions: Staff Info & Logout -->
  <div style="display: flex; align-items: center; gap: 14px;">
    <?php if (in_array('booking.php', $allowedPages) || $userRole === 'admin' || $userRole === 'reception'): ?>
      <a href="booking.php" target="_blank" class="btn-secondary" style="font-size: 12px; padding: 6px 12px; border-radius: var(--radius-full); text-decoration: none;">
        <i class="fa-solid fa-calendar-plus text-warning"></i> Đặt Bàn Mới (Web)
      </a>
    <?php endif; ?>

    <div class="user-info-chip" style="background: rgba(255,255,255,0.04); padding: 4px 12px; border-radius: var(--radius-full); border: 1px solid var(--dark-border); display: flex; align-items: center; gap: 10px;">
      <div style="width: 32px; height: 32px; background: <?= $currentBadge['text'] ?>; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #fff; font-size: 14px;">
        <i class="fa-solid <?= $currentBadge['icon'] ?>"></i>
      </div>
      <div>
        <div style="font-size: 13px; font-weight: 700; color: #fff;"><?= htmlspecialchars($currentUser['full_name']) ?></div>
        <div style="font-size: 11px; color: <?= $currentBadge['text'] ?>; font-weight: 600;">
          <?= htmlspecialchars($currentUser['sub_role']) ?>
        </div>
      </div>
    </div>

    <a href="login.php" class="btn-secondary" style="padding: 8px 12px; font-size: 13px; color: #ef4444; text-decoration: none;" title="Đổi ca / Đăng xuất">
      <i class="fa-solid fa-power-off"></i> Đổi Ca
    </a>
  </div>
</header>
