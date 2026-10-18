<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đặt Bàn Trực Tuyến - Nhà Hàng Hoàng Kim</title>
  <link rel="stylesheet" href="assets/css/app.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body style="background: #0f172a;">

<!-- Top Bar Công Khai Cho Khách -->
<header style="background: rgba(15, 23, 42, 0.95); border-bottom: 1px solid var(--dark-border); padding: 16px 30px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 50;">
  <div style="display: flex; align-items: center; gap: 14px;">
    <div style="width: 44px; height: 44px; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; color: #fff;">
      <i class="fa-solid fa-crown"></i>
    </div>
    <div>
      <div style="font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 700; color: #fff;">HOÀNG KIM RESTAURANT</div>
      <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase;">Ẩm Thực Hoàng Gia & Đẳng Cấp Á Âu</div>
    </div>
  </div>

  <div style="display: flex; align-items: center; gap: 16px;">
    <a href="login.php" class="btn-secondary" style="font-size: 13px; text-decoration: none;">
      <i class="fa-solid fa-lock"></i> Đăng Nhập Nhân Viên
    </a>
  </div>
</header>

<!-- Banner Hero & Giới thiệu ngắn -->
<section class="booking-hero">
  <div style="max-width: 800px; margin: 0 auto;">
    <span style="background: rgba(245, 158, 11, 0.2); color: #fbbf24; padding: 6px 16px; border-radius: 50px; font-size: 13px; font-weight: 700; text-transform: uppercase; border: 1px solid rgba(245, 158, 11, 0.4);">
      Trải Nghiệm Ẩm Thực Tinh Hoa
    </span>
    <h1 style="font-family: 'Playfair Display', serif; font-size: 44px; font-weight: 800; margin: 18px 0 12px; color: #fff;">
      ĐẶT BÀN TRỰC TUYẾN
    </h1>
    <p style="font-size: 16px; color: #cbd5e1; line-height: 1.6;">
      Chào mừng quý khách đến với Nhà Hàng Hoàng Kim. Để đảm bảo không gian tiếp đón chu đáo và trải nghiệm trọn vẹn nhất, quý khách vui lòng gửi thông tin đặt bàn dưới đây.
    </p>

    <div style="display: flex; justify-content: center; gap: 24px; margin-top: 24px; font-size: 13px; color: var(--text-sub);">
      <div><i class="fa-regular fa-clock text-warning"></i> Giờ mở cửa: <b>10:00 - 23:00 hàng ngày</b></div>
      <div><i class="fa-solid fa-phone text-warning"></i> Hotline hỗ trợ: <b>1900 8888</b></div>
    </div>
  </div>
</section>

