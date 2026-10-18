<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản Trị Nhà Hàng & Thống Kê - Hoàng Kim Restaurant</title>
  <link rel="stylesheet" href="assets/css/app.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php include __DIR__ . '/header.php'; ?>

<main class="app-container">
  <div class="manager-layout">
    
    <!-- SIDEBAR NAVIGATION DỌC DÀNH CHO QUẢN LÝ -->
    <aside class="manager-sidebar">
      <div style="padding: 12px; margin-bottom: 10px; border-bottom: 1px solid var(--dark-border);">
        <div style="font-size: 11px; text-transform: uppercase; color: var(--text-muted);">Cổng Quản Trị</div>
        <div style="font-weight: 800; font-size: 16px; color: var(--accent-gold);">MANAGER CENTER</div>
      </div>

      <a href="javascript:void(0)" class="manager-menu-link active" onclick="switchManagerTab('reports', this)">
        <i class="fa-solid fa-chart-pie"></i> Báo Cáo Thống Kê
      </a>
      <a href="javascript:void(0)" class="manager-menu-link" onclick="switchManagerTab('menu', this)">
        <i class="fa-solid fa-book-open"></i> Quản Lý Thực Đơn
      </a>
      <a href="tables.php" class="manager-menu-link">
        <i class="fa-solid fa-chair"></i> Sơ Đồ & Quản Lý Bàn
      </a>
      <a href="reservations.php" class="manager-menu-link">
        <i class="fa-solid fa-calendar-check"></i> Yêu Cầu Đặt Bàn
      </a>
      <a href="javascript:void(0)" class="manager-menu-link" onclick="showStaffList()">
        <i class="fa-solid fa-users"></i> Quản Lý Nhân Viên
      </a>
    </aside>

    <!-- KHU VỰC NỘI DUNG CHÍNH (CONTENT AREA) -->
    <section>
      
      <!-- 1. MÀN HÌNH BÁO CÁO THỐNG KÊ (REPORT CENTER) -->
      <div id="tabReportsSection">
        <!-- Top KPI Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
          <div class="stat-widget">
            <div class="stat-icon" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
              <i class="fa-solid fa-sack-dollar"></i>
            </div>
            <div>
              <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Doanh Thu Hôm Nay</div>
              <div id="kpiTodayRevenue" style="font-size: 22px; font-weight: 800; color: #fff;">0 đ</div>
            </div>
          </div>

          <div class="stat-widget">
            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
              <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
              <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Tổng Đơn Đã Thu</div>
              <div id="kpiTodayOrders" style="font-size: 22px; font-weight: 800; color: #fff;">0 đơn</div>
            </div>
          </div>

          <div class="stat-widget">
            <div class="stat-icon" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6;">
              <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <div>
              <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Tiền Mặt</div>
              <div id="kpiCashRev" style="font-size: 20px; font-weight: 800; color: #fff;">0 đ</div>
            </div>
          </div>

          <div class="stat-widget">
            <div class="stat-icon" style="background: rgba(168, 85, 247, 0.15); color: #a855f7;">
              <i class="fa-solid fa-qrcode"></i>
            </div>
            <div>
              <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Chuyển Khoản / Thẻ</div>
              <div id="kpiTransferRev" style="font-size: 20px; font-weight: 800; color: #fff;">0 đ</div>
            </div>
          </div>
        </div>

        <!-- Biểu đồ doanh thu tuần -->
        <div class="card-glass" style="padding: 24px; margin-bottom: 24px;">
          <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 16px;">
            <i class="fa-solid fa-chart-column text-warning me-2"></i> Xu Hướng Doanh Thu Theo Ngày Trong Tuần
          </h3>
          <div style="height: 280px;">
            <canvas id="revenueWeeklyChart"></canvas>
          </div>
        </div>

        <!-- Bảng xếp hạng MENU MIX -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
          <!-- Top Món Bán Chạy -->
          <div class="card-glass" style="padding: 20px;">
            <h3 style="font-size: 15px; font-weight: 700; color: #10b981; margin-bottom: 14px;">
              <i class="fa-solid fa-trophy me-2"></i> Top 5 Món Ăn Bán Chạy Nhất (Hot Sellers)
            </h3>
            <div id="topItemsContainer">
              <!-- Rendered items -->
            </div>
          </div>

          <!-- Món Cần Đẩy Mạnh -->
          <div class="card-glass" style="padding: 20px;">
            <h3 style="font-size: 15px; font-weight: 700; color: #f59e0b; margin-bottom: 14px;">
              <i class="fa-solid fa-arrow-trend-down me-2"></i> Menu Mix: Món Kém Hiệu Quả / Cần Khuyến Mãi
            </h3>
            <div id="lowItemsContainer">
              <!-- Rendered items -->
            </div>
          </div>
        </div>
      </div>

      <!-- 2. MÀN HÌNH QUẢN LÝ THỰC ĐƠN (MENU MANAGEMENT) -->
      <div id="tabMenuSection" style="display: none;">
        <div style="display: grid; grid-template-columns: 280px 1fr; gap: 20px;">
          <!-- Cây thư mục nhóm món (Tree View) -->
          <div class="card-glass" style="padding: 16px;">
            <h3 style="font-size: 14px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 12px;">
              <i class="fa-solid fa-folder-tree me-2"></i> Cây Danh Mục (Tree View)
            </h3>
            <div id="categoryTreeView" style="display: flex; flex-direction: column; gap: 6px;">
              <!-- Categories tree nodes -->
            </div>
          </div>

          <!-- Danh sách dạng bảng (Data Grid) của các món ăn -->
          <div class="card-glass" style="padding: 20px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
              <h3 style="font-size: 16px; font-weight: 700;">
                <i class="fa-solid fa-table-list text-warning me-2"></i> Danh Sách Món Ăn Chi Tiết
              </h3>
              <button class="btn-primary" onclick="openAddMenuItemModal()">
                <i class="fa-solid fa-plus"></i> Thêm Món Ăn Mới
              </button>
            </div>

            <div style="overflow-x: auto;">
              <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <thead>
                  <tr style="border-bottom: 1px solid var(--dark-border); text-align: left; color: var(--text-muted);">
                    <th style="padding: 10px;">Hình ảnh</th>
                    <th style="padding: 10px;">Tên món</th>
                    <th style="padding: 10px;">Nhóm món</th>
                    <th style="padding: 10px;">Giá bán</th>
                    <th style="padding: 10px;">Trạng thái</th>
                    <th style="padding: 10px; text-align: right;">Hành động</th>
                  </tr>
                </thead>
                <tbody id="menuManagementTableBody">
                  <!-- Data Grid rows -->
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    </section>

  </div>
