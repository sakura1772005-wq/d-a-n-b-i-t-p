-- Tạo cơ sở dữ liệu cho Hệ Thống Quản Lý Nhà Hàng
CREATE DATABASE IF NOT EXISTS `restaurant_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `restaurant_db`;

-- 1. Bảng Nhân viên & Phân quyền
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `role` ENUM('admin', 'reception', 'waiter', 'cashier', 'kitchen') NOT NULL DEFAULT 'waiter',
  `sub_role` VARCHAR(50) DEFAULT 'Nhân viên',
  `avatar` VARCHAR(255) DEFAULT '',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Bảng Khu vực (Khu vực / Tầng)
CREATE TABLE IF NOT EXISTS `areas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `display_order` INT DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Bảng Bàn ăn
CREATE TABLE IF NOT EXISTS `tables` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `table_code` VARCHAR(50) NOT NULL UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `area_id` INT NOT NULL,
  `capacity` INT DEFAULT 4,
  `shape` ENUM('square', 'round', 'rectangle') DEFAULT 'square',
  `status` ENUM('empty', 'occupied', 'reserved', 'maintenance') DEFAULT 'empty',
  `current_order_id` INT DEFAULT NULL,
  `occupied_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`area_id`) REFERENCES `areas`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Bảng Danh mục món ăn
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `icon` VARCHAR(50) DEFAULT 'utensils',
  `display_order` INT DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Bảng Món ăn (Menu Items)
CREATE TABLE IF NOT EXISTS `menu_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `price` DECIMAL(12, 2) NOT NULL DEFAULT 0,
  `image` VARCHAR(255) DEFAULT '',
  `status` ENUM('available', 'out_of_stock') DEFAULT 'available',
  `unit` VARCHAR(50) DEFAULT 'Phần',
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Bảng Đơn gọi món / Hóa đơn (Orders)
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_code` VARCHAR(50) NOT NULL UNIQUE,
  `table_id` INT NOT NULL,
  `staff_id` INT DEFAULT NULL,
  `customer_name` VARCHAR(100) DEFAULT '',
  `guest_count` INT DEFAULT 1,
  `status` ENUM('open', 'paid', 'cancelled') DEFAULT 'open',
  `subtotal` DECIMAL(12, 2) DEFAULT 0,
  `discount_percent` DECIMAL(5, 2) DEFAULT 0,
  `discount_amount` DECIMAL(12, 2) DEFAULT 0,
  `voucher_code` VARCHAR(50) DEFAULT '',
  `vat_percent` DECIMAL(5, 2) DEFAULT 8.0,
  `vat_amount` DECIMAL(12, 2) DEFAULT 0,
  `total_amount` DECIMAL(12, 2) DEFAULT 0,
  `customer_paid` DECIMAL(12, 2) DEFAULT 0,
  `change_amount` DECIMAL(12, 2) DEFAULT 0,
  `payment_method` ENUM('cash', 'transfer', 'card') DEFAULT 'cash',
  `opened_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `paid_at` DATETIME DEFAULT NULL,
  FOREIGN KEY (`table_id`) REFERENCES `tables`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Bảng Chi tiết món gọi (Order Items)
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `menu_item_id` INT NOT NULL,
  `item_name` VARCHAR(150) NOT NULL,
  `price` DECIMAL(12, 2) NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `notes` VARCHAR(255) DEFAULT '',
  `kitchen_status` ENUM('pending', 'cooking', 'ready', 'served') DEFAULT 'pending',
  `sent_kitchen_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `ready_at` DATETIME DEFAULT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Bảng Phiếu đặt bàn (Reservations)
CREATE TABLE IF NOT EXISTS `reservations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `reservation_code` VARCHAR(50) NOT NULL UNIQUE,
  `customer_name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `reservation_time` DATETIME NOT NULL,
  `guest_count` INT NOT NULL DEFAULT 2,
  `preferred_area` VARCHAR(100) DEFAULT 'Sảnh chính',
  `special_notes` TEXT,
  `status` ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
  `assigned_table_id` INT DEFAULT NULL,
  `confirmed_by` INT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`assigned_table_id`) REFERENCES `tables`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- DỮ LIỆU MẪU (SEED DATA)
-- Users: admin / staff / kitchen (Mật khẩu mặc định: 123456 hoặc demo md5/hash)
INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `sub_role`, `avatar`) VALUES
-- 1. Quản lý / Chủ nhà hàng (Admin/Manager)
(1, 'admin1', '123456', 'Nguyễn Huy Hoàng (Admin)', 'admin', 'Quản lý nhà hàng', 'assets/images/avatar1.png'),
(2, 'admin2', '123456', 'Trần Đình Trọng (Admin)', 'admin', 'Phó quản lý điều hành', 'assets/images/avatar1.png'),
(3, 'admin3', '123456', 'Vũ Thị Minh Anh (Admin)', 'admin', 'Giám sát vận hành chuỗi', 'assets/images/avatar1.png'),
-- 2. Bộ phận Lễ tân (Receptionist)
(4, 'letan1', '123456', 'Trần Lễ Tân', 'reception', 'Lễ tân / Tiếp đón', 'assets/images/avatar2.png'),
(5, 'letan2', '123456', 'Đỗ Thanh Hương', 'reception', 'Lễ tân ca tối', 'assets/images/avatar2.png'),
-- 3. Bộ phận Thu ngân (Cashier)
(6, 'thungan1', '123456', 'Lê Thu Ngân', 'cashier', 'Thu ngân chính', 'assets/images/avatar3.png'),
(7, 'thungan2', '123456', 'Bùi Lan Phương', 'cashier', 'Thu ngân ca tối', 'assets/images/avatar3.png'),
-- 4. Bộ phận Phục vụ (Waiter)
(8, 'phucvu1', '123456', ' Phục Vụ', 'waiter', 'Phục vụ bàn sảnh chính', 'assets/images/avatar4.png'),
(9, 'phucvu2', '123456', 'Nguyễn Văn Nam', 'waiter', 'Phục vụ bàn VIP', 'assets/images/avatar4.png'),
(10, 'phucvu3', '123456', 'Đinh Tuấn Kiệt', 'waiter', 'Phục vụ bàn tầng 2', 'assets/images/avatar4.png'),
-- 5. Bộ phận Bếp / Pha chế (Kitchen/Bar)
(11, 'bep1', '123456', 'Phạm Bếp Trưởng', 'kitchen', 'Bếp trưởng', 'assets/images/avatar5.png'),
(12, 'bep2', '123456', 'Lê Minh Khang', 'kitchen', 'Bếp phó / Bếp nóng', 'assets/images/avatar5.png')
ON DUPLICATE KEY UPDATE `username`=`username`;

-- Areas
INSERT INTO `areas` (`id`, `name`, `code`, `display_order`) VALUES
(1, 'Tầng 1 - Sảnh Chính', 'tang_1', 1),
(2, 'Tầng 2 - Không Gian Thoáng', 'tang_2', 2),
(3, 'Phòng VIP Hoàng Gia', 'phong_vip', 3)
ON DUPLICATE KEY UPDATE `name`=`name`;

-- Tables
INSERT INTO `tables` (`id`, `table_code`, `name`, `area_id`, `capacity`, `shape`, `status`, `occupied_at`) VALUES
-- Tầng 1
(1, 'TB-101', 'Bàn 101', 1, 4, 'square', 'occupied', DATE_SUB(NOW(), INTERVAL 45 MINUTE)),
(2, 'TB-102', 'Bàn 102', 1, 4, 'square', 'empty', NULL),
(3, 'TB-103', 'Bàn 103', 1, 6, 'round', 'reserved', NULL),
(4, 'TB-104', 'Bàn 104', 1, 2, 'square', 'empty', NULL),
(5, 'TB-105', 'Bàn 105', 1, 8, 'rectangle', 'occupied', DATE_SUB(NOW(), INTERVAL 75 MINUTE)),
(6, 'TB-106', 'Bàn 106', 1, 4, 'square', 'maintenance', NULL),
-- Tầng 2
(7, 'TB-201', 'Bàn 201', 2, 4, 'square', 'empty', NULL),
(8, 'TB-202', 'Bàn 202', 2, 6, 'round', 'occupied', DATE_SUB(NOW(), INTERVAL 25 MINUTE)),
(9, 'TB-203', 'Bàn 203', 2, 4, 'square', 'empty', NULL),
(10, 'TB-204', 'Bàn 204', 2, 8, 'rectangle', 'reserved', NULL),
(11, 'TB-205', 'Bàn 205', 2, 4, 'square', 'empty', NULL),
-- VIP
(12, 'VIP-01', 'VIP Hoàng Gia 1', 3, 10, 'round', 'occupied', DATE_SUB(NOW(), INTERVAL 90 MINUTE)),
(13, 'VIP-02', 'VIP Hoàng Gia 2', 3, 12, 'round', 'empty', NULL),
(14, 'VIP-03', 'VIP Sen Vàng', 3, 8, 'rectangle', 'reserved', NULL)
ON DUPLICATE KEY UPDATE `name`=`name`;

-- Categories
INSERT INTO `categories` (`id`, `name`, `icon`, `display_order`) VALUES
(1, 'Khai Vị', 'fa-seedling', 1),
(2, 'Món Chính Đặc Sắc', 'fa-fire-burner', 2),
(3, 'Hải Sản Tươi Sống', 'fa-fish', 3),
(4, 'Lẩu & Nướng', 'fa-bowl-rice', 4),
(5, 'Đồ Uống & Rượu Vang', 'fa-wine-glass', 5),
(6, 'Tráng Miệng', 'fa-ice-cream', 6)
ON DUPLICATE KEY UPDATE `name`=`name`;

-- Menu Items
INSERT INTO `menu_items` (`id`, `category_id`, `name`, `price`, `image`, `status`, `unit`, `description`) VALUES
-- Khai vị
(1, 1, 'Gỏi Ngó Sen Tôm Thịt', 125000, 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=500&auto=format&fit=crop&q=60', 'available', 'Dĩa', 'Gỏi ngó sen tươi giòn kèm tôm sú và thịt ba chỉ ngâm nước mắm chua ngọt đặc sản.'),
(2, 1, 'Soup Bào Ngư Vi Cá', 220000, 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=500&auto=format&fit=crop&q=60', 'available', 'Thố', 'Soup thượng hạng nấu cùng bào ngư tươi sống và nấm đông cô.'),
(3, 1, 'Chả Giò Hải Sản Hoàng Kim', 145000, 'https://images.unsplash.com/photo-1544025162-d76694265947?w=500&auto=format&fit=crop&q=60', 'available', 'Phần', 'Chả giò giòn rụm nhân tôm thịt sốt phô mai béo ngậy.'),

-- Món chính
(4, 2, 'Bò Wagyu Nướng Đá Muối', 580000, 'https://images.unsplash.com/photo-1544025162-d76694265947?w=500&auto=format&fit=crop&q=60', 'available', 'Phần', 'Thịt bò Wagyu vân mỡ tuyệt hảo nướng trên phiến đá muối Himalaya hồng.'),
(5, 2, 'Gà Đồi Nướng Tiêu Rừng Tây Bắc', 295000, 'https://images.unsplash.com/photo-1598103442097-8b74394b95c6?w=500&auto=format&fit=crop&q=60', 'available', 'Con', 'Gà thả đồi ướp hạt dổi mắc khén da giòn rụm thịt thơm ngọt đậm đà.'),
(6, 2, 'Sườn Heo Nướng Sốt BBQ Mật Ong', 320000, 'https://images.unsplash.com/photo-1544025162-d76694265947?w=500&auto=format&fit=crop&q=60', 'available', 'Phần', 'Dẻ sườn non đẫm sốt BBQ nướng thơm lừng mềm tan.'),
(7, 2, 'Cơm Chiên Hải Sản Hoàng Bào', 165000, 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=500&auto=format&fit=crop&q=60', 'available', 'Thố', 'Hạt cơm dẻo vàng óng bọc trứng cùng tôm mực tươi roi rói.'),

-- Hải sản
(8, 3, 'Tôm Hùm Alaska Hấp Rượu Vang', 850000, 'https://images.unsplash.com/photo-1565680018434-b513d5e5fd47?w=500&auto=format&fit=crop&q=60', 'available', 'Con', 'Tôm hùm thượng hạng hấp rượu vang Pháp giữ trọn vị ngọt tự nhiên.'),
(9, 3, 'Cua Huỳnh Đế Sốt Trứng Muối', 790000, 'https://images.unsplash.com/photo-1559742811-822873691df8?w=500&auto=format&fit=crop&q=60', 'available', 'Phần', 'Cua thịt chắc ngọt quyện đều sốt trứng muối béo thơm ngậy vị.'),
(10, 3, 'Cá Hồi Na Uy Áp Chảo Sốt Chanh Dây', 360000, 'https://images.unsplash.com/photo-1467003909585-2f8a72700288?w=500&auto=format&fit=crop&q=60', 'available', 'Phần', 'Cá hồi nhập khẩu da giòn thịt mềm ngậy kết hợp sốt chanh leo thanh mát.'),

-- Lẩu
(11, 4, 'Lẩu Nấm Thảo Mộc Dưỡng Nhan', 390000, 'https://images.unsplash.com/photo-1547928576-a4a33237cbc3?w=500&auto=format&fit=crop&q=60', 'available', 'Nồi', 'Nước dùng ninh từ 12 vị thảo dược và nấm quý thanh đạm bồi bổ sức khỏe.'),
(12, 4, 'Lẩu Thái Hải Sản Tom Yum', 420000, 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=500&auto=format&fit=crop&q=60', 'available', 'Nồi', 'Vị chua cay đậm đà chuẩn vị xứ Chùa Vàng cùng hải sản tươi cao cấp.'),

-- Đồ uống
(13, 5, 'Rượu Vang Đỏ Chateau Bordeaux', 950000, 'https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?w=500&auto=format&fit=crop&q=60', 'available', 'Chai', 'Vang đỏ cao cấp nguồn gốc Bordeaux lưu hương gỗ sồi và quả mọng.'),
(14, 5, 'Trà Trái Cây Nhiệt Đới Macchiato', 65000, 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=500&auto=format&fit=crop&q=60', 'available', 'Ly', 'Trà hoa quả tự nhiên thơm ngát với lớp foam sữa mặn béo ngậy.'),
(15, 5, 'Nước Ép Dưa Hấu Bạc Hà', 55000, 'https://images.unsplash.com/photo-1553530666-ba11a7da3888?w=500&auto=format&fit=crop&q=60', 'available', 'Ly', 'Nước ép dưa hấu nguyên chất thanh nhiệt ngày hè.'),
(16, 5, 'Bia Thủ Công IPA Craft Beer', 85000, 'https://images.unsplash.com/photo-1608270111666-c98f828a1c97?w=500&auto=format&fit=crop&q=60', 'available', 'Chai', 'Bia thủ công vị hoa bia nồng nàn tinh tế.'),

-- Tráng miệng
(17, 6, 'Panna Cotta Sốt Dâu Rừng', 75000, 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?w=500&auto=format&fit=crop&q=60', 'available', 'Hũ', 'Món tráng miệng Ý mềm mướt béo ngậy ngọt dịu cùng sốt quả dâu rừng đỏ mọng.'),
(18, 6, 'Chè Yến Hạt Sen Long Nhãn', 120000, 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=500&auto=format&fit=crop&q=60', 'available', 'Chén', 'Yến sào chưng đường phèn hạt sen thanh mát bổ dưỡng.')
ON DUPLICATE KEY UPDATE `name`=`name`;

-- Tạo sẵn một vài Orders thực tế
INSERT INTO `orders` (`id`, `order_code`, `table_id`, `staff_id`, `customer_name`, `guest_count`, `status`, `subtotal`, `vat_percent`, `vat_amount`, `total_amount`, `opened_at`) VALUES
(1, 'ORD-20261016-101', 1, 4, 'Anh Minh Hoàng', 3, 'open', 1130000, 8.0, 90400, 1220400, DATE_SUB(NOW(), INTERVAL 45 MINUTE)),
(2, 'ORD-20261016-105', 5, 4, 'Chị Thu Thảo', 6, 'open', 1890000, 8.0, 151200, 2041200, DATE_SUB(NOW(), INTERVAL 75 MINUTE)),
(3, 'ORD-20261016-202', 8, 4, 'Đoàn khách TechCorp', 4, 'open', 940000, 8.0, 75200, 1015200, DATE_SUB(NOW(), INTERVAL 25 MINUTE)),
(4, 'ORD-20261016-VIP1', 12, 4, 'VIP Giám Đốc Tuấn', 8, 'open', 3560000, 8.0, 284800, 3844800, DATE_SUB(NOW(), INTERVAL 90 MINUTE))
ON DUPLICATE KEY UPDATE `order_code`=`order_code`;

UPDATE `tables` SET `current_order_id` = 1 WHERE `id` = 1;
UPDATE `tables` SET `current_order_id` = 2 WHERE `id` = 5;
UPDATE `tables` SET `current_order_id` = 3 WHERE `id` = 8;
UPDATE `tables` SET `current_order_id` = 4 WHERE `id` = 12;

-- Order items
INSERT INTO `order_items` (`order_id`, `menu_item_id`, `item_name`, `price`, `quantity`, `notes`, `kitchen_status`, `sent_kitchen_at`) VALUES
-- Đơn bàn 101
(1, 1, 'Gỏi Ngó Sen Tôm Thịt', 125000, 1, 'Không cho ớt cay', 'ready', DATE_SUB(NOW(), INTERVAL 35 MINUTE)),
(1, 4, 'Bò Wagyu Nướng Đá Muối', 580000, 1, 'Chín vừa (Medium)', 'cooking', DATE_SUB(NOW(), INTERVAL 20 MINUTE)),
(1, 12, 'Lẩu Thái Hải Sản Tom Yum', 420000, 1, 'Ít cay, nước lẩu thêm rau', 'pending', DATE_SUB(NOW(), INTERVAL 5 MINUTE)),
(1, 15, 'Nước Ép Dưa Hấu Bạc Hà', 55000, 2, 'Ít đường, không đá', 'ready', DATE_SUB(NOW(), INTERVAL 40 MINUTE)),

-- Đơn bàn 105
(2, 3, 'Chả Giò Hải Sản Hoàng Kim', 145000, 2, 'Làm nóng giòn', 'ready', DATE_SUB(NOW(), INTERVAL 60 MINUTE)),
(2, 5, 'Gà Đồi Nướng Tiêu Rừng Tây Bắc', 295000, 1, 'Chặt nhỏ vừa ăn', 'ready', DATE_SUB(NOW(), INTERVAL 45 MINUTE)),
(2, 11, 'Lẩu Nấm Thảo Mộc Dưỡng Nhan', 390000, 1, 'Thêm nấm đông cô', 'cooking', DATE_SUB(NOW(), INTERVAL 15 MINUTE)),
(2, 13, 'Rượu Vang Đỏ Chateau Bordeaux', 950000, 1, 'Ướp lạnh trước khi khui', 'ready', DATE_SUB(NOW(), INTERVAL 70 MINUTE)),

-- Đơn bàn 202
(3, 1, 'Gỏi Ngó Sen Tôm Thịt', 125000, 2, 'Làm chuẩn vị chua ngọt', 'pending', DATE_SUB(NOW(), INTERVAL 10 MINUTE)),
(3, 6, 'Sườn Heo Nướng Sốt BBQ Mật Ong', 320000, 1, 'Không hành lá', 'cooking', DATE_SUB(NOW(), INTERVAL 18 MINUTE)),
(3, 7, 'Cơm Chiên Hải Sản Hoàng Bào', 165000, 1, '', 'pending', DATE_SUB(NOW(), INTERVAL 12 MINUTE)),

-- Đơn VIP 1
(4, 8, 'Tôm Hùm Alaska Hấp Rượu Vang', 850000, 2, 'Lấy nguyên con trình bày đẹp', 'cooking', DATE_SUB(NOW(), INTERVAL 30 MINUTE)),
(4, 9, 'Cua Huỳnh Đế Sốt Trứng Muối', 790000, 1, 'Sốt đậm đà', 'cooking', DATE_SUB(NOW(), INTERVAL 25 MINUTE)),
(4, 4, 'Bò Wagyu Nướng Đá Muối', 580000, 1, 'Medium Rare', 'ready', DATE_SUB(NOW(), INTERVAL 50 MINUTE)),
(4, 13, 'Rượu Vang Đỏ Chateau Bordeaux', 950000, 1, 'Ly thủy tinh cao cấp', 'ready', DATE_SUB(NOW(), INTERVAL 80 MINUTE));

-- Phiếu đặt bàn trực tuyến mẫu (Reservations)
INSERT INTO `reservations` (`id`, `reservation_code`, `customer_name`, `phone`, `reservation_time`, `guest_count`, `preferred_area`, `special_notes`, `status`, `assigned_table_id`) VALUES
(1, 'RES-20261016-001', 'Nguyễn Văn Hùng', '0912345678', DATE_ADD(NOW(), INTERVAL 2 HOUR), 6, 'Tầng 1 - Sảnh Chính', 'Có 1 trẻ em cần ghế con, kỷ niệm ngày cưới.', 'pending', NULL),
(2, 'RES-20261016-002', 'Lê Hải Yến', '0988776655', DATE_ADD(NOW(), INTERVAL 4 HOUR), 10, 'Phòng VIP Hoàng Gia', 'Khách VIP tiếp đối tác Nhật, chuẩn bị rượu vang trước.', 'pending', NULL),
(3, 'RES-20261016-003', 'Trần Đình Khang', '0903112233', DATE_ADD(NOW(), INTERVAL 1 DAY), 4, 'Tầng 2 - Không Gian Thoáng', 'Bàn gần cửa sổ ban công ngắm phố.', 'confirmed', 10),
(4, 'RES-20261016-004', 'Phạm Quỳnh Nga', '0977665544', DATE_SUB(NOW(), INTERVAL 3 HOUR), 2, 'Tầng 1 - Sảnh Chính', 'Khách bận đột xuất xin hủy.', 'cancelled', NULL)
ON DUPLICATE KEY UPDATE `reservation_code`=`reservation_code`;
