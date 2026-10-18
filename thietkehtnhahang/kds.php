<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Màn Hình Nhà Bếp (KDS) - Hoàng Kim Restaurant</title>
  <link rel="stylesheet" href="assets/css/app.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>

<?php include __DIR__ . '/header.php'; ?>

<main class="app-container">
  <!-- KDS Top Bar: Đồng hồ hệ thống & Tổng số lượng đơn đang chờ -->
  <div class="kds-header">
    <div style="display: flex; align-items: center; gap: 16px;">
      <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #ef4444, #dc2626); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: #fff;">
        <i class="fa-solid fa-fire-burner"></i>
      </div>
      <div>
        <h1 style="font-size: 22px; font-weight: 800; letter-spacing: 0.5px;">HỆ THỐNG HIỂN THỊ NHÀ BẾP (KDS)</h1>
        <div style="font-size: 13px; color: var(--text-muted);">Màn hình cảm ứng nhận đơn và chế biến theo thời gian thực</div>
      </div>
    </div>

    <div style="display: flex; align-items: center; gap: 20px;">
      <!-- Live Clock -->
      <div style="background: rgba(15, 23, 42, 0.6); padding: 8px 18px; border-radius: var(--radius-sm); border: 1px solid var(--dark-border); text-align: center;">
        <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase;">Đồng Hồ Hệ Thống</div>
        <div id="kdsClock" style="font-size: 20px; font-weight: 800; color: var(--accent-gold); font-family: monospace;">--:--:--</div>
      </div>

      <!-- Pending Counter -->
      <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); padding: 8px 18px; border-radius: var(--radius-sm); text-align: center;">
        <div style="font-size: 11px; color: #fca5a5; text-transform: uppercase;">Món Đang Chờ Làm</div>
        <div id="kdsTotalPending" style="font-size: 22px; font-weight: 800; color: #ef4444;">0</div>
      </div>

      <button class="btn-secondary" onclick="loadKdsOrders()" title="Làm mới">
        <i class="fa-solid fa-arrows-rotate"></i>
      </button>
    </div>
  </div>

  <!-- Bảng Kanban / Lưới Thẻ Phiếu Bếp -->
  <div class="kds-grid" id="kdsGrid">
    <!-- Rendered KDS Cards -->
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Cập nhật đồng hồ thời gian thực
function updateClock() {
  const now = new Date();
  const timeStr = now.toLocaleTimeString('vi-VN', { hour12: false });
  document.getElementById('kdsClock').innerText = timeStr;
}
setInterval(updateClock, 1000);
updateClock();

let kdsOrders = [];

// Tải danh sách đơn order bếp
async function loadKdsOrders() {
  try {
    const res = await fetch('api.php?action=get_kds_orders');
    const data = await res.json();
    if (!data.success) return;

    kdsOrders = data.cards;
    document.getElementById('kdsTotalPending').innerText = data.total_pending_items;
    renderKdsGrid();
  } catch (err) {
    console.error('Error loading KDS:', err);
  }
}

