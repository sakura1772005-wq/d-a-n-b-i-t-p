<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thanh Toán & Xuất Hóa Đơn - Hoàng Kim Restaurant</title>
  <link rel="stylesheet" href="assets/css/app.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>

<?php include __DIR__ . '/header.php'; ?>

<main class="app-container">
  <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
    <div>
      <h1 style="font-size: 22px; font-weight: 800;"><i class="fa-solid fa-receipt text-warning me-2"></i> THANH TOÁN & XUẤT HÓA ĐƠN</h1>
      <p style="font-size: 13px; color: var(--text-muted);">Tính toán tự động, chiết khấu voucher, tách hóa đơn và in hóa đơn VAT</p>
    </div>
    
    <!-- Quick Select Table for Checkout -->
    <div style="display: flex; align-items: center; gap: 10px;">
      <label class="form-label" style="margin-bottom: 0;">Chọn Bàn Thanh Toán:</label>
      <select id="checkoutTableSelect" class="form-control" style="width: 220px;" onchange="changeCheckoutTable(this.value)">
        <!-- Populated via JS -->
      </select>
    </div>
  </div>

  <div class="checkout-grid">
    
    <!-- BÊN TRÁI: XEM TRƯỚC HÓA ĐƠN IN (BILL PREVIEW) -->
    <section class="card-glass" style="padding: 24px;">
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
        <h3 style="font-size: 16px; font-weight: 700;"><i class="fa-solid fa-file-invoice text-primary me-2"></i> Phiếu Tạm Tính / Hóa Đơn</h3>
        <button class="btn-secondary" style="font-size: 12px; padding: 6px 14px;" onclick="openSplitBillModal()">
          <i class="fa-solid fa-scissors"></i> Tách Hóa Đơn (Split Bill)
        </button>
      </div>

      <!-- Khung giấy in Bill chuyên nghiệp -->
      <div class="bill-print-preview" id="printableBillArea">
        <div class="bill-header-print">
          <div style="font-size: 20px; font-weight: 900; letter-spacing: 1px;">HOÀNG KIM RESTAURANT</div>
          <div style="font-size: 12px; color: #4b5563;">Đ/C: 123 Đường Ẩm Thực, Quận 1, TP. Hồ Chí Minh</div>
          <div style="font-size: 12px; color: #4b5563;">Hotline: 1900 8888 • MST: 0312456789</div>
          <div style="font-size: 16px; font-weight: 700; margin-top: 10px; text-transform: uppercase;">PHIẾU THANH TOÁN</div>
          <div style="font-size: 12px; color: #6b7280;" id="billMetaInfo">Mã Đơn: -- | Bàn: -- | NV: --</div>
          <div style="font-size: 12px; color: #6b7280;" id="billDateInfo">Ngày: --</div>
        </div>

        <table class="bill-table-print">
          <thead>
            <tr>
              <th style="width: 45%;">Tên món</th>
              <th style="width: 15%; text-align: center;">SL</th>
              <th style="width: 20%; text-align: right;">Đơn giá</th>
              <th style="width: 20%; text-align: right;">Thành tiền</th>
            </tr>
          </thead>
          <tbody id="billItemsTableBody">
            <!-- Items rendered -->
          </tbody>
        </table>

        <!-- Bảng tính toán tự động -->
        <div style="border-top: 1px dashed #9ca3af; padding-top: 12px; font-size: 13px;">
          <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
            <span>Tổng tạm tính:</span>
            <span id="billSubtotalText" style="font-weight: 700;">0 đ</span>
          </div>
          <div style="display: flex; justify-content: space-between; margin-bottom: 6px; color: #b91c1c;">
            <span>Giảm giá / Voucher (<span id="billDiscountPercent">0%</span>):</span>
            <span id="billDiscountAmountText">- 0 đ</span>
          </div>
          <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
            <span>Thuế VAT (<span id="billVatPercent">8%</span>):</span>
            <span id="billVatAmountText">0 đ</span>
          </div>
          <div style="display: flex; justify-content: space-between; margin-top: 10px; padding-top: 8px; border-top: 2px solid #111827; font-size: 18px; font-weight: 900;">
            <span>TỔNG CỘNG:</span>
            <span id="billTotalAmountText" style="color: #b45309;">0 đ</span>
          </div>
        </div>

        <div style="text-align: center; margin-top: 25px; font-size: 12px; color: #6b7280; font-style: italic;">
          Cảm ơn Quý khách và hẹn gặp lại!<br>
          Wifi: HoangKim_Guest • Pass: hoangkim888
        </div>
      </div>
    </section>

    <!-- BÊN PHẢI: KHU VỰC KHUYẾN MÃI & GIAO DỊCH THANH TOÁN -->
    <section style="display: flex; flex-direction: column; gap: 20px;">
      
      <!-- Box Khuyến Mãi / Voucher -->
      <div class="card-glass" style="padding: 20px;">
        <h3 style="font-size: 15px; font-weight: 700; margin-bottom: 12px;"><i class="fa-solid fa-ticket text-warning me-2"></i> Khuyến Mãi & Giảm Giá</h3>
        <div style="display: flex; gap: 10px;">
          <input type="text" id="voucherInput" class="form-control" placeholder="Nhập mã voucher (vd: VIP10, VIP20, BIG50)">
          <button class="btn-primary" onclick="applyVoucher()" style="white-space: nowrap;">
            <i class="fa-solid fa-tag"></i> Áp Dụng
          </button>
        </div>
        <div style="display: flex; gap: 8px; margin-top: 10px;">
          <button class="role-chip" onclick="quickApplyVoucher('VIP10')">Mã VIP10 (-10%)</button>
          <button class="role-chip" onclick="quickApplyVoucher('VIP20')">Mã VIP20 (-20%)</button>
          <button class="role-chip" onclick="quickApplyVoucher('BIG50')">Mã BIG50 (-50%)</button>
        </div>
      </div>

      <!-- Box Giao Dịch & Tiền Khách Đưa -->
      <div class="card-glass" style="padding: 24px; flex: 1;">
        <h3 style="font-size: 15px; font-weight: 700; margin-bottom: 16px;"><i class="fa-solid fa-credit-card text-success me-2"></i> Phương Thức & Giao Dịch</h3>

        <!-- Phương thức thanh toán -->
        <div class="form-group">
          <label class="form-label">Chọn Hình Thức Thanh Toán:</label>
          <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">
            <label style="background: rgba(15, 23, 42, 0.6); padding: 12px; border-radius: var(--radius-sm); border: 1px solid var(--dark-border); display: flex; align-items: center; gap: 8px; cursor: pointer;">
              <input type="radio" name="paymentMethod" value="cash" checked onchange="handlePaymentMethodChange(this.value)">
              <span><i class="fa-solid fa-money-bill-wave text-success"></i> Tiền mặt</span>
            </label>
            <label style="background: rgba(15, 23, 42, 0.6); padding: 12px; border-radius: var(--radius-sm); border: 1px solid var(--dark-border); display: flex; align-items: center; gap: 8px; cursor: pointer;">
              <input type="radio" name="paymentMethod" value="transfer" onchange="handlePaymentMethodChange(this.value)">
              <span><i class="fa-solid fa-qrcode text-primary"></i> Chuyển khoản</span>
            </label>
            <label style="background: rgba(15, 23, 42, 0.6); padding: 12px; border-radius: var(--radius-sm); border: 1px solid var(--dark-border); display: flex; align-items: center; gap: 8px; cursor: pointer;">
              <input type="radio" name="paymentMethod" value="card" onchange="handlePaymentMethodChange(this.value)">
              <span><i class="fa-solid fa-credit-card text-warning"></i> Thẻ POS</span>
            </label>
          </div>
        </div>

        <!-- Khung quét mã QR BIDV VietQR khi chọn chuyển khoản -->
        <div id="transferQrBox" style="display: none; background: linear-gradient(135deg, rgba(15, 23, 42, 0.9), rgba(30, 41, 59, 0.8)); border: 1px solid rgba(16, 185, 129, 0.35); border-radius: var(--radius-md); padding: 16px; margin-bottom: 20px; text-align: center; box-shadow: 0 8px 24px rgba(0,0,0,0.3);">
          <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid rgba(255,255,255,0.08);">
            <div style="font-weight: 700; color: #10b981; font-size: 14px; display: flex; align-items: center; gap: 8px;">
              <i class="fa-solid fa-qrcode"></i> QUÉT MÃ VIETQR - BIDV
            </div>
            <button type="button" class="btn-secondary" style="padding: 4px 10px; font-size: 11px;" onclick="openQrPopup()">
              <i class="fa-solid fa-expand"></i> Phóng to
            </button>
          </div>
          <div style="display: flex; gap: 16px; align-items: center; justify-content: center; flex-wrap: wrap;">
            <div style="background: #ffffff; padding: 8px; border-radius: 12px; display: inline-block; box-shadow: 0 4px 12px rgba(0,0,0,0.25);">
              <img id="qrPreviewImg" src="assets/img/qr_bidv.png" alt="QR BIDV" style="width: 170px; height: auto; border-radius: 8px; display: block; object-fit: contain;">
            </div>
            <div style="text-align: left; font-size: 13px; line-height: 1.7; min-width: 200px;">
              <div style="color: #94a3b8; font-size: 12px;">Ngân hàng thụ hưởng:</div>
              <div style="font-weight: 800; color: #38bdf8; font-size: 14px;">BIDV (Ngân hàng TMCP Đầu tư & Phát triển VN)</div>
              <div style="color: #94a3b8; font-size: 12px; margin-top: 4px;">Chủ tài khoản:</div>
              <div style="font-weight: 800; color: #ffffff; font-size: 14px; letter-spacing: 0.5px;">NGUYEN HUY HOANG</div>
              <div style="color: #94a3b8; font-size: 12px; margin-top: 4px;">Số tài khoản:</div>
              <div style="font-weight: 800; color: #fbbf24; font-size: 16px; letter-spacing: 1px;">8864529843</div>
              <div style="color: #94a3b8; font-size: 12px; margin-top: 4px;">Số tiền thanh toán:</div>
              <div id="qrPayAmount" style="font-weight: 800; color: #10b981; font-size: 18px;">0 đ</div>
            </div>
          </div>
        </div>

        <!-- Tiền khách đưa & tiền thối -->
        <div style="background: rgba(15, 23, 42, 0.5); padding: 16px; border-radius: var(--radius-sm); border: 1px solid var(--dark-border); margin-bottom: 20px;">
          <div class="form-group">
            <label class="form-label" style="display: flex; justify-content: space-between;">
              <span>Số tiền khách đưa:</span>
              <span id="quickPayTags" style="cursor: pointer; color: var(--primary); font-size: 11px;">Khách đưa vừa đủ</span>
            </label>
            <input type="number" id="customerPaidInput" class="form-control" style="font-size: 20px; font-weight: 800; color: #10b981;" placeholder="0" oninput="calculateChange()">
          </div>

          <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 10px; border-top: 1px dashed var(--dark-border);">
            <span style="font-size: 14px; font-weight: 700; color: var(--text-sub);">Tiền thối lại cho khách:</span>
            <span id="changeAmountText" style="font-size: 22px; font-weight: 800; color: #fbbf24;">0 đ</span>
          </div>
        </div>

        <!-- Hành động nút bấm -->
        <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 12px;">
          <button class="btn-secondary" style="padding: 14px; font-weight: 700;" onclick="printBill()">
            <i class="fa-solid fa-print"></i> In Tạm Tính
          </button>
          <button class="btn-primary" style="padding: 14px; font-size: 15px; font-weight: 800; background: linear-gradient(135deg, #10b981, #059669);" onclick="processCheckout()">
            <i class="fa-solid fa-circle-check"></i> XÁC NHẬN & IN HÓA ĐƠN
          </button>
        </div>
      </div>

    </section>

  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const urlParams = new URLSearchParams(window.location.search);