</main>

<!-- Modal Thêm / Sửa Món Ăn -->
<div class="app-modal-backdrop" id="menuItemModal">
  <div class="app-modal-dialog">
    <div class="modal-header">
      <h3 id="menuItemModalTitle" style="font-size: 17px; font-weight: 700;">Thêm Món Ăn</h3>
      <button onclick="closeModal('menuItemModal')" style="background: none; border: none; color: #fff; font-size: 18px; cursor: pointer;">&times;</button>
    </div>
    <div class="modal-body">
      <form id="menuItemForm" onsubmit="saveMenuItem(event)">
        <input type="hidden" id="editItemId" value="0">
        <div class="form-group">
          <label class="form-label">Tên món ăn</label>
          <input type="text" id="editItemName" class="form-control" required placeholder="VD: Lẩu Nấm Bò Wagyu">
        </div>
        <div class="form-group">
          <label class="form-label">Thuộc nhóm danh mục</label>
          <select id="editItemCategory" class="form-control">
            <!-- Dynamic Category Options -->
          </select>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div class="form-group">
            <label class="form-label">Giá bán (VNĐ)</label>
            <input type="number" id="editItemPrice" class="form-control" required placeholder="VD: 150000">
          </div>
          <div class="form-group">
            <label class="form-label">Đơn vị tính</label>
            <input type="text" id="editItemUnit" class="form-control" value="Phần">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Link hình ảnh món (URL)</label>
          <input type="text" id="editItemImage" class="form-control" placeholder="https://images.unsplash.com/...">
        </div>
        <div class="form-group">
          <label class="form-label">Trạng thái bán</label>
          <select id="editItemStatus" class="form-control">
            <option value="available">Đang phục vụ (Available)</option>
            <option value="out_of_stock">Ngừng bán (Hết nguyên liệu)</option>
          </select>
        </div>
        <div class="modal-footer" style="padding-right: 0; padding-bottom: 0;">
          <button type="button" class="btn-secondary" onclick="closeModal('menuItemModal')">Hủy</button>
          <button type="submit" class="btn-primary"><i class="fa-solid fa-save"></i> Lưu Dữ Liệu</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let dashboardData = null;
