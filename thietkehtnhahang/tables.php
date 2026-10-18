<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sơ Đồ Bàn - Hệ Thống Nhà Hàng Hoàng Kim</title>
  <link rel="stylesheet" href="assets/css/app.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>

<?php include __DIR__ . '/header.php'; ?>

<main class="app-container">
  <!-- Status Bar & Stats -->
  <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 20px;">
    <!-- Color Coding Legend -->
    <div class="table-status-legend card-glass" style="padding: 12px 20px;">
      <div class="legend-item">
        <span class="legend-dot" style="background: var(--status-empty);"></span>
        <span>Xanh lá: Bàn trống (<b id="statEmpty">0</b>)</span>
      </div>
      <div class="legend-item">
        <span class="legend-dot" style="background: var(--status-occupied);"></span>
        <span>Đỏ: Đang có khách (<b id="statOccupied">0</b>)</span>
      </div>
      <div class="legend-item">
        <span class="legend-dot" style="background: var(--status-reserved);"></span>
        <span>Vàng: Đã đặt trước (<b id="statReserved">0</b>)</span>
      </div>
      <div class="legend-item">
        <span class="legend-dot" style="background: var(--status-maintenance);"></span>
        <span>Xám: Đang hỏng / Sửa (<b id="statMaintenance">0</b>)</span>
      </div>
    </div>

    <!-- Area Filter Tabs -->
    <div class="view-switcher" id="areaTabsContainer">
      <button class="view-btn active" onclick="filterArea('all', this)">
        <i class="fa-solid fa-layer-group"></i> Tất Cả Khu Vực
      </button>
      <!-- Dynamic Area Tabs injected via JS -->
    </div>
  </div>

  <!-- Table Cards Layout -->
  <div class="table-grid" id="tablesContainer">
    <!-- Rendered tables -->
  </div>
</main>

<!-- Context Menu Chuột Phải / Chạm Giữ (Phân quyền theo bộ phận) -->
<div id="tableContextMenu" class="custom-context-menu">
  <?php if (in_array('pos.php', $allowedPages)): ?>
    <div class="context-menu-item" onclick="contextAction('order')">
      <i class="fa-solid fa-tablet-screen-button text-primary"></i> Gọi Món (POS)
    </div>
  <?php endif; ?>

  <div class="context-menu-item" onclick="contextAction('open')">
    <i class="fa-solid fa-door-open text-success"></i> Mở Bàn Mới
  </div>

  <?php if (in_array('checkout.php', $allowedPages)): ?>
    <div class="context-menu-item" onclick="contextAction('checkout')">
      <i class="fa-solid fa-receipt text-warning"></i> Thanh Toán (Thu Ngân)
    </div>
  <?php endif; ?>

  <?php if (in_array('reservations.php', $allowedPages) || $userRole === 'admin' || $userRole === 'reception'): ?>
    <div class="context-menu-item" onclick="contextAction('reserve')">
      <i class="fa-solid fa-calendar-check text-info"></i> Đặt Bàn Trước (Lễ Tân)
    </div>
  <?php endif; ?>

  <div class="context-menu-item" onclick="contextAction('transfer')">
    <i class="fa-solid fa-arrow-right-arrow-left text-primary"></i> Chuyển Bàn
  </div>
  <div class="context-menu-item" onclick="contextAction('merge')">
    <i class="fa-solid fa-object-group text-danger"></i> Gộp Bàn
  </div>
  <div class="context-menu-item" onclick="contextAction('maintenance')">
    <i class="fa-solid fa-wrench text-secondary"></i> Báo Hỏng / Bảo Trì
  </div>
  <div class="context-menu-item" onclick="contextAction('empty')" style="border-top: 1px solid var(--dark-border); color: #ef4444;">
    <i class="fa-solid fa-trash-can"></i> Trả Bàn Trống
  </div>
</div>

<!-- Modal Mở Bàn -->
<div class="app-modal-backdrop" id="openTableModal">
  <div class="app-modal-dialog">
    <div class="modal-header">
      <h3 id="modalTableTitle" style="font-size: 17px; font-weight: 700;"><i class="fa-solid fa-door-open text-warning me-2"></i> Mở Bàn Phục Vụ</h3>
      <button onclick="closeModal('openTableModal')" style="background: none; border: none; color: #fff; font-size: 18px; cursor: pointer;">&times;</button>
    </div>
    <div class="modal-body">
      <form id="openTableForm" onsubmit="submitOpenTable(event)">
        <input type="hidden" id="openTableId">
        <div class="form-group">
          <label class="form-label">Tên khách hàng / Ghi chú</label>
          <input type="text" id="openCustomerName" class="form-control" value="Khách lẻ" required>
        </div>
        <div class="form-group">
          <label class="form-label">Số lượng khách ngồi</label>
          <input type="number" id="openGuestCount" class="form-control" value="2" min="1" max="50" required>
        </div>
        <div class="modal-footer" style="padding-right: 0; padding-bottom: 0;">
          <button type="button" class="btn-secondary" onclick="closeModal('openTableModal')">Hủy</button>
          <button type="submit" class="btn-primary"><i class="fa-solid fa-check"></i> Xác Nhận Mở Bàn</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Chuyển Bàn / Gộp Bàn -->
