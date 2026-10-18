<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản Lý Yêu Cầu Đặt Bàn - Hoàng Kim Restaurant</title>
  <link rel="stylesheet" href="assets/css/app.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>

<?php include __DIR__ . '/header.php'; ?>

<main class="app-container">
  <!-- Top Bar & Chuông Thông Báo -->
  <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
    <div>
      <div style="display: flex; align-items: center; gap: 12px;">
        <h1 style="font-size: 22px; font-weight: 800;"><i class="fa-solid fa-bell text-warning me-2"></i> TIẾP NHẬN YÊU CẦU ĐẶT BÀN (LỄ TÂN)</h1>
        <span class="table-badge noti-bell-badge" style="background: rgba(239, 68, 68, 0.2); color: #ef4444; font-size: 13px; padding: 4px 12px;" id="bellNotiBadge">
          <i class="fa-solid fa-bell"></i> Có <b id="pendingCountText">0</b> yêu cầu mới từ Web
          <span class="noti-pulse"></span>
        </span>
      </div>
      <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">Xử lý luồng: Khách gửi Web -> Tiếp nhận -> Gọi điện xác nhận -> Gán bàn & cập nhật trạng thái [Đã đặt]</p>
    </div>

    <!-- Tab Lọc Trạng Thái -->
    <div class="view-switcher" id="resStatusTabs">
      <button class="view-btn active" onclick="filterReservationStatus('pending', this)">
        <i class="fa-solid fa-clock-rotate-left text-warning"></i> Chờ Xác Nhận (<span id="tabCountPending">0</span>)
      </button>
      <button class="view-btn" onclick="filterReservationStatus('confirmed', this)">
        <i class="fa-solid fa-circle-check text-success"></i> Đã Xác Nhận (<span id="tabCountConfirmed">0</span>)
      </button>
      <button class="view-btn" onclick="filterReservationStatus('cancelled', this)">
        <i class="fa-solid fa-ban text-danger"></i> Đã Hủy (<span id="tabCountCancelled">0</span>)
      </button>
      <button class="view-btn" onclick="filterReservationStatus('all', this)">
        <i class="fa-solid fa-list"></i> Tất Cả
      </button>
    </div>
  </div>

  <!-- Bảng Danh Sách Yêu Cầu -->
  <div class="card-glass" style="padding: 20px;">
    <div style="overflow-x: auto;">
      <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
        <thead>
          <tr style="border-bottom: 1px solid var(--dark-border); text-align: left; color: var(--text-muted);">
            <th style="padding: 12px;">Mã phiếu</th>
            <th style="padding: 12px;">Khách hàng</th>
            <th style="padding: 12px;">Số điện thoại</th>
            <th style="padding: 12px;">Thời gian đến</th>
            <th style="padding: 12px;">Số người</th>
            <th style="padding: 12px;">Khu vực yêu cầu</th>
            <th style="padding: 12px;">Bàn đã gán</th>
            <th style="padding: 12px;">Trạng thái</th>
            <th style="padding: 12px; text-align: right;">Hành động</th>
          </tr>
        </thead>
        <tbody id="reservationsTableBody">
          <!-- Rows rendered -->
        </tbody>
      </table>
    </div>
  </div>
</main>

<!-- Modal Xử Lý & Xác Nhận Đặt Bàn (Gán Bàn Trống) -->
<div class="app-modal-backdrop" id="handleReservationModal">
  <div class="app-modal-dialog">
    <div class="modal-header">
      <h3 style="font-size: 17px; font-weight: 700;"><i class="fa-solid fa-phone-volume text-warning me-2"></i> Xử Lý & Gán Bàn Khách Đặt</h3>
      <button onclick="closeModal('handleReservationModal')" style="background: none; border: none; color: #fff; font-size: 18px; cursor: pointer;">&times;</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="selectedResId">
      
      <!-- Thông tin khách -->
      <div style="background: rgba(15, 23, 42, 0.6); padding: 14px; border-radius: var(--radius-sm); border: 1px solid var(--dark-border); margin-bottom: 16px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
          <span style="color: var(--text-muted);">Khách hàng:</span>
          <b id="modalCustName" style="color: #fff; font-size: 15px;">--</b>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
          <span style="color: var(--text-muted);">Số điện thoại:</span>
          <b id="modalCustPhone" style="color: var(--accent-gold); font-size: 15px;">--</b>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
          <span style="color: var(--text-muted);">Ngày giờ đặt:</span>
          <span id="modalResTime" style="font-weight: 600;">--</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
          <span style="color: var(--text-muted);">Số lượng khách:</span>
          <span id="modalGuestCount" style="font-weight: 700;">--</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
          <span style="color: var(--text-muted);">Khu vực mong muốn:</span>
          <span id="modalPrefArea">--</span>
        </div>
        <div style="margin-top: 8px; padding-top: 8px; border-top: 1px dashed var(--dark-border); font-size: 12px; color: #fca5a5;">
          <i class="fa-solid fa-triangle-exclamation"></i> Ghi chú đặc biệt: <span id="modalNotes">--</span>
        </div>
      </div>

      <!-- Lựa chọn bàn trống để gán -->
      <div class="form-group">
        <label class="form-label" style="font-weight: 700; color: #10b981;">
          <i class="fa-solid fa-chair me-1"></i> Chọn Bàn Trống Phù Hợp Để Gán:
        </label>
        <select id="modalAssignTableSelect" class="form-control" style="font-weight: 600;">
          <!-- Empty tables populated -->
        </select>
        <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">
          * Sau khi xác nhận, bàn này trên [Sơ Đồ Bàn] sẽ tự động đổi sang màu Vàng (Đã đặt trước).
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="modal-footer" style="padding-right: 0; padding-bottom: 0;">
        <button type="button" class="btn-danger" onclick="cancelReservation()">
          <i class="fa-solid fa-xmark"></i> Từ Chối / Hủy
        </button>
        <button type="button" class="btn-success" onclick="confirmReservation()">
          <i class="fa-solid fa-check"></i> Xác Nhận & Gán Bàn
        </button>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let currentStatusFilter = 'pending';