function renderKdsGrid() {
  const container = document.getElementById('kdsGrid');
  if (kdsOrders.length === 0) {
    container.innerHTML = `
      <div style="grid-column: 1/-1; text-align: center; padding: 80px 20px; background: #1e293b; border-radius: var(--radius-md); border: 1px dashed var(--dark-border);">
        <i class="fa-solid fa-check-double" style="font-size: 48px; color: #10b981; margin-bottom: 12px;"></i>
        <h3 style="font-size: 20px;">Không Có Đơn Chờ Chế Biến!</h3>
        <p style="color: var(--text-muted); font-size: 14px;">Tất cả các món ăn đã được hoàn thành hoặc chưa có order mới.</p>
      </div>
    `;
    return;
  }

  container.innerHTML = kdsOrders.map(card => {
    // Trạng thái thẻ: pending (chờ làm), cooking (đang làm), ready (đã xong)
    const cardStatusClass = `status-${card.card_status}`;
    const waitMins = card.wait_minutes || 0;
    const waitColor = waitMins > 20 ? '#ef4444' : (waitMins > 10 ? '#f59e0b' : '#10b981');

    return `
      <div class="kds-card ${cardStatusClass}" id="kdsCard-${card.order_id}">
        <!-- Header thẻ: Tên bàn + Thời gian đã chờ -->
        <div class="kds-card-header">
          <div>
            <div class="kds-table-title"><i class="fa-solid fa-chair text-warning"></i> ${card.table_name}</div>
            <div style="font-size: 11px; color: var(--text-muted);">${card.area_name} • ${card.order_code}</div>
          </div>
          <div class="kds-timer-badge" style="color: ${waitColor}; border: 1px solid ${waitColor};">
            <i class="fa-regular fa-clock"></i> Chờ ${waitMins} phút
          </div>
        </div>

        <!-- Danh sách món cần làm kèm ghi chú kỹ thuật -->
        <div class="kds-card-body">
          ${card.items.map(it => {
            const itemCooking = it.kitchen_status === 'cooking' ? 'item-cooking' : '';
            const itemReady = it.kitchen_status === 'ready' ? 'item-ready' : '';
            
            let statusIcon = '<i class="fa-regular fa-circle" style="color: var(--text-muted);"></i>';
            if (it.kitchen_status === 'cooking') {
              statusIcon = '<i class="fa-solid fa-fire text-warning"></i>';
            } else if (it.kitchen_status === 'ready') {
              statusIcon = '<i class="fa-solid fa-circle-check text-success"></i>';
            }

            return `
              <div class="kds-item-row ${itemCooking} ${itemReady}" onclick="toggleItemStatus(${it.id}, '${it.kitchen_status}')">
                <div style="flex: 1;">
                  <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 16px; font-weight: 800; color: var(--accent-gold);">${it.quantity}x</span>
                    <span style="font-weight: 700; font-size: 15px;">${it.item_name}</span>
                  </div>
                  ${it.notes ? `<div style="font-size: 12px; color: #f87171; margin-top: 3px; font-style: italic;"><i class="fa-solid fa-triangle-exclamation"></i> Ghi chú: ${it.notes}</div>` : ''}
                </div>
                <div style="font-size: 18px; margin-left: 10px;">
                  ${statusIcon}
                </div>
              </div>
            `;
          }).join('')}
        </div>

        <!-- Nút tương tác nhanh toàn thẻ (Đầu bếp chạm 1 lần: Đang làm, chạm lần 2: Đã xong) -->
        <div class="kds-card-actions">
          ${card.card_status === 'pending' ? `
            <button class="btn-primary" style="flex: 1; background: linear-gradient(135deg, #f59e0b, #d97706);" onclick="updateCardStatus(${card.order_id}, 'cooking')">
              <i class="fa-solid fa-fire"></i> BẮT ĐẦU NẤU (Chạm 1)
            </button>
          ` : (card.card_status === 'cooking' ? `
            <button class="btn-success" style="flex: 1;" onclick="updateCardStatus(${card.order_id}, 'ready')">
              <i class="fa-solid fa-bell"></i> XONG MÓN & BÁO PHỤC VỤ (Chạm 2)
            </button>
          ` : `
            <div style="flex: 1; text-align: center; color: #10b981; font-weight: bold; font-size: 13px; padding: 6px;">
              <i class="fa-solid fa-circle-check"></i> ĐÃ HOÀN THÀNH - ĐANG PHỤC VỤ
            </div>
          `)}
        </div>
      </div>
    `;
  }).join('');
}

// Chạm vào từng món đơn lẻ
async function toggleItemStatus(itemId, currentStatus) {
  let nextStatus = 'cooking';
  if (currentStatus === 'cooking') nextStatus = 'ready';
  else if (currentStatus === 'ready') nextStatus = 'pending';

  const formData = new FormData();
  formData.append('action', 'update_kds_status');
  formData.append('type', 'item');
  formData.append('target_id', itemId);
  formData.append('new_status', nextStatus);

  const res = await fetch('api.php', { method: 'POST', body: formData });
  const data = await res.json();
  if (data.success) {
    loadKdsOrders();
  }
}

// Chạm toàn thẻ
async function updateCardStatus(orderId, nextStatus) {
  const formData = new FormData();
  formData.append('action', 'update_kds_status');
  formData.append('type', 'card');
  formData.append('target_id', orderId);
  formData.append('new_status', nextStatus);

  const res = await fetch('api.php', { method: 'POST', body: formData });
  const data = await res.json();
  if (data.success) {
    if (nextStatus === 'ready') {
      Swal.fire({
        icon: 'success',
        title: 'Đã nấu xong!',
        text: 'Hệ thống đã gửi thông báo đến nhân viên phục vụ để lên đồ cho bàn!',
        timer: 1500,
        showConfirmButton: false,
        background: '#1e293b',
        color: '#fff'
      });
    }
    loadKdsOrders();
  }
}

// Tự động kiểm tra order mới mỗi 10 giây
setInterval(loadKdsOrders, 10000);
loadKdsOrders();
</script>
</body>
</html>
