<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bán Hàng & Gọi Món (POS) - Hoàng Kim Restaurant</title>
  <link rel="stylesheet" href="assets/css/app.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body style="overflow: hidden;">

<?php include __DIR__ . '/header.php'; ?>

<main style="padding: 16px 24px;">
  <div class="pos-container">
    
    <!-- PHẦN BÊN TRÁI: ORDER LIST -->
    <section class="pos-order-sidebar">
      <div class="pos-order-header">
        <div>
          <div style="font-size: 11px; text-transform: uppercase; color: var(--text-muted);">Đang phục vụ</div>
          <h2 id="posTableName" style="font-size: 20px; font-weight: 800; color: #fff;">
            <i class="fa-solid fa-chair text-warning"></i> Bàn --
          </h2>
        </div>
        <div style="text-align: right;">
          <span id="posTableStatus" class="table-badge" style="background: rgba(239, 68, 68, 0.2); color: #ef4444;">CÓ KHÁCH</span>
          <div id="posCustomerName" style="font-size: 12px; color: var(--text-sub); margin-top: 4px;">Khách lẻ</div>
        </div>
      </div>

      <!-- Danh sách món đã chọn -->
      <div class="pos-item-list" id="posItemList">
        <!-- Rendered order items -->
      </div>

      <!-- Footer tính tiền và nút GỬI BẾP -->
      <div class="pos-order-footer">
        <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 13px; color: var(--text-muted);">
          <span>Tổng số lượng món:</span>
          <b id="posTotalQty" style="color: #fff;">0 món</b>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 14px;">
          <span style="font-size: 14px; font-weight: 700;">TỔNG TẠM TÍNH:</span>
          <span id="posSubtotal" style="font-size: 24px; font-weight: 800; color: var(--accent-gold);">0 đ</span>
        </div>

        <div style="display: grid; grid-template-columns: <?= in_array('checkout.php', $allowedPages) ? '1fr 1fr' : '1fr' ?>; gap: 10px; margin-bottom: 10px;">
          <button class="btn-secondary" onclick="clearCurrentOrder()" style="padding: 10px; font-size: 13px; justify-content: center;">
            <i class="fa-solid fa-trash-can"></i> Xóa Đơn
          </button>
          <?php if (in_array('checkout.php', $allowedPages)): ?>
            <a id="btnToCheckout" href="checkout.php" class="btn-secondary" style="padding: 10px; font-size: 13px; justify-content: center; text-decoration: none; color: #38bdf8;">
              <i class="fa-solid fa-receipt"></i> Sang Tính Tiền
            </a>
          <?php endif; ?>
        </div>

        <button class="btn-send-kitchen" onclick="sendToKitchen()">
          <i class="fa-solid fa-paper-plane"></i> GỬI YÊU CẦU XUỐNG BẾP (KDS)
        </button>
      </div>
    </section>

    <!-- PHẦN BÊN PHẢI: THỰC ĐƠN TRỰC QUAN (TOUCH-OPTIMIZED) -->
    <section class="pos-menu-section">
      <!-- Search & Filters -->
      <div style="display: flex; gap: 12px; margin-bottom: 12px;">
        <div style="position: relative; flex: 1;">
          <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 14px; top: 12px; color: var(--text-muted);"></i>
          <input type="text" id="menuSearchInput" class="form-control" placeholder="Tìm kiếm nhanh món ăn..." style="padding-left: 40px;" oninput="handleSearchMenu()">
        </div>
        <select id="switchTableSelect" class="form-control" style="width: 200px;" onchange="switchTable(this.value)">
          <!-- Switch Table Options -->
        </select>
      </div>

      <!-- Categories Tabs -->
      <div class="menu-category-tabs" id="menuCatTabs">
        <!-- Rendered categories -->
      </div>

      <!-- Items Grid -->
      <div class="menu-item-grid" id="menuGridContainer">
        <!-- Rendered menu cards -->
      </div>
    </section>

  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const urlParams = new URLSearchParams(window.location.search);
let currentTableId = parseInt(urlParams.get('table_id') || 1);

let categories = [];
let allMenuItems = [];
let currentCategory = 'all';
let currentOrderItems = []; // [{menu_item_id, item_name, price, quantity, notes, kitchen_status}]