let currentTableId = parseInt(urlParams.get('table_id') || 1);

let checkoutOrder = null;
let checkoutItems = [];

async function loadCheckoutData() {
  try {
    // Nạp dropdown bàn
    const tRes = await fetch('api.php?action=get_tables_overview');
    const tData = await tRes.json();
    if (tData.success) {
      const select = document.getElementById('checkoutTableSelect');
      const occupiedTables = tData.tables.filter(t => t.status === 'occupied');
      
      select.innerHTML = occupiedTables.map(t => `
        <option value="${t.id}" ${t.id == currentTableId ? 'selected' : ''}>
          ${t.name} (${t.area_name}) - ${parseInt(t.total_amount || 0).toLocaleString('vi-VN')} đ
        </option>
      `).join('');

      if (occupiedTables.length > 0 && !occupiedTables.some(t => t.id == currentTableId)) {
        currentTableId = occupiedTables[0].id;
      }
    }

    // Nạp dữ liệu hóa đơn của bàn
    const res = await fetch(`api.php?action=get_checkout_data&table_id=${currentTableId}`);
    const data = await res.json();
    if (!data.success) {
      document.getElementById('billItemsTableBody').innerHTML = `<tr><td colspan="4" style="text-align: center; padding: 20px;">Bàn này hiện chưa có món gọi hoặc đã thanh toán!</td></tr>`;
      return;
    }

    checkoutOrder = data.order;
    checkoutItems = data.items;
    renderBillPreview();
  } catch (err) {
    console.error('Error loading checkout:', err);
  }
}

