-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 03, 2025 lúc 04:06 AM
-- Phiên bản máy phục vụ: 10.4.24-MariaDB
-- Phiên bản PHP: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `kltn`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cods`
--

CREATE TABLE `cods` (
  `ID` int(11) NOT NULL,
  `OrderID` int(11) NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `Status` enum('pending','collected','settled') DEFAULT 'pending',
  `Settled_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `cods`
--

INSERT INTO `cods` (`ID`, `OrderID`, `Amount`, `Status`, `Settled_at`) VALUES
(29, 10174717, '200000.00', 'pending', '2025-10-17 05:40:02'),
(30, 10178154, '200000.00', 'pending', '2025-10-17 05:52:02'),
(31, 10174039, '120000.00', 'pending', '2025-10-17 06:02:30'),
(32, 10216894, '120000.00', 'pending', '2025-10-21 03:02:52'),
(33, 11019179, '99000.00', 'pending', '2025-11-01 15:16:15'),
(34, 11021978, '69000.00', 'pending', '2025-11-02 14:00:19');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `ID` int(11) NOT NULL,
  `CustomerID` int(11) NOT NULL,
  `ShipperID` int(11) DEFAULT NULL,
  `Pick_up_address` varchar(255) NOT NULL,
  `Pick_up_lat` decimal(10,7) DEFAULT NULL,
  `Pick_up_lng` decimal(10,7) DEFAULT NULL,
  `Delivery_address` varchar(255) NOT NULL,
  `Delivery_lat` decimal(10,7) DEFAULT NULL,
  `Delivery_lng` decimal(10,7) DEFAULT NULL,
  `Recipient` varchar(100) DEFAULT NULL,
  `status` enum('pending','accepted','picked_up','in_transit','delivered','delivery_failed','cancelled') NOT NULL DEFAULT 'pending',
  `COD_amount` decimal(10,2) DEFAULT 0.00,
  `CODFee` decimal(10,2) DEFAULT 0.00,
  `WarehouseID` int(11) DEFAULT NULL,
  `Weight` decimal(10,2) DEFAULT NULL,
  `ShippingFee` decimal(10,2) DEFAULT 0.00,
  `Created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `Accepted_at` timestamp NULL DEFAULT NULL,
  `Note` varchar(255) DEFAULT NULL,
  `RecipientPhone` varchar(20) DEFAULT NULL,
  `hidden` int(11) NOT NULL DEFAULT 1,
  `is_rated` tinyint(1) NOT NULL DEFAULT 0,
  `fee_payer` enum('sender','receiver') NOT NULL DEFAULT 'sender',
  `PickUp_Photo_Path` varchar(255) DEFAULT NULL,
  `Delivery_Photo_Path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`ID`, `CustomerID`, `ShipperID`, `Pick_up_address`, `Pick_up_lat`, `Pick_up_lng`, `Delivery_address`, `Delivery_lat`, `Delivery_lng`, `Recipient`, `status`, `COD_amount`, `CODFee`, `WarehouseID`, `Weight`, `ShippingFee`, `Created_at`, `Accepted_at`, `Note`, `RecipientPhone`, `hidden`, `is_rated`, `fee_payer`, `PickUp_Photo_Path`, `Delivery_Photo_Path`) VALUES