<div class="app-modal-backdrop" id="transferMergeModal">
  <div class="app-modal-dialog">
    <div class="modal-header">
      <h3 id="transferModalTitle" style="font-size: 17px; font-weight: 700;"><i class="fa-solid fa-arrow-right-arrow-left text-warning me-2"></i> Chuyển Bàn</h3>
      <button onclick="closeModal('transferMergeModal')" style="background: none; border: none; color: #fff; font-size: 18px; cursor: pointer;">&times;</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="tmActionType">
      <input type="hidden" id="tmSourceTableId">
      <div style="margin-bottom: 16px;" id="tmDescText">Chọn bàn đích để thực hiện chuyển món:</div>
      <div class="form-group">
        <label class="form-label">Chọn Bàn Đích</label>
        <select id="tmTargetTableSelect" class="form-control">
          <!-- Populated via JS -->
        </select>
      </div>
      <div class="modal-footer" style="padding-right: 0; padding-bottom: 0;">
        <button type="button" class="btn-secondary" onclick="closeModal('transferMergeModal')">Hủy</button>
        <button type="button" class="btn-primary" onclick="submitTransferMerge()"><i class="fa-solid fa-check"></i> Thực Hiện Ngay</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let allTables = [];
let allAreas = [];
let currentFilterArea = 'all';
let selectedTable = null;

// Tải sơ đồ bàn từ API
async function loadTables() {
  try {
    const res = await fetch('api.php?action=get_tables_overview');
    const data = await res.json();
    if (!data.success) return;

    allTables = data.tables;
    allAreas = data.areas;

    // Cập nhật thống kê
    document.getElementById('statEmpty').innerText = data.stats.empty;
    document.getElementById('statOccupied').innerText = data.stats.occupied;
    document.getElementById('statReserved').innerText = data.stats.reserved;
    document.getElementById('statMaintenance').innerText = data.stats.maintenance;

    renderAreaTabs();
    renderTables();
  } catch (err) {
    console.error('Error loading tables:', err);
  }
}

function renderAreaTabs() {
  const container = document.getElementById('areaTabsContainer');
  let html = `<button class="view-btn ${currentFilterArea === 'all' ? 'active' : ''}" onclick="filterArea('all', this)">
                <i class="fa-solid fa-layer-group"></i> Tất Cả Khu Vực
              </button>`;
  allAreas.forEach(a => {
    html += `<button class="view-btn ${currentFilterArea == a.id ? 'active' : ''}" onclick="filterArea(${a.id}, this)">
              <i class="fa-solid fa-location-dot"></i> ${a.name}
            </button>`;
  });
  container.innerHTML = html;
}