function changeCheckoutTable(tblId) {
  currentTableId = tblId;
  window.history.pushState({}, '', `checkout.php?table_id=${tblId}`);
  loadCheckoutData();
}

function renderBillPreview() {
  if (!checkoutOrder) return;

  document.getElementById('billMetaInfo').innerText = `Mã: ${checkoutOrder.order_code} | ${checkoutOrder.table_name} | NV: ${checkoutOrder.staff_name || 'Lễ tân'}`;
  document.getElementById('billDateInfo').innerText = `Giờ vào: ${checkoutOrder.opened_at} | Khách: ${checkoutOrder.customer_name || 'Khách lẻ'}`;

  const tbody = document.getElementById('billItemsTableBody');
  tbody.innerHTML = checkoutItems.map(it => {
    const total = it.price * it.quantity;
    return `
      <tr>
        <td><b>${it.item_name}</b></td>
        <td style="text-align: center;">${it.quantity}</td>
        <td style="text-align: right;">${parseInt(it.price).toLocaleString('vi-VN')}</td>
        <td style="text-align: right; font-weight: 700;">${parseInt(total).toLocaleString('vi-VN')}</td>
      </tr>
    `;
  }).join('');

  document.getElementById('billSubtotalText').innerText = parseInt(checkoutOrder.subtotal).toLocaleString('vi-VN') + ' đ';
  document.getElementById('billDiscountPercent').innerText = `${checkoutOrder.discount_percent}%`;
  document.getElementById('billDiscountAmountText').innerText = `- ${parseInt(checkoutOrder.discount_amount).toLocaleString('vi-VN')} đ`;
  document.getElementById('billVatPercent').innerText = `${checkoutOrder.vat_percent}%`;
  document.getElementById('billVatAmountText').innerText = `+ ${parseInt(checkoutOrder.vat_amount).toLocaleString('vi-VN')} đ`;
  document.getElementById('billTotalAmountText').innerText = parseInt(checkoutOrder.total_amount).toLocaleString('vi-VN') + ' đ';

  const qrAmountEl = document.getElementById('qrPayAmount');
  if (qrAmountEl) {
    qrAmountEl.innerText = parseInt(checkoutOrder.total_amount).toLocaleString('vi-VN') + ' đ';
  }

  // Thiết lập mặc định tiền khách đưa
  document.getElementById('customerPaidInput').value = checkoutOrder.total_amount;
  document.getElementById('quickPayTags').onclick = () => {
    document.getElementById('customerPaidInput').value = checkoutOrder.total_amount;
    calculateChange();
  };
  calculateChange();
}