<!-- Hình ảnh giới thiệu các khu vực -->
<div class="app-container" style="max-width: 1000px; margin-top: 30px;">
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-bottom: 30px;">
    <div style="position: relative; border-radius: 12px; overflow: hidden; height: 160px; border: 1px solid var(--dark-border);">
      <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=600&auto=format&fit=crop&q=60" style="width: 100%; height: 100%; object-fit: cover;">
      <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.85), transparent); display: flex; flex-direction: column; justify-content: flex-end; padding: 14px;">
        <div style="font-weight: 700; color: #fff;">Sảnh Chính (Tầng 1)</div>
        <div style="font-size: 12px; color: var(--text-muted);">Không gian mở, ấm cúng và sang trọng</div>
      </div>
    </div>

    <div style="position: relative; border-radius: 12px; overflow: hidden; height: 160px; border: 1px solid var(--dark-border);">
      <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=600&auto=format&fit=crop&q=60" style="width: 100%; height: 100%; object-fit: cover;">
      <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.85), transparent); display: flex; flex-direction: column; justify-content: flex-end; padding: 14px;">
        <div style="font-weight: 700; color: #fff;">Tầng 2 - Không Gian Thoáng</div>
        <div style="font-size: 12px; color: var(--text-muted);">View ngắm phố thơ mộng, lãng mạn</div>
      </div>
    </div>

    <div style="position: relative; border-radius: 12px; overflow: hidden; height: 160px; border: 1px solid var(--dark-border);">
      <img src="https://images.unsplash.com/photo-1550966871-3ed3cdb5ed0c?w=600&auto=format&fit=crop&q=60" style="width: 100%; height: 100%; object-fit: cover;">
      <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.85), transparent); display: flex; flex-direction: column; justify-content: flex-end; padding: 14px;">
        <div style="font-weight: 700; color: #fff;">Phòng VIP Hoàng Gia</div>
        <div style="font-size: 12px; color: var(--text-muted);">Riêng tư tuyệt đối cho tiệc đối tác, gia đình</div>
      </div>
    </div>
  </div>

  <!-- Form Nhập Liệu Đặt Bàn -->
  <div class="booking-form-box">
    <div style="text-align: center; margin-bottom: 24px;">
      <h2 style="font-size: 20px; font-weight: 800; color: #fff;"><i class="fa-solid fa-calendar-check text-warning me-2"></i> THÔNG TIN ĐẶT BÀN</h2>
      <p style="font-size: 13px; color: var(--text-muted);">Vui lòng điền đầy đủ các mục để nhà hàng liên hệ xác nhận</p>
    </div>

    <form id="publicBookingForm" onsubmit="submitBooking(event)">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div class="form-group">
          <label class="form-label"><i class="fa-solid fa-user me-1 text-warning"></i> Tên khách hàng *</label>
          <input type="text" id="custName" class="form-control" placeholder="Họ và tên của quý khách" required>
        </div>

        <div class="form-group">
          <label class="form-label"><i class="fa-solid fa-phone me-1 text-warning"></i> Số điện thoại liên hệ *</label>
          <input type="tel" id="custPhone" class="form-control" placeholder="VD: 0912345678" required>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div class="form-group">
          <label class="form-label"><i class="fa-regular fa-clock me-1 text-warning"></i> Ngày và Giờ đến *</label>
          <input type="datetime-local" id="custDateTime" class="form-control" required>
        </div>

        <div class="form-group">
          <label class="form-label"><i class="fa-solid fa-users me-1 text-warning"></i> Số lượng người *</label>
          <select id="custGuestCount" class="form-control">
            <option value="1">1 Người (Khách lẻ)</option>
            <option value="2" selected>2 Người (Cặp đôi)</option>
            <option value="4">4 Người (Gia đình nhỏ)</option>
            <option value="6">6 Người (Nhóm bạn)</option>
            <option value="8">8 Người</option>
            <option value="10">10 Người trở lên (Tiệc)</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label"><i class="fa-solid fa-location-dot me-1 text-warning"></i> Khu vực mong muốn</label>
        <select id="custArea" class="form-control">
          <option value="Sảnh chính (Tầng 1)">Sảnh chính (Tầng 1 - Không gian mở)</option>
          <option value="Tầng 2 - Không Gian Thoáng">Tầng 2 - Không Gian Thoáng (Ban công view phố)</option>
          <option value="Phòng VIP Hoàng Gia">Phòng VIP Hoàng Gia (Riêng tư, cách âm)</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label"><i class="fa-solid fa-comment-dots me-1 text-warning"></i> Ghi chú / Yêu cầu đặc biệt</label>
        <textarea id="custNotes" class="form-control" rows="3" placeholder="Ví dụ: Ghế trẻ em, trang trí sinh nhật, dị ứng thức ăn hoặc cần chuẩn bị trước rượu vang..."></textarea>
      </div>

      <button type="submit" class="btn-primary" style="width: 100%; padding: 15px; font-size: 16px; font-weight: 800; margin-top: 10px;">
        <i class="fa-solid fa-paper-plane"></i> GỬI YÊU CẦU ĐẶT BÀN
      </button>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Thiết lập mặc định ngày giờ đến là 2 tiếng sau
const now = new Date();
now.setHours(now.getHours() + 2);
now.setMinutes(0);
const isoStr = new Date(now.getTime() - (now.getTimezoneOffset() * 60000)).toISOString().slice(0, 16);
document.getElementById('custDateTime').value = isoStr;

async function submitBooking(e) {
  e.preventDefault();
  const name = document.getElementById('custName').value.trim();
  const phone = document.getElementById('custPhone').value.trim();
  const dt = document.getElementById('custDateTime').value;
  const guests = document.getElementById('custGuestCount').value;
  const area = document.getElementById('custArea').value;
  const notes = document.getElementById('custNotes').value.trim();

  const formData = new FormData();
  formData.append('action', 'submit_booking');
  formData.append('customer_name', name);
  formData.append('phone', phone);
  formData.append('reservation_time', dt);
  formData.append('guest_count', guests);
  formData.append('preferred_area', area);
  formData.append('special_notes', notes);

  try {
    const res = await fetch('api.php', { method: 'POST', body: formData });
    const data = await res.json();

    if (data.success) {
      Swal.fire({
        icon: 'success',
        title: 'Gửi yêu cầu thành công!',
        html: `
          <div style="text-align: center;">
            <p style="font-size: 15px; color: #10b981; font-weight: bold;">Mã phiếu: ${data.reservation_code}</p>
            <p style="margin-top: 10px; font-size: 14px;">${data.message}</p>
          </div>
        `,
        confirmButtonText: 'Đã Hiểu',
        background: '#1e293b',
        color: '#fff'
      }).then(() => {
        document.getElementById('publicBookingForm').reset();
      });
    } else {
      Swal.fire('Lỗi', data.message, 'error');
    }
  } catch (err) {
    Swal.fire('Lỗi kết nối', 'Không thể gửi yêu cầu đặt bàn!', 'error');
  }
}
</script>
</body>
</html>
