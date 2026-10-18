<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng Nhập - Hệ Thống Quản Lý Nhà Hàng Hoàng Kim</title>
  <link rel="stylesheet" href="assets/css/app.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>

<div class="login-wrapper">
  <div class="login-box">
    <div class="login-logo">
      <i class="fa-solid fa-crown"></i>
    </div>
    <h1 class="login-title">HOÀNG KIM RESTAURANT</h1>
    <p class="login-subtitle">Hệ Thống Quản Trị & Bán Hàng Nhà Hàng Thông Minh</p>

    <form id="loginForm" onsubmit="handleLogin(event)">
      <div class="form-group" style="text-align: left;">
        <label class="form-label"><i class="fa-solid fa-user me-1"></i> Tên đăng nhập</label>
        <input type="text" id="username" class="form-control" placeholder="Nhập tên đăng nhập (vd: admin, letan, bep)" required value="admin">
      </div>

      <div class="form-group" style="text-align: left;">
        <label class="form-label"><i class="fa-solid fa-lock me-1"></i> Mật khẩu</label>
        <input type="password" id="password" class="form-control" placeholder="Nhập mật khẩu" required value="123456">
      </div>

      <button type="submit" class="btn-primary" style="width: 100%; padding: 14px; font-size: 15px; margin-top: 10px;">
        <i class="fa-solid fa-right-to-bracket"></i> ĐĂNG NHẬP
      </button>
    </form>

    <!-- Quick Role Picker for Testing Convenience -->
    <div class="role-quick-chips">
      <span style="width: 100%; font-size: 11px; color: var(--text-muted); margin-bottom: 4px;">
        <i class="fa-solid fa-bolt text-warning"></i> Chọn nhanh 5 vai trò để kiểm thử phân quyền:
      </span>
      <button class="role-chip" onclick="quickFill('admin1', '123456')">
        <i class="fa-solid fa-crown text-warning"></i> Quản lý (admin1)
      </button>
      <button class="role-chip" onclick="quickFill('letan1', '123456')">
        <i class="fa-solid fa-bell-concierge text-success"></i> Lễ tân (letan1)
      </button>
      <button class="role-chip" onclick="quickFill('phucvu1', '123456')">
        <i class="fa-solid fa-utensils text-primary"></i> Phục vụ (phucvu1)
      </button>
      <button class="role-chip" onclick="quickFill('thungan1', '123456')">
        <i class="fa-solid fa-cash-register text-info"></i> Thu ngân (thungan1)
      </button>
      <button class="role-chip" onclick="quickFill('bep1', '123456')">
        <i class="fa-solid fa-fire-burner text-danger"></i> Bếp (bep1)
      </button>
    </div>

    <div style="margin-top: 24px;">
      <a href="booking.php" target="_blank" style="color: var(--accent-gold); font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
        <i class="fa-solid fa-globe"></i> Mở Cổng Đặt Bàn Khách Hàng (Public Web) <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 11px;"></i>
      </a>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function quickFill(u, p) {
  document.getElementById('username').value = u;
  document.getElementById('password').value = p;
}

async function handleLogin(e) {
  e.preventDefault();
  const u = document.getElementById('username').value.trim();
  const p = document.getElementById('password').value.trim();

  const formData = new FormData();
  formData.append('action', 'login');
  formData.append('username', u);
  formData.append('password', p);

  try {
    const res = await fetch('api.php', { method: 'POST', body: formData });
    const data = await res.json();

    if (data.success) {
      const user = data.user;
      Swal.fire({
        icon: 'success',
        title: 'Đăng nhập thành công!',
        text: `Chào mừng ${user.full_name} (${user.sub_role})`,
        timer: 1200,
        showConfirmButton: false,
        background: '#1e293b',
        color: '#fff'
      }).then(() => {
        // Điều hướng theo nghiệp vụ chuẩn của từng bộ phận:
        if (user.role === 'admin') {
          window.location.href = 'dashboard.php';
        } else if (user.role === 'reception') {
          window.location.href = 'reservations.php';
        } else if (user.role === 'kitchen') {
          window.location.href = 'kds.php';
        } else if (user.role === 'waiter' || user.role === 'cashier') {
          window.location.href = 'tables.php';
        } else {
          window.location.href = 'tables.php';
        }
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Thất bại',
        text: data.message,
        background: '#1e293b',
        color: '#fff'
      });
    }
  } catch (err) {
    Swal.fire({
      icon: 'error',
      title: 'Lỗi máy chủ',
      text: 'Không thể kết nối đến máy chủ XAMPP!',
      background: '#1e293b',
      color: '#fff'
    });
  }
}
</script>
</body>
</html>