function calculateChange() {
  if (!checkoutOrder) return;
  const paid = parseFloat(document.getElementById('customerPaidInput').value || 0);
  const total = parseFloat(checkoutOrder.total_amount || 0);
  const change = Math.max(0, paid - total);
  document.getElementById('changeAmountText').innerText = parseInt(change).toLocaleString('vi-VN') + ' đ';
}

function quickApplyVoucher(code) {
  document.getElementById('voucherInput').value = code;
  applyVoucher();
}

async function applyVoucher() {
  if (!checkoutOrder) return;
  const code = document.getElementById('voucherInput').value.trim();

  const formData = new FormData();
  formData.append('action', 'apply_voucher');
  formData.append('order_id', checkoutOrder.id);
  formData.append('voucher_code', code);

  const res = await fetch('api.php', { method: 'POST', body: formData });
  const data = await res.json();
  if (data.success) {
    Swal.fire({
      icon: 'success',
      title: 'Đã áp dụng Voucher!',
      text: data.message,
      background: '#1e293b',
      color: '#fff'
    });
    loadCheckoutData();
  } else {
    Swal.fire('Lỗi Voucher', data.message, 'error');
  }
}

function handlePaymentMethodChange(val) {
  const qrBox = document.getElementById('transferQrBox');
  if (val === 'transfer') {
    if (qrBox) {
      qrBox.style.display = 'block';
      const total = checkoutOrder ? parseInt(checkoutOrder.total_amount).toLocaleString('vi-VN') : '0';
      const elAmount = document.getElementById('qrPayAmount');
      if (elAmount) elAmount.innerText = `${total} đ`;
    }
    openQrPopup();
  } else {
    if (qrBox) {
      qrBox.style.display = 'none';
    }
  }
}