// Khởi chạy dữ liệu POS
async function loadPosData() {
  try {
    const res = await fetch(`api.php?action=get_pos_data&table_id=${currentTableId}`);
    const data = await res.json();
    if (!data.success) return;

    categories = data.categories;
    allMenuItems = data.menu_items;

    // Render thông tin bàn
    if (data.table) {
      document.getElementById('posTableName').innerHTML = `<i class="fa-solid fa-chair text-warning"></i> ${data.table.name} (${data.table.area_name})`;
      document.getElementById('posCustomerName').innerText = (data.order && data.order.customer_name) ? `Khách: ${data.order.customer_name}` : 'Khách lẻ';
      document.getElementById('btnToCheckout').href = `checkout.php?table_id=${currentTableId}`;
    }

    // Nạp các món trong đơn hiện tại
    currentOrderItems = (data.order_items || []).map(oi => ({
      menu_item_id: oi.menu_item_id,
      item_name: oi.item_name,
      price: parseFloat(oi.price),
      quantity: parseInt(oi.quantity),
      notes: oi.notes || '',
      kitchen_status: oi.kitchen_status || 'pending'
    }));

    renderCategoryTabs();
    renderMenuGrid();
    renderOrderList();
    loadAllTablesDropdown();
  } catch (err) {
    console.error('Error loading POS:', err);
  }
}

// Danh sách bàn chuyển đổi nhanh
async function loadAllTablesDropdown() {
  const res = await fetch('api.php?action=get_tables_overview');
  const data = await res.json();
  if (data.success) {
    const select = document.getElementById('switchTableSelect');
    select.innerHTML = data.tables.map(t => `
      <option value="${t.id}" ${t.id == currentTableId ? 'selected' : ''}>
        ${t.name} (${t.status === 'occupied' ? 'Có khách' : 'Trống'})
      </option>
    `).join('');
  }
}

function switchTable(tblId) {
  window.location.href = `pos.php?table_id=${tblId}`;
}

// Render Tabs danh mục món
function renderCategoryTabs() {
  const container = document.getElementById('menuCatTabs');
  let html = `<button class="category-tab-btn ${currentCategory === 'all' ? 'active' : ''}" onclick="filterCategory('all', this)">
                <i class="fa-solid fa-utensils me-1"></i> Tất Cả
              </button>`;
  categories.forEach(c => {
    html += `<button class="category-tab-btn ${currentCategory == c.id ? 'active' : ''}" onclick="filterCategory(${c.id}, this)">
              <i class="fa-solid ${c.icon || 'fa-bowl-food'} me-1"></i> ${c.name}
            </button>`;
  });
  container.innerHTML = html;
}