(9175208, 185, 141, 'Khoa Cơ Khí - IUH, Đại học Công nghiệp Tp.Hồ Chí Minh, 12 Nguyễn Văn Bảo, Phường 4, Gò Vấp, Hồ Chí Minh', '10.8221072', '106.6879015', 'Vinhomes Grand Park, Long Bình, Thủ Đức, Hồ Chí Minh', '10.8429630', '106.8407200', 'Zaa', 'delivered', '500000.00', '5000.00', NULL, '1.20', '18000.00', '2025-09-17 04:53:35', '2025-10-17 15:04:29', 'Hàng dễ vỡ', '0998998999', 1, 0, 'sender', NULL, NULL),
(9178848, 185, 139, 'Vinschool, Nguyễn Hữu Cảnh, Bến Nghé, Quận 1, Hồ Chí Minh', '10.7862422', '106.7114781', 'Khoa Cơ Khí - IUH, Đại học Công nghiệp Tp.Hồ Chí Minh, 12 Nguyễn Văn Bảo, Phường 4, Gò Vấp, Hồ Chí Minh', '10.8221072', '106.6879015', 'Tom', 'delivery_failed', '0.00', '0.00', NULL, '1.00', '18000.00', '2025-09-17 04:03:20', '2025-10-09 04:07:35', 'Hàng điện tử', '0912345000', 1, 0, 'sender', NULL, NULL),
(9182385, 185, 139, '66 D. Lê Lợi, Phường 1, Gò Vấp, Hồ Chí Minh 700000, Việt Nam', '10.8205291', '106.6863567', '66b Nguyễn Sỹ Sách, Phường 15, Tân Bình, Hồ Chí Minh 70000, Việt Nam', '10.8199509', '106.6358395', 'Nguyễn Lâm', 'delivered', '0.00', '0.00', NULL, '1.00', '18000.00', '2025-09-18 10:33:01', '2025-10-14 02:42:46', 'Hàng điện tử', '0999888909', 1, 0, 'sender', NULL, NULL),
(9186174, 185, 141, '167/2/5 Ngô Tất Tố, P. 22, Phường 22, Bình Thạnh, Hồ Chí Minh 700000, Việt Nam', '10.7911801', '106.7148782', 'Khoa Cơ Khí - IUH, Đại học Công nghiệp Tp.Hồ Chí Minh, 12 Nguyễn Văn Bảo, Phường 4, Gò Vấp, Hồ Chí Minh', '10.8221072', '106.6879015', 'Trần An', 'delivered', '120000.00', '5000.00', NULL, '2.00', '18000.00', '2025-09-18 10:45:51', '2025-10-11 10:28:12', 'Hàng dễ vỡ', '0912098002', 1, 0, 'sender', NULL, 'https://firebasestorage.googleapis.com/v0/b/kltn-97864.firebasestorage.app/o/shipper_proofs%2F9186174%2Fdelivered_1762094015859.jpg?alt=media&token=bcd081fb-6ac6-47c0-9da3-697d1e7ec19b'),
(9186919, 185, NULL, '144 Xuân Thủy, Dịch Vọng Hậu, Cầu Giấy, Hà Nội', '21.0368282', '105.7820251', '222 Trần Duy Hưng, Cầu Giấy', '21.0069095', '105.7933494', 'Lê Phong', 'pending', '0.00', '0.00', NULL, '1.00', '18000.00', '2025-09-18 13:53:32', NULL, 'Hàng dễ vỡ', '0921876987', 1, 0, 'sender', NULL, NULL),
(9221121, 185, 141, 'Trạm ép giấy Xuân Trường, Nguyễn Văn Quỳ, Tân Thuận Đông, Quận 7, Hồ Chí Minh', '10.7429218', '106.7390444', 'Sân Bay Tân Sơn Nhất - Trường Sơn, Cảng hàng không Quốc tế Tân Sơn Nhất, Phường 2, Tân Bình, Hồ Chí Minh', '10.8156395', '106.6638113', 'Lê Anh', 'delivered', '0.00', '0.00', NULL, '1.00', '18000.00', '2025-09-21 17:38:24', '2025-10-11 09:43:24', 'Hàng dễ vỡ', '0934999210', 1, 0, 'sender', NULL, NULL),
(9229334, 185, NULL, 'Trạm ép giấy Xuân Trường, Nguyễn Văn Quỳ, Tân Thuận Đông, Quận 7, Hồ Chí Minh', '10.7429218', '106.7390444', 'Chợ Thủ Đức B, Đoàn Công Hớn, Trường Thọ, Thủ Đức, Hồ Chí Minh', '10.8502291', '106.7557012', 'Trần Lam', 'pending', '0.00', '0.00', NULL, '2.00', '18000.00', '2025-09-21 17:40:03', '2025-10-04 04:29:10', '', '0924666892', 1, 0, 'sender', NULL, NULL),
(10046774, 185, 141, '81 Đ. Võ Duy Ninh, Phường 22, Bình Thạnh, Hồ Chí Minh 90000, Việt Nam', '10.7919236', '106.7159995', 'Nguyễn Văn Bảo/Số 12 ĐH Công Nghiệp, Phường 1, Gò Vấp, Hồ Chí Minh 71408, Việt Nam', '10.8221589', '106.6868454', 'Nguyễn Sa', 'picked_up', '0.00', '0.00', NULL, '1.00', '18000.00', '2025-10-04 06:44:46', '2025-10-21 03:08:00', 'Tập tài liệu', '0900000878', 1, 0, 'sender', 'https://firebasestorage.googleapis.com/v0/b/kltn-97864.firebasestorage.app/o/shipper_proofs%2F10046774%2Fpicked_up_1762097162482.jpg?alt=media&token=2af76a8b-b56a-4457-a656-200e6eed5c39', NULL),
(10046898, 185, 141, 'Katinat, 91 Đồng Khởi, Bến Nghé, Quận 1, Hồ Chí Minh', '10.7747667', '106.7043670', '66B Nguyễn Sỹ Sách, Phường 15, Tân Bình, Hồ Chí Minh', '10.8199447', '106.6358023', 'Lê Lam', 'delivered', '0.00', '0.00', NULL, '0.50', '15000.00', '2025-10-04 04:15:03', '2025-10-11 02:57:25', 'Hàng dễ vỡ', '0909000231', 1, 1, 'sender', NULL, NULL),
(10067527, 185, NULL, 'Katinat Phan Văn Trị, 18A Đ. Phan Văn Trị, Phường 1, Gò Vấp, Hồ Chí Minh, Việt Nam', NULL, NULL, 'Cheese Coffee, 190C Đ. Phan Văn Trị, Phường 14, Bình Thạnh, Hồ Chí Minh, Việt Nam', NULL, NULL, 'Nguyen Bao', 'cancelled', '0.00', '0.00', NULL, '0.30', '15000.00', '2025-10-06 01:14:57', NULL, 'Tai lieu', '0989878465', 1, 0, 'sender', NULL, NULL),
(10142116, 187, NULL, 'Lê Văn Khương, Thới An, Quận 12, Ho Chi Minh City', '10.8632542', '106.6497280', 'Đại học Văn Lang (Cơ sở 3), 68 Hẻm 80 Dương Quảng Hàm, Phường 5, Gò Vấp, Hồ Chí Minh', '10.8270654', '106.6987296', 'Hồ Bảo Ngọc', 'pending', '0.00', '0.00', NULL, '2.00', '18000.00', '2025-10-14 02:07:33', NULL, 'Hàng dễ vỡ', '0379654880', 1, 0, 'sender', NULL, NULL),
(10146432, 185, NULL, 'Chợ Đông Thạnh, Đặng Thúc Vịnh, Đông Thạnh, Hóc Môn, Hồ Chí Minh', '10.9043722', '106.6367921', 'KTX Đại Học Công Nghiệp ( IUHer), Nguyễn Văn Bảo, phường 4, Gò Vấp, Hồ Chí Minh', '10.8218768', '106.6870616', 'Lê Tú', 'delivery_failed', '0.00', '0.00', NULL, '1.00', '18000.00', '2025-10-13 17:26:59', NULL, 'Tài liệu giấy', '0923888970', 1, 0, 'sender', NULL, NULL),
(10174039, 185, 139, 'Chợ Đông Thạnh, Đặng Thúc Vịnh, Đông Thạnh, Hóc Môn, Hồ Chí Minh', '10.9043722', '106.6367921', '366 Đ. Phan Văn Trị, Phường 5, Gò Vấp, Thành phố Hồ Chí Minh, Việt Nam', '10.8238822', '106.6933738', 'Nguyễn Lâm Anh', 'delivered', '120000.00', '5000.00', NULL, '1.00', '18000.00', '2025-10-17 06:02:30', '2025-11-01 15:17:41', 'Tài liệu', '0361897001', 1, 0, 'receiver', 'https://firebasestorage.googleapis.com/v0/b/kltn-97864.firebasestorage.app/o/shipper_proofs%2F10174039%2Fpicked_up_1762010296206.jpg?alt=media&token=3cc8d74c-5cf5-49f1-9eff-1287bc6944dc', 'https://firebasestorage.googleapis.com/v0/b/kltn-97864.firebasestorage.app/o/shipper_proofs%2F10174039%2Fdelivered_1762010411554.jpg?alt=media&token=669e8cbf-4eda-4245-8819-949e086ac529'),
(10174717, 187, NULL, 'LOTTE Mart Gò Vấp, 18 Đ. Phan Văn Trị, Phường 10, Gò Vấp, Thành phố Hồ Chí Minh, Việt Nam', '10.8382576', '106.6708474', 'AEON MALL TÂN PHÚ, 30 Đ. Tân Thắng, Sơn Kỳ, Tân Phú, Thành phố Hồ Chí Minh 700000, Việt Nam\\', '10.8034355', '106.6178294', 'Tran Thi Đinh Tam', 'pending', '200000.00', '5000.00', 1, '3.00', '23000.00', '2025-10-17 05:40:02', NULL, 'Giao trong giờ hành chính', '0367781923', 1, 0, 'sender', NULL, NULL),
(10178154, 185, NULL, '208 Nguyễn Hữu Cảnh, Vinhomes Tân Cảng, Bình Thạnh, Thành phố Hồ Chí Minh 700000, Việt Nam', '10.7940264', '106.7206721', '2B Đ. Phổ Quang, Phường 2, Tân Bình, Thành phố Hồ Chí Minh 700000, Việt Nam', '10.8029270', '106.6659258', 'Tran Thi Đinh Tam', 'pending', '200000.00', '5000.00', NULL, '3.00', '23000.00', '2025-10-17 05:52:02', '2025-11-01 04:16:48', 'Giao trong giờ hành chính', '0367781923', 1, 0, 'sender', NULL, NULL),
(10216894, 185, NULL, 'Empire 88 Tower - Empire City, Thủ Thiêm, Thủ Đức, Hồ Chí Minh', '10.7697001', '106.7160034', 'Landmark 81, Vinhomes Central Park, Phường 22, Bình Thạnh, Hồ Chí Minh', '10.7948522', '106.7218363', 'Nguyen Van B', 'pending', '120000.00', '5000.00', NULL, '1.00', '18000.00', '2025-10-21 03:02:52', '2025-11-01 04:56:00', 'Hang de vo', '0379546210', 1, 0, 'sender', NULL, NULL),
(11019179, 185, 139, '256/39/31e ấp 2, Đường Đông Thạnh 2-5, Hóc Môn, Hồ Chí Minh', '10.9066919', '106.6348243', 'Katinat, 3 Tháng 2, Phường 12, Quận 10, Hồ Chí Minh', '10.7778520', '106.6810900', 'Lê Ân Linh', 'in_transit', '99000.00', '5000.00', NULL, '1.00', '18000.00', '2025-11-01 15:16:15', '2025-11-01 15:20:38', 'Hàng thực phẩm', '0986421357', 1, 0, 'receiver', 'https://firebasestorage.googleapis.com/v0/b/kltn-97864.firebasestorage.app/o/shipper_proofs%2F11019179%2Fpicked_up_1762010981122.jpg?alt=media&token=ab4d6e63-87a6-4349-88a6-944b09b85c4b', NULL),
(11021978, 194, NULL, 'KFC Đặng Thúc Vịnh, 253-287 Âp 7, Đông Thạnh, Hóc Môn, Hồ Chí Minh', '10.9039511', '106.6358836', 'Ways station Gym & Billiard, 395 Đ. An Dương Vương, Phường 10, Quận 6, Hồ Chí Minh', '10.7419791', '106.6235623', 'Vũ Hà Linh', 'pending', '69000.00', '5000.00', NULL, '1.00', '18000.00', '2025-11-02 14:00:19', NULL, 'Thực phẩm', '0383645978', 1, 0, 'sender', NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_warehouse_tracking`
--

CREATE TABLE `order_warehouse_tracking` (
  `ID` int(11) NOT NULL,
  `OrderID` int(11) NOT NULL,
  `WarehouseID` int(11) NOT NULL,
  `Handled_by` int(11) DEFAULT NULL,
  `ActionType` enum('import','export') NOT NULL,
  `Timestamp` datetime DEFAULT current_timestamp(),
  `Note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `shipper_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `rating_value` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `ratings`
--

INSERT INTO `ratings` (`id`, `order_id`, `shipper_id`, `customer_id`, `rating_value`, `created_at`) VALUES
(1, 10046898, 141, 185, 5, '2025-10-13 05:26:39');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `ID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`ID`, `Name`, `Description`) VALUES
(1, 'admin', 'admin role\r\n'),
(2, 'Quản lý ', 'management role'),
(3, 'Nhân viên tiếp nhận', 'tiepnhan role\r\n'),
(4, 'Quản lý kho', 'management warehouse role'),
(5, 'Kế toán', 'accountant role'),
(6, 'Shipper', 'shipper role'),
(7, 'Khách hàng', 'customer role');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `shipper_locations`
--