function openQrPopup() {
  const totalStr = checkoutOrder ? parseInt(checkoutOrder.total_amount).toLocaleString('vi-VN') : '0';
  const orderCode = checkoutOrder ? checkoutOrder.order_code : 'BILL';

  Swal.fire({
    title: '<span style="color: #10b981; font-size: 20px;"><i class="fa-solid fa-qrcode"></i> Thanh Toán VietQR</span>',
    html: `
      <div style="text-align: center; color: #f1f5f9;">
        <div style="background: #ffffff; padding: 12px; border-radius: 16px; display: inline-block; margin-bottom: 14px; box-shadow: 0 8px 24px rgba(0,0,0,0.35);">
          <img src="assets/img/qr_bidv.png" alt="BIDV VietQR" style="width: 260px; max-width: 100%; height: auto; border-radius: 10px; display: block;">
        </div>
        <div style="background: rgba(15, 23, 42, 0.7); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 10px; padding: 12px; margin-top: 6px; text-align: left; font-size: 13px; line-height: 1.8;">
          <div style="display: flex; justify-content: space-between;"><span style="color: #94a3b8;">Ngân hàng:</span> <b style="color: #38bdf8;">BIDV (Ngân hàng TMCP Đầu tư & Phát triển Việt Nam)</b></div>
          <div style="display: flex; justify-content: space-between;"><span style="color: #94a3b8;">Chủ tài khoản:</span> <b style="color: #ffffff;">NGUYEN HUY HOANG</b></div>
          <div style="display: flex; justify-content: space-between;"><span style="color: #94a3b8;">Số tài khoản:</span> <b style="color: #fbbf24; font-size: 15px; letter-spacing: 1px;">8864529843</b></div>
          <div style="display: flex; justify-content: space-between; border-top: 1px dashed rgba(255,255,255,0.1); padding-top: 6px; margin-top: 4px;">
            <span style="color: #94a3b8;">Số tiền cần trả:</span> <b style="color: #10b981; font-size: 16px;">${totalStr} đ</b>
          </div>
          <div style="display: flex; justify-content: space-between;"><span style="color: #94a3b8;">Nội dung CK:</span> <b style="color: #e2e8f0;">${orderCode}</b></div>
        </div>
      </div>
    `,
    showCancelButton: true,
    confirmButtonText: '<i class="fa-solid fa-circle-check"></i> Đã Nhận Chuyển Khoản',
    cancelButtonText: 'Đóng',
    background: '#1e293b',
    color: '#fff',
    width: '450px'
  });
}