let allCategories = [];
let allMenuItems = [];
let selectedTreeCategory = 'all';

function switchManagerTab(tab, btn) {
  document.querySelectorAll('.manager-menu-link').forEach(l => l.classList.remove('active'));
  btn.classList.add('active');

  if (tab === 'reports') {
    document.getElementById('tabReportsSection').style.display = 'block';
    document.getElementById('tabMenuSection').style.display = 'none';
  } else {
    document.getElementById('tabReportsSection').style.display = 'none';
    document.getElementById('tabMenuSection').style.display = 'block';
    loadMenuManagementData();
  }
}

async function loadManagerDashboard() {
  try {
    const res = await fetch('api.php?action=get_manager_dashboard');
    const data = await res.json();
    if (!data.success) return;

    dashboardData = data;
    document.getElementById('kpiTodayRevenue').innerText = parseInt(data.today_revenue).toLocaleString('vi-VN') + ' đ';
    document.getElementById('kpiTodayOrders').innerText = `${data.today_orders} đơn`;
    document.getElementById('kpiCashRev').innerText = parseInt(data.cash_rev).toLocaleString('vi-VN') + ' đ';
    document.getElementById('kpiTransferRev').innerText = parseInt(data.transfer_rev).toLocaleString('vi-VN') + ' đ';

    renderWeeklyChart(data.weekly_data);
    renderMenuMix(data.top_items, data.low_items);
  } catch (err) {
    console.error('Error loading dashboard:', err);
  }
}

function renderWeeklyChart(weeklyData) {
  const ctx = document.getElementById('revenueWeeklyChart').getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: weeklyData.map(d => d.day),
      datasets: [{
        label: 'Doanh thu (VNĐ)',
        data: weeklyData.map(d => d.revenue),
        backgroundColor: 'rgba(217, 119, 6, 0.65)',
        borderColor: '#d97706',
        borderWidth: 2,
        borderRadius: 8
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { labels: { color: '#94a3b8' } }
      },
      scales: {
        x: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(255,255,255,0.05)' } },
        y: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(255,255,255,0.05)' } }
      }
    }
  });
}

function renderMenuMix(topItems, lowItems) {
  const topBox = document.getElementById('topItemsContainer');
  topBox.innerHTML = topItems.map((it, idx) => `
    <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px; border-bottom: 1px solid var(--dark-border);">
      <div style="display: flex; align-items: center; gap: 10px;">
        <span style="width: 24px; height: 24px; background: rgba(16, 185, 129, 0.2); color: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12px;">${idx + 1}</span>
        <span style="font-weight: 600;">${it.item_name}</span>
      </div>
      <div>
        <span style="color: var(--accent-gold); font-weight: 800;">${it.total_qty} phần</span>
        <div style="font-size: 11px; color: var(--text-muted); text-align: right;">${parseInt(it.total_sales).toLocaleString('vi-VN')} đ</div>
      </div>
    </div>
  `).join('');

  const lowBox = document.getElementById('lowItemsContainer');
  lowBox.innerHTML = lowItems.map((it, idx) => `
    <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px; border-bottom: 1px solid var(--dark-border);">
      <div style="display: flex; align-items: center; gap: 10px;">
        <span style="width: 24px; height: 24px; background: rgba(245, 158, 11, 0.2); color: #f59e0b; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12px;">${idx + 1}</span>
        <span style="font-weight: 600;">${it.item_name}</span>
      </div>
      <div>
        <span style="color: #ef4444; font-weight: 700;">${it.total_qty} đã bán</span>
        <div style="font-size: 11px; color: var(--text-muted); text-align: right;">Giá: ${parseInt(it.price).toLocaleString('vi-VN')} đ</div>
      </div>
    </div>
  `).join('');
}