let reservationsList = [];
let emptyTablesList = [];
let activeReservation = null;

async function loadReservations() {
  try {
    const res = await fetch(`api.php?action=get_reservations&status=${currentStatusFilter}`);
    const data = await res.json();
    if (!data.success) return;

    reservationsList = data.reservations;
    emptyTablesList = data.empty_tables;

    // Cập nhật số đếm các tab
    document.getElementById('pendingCountText').innerText = data.counts.pending;
    document.getElementById('tabCountPending').innerText = data.counts.pending;
    document.getElementById('tabCountConfirmed').innerText = data.counts.confirmed;
    document.getElementById('tabCountCancelled').innerText = data.counts.cancelled;

    // Chuông thông báo
    const bell = document.getElementById('bellNotiBadge');
    if (data.counts.pending > 0) {
      bell.style.display = 'inline-flex';
    } else {
      bell.style.display = 'none';
    }

    renderReservationsTable();
  } catch (err) {
    console.error('Error loading reservations:', err);
  }
}

function filterReservationStatus(status, btn) {
  currentStatusFilter = status;
  document.querySelectorAll('#resStatusTabs .view-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  loadReservations();
}

function renderReservationsTable() {
  const tbody = document.getElementById('reservationsTableBody');
  if (reservationsList.length === 0) {
    tbody.innerHTML = `<tr><td colspan="9" style="text-align: center; padding: 40px; color: var(--text-muted);">Không có yêu cầu đặt bàn nào trong mục này.</td></tr>`;
    return;
  }

  tbody.innerHTML = reservationsList.map(r => {
    let statusBadge = '<span class="table-badge" style="background: rgba(245, 158, 11, 0.2); color: #f59e0b;">Chờ xác nhận</span>';
    if (r.status === 'confirmed') {
      statusBadge = '<span class="table-badge" style="background: rgba(16, 185, 129, 0.2); color: #10b981;">Đã xác nhận</span>';
    } else if (r.status === 'cancelled') {
      statusBadge = '<span class="table-badge" style="background: rgba(239, 68, 68, 0.2); color: #ef4444;">Đã hủy</span>';
    }

    return `
      <tr style="border-bottom: 1px solid var(--dark-border);">
        <td style="padding: 12px; font-weight: 700; color: var(--accent-gold);">${r.reservation_code}</td>
        <td style="padding: 12px; font-weight: 700;">${r.customer_name}</td>
        <td style="padding: 12px;">
          <a href="tel:${r.phone}" style="color: #38bdf8; text-decoration: none;"><i class="fa-solid fa-phone me-1"></i>${r.phone}</a>
        </td>
        <td style="padding: 12px; color: #fff;">${r.reservation_time}</td>
        <td style="padding: 12px; font-weight: 700;">${r.guest_count} khách</td>
        <td style="padding: 12px; color: var(--text-sub);">${r.preferred_area || 'Sảnh chính'}</td>
        <td style="padding: 12px; font-weight: 800; color: #10b981;">${r.table_name || '--'}</td>
        <td style="padding: 12px;">${statusBadge}</td>
        <td style="padding: 12px; text-align: right;">
          ${r.status === 'pending' ? `
            <button class="btn-primary" style="padding: 6px 14px; font-size: 12px;" onclick="openHandleModal(${r.id})">
              <i class="fa-solid fa-check"></i> Xử Lý & Gán Bàn
            </button>
          ` : `
            <button class="btn-secondary" style="padding: 6px 12px; font-size: 12px;" onclick="viewDetail(${r.id})">
              <i class="fa-solid fa-eye"></i> Chi tiết
            </button>
          `}
        </td>
      </tr>
    `;
  }).join('');
}

function openHandleModal(id) {
  activeReservation = reservationsList.find(r => r.id == id);
  if (!activeReservation) return;

  document.getElementById('selectedResId').value = activeReservation.id;
  document.getElementById('modalCustName').innerText = activeReservation.customer_name;
  document.getElementById('modalCustPhone').innerText = activeReservation.phone;
  document.getElementById('modalResTime').innerText = activeReservation.reservation_time;
  document.getElementById('modalGuestCount').innerText = `${activeReservation.guest_count} Khách`;
  document.getElementById('modalPrefArea').innerText = activeReservation.preferred_area;
  document.getElementById('modalNotes').innerText = activeReservation.special_notes || 'Không có ghi chú';

  // Nạp danh sách bàn trống
  const select = document.getElementById('modalAssignTableSelect');
  if (emptyTablesList.length === 0) {
    select.innerHTML = '<option value="">Hiện tại không còn bàn trống nào!</option>';
  } else {
    select.innerHTML = emptyTablesList.map(t => `
      <option value="${t.id}">${t.name} (${t.area_name}) - Sức chứa: ${t.capacity} người</option>
    `).join('');
  }

  document.getElementById('handleReservationModal').style.display = 'flex';
}

function closeModal(id) {
  document.getElementById(id).style.display = 'none';
}

async function confirmReservation() {
  const tableId = document.getElementById('modalAssignTableSelect').value;
  if (!tableId) {
    Swal.fire('Cảnh báo', 'Vui lòng chọn một bàn trống để gán cho khách!', 'warning');
    return;
  }

  const formData = new FormData();
  formData.append('action', 'confirm_booking');
  formData.append('reservation_id', activeReservation.id);
  formData.append('assigned_table_id', tableId);

  const res = await fetch('api.php', { method: 'POST', body: formData });
  const data = await res.json();

  if (data.success) {
    closeModal('handleReservationModal');
    Swal.fire({
      icon: 'success',
      title: 'Đã xác nhận & Gán bàn!',
      html: `
        <div>
          <p>${data.message}</p>
          <div style="margin-top: 10px; font-size: 13px; color: #10b981;">
            <i class="fa-solid fa-paper-plane"></i> Đã gửi SMS/Email xác nhận thành công cho khách hàng ${activeReservation.customer_name}.
          </div>
        </div>
      `,
      background: '#1e293b',
      color: '#fff'
    });
    loadReservations();
  } else {
    Swal.fire('Lỗi', data.message, 'error');
  }
}

async function cancelReservation() {
  Swal.fire({
    title: 'Xác nhận từ chối / hủy yêu cầu này?',
    text: 'Trạng thái phiếu sẽ chuyển thành [Đã Hủy].',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Đồng ý hủy',
    cancelButtonText: 'Không',
    background: '#1e293b',
    color: '#fff'
  }).then(async res => {
    if (res.isConfirmed) {
      const formData = new FormData();
      formData.append('action', 'cancel_booking');
      formData.append('reservation_id', activeReservation.id);

      const r = await fetch('api.php', { method: 'POST', body: formData });
      const d = await r.json();
      if (d.success) {
        closeModal('handleReservationModal');
        loadReservations();
      }
    }
  });
}

function viewDetail(id) {
  const r = reservationsList.find(x => x.id == id);
  if (!r) return;
  Swal.fire({
    title: `Phiếu Đặt Bàn: ${r.reservation_code}`,
    html: `
      <div style="text-align: left; font-size: 13px; line-height: 1.8;">
        <div><b>Khách hàng:</b> ${r.customer_name} (${r.phone})</div>
        <div><b>Thời gian:</b> ${r.reservation_time}</div>
        <div><b>Số người:</b> ${r.guest_count} khách</div>
        <div><b>Khu vực:</b> ${r.preferred_area}</div>
        <div><b>Bàn gán:</b> ${r.table_name || 'Chưa gán'}</div>
        <div><b>Ghi chú:</b> ${r.special_notes || 'Không có'}</div>
        <div><b>Trạng thái:</b> ${r.status}</div>
      </div>
    `,
    confirmButtonText: 'Đóng',
    background: '#1e293b',
    color: '#fff'
  });
}

// Lặp kiểm tra có khách đặt mới mỗi 10 giây
setInterval(loadReservations, 10000);
loadReservations();
</script>
</body>
</html>