function printBill() {
  const printContents = document.getElementById('printableBillArea').innerHTML;
  const win = window.open('', '', 'height=700,width=800');
  win.document.write('<html><head><title>In Phiếu Tạm Tính</title>');
  win.document.write('<style>body{font-family: monospace; padding: 20px;} table{width:100%; border-collapse:collapse;} th,td{padding:6px; text-align:left;}</style>');
  win.document.write('</head><body>');
  win.document.write(printContents);
  win.document.write('</body></html>');
  win.document.close();
  win.print();
}

function openSplitBillModal() {
  Swal.fire({
    title: 'Tách Hóa Đơn (Split Bill)',
    html: `
      <div style="text-align: left; font-size: 13px;">
        <p>Chọn phương thức tách hóa đơn cho bàn này:</p>
        <div style="margin: 10px 0;">
          <label style="display:block; margin-bottom: 8px;"><input type="radio" name="split_opt" value="equal" checked> Chia đều theo số người (${checkoutOrder ? checkoutOrder.guest_count : 2} người)</label>
          <label style="display:block;"><input type="radio" name="split_opt" value="items"> Tách theo món ăn riêng lẻ</label>
        </div>
        <div style="background: rgba(255,255,255,0.05); padding: 10px; border-radius: 6px;">
          Mỗi người cần thanh toán: <b style="color: #fbbf24;">${checkoutOrder ? parseInt(checkoutOrder.total_amount / (checkoutOrder.guest_count || 1)).toLocaleString('vi-VN') : 0} đ</b>
        </div>
      </div>
    `,
    showCancelButton: true,
    confirmButtonText: 'Xác Nhận Tách',
    cancelButtonText: 'Đóng',
    background: '#1e293b',
    color: '#fff'
  });
}

async function processCheckout() {
  if (!checkoutOrder) {
    Swal.fire('Lỗi', 'Không có hóa đơn để thanh toán!', 'error');
    return;
  }

  const paid = parseFloat(document.getElementById('customerPaidInput').value || 0);
  const total = parseFloat(checkoutOrder.total_amount || 0);
  const method = document.querySelector('input[name="paymentMethod"]:checked').value;

  if (paid < total && method === 'cash') {
    Swal.fire('Cảnh báo', 'Số tiền khách đưa chưa đủ để thanh toán hóa đơn!', 'warning');
    return;
  }

  const change = Math.max(0, paid - total);

  const formData = new FormData();
  formData.append('action', 'process_payment');
  formData.append('order_id', checkoutOrder.id);
  formData.append('payment_method', method);
  formData.append('customer_paid', paid);
  formData.append('change_amount', change);
  formData.append('discount_percent', checkoutOrder.discount_percent || 0);

  const res = await fetch('api.php', { method: 'POST', body: formData });
  const data = await res.json();

  if (data.success) {
    Swal.fire({
      icon: 'success',
      title: 'Thanh toán thành công!',
      text: 'Đã hoàn tất hóa đơn và giải phóng bàn về trạng thái [TRỐNG]!',
      background: '#1e293b',
      color: '#fff'
    }).then(() => {
      window.location.href = 'tables.php';
    });
  } else {
    Swal.fire('Lỗi', data.message, 'error');
  }
}

loadCheckoutData();
</script>
</body>
</html>