// QUẢN LÝ THỰC ĐƠN
async function loadMenuManagementData() {
  const res = await fetch('api.php?action=get_pos_data');
  const data = await res.json();
  if (!data.success) return;

  allCategories = data.categories;
  allMenuItems = data.menu_items;

  renderCategoryTree();
  renderMenuManagementTable();
  populateCategorySelect();
}

function renderCategoryTree() {
  const box = document.getElementById('categoryTreeView');
  let html = `
    <div class="manager-menu-link ${selectedTreeCategory === 'all' ? 'active' : ''}" style="cursor: pointer;" onclick="selectCategoryTreeNode('all')">
      <i class="fa-solid fa-list"></i> Tất Cả (${allMenuItems.length})
    </div>
  `;
  allCategories.forEach(c => {
    const count = allMenuItems.filter(m => m.category_id == c.id).length;
    html += `
      <div class="manager-menu-link ${selectedTreeCategory == c.id ? 'active' : ''}" style="cursor: pointer;" onclick="selectCategoryTreeNode(${c.id})">
        <i class="fa-solid fa-folder"></i> ${c.name} (${count})
      </div>
    `;
  });
  box.innerHTML = html;
}

function selectCategoryTreeNode(catId) {
  selectedTreeCategory = catId;
  renderCategoryTree();
  renderMenuManagementTable();
}

function renderMenuManagementTable() {
  const tbody = document.getElementById('menuManagementTableBody');
  const filtered = selectedTreeCategory === 'all' 
    ? allMenuItems 
    : allMenuItems.filter(m => m.category_id == selectedTreeCategory);

  tbody.innerHTML = filtered.map(m => {
    const isOut = m.status === 'out_of_stock';
    return `
      <tr style="border-bottom: 1px solid var(--dark-border);">
        <td style="padding: 10px;">
          <img src="${m.image}" style="width: 50px; height: 40px; object-fit: cover; border-radius: 6px;" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&auto=format&fit=crop&q=60'">
        </td>
        <td style="padding: 10px; font-weight: 700;">${m.name}</td>
        <td style="padding: 10px; color: var(--text-muted);">${m.category_name}</td>
        <td style="padding: 10px; font-weight: 800; color: var(--accent-gold);">${parseInt(m.price).toLocaleString('vi-VN')} đ / ${m.unit || 'Phần'}</td>
        <td style="padding: 10px;">
          <span style="font-size: 11px; padding: 3px 8px; border-radius: var(--radius-full); font-weight: bold; background: ${isOut ? 'rgba(239, 68, 68, 0.2)' : 'rgba(16, 185, 129, 0.2)'}; color: ${isOut ? '#ef4444' : '#10b981'};">
            ${isOut ? 'Ngừng bán' : 'Đang bán'}
          </span>
        </td>
        <td style="padding: 10px; text-align: right;">
          <button class="btn-secondary" style="padding: 5px 10px; font-size: 12px; margin-right: 4px;" onclick="editMenuItem(${m.id})">
            <i class="fa-solid fa-pen-to-square"></i> Sửa
          </button>
          <button class="btn-secondary" style="padding: 5px 10px; font-size: 12px; color: ${isOut ? '#10b981' : '#f59e0b'};" onclick="toggleItemStatus(${m.id}, '${isOut ? 'available' : 'out_of_stock'}')">
            <i class="fa-solid ${isOut ? 'fa-play' : 'fa-ban'}"></i>
          </button>
          <button class="btn-secondary" style="padding: 5px 10px; font-size: 12px; color: #ef4444; margin-left: 4px;" onclick="deleteMenuItem(${m.id})">
            <i class="fa-solid fa-trash"></i>
          </button>
        </td>
      </tr>
    `;
  }).join('');
}