function filterArea(areaId, btn) {
  currentFilterArea = areaId;
  document.querySelectorAll('#areaTabsContainer .view-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  renderTables();
}

function renderTables() {
  const container = document.getElementById('tablesContainer');
  const filtered = currentFilterArea === 'all' 
    ? allTables 
    : allTables.filter(t => t.area_id == currentFilterArea);

  if (filtered.length === 0) {
    container.innerHTML = `<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: var(--text-muted);">Không có bàn nào trong khu vực này.</div>`;
    return;
  }

  container.innerHTML = filtered.map(t => {
    const shapeClass = t.shape === 'round' ? 'shape-round' : (t.shape === 'rectangle' ? 'shape-rectangle' : '');
    const statusClass = `status-${t.status}`;
    
    // Status text & icon
    let statusText = 'TRỐNG';
    let icon = 'fa-check';
    if (t.status === 'occupied') { statusText = 'CÓ KHÁCH'; icon = 'fa-fire'; }
    if (t.status === 'reserved') { statusText = 'ĐÃ ĐẶT'; icon = 'fa-calendar'; }
    if (t.status === 'maintenance') { statusText = 'SỬA CHỮA'; icon = 'fa-wrench'; }

    const amountFormatted = t.total_amount ? parseInt(t.total_amount).toLocaleString('vi-VN') + ' đ' : '0 đ';
    const sittingMins = t.sitting_minutes ? `${t.sitting_minutes} phút` : (t.status === 'occupied' ? 'Vừa vào' : '--');

    return `
      <div class="table-card ${shapeClass} ${statusClass}" 
           data-id="${t.id}"
           onclick="handleTableClick(${t.id})"
           oncontextmenu="handleTableContextMenu(event, ${t.id})">
        <div class="table-header">
          <div class="table-name"><i class="fa-solid fa-chair text-muted" style="font-size: 13px;"></i> ${t.name}</div>
          <span class="table-badge"><i class="fa-solid ${icon}"></i> ${statusText}</span>
        </div>

        <div class="table-content">
          <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase;">Tổng tạm tính:</div>
          <div class="table-bill-amount">${t.status === 'occupied' ? amountFormatted : '--'}</div>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px dashed rgba(255,255,255,0.08); padding-top: 8px;">
          <div class="table-timer">
            <i class="fa-regular fa-clock"></i> <span>${t.status === 'occupied' ? sittingMins : (t.capacity + ' Chỗ')}</span>
          </div>
          <div style="font-size: 12px; color: var(--primary);">
            <i class="fa-solid fa-ellipsis-vertical"></i>
          </div>
        </div>
      </div>
    `;
  }).join('');
}

// Bấm vào bàn
function handleTableClick(tableId) {
  const tbl = allTables.find(t => t.id == tableId);
  if (!tbl) return;

  if (tbl.status === 'occupied') {
    // Thu ngân -> Vào màn hình thanh toán, còn lại -> Vào POS gọi món
    const userRole = '<?= $userRole ?>';
    if (userRole === 'cashier') {
      window.location.href = `checkout.php?table_id=${tbl.id}`;
    } else {
      window.location.href = `pos.php?table_id=${tbl.id}`;
    }
  } else if (tbl.status === 'empty') {
    // Bàn trống -> Mở modal xác nhận mở bàn
    openStartTableModal(tbl);
  } else if (tbl.status === 'reserved') {
    Swal.fire({
      title: `${tbl.name} đã được đặt trước!`,
      text: 'Khách đặt đã tới nhận bàn?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Khách Vào Bàn (Mở Bàn)',
      cancelButtonText: 'Đóng',
      background: '#1e293b',
      color: '#fff'
    }).then(res => {
      if (res.isConfirmed) {
        openStartTableModal(tbl);
      }
    });
  } else {
    Swal.fire({
      title: 'Bàn đang bảo trì',
      text: 'Bàn đang hỏng hoặc sửa chữa, bạn có muốn mở lại thành Bàn Trống không?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Đổi thành Trống',
      cancelButtonText: 'Không',
      background: '#1e293b',
      color: '#fff'
    }).then(res => {
      if (res.isConfirmed) {
        setTableStatus(tbl.id, 'empty');
      }
    });
  }
}

// Chuột phải / Chạm giữ hiển thị Context Menu
function handleTableContextMenu(e, tableId) {
  e.preventDefault();
  selectedTable = allTables.find(t => t.id == tableId);
  if (!selectedTable) return;

  const menu = document.getElementById('tableContextMenu');
  menu.style.display = 'block';
  
  // Tính tọa độ không tràn màn hình
  const x = Math.min(e.pageX, window.innerWidth - 200);
  const y = Math.min(e.pageY, window.innerHeight - 250);
  menu.style.left = `${x}px`;
  menu.style.top = `${y}px`;
}

// Đóng menu khi click ra ngoài
document.addEventListener('click', (e) => {
  if (!e.target.closest('#tableContextMenu')) {
    document.getElementById('tableContextMenu').style.display = 'none';
  }
});

// Xử lý các tùy chọn trong Context Menu
function contextAction(action) {
  document.getElementById('tableContextMenu').style.display = 'none';
  if (!selectedTable) return;

  if (action === 'order') {
    window.location.href = `pos.php?table_id=${selectedTable.id}`;
  } else if (action === 'open') {
    openStartTableModal(selectedTable);
  } else if (action === 'checkout') {
    window.location.href = `checkout.php?table_id=${selectedTable.id}`;
  } else if (action === 'reserve') {
    setTableStatus(selectedTable.id, 'reserved');
  } else if (action === 'maintenance') {
    setTableStatus(selectedTable.id, 'maintenance');
  } else if (action === 'empty') {
    setTableStatus(selectedTable.id, 'empty');
  } else if (action === 'transfer') {
    openTransferMergeModal('transfer');
  } else if (action === 'merge') {
    openTransferMergeModal('merge');
  }
}

function openStartTableModal(tbl) {
  document.getElementById('openTableId').value = tbl.id;
  document.getElementById('modalTableTitle').innerHTML = `<i class="fa-solid fa-door-open text-warning me-2"></i> Mở Bàn: ${tbl.name}`;
  document.getElementById('openTableModal').style.display = 'flex';
}

function closeModal(modalId) {
  document.getElementById(modalId).style.display = 'none';
}

async function submitOpenTable(e) {
  e.preventDefault();
  const tableId = document.getElementById('openTableId').value;
  const customerName = document.getElementById('openCustomerName').value;
  const guestCount = document.getElementById('openGuestCount').value;

  const formData = new FormData();
  formData.append('action', 'table_action');
  formData.append('sub_action', 'open_table');
  formData.append('table_id', tableId);
  formData.append('customer_name', customerName);
  formData.append('guest_count', guestCount);

  const res = await fetch('api.php', { method: 'POST', body: formData });
  const data = await res.json();
  if (data.success) {
    closeModal('openTableModal');
    // Thu ngân -> Thanh toán, còn lại -> POS gọi món
    const role = '<?= $userRole ?>';
    if (role === 'cashier') {
      window.location.href = `checkout.php?table_id=${tableId}`;
    } else {
      window.location.href = `pos.php?table_id=${tableId}`;
    }
  } else {
    Swal.fire('Lỗi', data.message, 'error');
  }
}

async function setTableStatus(tableId, status) {
  const formData = new FormData();
  formData.append('action', 'table_action');
  formData.append('sub_action', 'set_status');
  formData.append('table_id', tableId);
  formData.append('status', status);

  const res = await fetch('api.php', { method: 'POST', body: formData });
  const data = await res.json();
  if (data.success) {
    loadTables();
  }
}

function openTransferMergeModal(type) {
  document.getElementById('tmActionType').value = type;
  document.getElementById('tmSourceTableId').value = selectedTable.id;
  
  const title = document.getElementById('transferModalTitle');
  const desc = document.getElementById('tmDescText');
  const select = document.getElementById('tmTargetTableSelect');

  if (type === 'transfer') {
    title.innerHTML = `<i class="fa-solid fa-arrow-right-arrow-left text-warning me-2"></i> Chuyển Từ ${selectedTable.name} Sang Bàn Khác`;
    desc.innerText = 'Chọn bàn trống để chuyển toàn bộ khách và món sang:';
    // Lấy các bàn trống
    const targets = allTables.filter(t => t.id != selectedTable.id && t.status === 'empty');
    select.innerHTML = targets.map(t => `<option value="${t.id}">${t.name} (${t.area_name}) - Sức chứa: ${t.capacity} khách</option>`).join('');
  } else {
    title.innerHTML = `<i class="fa-solid fa-object-group text-danger me-2"></i> Gộp Bàn ${selectedTable.name} Vào Bàn Khác`;
    desc.innerText = 'Chọn bàn đang có khách để gộp hóa đơn và món ăn:';
    // Lấy các bàn đang có khách
    const targets = allTables.filter(t => t.id != selectedTable.id && t.status === 'occupied');
    select.innerHTML = targets.map(t => `<option value="${t.id}">${t.name} (${t.area_name}) - Khách: ${t.customer_name || 'Khách lẻ'}</option>`).join('');
  }

  document.getElementById('transferMergeModal').style.display = 'flex';
}

async function submitTransferMerge() {
  const type = document.getElementById('tmActionType').value;
  const sourceTableId = document.getElementById('tmSourceTableId').value;
  const targetTableId = document.getElementById('tmTargetTableSelect').value;

  if (!targetTableId) {
    Swal.fire('Thông báo', 'Không có bàn phù hợp để thực hiện!', 'warning');
    return;
  }

  const formData = new FormData();
  formData.append('action', 'table_action');
  formData.append('sub_action', type === 'transfer' ? 'transfer_table' : 'merge_table');
  formData.append('table_id', sourceTableId);
  formData.append('target_table_id', targetTableId);

  const res = await fetch('api.php', { method: 'POST', body: formData });
  const data = await res.json();
  if (data.success) {
    closeModal('transferMergeModal');
    Swal.fire({
      icon: 'success',
      title: 'Thành công',
      text: data.message,
      background: '#1e293b',
      color: '#fff'
    });
    loadTables();
  } else {
    Swal.fire('Lỗi', data.message, 'error');
  }
}

// Auto reload mỗi 15 giây
setInterval(loadTables, 15000);
loadTables();
</script>
</body>
</html>