function filterCategory(catId, btn) {
  currentCategory = catId;
  document.querySelectorAll('#menuCatTabs .category-tab-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  renderMenuGrid();
}

function handleSearchMenu() {
  renderMenuGrid();
}

// Render lưới món ăn (Grid)
function renderMenuGrid() {
  const container = document.getElementById('menuGridContainer');
  const keyword = document.getElementById('menuSearchInput').value.toLowerCase().trim();

  let filtered = allMenuItems;
  if (currentCategory !== 'all') {
    filtered = filtered.filter(m => m.category_id == currentCategory);
  }
  if (keyword) {
    filtered = filtered.filter(m => m.name.toLowerCase().includes(keyword));
  }

  if (filtered.length === 0) {
    container.innerHTML = `<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: var(--text-muted);">Không tìm thấy món ăn phù hợp!</div>`;
    return;
  }

  container.innerHTML = filtered.map(m => `
    <div class="menu-item-card" onclick="addMenuItemToOrder(${m.id})">
      <img src="${m.image}" class="menu-item-img" alt="${m.name}" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&auto=format&fit=crop&q=60'">
      <div class="menu-item-info">
        <div class="menu-item-title">${m.name}</div>
        <div style="display: flex; align-items: center; justify-content: space-between;">
          <span class="menu-item-cost">${parseInt(m.price).toLocaleString('vi-VN')} đ</span>
          <span style="font-size: 11px; color: var(--text-muted); background: rgba(255,255,255,0.06); padding: 2px 6px; border-radius: 4px;">+ Thêm</span>
        </div>
      </div>
    </div>
  `).join('');
}

// Thao tác Order: Chạm để thêm món
function addMenuItemToOrder(menuItemId) {
  const item = allMenuItems.find(m => m.id == menuItemId);
  if (!item) return;

  const existing = currentOrderItems.find(oi => oi.menu_item_id == menuItemId);
  if (existing) {
    existing.quantity += 1;
  } else {
    currentOrderItems.push({
      menu_item_id: item.id,
      item_name: item.name,
      price: parseFloat(item.price),
      quantity: 1,
      notes: '',
      kitchen_status: 'pending'
    });
  }
  renderOrderList();
}

// Tăng / Giảm số lượng
function updateQty(index, delta) {
  currentOrderItems[index].quantity += delta;
  if (currentOrderItems[index].quantity <= 0) {
    currentOrderItems.splice(index, 1);
  }
  renderOrderList();
}

// Cập nhật ghi chú (VD: Không hành, ít cay)
function updateNote(index, noteVal) {
  currentOrderItems[index].notes = noteVal;
}

// Render Order List bên trái
function renderOrderList() {
  const container = document.getElementById('posItemList');

  if (currentOrderItems.length === 0) {
    container.innerHTML = `
      <div style="text-align: center; padding: 60px 20px; color: var(--text-muted);">
        <i class="fa-solid fa-bowl-rice" style="font-size: 40px; margin-bottom: 12px; opacity: 0.3;"></i>
        <div>Chưa có món nào được chọn.</div>
        <div style="font-size: 12px;">Chạm vào món ăn bên phải để thêm vào bàn!</div>
      </div>
    `;
    updateOrderSummary(0, 0);
    return;
  }

  let totalQty = 0;
  let subtotal = 0;

  container.innerHTML = currentOrderItems.map((it, idx) => {
    totalQty += it.quantity;
    const itemTotal = it.price * it.quantity;
    subtotal += itemTotal;

    return `
      <div class="pos-order-item-row">
        <div class="pos-item-top">
          <div class="pos-item-name">${it.item_name}</div>
          <div class="pos-item-price">${itemTotal.toLocaleString('vi-VN')} đ</div>
        </div>
        <div style="display: flex; align-items: center; justify-content: space-between;">
          <div style="font-size: 12px; color: var(--text-muted);">${it.price.toLocaleString('vi-VN')} đ/phần</div>
          <div class="pos-qty-controls">
            <button class="qty-btn" onclick="updateQty(${idx}, -1)">-</button>
            <span style="font-weight: 700; width: 22px; text-align: center;">${it.quantity}</span>
            <button class="qty-btn" onclick="updateQty(${idx}, 1)">+</button>
          </div>
        </div>
        <input type="text" class="pos-item-note-input" placeholder="Ghi chú (VD: Không hành, ít cay...)" 
               value="${it.notes || ''}" 
               onchange="updateNote(${idx}, this.value)">
      </div>
    `;
  }).join('');

  updateOrderSummary(totalQty, subtotal);
}

function updateOrderSummary(qty, subtotal) {
  document.getElementById('posTotalQty').innerText = `${qty} món`;
  document.getElementById('posSubtotal').innerText = `${subtotal.toLocaleString('vi-VN')} đ`;
}

function clearCurrentOrder() {
  Swal.fire({
    title: 'Xóa toàn bộ món đang chọn?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Đồng ý',
    cancelButtonText: 'Hủy',
    background: '#1e293b',
    color: '#fff'
  }).then(res => {
    if (res.isConfirmed) {
      currentOrderItems = [];
      renderOrderList();
    }
  });
}

// GỬI BẾP (SEND TO KITCHEN)
async function sendToKitchen() {
  if (currentOrderItems.length === 0) {
    Swal.fire('Thông báo', 'Vui lòng chọn ít nhất 1 món ăn trước khi gửi bếp!', 'warning');
    return;
  }

  try {
    const res = await fetch('api.php?action=save_order_pos', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        table_id: currentTableId,
        items: currentOrderItems
      })
    });
    const data = await res.json();

    if (data.success) {
      Swal.fire({
        icon: 'success',
        title: 'Thành công!',
        text: 'Phiếu gọi món đã được gửi xuống Hệ Thống Nhà Bếp (KDS)!',
        background: '#1e293b',
        color: '#fff'
      }).then(() => {
        loadPosData();
      });
    } else {
      Swal.fire('Lỗi', data.message, 'error');
    }
  } catch (err) {
    Swal.fire('Lỗi kết nối', 'Không thể gửi order đến máy chủ!', 'error');
  }
}
loadPosData();
</script>
</body>
</html>