function populateCategorySelect() {
  const sel = document.getElementById('editItemCategory');
  sel.innerHTML = allCategories.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
}

function openAddMenuItemModal() {
  document.getElementById('editItemId').value = 0;
  document.getElementById('menuItemModalTitle').innerText = 'Thêm Món Ăn Mới';
  document.getElementById('menuItemForm').reset();
  document.getElementById('menuItemModal').style.display = 'flex';
}

function editMenuItem(id) {
  const item = allMenuItems.find(m => m.id == id);
  if (!item) return;

  document.getElementById('editItemId').value = item.id;
  document.getElementById('menuItemModalTitle').innerText = `Sửa Món: ${item.name}`;
  document.getElementById('editItemName').value = item.name;
  document.getElementById('editItemCategory').value = item.category_id;
  document.getElementById('editItemPrice').value = item.price;
  document.getElementById('editItemUnit').value = item.unit || 'Phần';
  document.getElementById('editItemImage').value = item.image || '';
  document.getElementById('editItemStatus').value = item.status;
  document.getElementById('menuItemModal').style.display = 'flex';
}

function closeModal(id) {
  document.getElementById(id).style.display = 'none';
}

async function saveMenuItem(e) {
  e.preventDefault();
  const formData = new FormData();
  formData.append('action', 'manage_menu');
  formData.append('sub_action', 'save_item');
  formData.append('id', document.getElementById('editItemId').value);
  formData.append('name', document.getElementById('editItemName').value);
  formData.append('category_id', document.getElementById('editItemCategory').value);
  formData.append('price', document.getElementById('editItemPrice').value);
  formData.append('unit', document.getElementById('editItemUnit').value);
  formData.append('image', document.getElementById('editItemImage').value);
  formData.append('status', document.getElementById('editItemStatus').value);

  const res = await fetch('api.php', { method: 'POST', body: formData });
  const data = await res.json();
  if (data.success) {
    closeModal('menuItemModal');
    Swal.fire({
      icon: 'success',
      title: 'Thành công',
      text: data.message,
      background: '#1e293b',
      color: '#fff'
    });
    loadMenuManagementData();
  } else {
    Swal.fire('Lỗi', data.message, 'error');
  }
}

async function toggleItemStatus(id, newStatus) {
  const formData = new FormData();
  formData.append('action', 'manage_menu');
  formData.append('sub_action', 'toggle_status');
  formData.append('id', id);
  formData.append('status', newStatus);

  const res = await fetch('api.php', { method: 'POST', body: formData });
  const data = await res.json();
  if (data.success) {
    loadMenuManagementData();
  }
}

async function deleteMenuItem(id) {
  Swal.fire({
    title: 'Xác nhận xóa món ăn này?',
    text: 'Thao tác này sẽ xóa món khỏi thực đơn của nhà hàng!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Xóa ngay',
    cancelButtonText: 'Hủy',
    background: '#1e293b',
    color: '#fff'
  }).then(async res => {
    if (res.isConfirmed) {
      const formData = new FormData();
      formData.append('action', 'manage_menu');
      formData.append('sub_action', 'delete_item');
      formData.append('id', id);

      const r = await fetch('api.php', { method: 'POST', body: formData });
      const d = await r.json();
      if (d.success) {
        loadMenuManagementData();
      }
    }
  });
}