CREATE TABLE `shipper_locations` (
  `shipper_id` int(11) NOT NULL,
  `lat` double NOT NULL,
  `lng` double NOT NULL,
  `accuracy` float DEFAULT NULL,
  `speed` float DEFAULT NULL,
  `heading` float DEFAULT NULL,
  `status` enum('offline','online','busy') DEFAULT 'offline',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `shipper_locations`
--

INSERT INTO `shipper_locations` (`shipper_id`, `lat`, `lng`, `accuracy`, `speed`, `heading`, `status`, `updated_at`) VALUES
(139, 10.9066917, 106.6348359, NULL, NULL, NULL, 'offline', '2025-11-02 13:47:49'),
(141, 10.8215179, 106.6869817, NULL, NULL, NULL, 'online', '2025-11-03 03:06:05'),
(157, 10.7703004, 106.7170031, NULL, NULL, NULL, 'offline', '2025-10-12 16:19:04'),
(158, 10.9066972, 106.6348068, NULL, NULL, NULL, 'offline', '2025-11-02 14:01:04');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `trackings`
--

CREATE TABLE `trackings` (
  `ID` int(11) NOT NULL,
  `OrderID` int(11) NOT NULL,
  `Status` varchar(1000) NOT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `Updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `trackings`
--

INSERT INTO `trackings` (`ID`, `OrderID`, `Status`, `Location`, `Updated_at`) VALUES
(137, 9178848, 'Đơn hàng đã được tạo.', NULL, '2025-09-17 04:03:20'),
(138, 9175208, 'Đơn hàng đã được tạo.', NULL, '2025-09-17 04:53:35'),
(139, 9182385, 'Đơn hàng đã được tạo.', NULL, '2025-09-18 10:33:01'),
(140, 9186174, 'Đơn hàng đã được tạo.', NULL, '2025-09-18 10:45:51'),
(141, 9186919, 'Đơn hàng đã được tạo.', NULL, '2025-09-18 13:53:32'),
(142, 9221121, 'Đơn hàng đã được tạo.', NULL, '2025-09-21 17:38:24'),
(143, 9229334, 'Đơn hàng đã được tạo.', NULL, '2025-09-21 17:40:03'),
(223, 10046898, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-11 02:57:25'),
(224, 10046898, 'Shipper đã lấy hàng thành công.', NULL, '2025-10-11 09:05:56'),
(225, 10046898, 'Đơn hàng đang trên đường giao đến bạn.', NULL, '2025-10-11 09:06:06'),
(226, 10046898, 'Giao hàng thành công!', NULL, '2025-10-11 09:06:22'),
(227, 9221121, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-11 09:43:24'),
(228, 9221121, 'Shipper đã lấy hàng thành công.', NULL, '2025-10-11 10:13:40'),
(229, 9221121, 'Đơn hàng đang trên đường giao đến bạn.', NULL, '2025-10-11 10:14:12'),
(230, 9221121, 'Giao hàng thành công!', NULL, '2025-10-11 10:14:28'),
(231, 9186174, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-11 10:28:12'),
(232, 10067527, 'Đơn hàng đã được hủy bởi khách hàng.', NULL, '2025-10-12 15:38:28'),
(233, 10046774, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-13 17:17:07'),
(234, 9186174, 'Shipper đã lấy hàng thành công.', NULL, '2025-10-13 17:23:56'),
(235, 9186174, 'Đơn hàng đang trên đường giao đến bạn.', NULL, '2025-10-13 17:23:58'),
(237, 10146432, 'Đơn hàng đã được tạo.', NULL, '2025-10-13 17:26:59'),
(238, 10142116, 'Đơn hàng đã được tạo.', NULL, '2025-10-14 02:07:33'),
(239, 9182385, 'Shipper 139 đã nhận đơn.', NULL, '2025-10-14 02:42:46'),
(240, 9182385, 'Shipper đã lấy hàng thành công.', NULL, '2025-10-14 02:43:04'),
(242, 9182385, 'Giao hàng thành công!', NULL, '2025-10-14 02:43:35'),
(250, 10174717, 'Đơn hàng đã được tạo.', NULL, '2025-10-17 05:40:02'),
(251, 10178154, 'Đơn hàng đã được tạo.', NULL, '2025-10-17 05:52:02'),
(252, 10174039, 'Đơn hàng đã được tạo.', NULL, '2025-10-17 06:02:30'),
(253, 9175208, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-17 15:04:29'),
(254, 9175208, 'Shipper đã lấy hàng thành công.', NULL, '2025-10-18 09:26:02'),
(255, 9175208, 'Đơn hàng đang trên đường giao đến bạn.', NULL, '2025-10-18 09:26:23'),
(256, 9175208, 'Giao hàng thành công!', NULL, '2025-10-18 09:26:30'),
(257, 10216894, 'Đơn hàng đã được tạo.', NULL, '2025-10-21 03:02:52'),
(258, 10046774, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-21 03:08:00'),
(259, 10178154, 'Shipper 139 đã nhận đơn.', NULL, '2025-11-01 04:16:48'),
(261, 9178848, 'Giao hàng không thành công. Lý do: Người nhận không liên lạc được', NULL, '2025-11-01 05:48:32'),
(266, 10216894, 'Đơn hàng đang trên đường giao đến bạn.', NULL, '2025-11-01 10:58:40'),
(267, 10216894, 'Đơn hàng đang trên đường giao đến bạn.', NULL, '2025-11-01 10:59:33'),
(268, 10178154, 'Shipper đã lấy hàng thành công.', NULL, '2025-11-01 11:07:28'),
(273, 11019179, 'Đơn hàng đã được tạo.', NULL, '2025-11-01 15:16:15'),
(274, 10174039, 'Shipper 139 đã nhận đơn.', NULL, '2025-11-01 15:17:41'),
(275, 10174039, 'Shipper đã lấy hàng thành công.', NULL, '2025-11-01 15:18:26'),
(276, 10174039, 'Đơn hàng đang trên đường giao đến bạn.', NULL, '2025-11-01 15:18:34'),
(277, 10174039, 'Giao hàng thành công!', NULL, '2025-11-01 15:20:24'),
(278, 11019179, 'Shipper 139 đã nhận đơn.', NULL, '2025-11-01 15:20:38'),
(279, 11019179, 'Shipper đã lấy hàng thành công.', NULL, '2025-11-01 15:29:50'),
(280, 11019179, 'Đơn hàng đang trên đường giao đến bạn.', NULL, '2025-11-01 15:30:08'),
(281, 11021978, 'Đơn hàng đã được tạo.', NULL, '2025-11-02 14:00:19'),
(282, 9186174, 'Giao hàng thành công!', NULL, '2025-11-02 14:33:48'),
(283, 10046774, 'Shipper đã lấy hàng thành công.', NULL, '2025-11-02 15:26:16'),
(284, 10046774, 'Đơn hàng đang trên đường giao đến bạn.', NULL, '2025-11-02 15:34:42');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `transactions`
--

CREATE TABLE `transactions` (
  `ID` int(11) NOT NULL,
  `OrderID` int(11) DEFAULT NULL,
  `UserID` int(11) NOT NULL,
  `Type` enum('shipping_fee','collect_cod','deposit_cod','withdraw','bonus','penalty') NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `Status` enum('pending','completed','failed') DEFAULT 'pending',
  `Note` varchar(255) DEFAULT NULL,
  `Created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `transactions`
--

INSERT INTO `transactions` (`ID`, `OrderID`, `UserID`, `Type`, `Amount`, `Status`, `Note`, `Created_at`) VALUES
(21, 10216894, 139, 'shipping_fee', '18000.00', 'completed', NULL, '2025-11-01 11:20:18'),
(22, 10216894, 139, 'collect_cod', '125000.00', 'completed', NULL, '2025-11-01 11:20:18'),
(23, 10216894, 139, 'shipping_fee', '18000.00', 'completed', NULL, '2025-11-01 15:10:12'),
(24, 10216894, 139, 'collect_cod', '125000.00', 'completed', NULL, '2025-11-01 15:10:12'),
(25, 10174039, 139, 'shipping_fee', '18000.00', 'completed', NULL, '2025-11-01 15:20:24'),
(26, 10174039, 139, 'collect_cod', '125000.00', 'completed', NULL, '2025-11-01 15:20:24'),
(34, 9175208, 141, 'deposit_cod', '5000.00', 'completed', '', '2025-11-02 14:32:58'),
(35, 9186174, 141, 'shipping_fee', '18000.00', 'completed', NULL, '2025-11-02 14:33:48'),
(36, 9186174, 141, 'collect_cod', '125000.00', 'completed', NULL, '2025-11-02 14:33:48');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `PhoneNumber` varchar(255) NOT NULL,
  `Role` int(11) DEFAULT NULL,
  `account_status` enum('active','locked','pending') NOT NULL DEFAULT 'active',
  `rating` decimal(3,2) DEFAULT NULL CHECK (`rating` between 0.00 and 5.00),
  `Note` varchar(255) NOT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `hidden` int(11) NOT NULL DEFAULT 1,
  `rating_count` int(11) NOT NULL DEFAULT 0,
  `rating_sum` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`ID`, `Username`, `Email`, `Password`, `PhoneNumber`, `Role`, `account_status`, `rating`, `Note`, `warehouse_id`, `hidden`, `rating_count`, `rating_sum`, `created_at`) VALUES
(1, 'admin1', 'admin1@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0379666888', 1, 'active', NULL, 'pass-12345', NULL, 1, 0, 0, '2025-10-14 05:30:32'),
(2, 'admin2', 'admin2@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0379666999', 1, 'active', NULL, '', NULL, 1, 0, 0, '2025-10-14 05:30:32'),
(3, 'quanly1', 'quanly1@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234502', 2, 'active', NULL, '', NULL, 1, 0, 0, '2025-10-14 05:30:32'),
(4, 'quanly2', 'quanly2@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234503', 2, 'active', NULL, '', NULL, 1, 0, 0, '2025-10-14 05:30:32'),
(5, 'nhanvientiepnhan1', 'nhanvientiepnhan1@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234504', 3, 'active', NULL, '', 1, 1, 0, 0, '2025-10-14 05:30:32'),
(6, 'nhanvientiepnhan2', 'nhanvientiepnhan2@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234505', 3, 'active', NULL, '', NULL, 1, 0, 0, '2025-10-14 05:30:32'),
(7, 'quanlykho1', 'quanlykho1@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234506', 4, 'active', NULL, '', 1, 1, 0, 0, '2025-10-14 05:30:32'),
(8, 'quanlykho2', 'quanlykho2@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234507', 4, 'active', NULL, '', NULL, 1, 0, 0, '2025-10-14 05:30:32'),
(9, 'ketoan1', 'ketoan1@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234508', 5, 'active', NULL, '', NULL, 1, 0, 0, '2025-10-14 05:30:32'),
(10, 'ketoan2', 'ketoan2@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234509', 5, 'active', NULL, '', NULL, 1, 0, 0, '2025-10-14 05:30:32'),
(11, 'shipper1', 'shipper1@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234510', 6, 'active', NULL, 'Go Vap-Binh Thanh', 1, 1, 0, 0, '2025-10-14 05:30:32'),
(12, 'shipper2', 'shipper2@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234511', 6, 'active', NULL, 'Cu Chi - Hoc Mon', 1, 1, 0, 0, '2025-10-14 05:30:32'),
(75, 'Tom', 'tom@gmail.com', '15de21c670ae7c3f6f3f1f37029303c9', '0979345532', 7, 'active', NULL, '', NULL, 1, 0, 0, '2025-10-14 05:30:32'),
(77, 'Dom', 'dom2@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0989777666', 2, 'active', NULL, '', NULL, 1, 0, 0, '2025-10-14 05:30:32'),
(100, 'QuanlyKho3', 'quanly3@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0983251777', 4, 'active', NULL, 'QL3', 4, 1, 0, 0, '2025-10-14 05:30:32'),
(139, 'shipper3', 'shipper3@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0379111222', 6, 'active', '4.50', '', 1, 1, 0, 0, '2025-10-10 02:30:32'),
(141, 'shipper4', 'shipper4@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0379111000', 6, 'locked', '5.00', '', 1, 1, 3, 15, '2025-10-14 05:30:32'),
(157, 'shipper5', 'shipper5@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0379111333', 6, 'active', NULL, '', 2, 1, 0, 0, '2025-10-14 05:30:32'),
(158, 'shipper6', 'shipper6@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0379111444', 6, 'active', NULL, '', 2, 1, 0, 0, '2025-10-14 05:30:32'),
(159, 'shipper7', 'shipper7@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0936778998', 6, 'active', NULL, '', 2, 1, 0, 0, '2025-10-14 05:30:32'),
(160, 'shipper8', 'shipper8@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0983557998', 6, 'active', NULL, '', 2, 1, 0, 0, '2025-10-14 05:30:32'),
(161, 'shipper9', 'shipper9@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0912345678', 6, 'active', NULL, '', 4, 1, 0, 0, '2025-10-14 05:30:32'),
(162, 'shipper10', 'shipper10@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0987654321', 6, 'active', NULL, '', 4, 1, 0, 0, '2025-10-14 05:30:32'),
(163, 'shipper11', 'shipper11@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901122334', 6, 'active', NULL, '', 4, 1, 0, 0, '2025-10-14 05:30:32'),
(164, 'shipper12', 'shipper12@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0934567890', 6, 'active', NULL, '', 4, 1, 0, 0, '2025-10-14 05:30:32'),
(165, 'shipper13', 'shipper13@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0961234567', 6, 'active', NULL, '', 5, 1, 0, 0, '2025-10-14 05:30:32'),
(166, 'shipper14', 'shipper14@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0979876543', 6, 'active', NULL, '', 5, 1, 0, 0, '2025-10-14 05:30:32'),
(167, 'shipper15', 'shipper15@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0923456789', 6, 'active', NULL, '', 5, 1, 0, 0, '2025-10-14 05:30:32'),
(168, 'shipper16', 'shipper16@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0941122334', 6, 'active', NULL, '', 5, 1, 0, 0, '2025-10-14 05:30:32'),
(169, 'shipper17', 'shipper17@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0956789123', 6, 'active', NULL, '', 3, 1, 0, 0, '2025-10-14 05:30:32'),
(170, 'shipper18', 'shipper18@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0998765432', 6, 'active', NULL, '', 3, 1, 0, 0, '2025-10-14 05:30:32'),
(171, 'shipper19', 'shipper19@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0909988776', 6, 'active', NULL, '', 3, 1, 0, 0, '2025-10-14 05:30:32'),
(172, 'shipper20', 'shipper20@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0933322110', 6, 'active', NULL, '', 3, 1, 0, 0, '2025-10-14 05:30:32'),
(185, 'Nguyễn Khách Hàng', 'khachhangnguyen@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0979730421', 7, 'active', NULL, '', NULL, 1, 0, 0, '2025-10-14 05:30:32'),
(187, 'Trần Khách Hàng', 'tranKH@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0979730422', 7, 'active', NULL, '', NULL, 1, 0, 0, '2025-10-14 05:30:32'),
(188, 'Nguyễn Văn Shipper', 'nvshipper@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0379111555', 6, 'active', NULL, '', NULL, 1, 0, 0, '2025-10-14 09:01:50'),
(189, 'Nguyen Van B Test New', 'guest_1760679602@fake.local', '$2y$10$Boj3qaANVn6tB.s4.l6jLuZwRI1zevbaEpnniDoJFZRyRt9/nuy1G', '0989789021', 7, 'active', NULL, '', NULL, 1, 0, 0, '2025-10-17 05:40:02'),
(190, 'Nguyen Van B Test New 2', 'guest_1760680322@fake.local', '$2y$10$D2GnXU9TeYTFiA.h/kQMHehxG18E7ZN3aKz.qzF6S/Kyh7kSmihf2', '0989789028', 7, 'active', NULL, '', NULL, 1, 0, 0, '2025-10-17 05:52:02'),
(191, 'Nguyễn Văn Ba', 'nguyenba@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0379111666', 6, 'active', NULL, '', NULL, 1, 0, 0, '2025-10-20 16:25:07'),
(192, 'test321', 'ts321@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0987987909', 6, 'active', NULL, '', NULL, 1, 0, 0, '2025-10-31 02:54:39'),
(194, 'Trong Phat Le', 'trongphat@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0379974903', 7, 'active', NULL, '', NULL, 1, 0, 0, '2025-10-31 03:10:49');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `shipper_id` int(11) NOT NULL,
  `license_plate` varchar(20) NOT NULL,
  `model` varchar(100) DEFAULT NULL COMMENT 'Ví dụ: Honda Wave 110',
  `type` enum('motorbike','car') DEFAULT 'motorbike',
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'Xe đang được sử dụng chính'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `vehicles`
--

INSERT INTO `vehicles` (`id`, `shipper_id`, `license_plate`, `model`, `type`, `is_active`) VALUES
(1, 141, '93E-30690', 'Yamaha Sirius 110', 'motorbike', 1),
(2, 139, '59E-04963', 'Wave RSX', 'motorbike', 1),
(3, 157, '51K - 87645', 'Honda AirBlade', 'motorbike', 1),
(4, 158, '51E - 36618', 'Yamaha Exciter', 'motorbike', 1),
(5, 188, '49E-65271', 'Honda Lead', 'motorbike', 1),
(6, 191, '54Y-66872', 'Honda Vision', 'motorbike', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `warehouses`
--

CREATE TABLE `warehouses` (
  `ID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `Quantity` int(11) NOT NULL DEFAULT 0,
  `Capacity` int(11) DEFAULT 1000,
  `manager_id` int(11) DEFAULT NULL,
  `operation_status` enum('active','paused','full') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `warehouses`
--

INSERT INTO `warehouses` (`ID`, `Name`, `Address`, `Quantity`, `Capacity`, `manager_id`, `operation_status`, `created_at`, `updated_at`) VALUES
(1, 'Kho Quận 1', '123 Nguyễn Huệ, Quận 1, TP.HCM', 1, 100, 7, 'active', '2025-05-08 03:47:41', '2025-08-07 13:14:42'),
(2, 'Kho Quận 7', '456 Nguyễn Thị Thập, Quận 7, TP.HCM', 1, 100, 8, 'active', '2025-05-08 03:47:41', '2025-05-18 15:36:41'),
(3, 'Kho Thủ Đức', '789 Võ Văn Ngân, TP. Thủ Đức, TP.HCM', 0, 0, 8, 'full', '2025-05-08 03:47:41', '2025-05-18 15:24:11'),
(4, 'Kho Hóc Môn', '432 Trịnh Thị Miếng, Thới Tam Thôn, Hóc Môn, TP.HCM', 150, 1500, 100, 'active', '2025-05-09 15:02:31', '2025-05-26 16:36:43'),
(5, 'Kho Bình Chánh', '171 Đ. Nguyễn Văn Linh, Phong Phú, Bình Chánh, TP.HCM', 0, 200, 8, 'paused', '2025-05-12 04:03:45', '2025-05-26 16:36:58');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `cods`
--
ALTER TABLE `cods`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `OrderID` (`OrderID`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `CustomerID` (`CustomerID`),
  ADD KEY `ShipperID` (`ShipperID`),
  ADD KEY `WarehouseID` (`WarehouseID`);

--
-- Chỉ mục cho bảng `order_warehouse_tracking`
--
ALTER TABLE `order_warehouse_tracking`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `OrderID` (`OrderID`),
  ADD KEY `WarehouseID` (`WarehouseID`),
  ADD KEY `Handled_by` (`Handled_by`);

--
-- Chỉ mục cho bảng `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_order_rating` (`order_id`);

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Name` (`Name`);

--
-- Chỉ mục cho bảng `shipper_locations`
--
ALTER TABLE `shipper_locations`
  ADD PRIMARY KEY (`shipper_id`),
  ADD KEY `idx_status_time` (`status`,`updated_at`),
  ADD KEY `idx_lat` (`lat`),
  ADD KEY `idx_lng` (`lng`);

--
-- Chỉ mục cho bảng `trackings`
--
ALTER TABLE `trackings`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `OrderID` (`OrderID`);

--
-- Chỉ mục cho bảng `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `OrderID` (`OrderID`),
  ADD KEY `UserID` (`UserID`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `unique_phone_number` (`PhoneNumber`),
  ADD KEY `Role` (`Role`),
  ADD KEY `fk_users_warehouse` (`warehouse_id`);

--
-- Chỉ mục cho bảng `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `license_plate_unique` (`license_plate`),
  ADD KEY `shipper_id` (`shipper_id`);

--
-- Chỉ mục cho bảng `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Name` (`Name`),
  ADD KEY `fk_manager_id` (`manager_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `cods`
--
ALTER TABLE `cods`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2147483648;

--
-- AUTO_INCREMENT cho bảng `order_warehouse_tracking`
--
ALTER TABLE `order_warehouse_tracking`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT cho bảng `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `trackings`
--
ALTER TABLE `trackings`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=285;

--
-- AUTO_INCREMENT cho bảng `transactions`
--
ALTER TABLE `transactions`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=195;

--
-- AUTO_INCREMENT cho bảng `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `cods`
--
ALTER TABLE `cods`
  ADD CONSTRAINT `cods_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `orders` (`ID`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `users` (`ID`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`ShipperID`) REFERENCES `users` (`ID`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`WarehouseID`) REFERENCES `warehouses` (`ID`);

--
-- Các ràng buộc cho bảng `order_warehouse_tracking`
--
ALTER TABLE `order_warehouse_tracking`
  ADD CONSTRAINT `order_warehouse_tracking_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `orders` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_warehouse_tracking_ibfk_2` FOREIGN KEY (`WarehouseID`) REFERENCES `warehouses` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_warehouse_tracking_ibfk_3` FOREIGN KEY (`Handled_by`) REFERENCES `users` (`ID`);

--
-- Các ràng buộc cho bảng `shipper_locations`
--
ALTER TABLE `shipper_locations`
  ADD CONSTRAINT `fk_shipper_locations_user` FOREIGN KEY (`shipper_id`) REFERENCES `users` (`ID`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `trackings`
--
ALTER TABLE `trackings`
  ADD CONSTRAINT `trackings_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `orders` (`ID`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `orders` (`ID`) ON DELETE SET NULL,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `users` (`ID`);

--
-- Các ràng buộc cho bảng `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_warehouse` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`Role`) REFERENCES `roles` (`ID`);

--
-- Các ràng buộc cho bảng `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_ibfk_1` FOREIGN KEY (`shipper_id`) REFERENCES `users` (`ID`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `warehouses`
--
ALTER TABLE `warehouses`
  ADD CONSTRAINT `fk_manager_id` FOREIGN KEY (`manager_id`) REFERENCES `users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