function showStaffList() {
  Swal.fire({
    title: 'Danh Sách Nhân Viên & Phân Quyền 5 Bộ Phận',
    width: 650,
    html: `
      <div style="text-align: left; font-size: 13px; max-height: 400px; overflow-y: auto;">
        <div style="margin-bottom: 8px; font-weight: bold; color: #ef4444;"><i class="fa-solid fa-crown me-1"></i> 1. Quản lý / Chủ nhà hàng (Admin):</div>
        <div style="padding: 4px 8px; background: rgba(255,255,255,0.03); margin-bottom: 3px; border-radius: 4px;">• <b>admin1</b>: Nguyễn Huy Hoàng (Admin) - Quản lý nhà hàng</div>
        <div style="padding: 4px 8px; background: rgba(255,255,255,0.03); margin-bottom: 3px; border-radius: 4px;">• <b>admin2</b>: Trần Đình Trọng (Admin) - Phó quản lý điều hành</div>
        <div style="padding: 4px 8px; background: rgba(255,255,255,0.03); margin-bottom: 8px; border-radius: 4px;">• <b>admin3</b>: Vũ Thị Minh Anh (Admin) - Giám sát vận hành chuỗi</div>

        <div style="margin-bottom: 8px; font-weight: bold; color: #10b981;"><i class="fa-solid fa-bell-concierge me-1"></i> 2. Bộ phận Lễ tân (Receptionist):</div>
        <div style="padding: 4px 8px; background: rgba(255,255,255,0.03); margin-bottom: 3px; border-radius: 4px;">• <b>letan1</b>: Trần Lễ Tân - Tiếp đón khách & Xếp bàn</div>
        <div style="padding: 4px 8px; background: rgba(255,255,255,0.03); margin-bottom: 8px; border-radius: 4px;">• <b>letan2</b>: Đỗ Thanh Hương - Lễ tân ca tối & Đặt bàn</div>

        <div style="margin-bottom: 8px; font-weight: bold; color: #3b82f6;"><i class="fa-solid fa-utensils me-1"></i> 3. Bộ phận Phục vụ (Waiter):</div>
        <div style="padding: 4px 8px; background: rgba(255,255,255,0.03); margin-bottom: 3px; border-radius: 4px;">• <b>phucvu1</b>: Đạt Phục Vụ - Order món & Phục vụ sảnh chính</div>
        <div style="padding: 4px 8px; background: rgba(255,255,255,0.03); margin-bottom: 3px; border-radius: 4px;">• <b>phucvu2</b>: Nguyễn Văn Nam - Phục vụ phòng VIP</div>
        <div style="padding: 4px 8px; background: rgba(255,255,255,0.03); margin-bottom: 8px; border-radius: 4px;">• <b>phucvu3</b>: Đinh Tuấn Kiệt - Phục vụ bàn tầng 2</div>

        <div style="margin-bottom: 8px; font-weight: bold; color: #06b6d4;"><i class="fa-solid fa-cash-register me-1"></i> 4. Bộ phận Thu ngân (Cashier):</div>
        <div style="padding: 4px 8px; background: rgba(255,255,255,0.03); margin-bottom: 3px; border-radius: 4px;">• <b>thungan1</b>: Lê Thu Ngân - Thu ngân ca ngày</div>
        <div style="padding: 4px 8px; background: rgba(255,255,255,0.03); margin-bottom: 8px; border-radius: 4px;">• <b>thungan2</b>: Bùi Lan Phương - Thu ngân ca tối</div>

        <div style="margin-bottom: 8px; font-weight: bold; color: #f59e0b;"><i class="fa-solid fa-fire-burner me-1"></i> 5. Bộ phận Bếp / Pha chế (Kitchen/Bar):</div>
        <div style="padding: 4px 8px; background: rgba(255,255,255,0.03); margin-bottom: 3px; border-radius: 4px;">• <b>bep1</b>: Phạm Bếp Trưởng - Bếp trưởng điều phối</div>
        <div style="padding: 4px 8px; background: rgba(255,255,255,0.03); margin-bottom: 4px; border-radius: 4px;">• <b>bep2</b>: Lê Minh Khang - Bếp phó / Bếp nóng</div>
      </div>
    `,
    confirmButtonText: 'Đóng',
    confirmButtonColor: '#d97706',
    background: '#1e293b',
    color: '#fff'
  });
}

loadManagerDashboard();
</script>
</body>
</html>
