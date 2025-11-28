-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 28, 2025 lúc 09:09 AM
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
  `Delivery_Photo_Path` varchar(255) DEFAULT NULL,
  `distance` double NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`ID`, `CustomerID`, `ShipperID`, `Pick_up_address`, `Pick_up_lat`, `Pick_up_lng`, `Delivery_address`, `Delivery_lat`, `Delivery_lng`, `Recipient`, `status`, `COD_amount`, `CODFee`, `Weight`, `ShippingFee`, `Created_at`, `Accepted_at`, `Note`, `RecipientPhone`, `hidden`, `is_rated`, `fee_payer`, `PickUp_Photo_Path`, `Delivery_Photo_Path`, `distance`) VALUES
(9175208, 185, 141, 'Khoa Cơ Khí - IUH, Đại học Công nghiệp Tp.Hồ Chí Minh, 12 Nguyễn Văn Bảo, Phường 4, Gò Vấp, Hồ Chí Minh', '10.8221072', '106.6879015', 'Vinhomes Grand Park, Long Bình, Thủ Đức, Hồ Chí Minh', '10.8429630', '106.8407200', 'Zaa', 'delivered', '500000.00', '5000.00', '1.20', '18000.00', '2025-09-17 04:53:35', '2025-10-17 15:04:29', 'Hàng dễ vỡ', '0998998999', 1, 0, 'sender', NULL, NULL, 0),
(9178848, 185, 139, 'Vinschool, Nguyễn Hữu Cảnh, Bến Nghé, Quận 1, Hồ Chí Minh', '10.7862422', '106.7114781', 'Khoa Cơ Khí - IUH, Đại học Công nghiệp Tp.Hồ Chí Minh, 12 Nguyễn Văn Bảo, Phường 4, Gò Vấp, Hồ Chí Minh', '10.8221072', '106.6879015', 'Tom', 'delivery_failed', '0.00', '0.00', '1.00', '18000.00', '2025-09-17 04:03:20', '2025-10-09 04:07:35', 'Hàng điện tử', '0912345000', 1, 0, 'sender', NULL, NULL, 0),
(9182385, 185, 139, '66 D. Lê Lợi, Phường 1, Gò Vấp, Hồ Chí Minh 700000, Việt Nam', '10.8205291', '106.6863567', '66b Nguyễn Sỹ Sách, Phường 15, Tân Bình, Hồ Chí Minh 70000, Việt Nam', '10.8199509', '106.6358395', 'Nguyễn Lâm', 'delivered', '0.00', '0.00', '1.00', '18000.00', '2025-09-18 10:33:01', '2025-10-14 02:42:46', 'Hàng điện tử', '0999888909', 1, 0, 'sender', NULL, NULL, 0),
(9186174, 185, 141, '167/2/5 Ngô Tất Tố, P. 22, Phường 22, Bình Thạnh, Hồ Chí Minh 700000, Việt Nam', '10.7911801', '106.7148782', 'Khoa Cơ Khí - IUH, Đại học Công nghiệp Tp.Hồ Chí Minh, 12 Nguyễn Văn Bảo, Phường 4, Gò Vấp, Hồ Chí Minh', '10.8221072', '106.6879015', 'Trần An', 'delivered', '120000.00', '5000.00', '2.00', '18000.00', '2025-09-18 10:45:51', '2025-10-11 10:28:12', 'Hàng dễ vỡ', '0912098002', 1, 0, 'sender', NULL, 'https://firebasestorage.googleapis.com/v0/b/kltn-97864.firebasestorage.app/o/shipper_proofs%2F9186174%2Fdelivered_1762094015859.jpg?alt=media&token=bcd081fb-6ac6-47c0-9da3-697d1e7ec19b', 0),
(9186919, 185, NULL, '144 Xuân Thủy, Dịch Vọng Hậu, Cầu Giấy, Hà Nội', '21.0368282', '105.7820251', '222 Trần Duy Hưng, Cầu Giấy', '21.0069095', '105.7933494', 'Lê Phong', 'pending', '0.00', '0.00', '1.00', '18000.00', '2025-09-18 13:53:32', NULL, 'Hàng dễ vỡ', '0921876987', 1, 0, 'sender', NULL, NULL, 0),
(9221121, 185, 141, 'Trạm ép giấy Xuân Trường, Nguyễn Văn Quỳ, Tân Thuận Đông, Quận 7, Hồ Chí Minh', '10.7429218', '106.7390444', 'Sân Bay Tân Sơn Nhất - Trường Sơn, Cảng hàng không Quốc tế Tân Sơn Nhất, Phường 2, Tân Bình, Hồ Chí Minh', '10.8156395', '106.6638113', 'Lê Anh', 'delivered', '0.00', '0.00', '1.00', '18000.00', '2025-09-21 17:38:24', '2025-10-11 09:43:24', 'Hàng dễ vỡ', '0934999210', 1, 0, 'sender', NULL, NULL, 0),
(9229334, 185, NULL, 'Trạm ép giấy Xuân Trường, Nguyễn Văn Quỳ, Tân Thuận Đông, Quận 7, Hồ Chí Minh', '10.7429218', '106.7390444', 'Chợ Thủ Đức B, Đoàn Công Hớn, Trường Thọ, Thủ Đức, Hồ Chí Minh', '10.8502291', '106.7557012', 'Trần Lam', 'pending', '0.00', '0.00', '2.00', '18000.00', '2025-09-21 17:40:03', '2025-10-04 04:29:10', '', '0924666892', 1, 0, 'sender', NULL, NULL, 0),
(10046774, 185, 141, '81 Đ. Võ Duy Ninh, Phường 22, Bình Thạnh, Hồ Chí Minh 90000, Việt Nam', '10.7919236', '106.7159995', 'Nguyễn Văn Bảo/Số 12 ĐH Công Nghiệp, Phường 1, Gò Vấp, Hồ Chí Minh 71408, Việt Nam', '10.8221589', '106.6868454', 'Nguyễn Sa', 'delivered', '0.00', '0.00', '1.00', '18000.00', '2025-10-04 06:44:46', '2025-10-21 03:08:00', 'Tập tài liệu', '0900000878', 1, 0, 'sender', 'https://firebasestorage.googleapis.com/v0/b/kltn-97864.firebasestorage.app/o/shipper_proofs%2F10046774%2Fpicked_up_1762097162482.jpg?alt=media&token=2af76a8b-b56a-4457-a656-200e6eed5c39', 'https://firebasestorage.googleapis.com/v0/b/kltn-97864.firebasestorage.app/o/shipper_proofs%2F10046774%2Fdelivered_1762436311009.jpg?alt=media&token=091edaba-2ff9-4377-b8a2-c9060b09d855', 0),
(10046898, 185, 141, 'Katinat, 91 Đồng Khởi, Bến Nghé, Quận 1, Hồ Chí Minh', '10.7747667', '106.7043670', '66B Nguyễn Sỹ Sách, Phường 15, Tân Bình, Hồ Chí Minh', '10.8199447', '106.6358023', 'Lê Lam', 'delivered', '0.00', '0.00', '0.50', '15000.00', '2025-10-04 04:15:03', '2025-10-11 02:57:25', 'Hàng dễ vỡ', '0909000231', 1, 1, 'sender', NULL, NULL, 0),
(10067527, 185, NULL, 'Katinat Phan Văn Trị, 18A Đ. Phan Văn Trị, Phường 1, Gò Vấp, Hồ Chí Minh, Việt Nam', NULL, NULL, 'Cheese Coffee, 190C Đ. Phan Văn Trị, Phường 14, Bình Thạnh, Hồ Chí Minh, Việt Nam', NULL, NULL, 'Nguyen Bao', 'cancelled', '0.00', '0.00', '0.30', '15000.00', '2025-10-06 01:14:57', NULL, 'Tai lieu', '0989878465', 1, 0, 'sender', NULL, NULL, 0),
(10142116, 187, NULL, 'Lê Văn Khương, Thới An, Quận 12, Ho Chi Minh City', '10.8632542', '106.6497280', 'Đại học Văn Lang (Cơ sở 3), 68 Hẻm 80 Dương Quảng Hàm, Phường 5, Gò Vấp, Hồ Chí Minh', '10.8270654', '106.6987296', 'Hồ Bảo Ngọc', 'pending', '0.00', '0.00', '2.00', '18000.00', '2025-10-14 02:07:33', NULL, 'Hàng dễ vỡ', '0379654880', 1, 0, 'sender', NULL, NULL, 0),
(10146432, 185, NULL, 'Chợ Đông Thạnh, Đặng Thúc Vịnh, Đông Thạnh, Hóc Môn, Hồ Chí Minh', '10.9043722', '106.6367921', 'KTX Đại Học Công Nghiệp ( IUHer), Nguyễn Văn Bảo, phường 4, Gò Vấp, Hồ Chí Minh', '10.8218768', '106.6870616', 'Lê Tú', 'delivery_failed', '0.00', '0.00', '1.00', '18000.00', '2025-10-13 17:26:59', NULL, 'Tài liệu giấy', '0923888970', 1, 0, 'sender', NULL, NULL, 0),
(10174039, 185, 139, 'Chợ Đông Thạnh, Đặng Thúc Vịnh, Đông Thạnh, Hóc Môn, Hồ Chí Minh', '10.9043722', '106.6367921', '366 Đ. Phan Văn Trị, Phường 5, Gò Vấp, Thành phố Hồ Chí Minh, Việt Nam', '10.8238822', '106.6933738', 'Nguyễn Lâm Anh', 'delivered', '120000.00', '5000.00', '1.00', '18000.00', '2025-10-17 06:02:30', '2025-11-01 15:17:41', 'Tài liệu', '0361897001', 1, 0, 'receiver', 'https://firebasestorage.googleapis.com/v0/b/kltn-97864.firebasestorage.app/o/shipper_proofs%2F10174039%2Fpicked_up_1762010296206.jpg?alt=media&token=3cc8d74c-5cf5-49f1-9eff-1287bc6944dc', 'https://firebasestorage.googleapis.com/v0/b/kltn-97864.firebasestorage.app/o/shipper_proofs%2F10174039%2Fdelivered_1762010411554.jpg?alt=media&token=669e8cbf-4eda-4245-8819-949e086ac529', 0),
(10174717, 187, NULL, 'LOTTE Mart Gò Vấp, 18 Đ. Phan Văn Trị, Phường 10, Gò Vấp, Thành phố Hồ Chí Minh, Việt Nam', '10.8382576', '106.6708474', 'AEON MALL TÂN PHÚ, 30 Đ. Tân Thắng, Sơn Kỳ, Tân Phú, Thành phố Hồ Chí Minh 700000, Việt Nam\\', '10.8034355', '106.6178294', 'Tran Thi Đinh Tam', 'pending', '200000.00', '5000.00', '3.00', '23000.00', '2025-10-17 05:40:02', NULL, 'Giao trong giờ hành chính', '0367781923', 1, 0, 'sender', NULL, NULL, 0),
(10178154, 185, NULL, '208 Nguyễn Hữu Cảnh, Vinhomes Tân Cảng, Bình Thạnh, Thành phố Hồ Chí Minh 700000, Việt Nam', '10.7940264', '106.7206721', '2B Đ. Phổ Quang, Phường 2, Tân Bình, Thành phố Hồ Chí Minh 700000, Việt Nam', '10.8029270', '106.6659258', 'Tran Thi Đinh Tam', 'pending', '200000.00', '5000.00', '3.00', '23000.00', '2025-10-17 05:52:02', '2025-11-01 04:16:48', 'Giao trong giờ hành chính', '0367781923', 1, 0, 'sender', NULL, NULL, 0),
(10216894, 185, NULL, 'Empire 88 Tower - Empire City, Thủ Thiêm, Thủ Đức, Hồ Chí Minh', '10.7697001', '106.7160034', 'Landmark 81, Vinhomes Central Park, Phường 22, Bình Thạnh, Hồ Chí Minh', '10.7948522', '106.7218363', 'Nguyen Van B', 'pending', '120000.00', '5000.00', '1.00', '18000.00', '2025-10-21 03:02:52', '2025-11-01 04:56:00', 'Hang de vo', '0379546210', 1, 0, 'sender', NULL, NULL, 0),
(11019179, 185, 139, '256/39/31e ấp 2, Đường Đông Thạnh 2-5, Hóc Môn, Hồ Chí Minh', '10.9066919', '106.6348243', 'Katinat, 3 Tháng 2, Phường 12, Quận 10, Hồ Chí Minh', '10.7778520', '106.6810900', 'Lê Ân Linh', 'in_transit', '99000.00', '5000.00', '1.00', '18000.00', '2025-11-01 15:16:15', '2025-11-01 15:20:38', 'Hàng thực phẩm', '0986421357', 1, 0, 'receiver', 'https://firebasestorage.googleapis.com/v0/b/kltn-97864.firebasestorage.app/o/shipper_proofs%2F11019179%2Fpicked_up_1762010981122.jpg?alt=media&token=ab4d6e63-87a6-4349-88a6-944b09b85c4b', NULL, 0),
(11021978, 194, 141, 'KFC Đặng Thúc Vịnh, 253-287 Âp 7, Đông Thạnh, Hóc Môn, Hồ Chí Minh', '10.9039511', '106.6358836', 'Ways station Gym & Billiard, 395 Đ. An Dương Vương, Phường 10, Quận 6, Hồ Chí Minh', '10.7419791', '106.6235623', 'Vũ Hà Linh', 'delivered', '69000.00', '5000.00', '1.00', '15000.00', '2025-11-02 14:00:19', '2025-11-06 13:46:11', 'Thực phẩm', '0383645978', 1, 0, 'sender', 'https://firebasestorage.googleapis.com/v0/b/kltn-97864.firebasestorage.app/o/shipper_proofs%2F11021978%2Fpicked_up_1762675243576.jpg?alt=media&token=5aca71b7-c66c-4b12-a2fa-f79ab58fce2b', 'https://firebasestorage.googleapis.com/v0/b/kltn-97864.firebasestorage.app/o/shipper_proofs%2F11021978%2Fdelivered_1762679347413.jpg?alt=media&token=abc9d780-5c42-48ee-b863-72c08f9197da', 0),
(11068347, 185, NULL, 'Lê Văn Khương, Thới An, Quận 12, Ho Chi Minh City', '10.8632542', '106.6497280', 'Anh ngữ Ms Hoa TOEIC, 82 Lê Văn Việt, Hiệp Phú, Thủ Đức, Hồ Chí Minh', '10.8469475', '106.7769739', 'Nguyễn Diệu Anh', 'cancelled', '0.00', '0.00', '1.00', '15000.00', '2025-11-06 15:42:43', NULL, 'Tài liệu', '0379645888', 1, 0, 'sender', NULL, NULL, 0),
(11094471, 194, 141, '256/39/31e ấp 2, Đường Đông Thạnh 2-5, Hóc Môn, Hồ Chí Minh', '10.9067210', '106.6348573', 'Cầu vượt Tân Thới Hiệp, Thới An, Quận 12, Hồ Chí Minh', '10.8619885', '106.6499294', 'Lê văn trung', 'delivered', '99000.00', '5000.00', '1.00', '18000.00', '2025-11-09 14:17:07', '2025-11-10 16:50:00', 'Thực phẩm', '0986368996', 1, 0, 'sender', 'https://firebasestorage.googleapis.com/v0/b/kltn-97864.firebasestorage.app/o/shipper_proofs%2F11094471%2Fpicked_up_1762794068532.jpg?alt=media&token=e3ef70d3-dc80-4c59-ac37-0a0f64c75c39', 'https://firebasestorage.googleapis.com/v0/b/kltn-97864.firebasestorage.app/o/shipper_proofs%2F11094471%2Fdelivered_1764131899219.jpg?alt=media&token=6920627b-dfaf-4d4c-bccf-52e551519e7a', 0),
(11166554, 185, 141, '256/39/31e ấp 2, Đường Đông Thạnh 2-5, Hóc Môn, Hồ Chí Minh', '10.9067034', '106.6348626', 'Bitexco Financial Tower, 2 Hải Triều, Bến Nghé, Quận 1, Hồ Chí Minh', '10.7718433', '106.7044222', 'Hoàng Oanh', 'delivered', '0.00', '0.00', '1.00', '15000.00', '2025-11-16 08:51:51', '2025-11-28 03:03:24', 'Hàng dễ vỡ', '0963456880', 1, 0, 'receiver', 'https://firebasestorage.googleapis.com/v0/b/kltn-97864.firebasestorage.app/o/shipper_proofs%2F11166554%2Fpicked_up_1764301844518.jpg?alt=media&token=b8d9a53e-912d-4797-93bf-d910020dcbc3', 'https://firebasestorage.googleapis.com/v0/b/kltn-97864.firebasestorage.app/o/shipper_proofs%2F11166554%2Fdelivered_1764315805518.jpg?alt=media&token=3fee3382-adc0-4226-8d6e-c331ba675f3c', 0),
(11283885, 185, NULL, 'Chợ Đông Thạnh, Đặng Thúc Vịnh, Đông Thạnh, Hóc Môn, Hồ Chí Minh', '10.9043722', '106.6367921', 'Bến xe An Sương, Quốc Lộ 22, Bà Điểm, Hóc Môn, Hồ Chí Minh', '10.8439389', '106.6134854', 'Ngô Hoài Lâm', 'pending', '0.00', '0.00', '1.00', '44000.00', '2025-11-28 07:18:07', NULL, 'Hàng dễ vỡ', '0963456996', 1, 0, 'sender', NULL, NULL, 8.425),
(11289907, 190, NULL, '12 Nguyen Van Bao, Go Vap, TP.HCM', NULL, NULL, '35 Hai Ba Trung, Quan 1, TP.HCM', NULL, NULL, 'Tran Thi Đinh Tam', 'pending', '200000.00', '5000.00', '3.00', '44000.00', '2025-11-28 07:17:55', NULL, 'Giao trong giờ hành chính', '0367781923', 1, 0, 'sender', NULL, NULL, 5);

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
(139, 10.9016663, 106.6450596, NULL, NULL, NULL, 'offline', '2025-11-17 10:24:11'),
(141, 10.9067028, 106.6348687, NULL, NULL, NULL, 'online', '2025-11-28 08:06:12'),
(157, 10.7703004, 106.7170031, NULL, NULL, NULL, 'offline', '2025-10-12 16:19:04'),
(158, 10.9066972, 106.6348068, NULL, NULL, NULL, 'offline', '2025-11-02 14:01:04'),
(188, 10.906494, 106.634823, NULL, NULL, NULL, 'offline', '2025-11-23 16:18:17');

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
(284, 10046774, 'Đơn hàng đang trên đường giao đến bạn.', NULL, '2025-11-02 15:34:42'),
(285, 10046774, 'Đơn hàng đang trên đường giao đến bạn.', NULL, '2025-11-06 13:25:30'),
(286, 10046774, 'Giao hàng thành công!', NULL, '2025-11-06 13:38:59'),
(287, 11021978, 'Shipper 141 đã nhận đơn.', NULL, '2025-11-06 13:46:11'),
(288, 11068347, 'Đơn hàng đã được tạo.', NULL, '2025-11-06 15:42:43'),
(290, 11021978, 'Shipper đã lấy hàng thành công.', NULL, '2025-11-09 08:00:55'),
(291, 11021978, 'Đơn hàng đang trên đường giao đến bạn.', NULL, '2025-11-09 09:08:31'),
(292, 11021978, 'Giao hàng thành công!', NULL, '2025-11-09 09:09:18'),
(293, 11094471, 'Đơn hàng đã được tạo.', NULL, '2025-11-09 14:17:07'),
(294, 11094471, 'Shipper 141 đã nhận đơn.', NULL, '2025-11-10 16:50:00'),
(295, 11094471, 'Shipper đã lấy hàng thành công.', NULL, '2025-11-10 17:01:20'),
(296, 11094471, 'Đơn hàng đang trên đường giao đến bạn.', NULL, '2025-11-10 17:01:23'),
(297, 11166554, 'Đơn hàng đã được tạo.', NULL, '2025-11-16 08:51:51'),
(304, 11094471, 'Giao hàng thành công!', NULL, '2025-11-26 04:38:34'),
(306, 11166554, 'Shipper 141 đã nhận đơn.', NULL, '2025-11-28 03:03:24'),
(307, 11166554, 'Shipper đã lấy hàng thành công.', NULL, '2025-11-28 03:16:02'),
(309, 11166554, 'Shipper đã lấy hàng thành công.', NULL, '2025-11-28 03:37:15'),
(310, 11166554, 'Đơn hàng đang trên đường giao đến bạn.', NULL, '2025-11-28 03:37:24'),
(311, 11166554, 'Shipper đã lấy hàng thành công.', NULL, '2025-11-28 03:51:00'),
(312, 11166554, 'Đơn hàng đang trên đường giao đến bạn.', NULL, '2025-11-28 03:51:05'),
(313, 11289907, 'Đơn hàng đã được tạo.', NULL, '2025-11-28 07:17:55'),
(314, 11283885, 'Đơn hàng đã được tạo.', NULL, '2025-11-28 07:18:07'),
(315, 11166554, 'Giao hàng thành công!', NULL, '2025-11-28 07:43:39');

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
  `Created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ProofImage` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `transactions`
--

INSERT INTO `transactions` (`ID`, `OrderID`, `UserID`, `Type`, `Amount`, `Status`, `Note`, `Created_at`, `ProofImage`) VALUES
(21, 10216894, 139, 'shipping_fee', '18000.00', 'completed', NULL, '2025-11-01 11:20:18', NULL),
(22, 10216894, 139, 'collect_cod', '125000.00', 'completed', NULL, '2025-11-01 11:20:18', NULL),
(23, 10216894, 139, 'shipping_fee', '18000.00', 'completed', NULL, '2025-11-01 15:10:12', NULL),
(24, 10216894, 139, 'collect_cod', '125000.00', 'completed', NULL, '2025-11-01 15:10:12', NULL),
(25, 10174039, 139, 'shipping_fee', '18000.00', 'completed', NULL, '2025-11-01 15:20:24', NULL),
(26, 10174039, 139, 'collect_cod', '125000.00', 'completed', NULL, '2025-11-01 15:20:24', NULL),
(34, 9175208, 141, 'deposit_cod', '5000.00', 'completed', '', '2025-11-02 14:32:58', NULL),
(35, 9186174, 141, 'shipping_fee', '18000.00', 'completed', NULL, '2025-11-02 14:33:48', NULL),
(36, 9186174, 141, 'collect_cod', '125000.00', 'completed', NULL, '2025-11-02 14:33:48', NULL),
(37, 10046774, 141, 'shipping_fee', '18000.00', 'completed', NULL, '2025-11-06 13:38:59', NULL),
(38, 11021978, 141, 'shipping_fee', '18000.00', 'completed', NULL, '2025-11-09 09:09:18', NULL),
(39, 11021978, 141, 'collect_cod', '74000.00', 'completed', NULL, '2025-11-09 09:09:18', NULL),
(40, 9186174, 141, 'deposit_cod', '5000.00', 'completed', '', '2025-11-09 14:36:08', NULL),
(41, 10174039, 139, 'deposit_cod', '5000.00', 'completed', '', '2025-11-09 14:36:31', NULL),
(42, 11021978, 141, 'deposit_cod', '5000.00', 'completed', '', '2025-11-23 16:09:16', NULL),
(43, 11094471, 141, 'shipping_fee', '18000.00', 'completed', NULL, '2025-11-26 04:38:34', NULL),
(44, 11094471, 141, 'collect_cod', '104000.00', 'completed', NULL, '2025-11-26 04:38:34', NULL),
(56, 11094471, 141, 'deposit_cod', '5000.00', 'completed', 'Test serc ck', '2025-11-27 04:04:09', 'https://res.cloudinary.com/dbaeafw6z/image/upload/v1764216246/transaction_proofs/t8kk32ewntri2p0zsfwo.png'),
(57, 11166554, 141, 'shipping_fee', '15000.00', 'completed', NULL, '2025-11-28 07:43:39', NULL);

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
  `Avatar` varchar(255) DEFAULT NULL,
  `Role` int(11) DEFAULT NULL,
  `account_status` enum('active','locked','pending') NOT NULL DEFAULT 'active',
  `rating` decimal(3,2) DEFAULT NULL CHECK (`rating` between 0.00 and 5.00),
  `Note` varchar(255) NOT NULL,
  `hidden` int(11) NOT NULL DEFAULT 1,
  `rating_count` int(11) NOT NULL DEFAULT 0,
  `rating_sum` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`ID`, `Username`, `Email`, `Password`, `PhoneNumber`, `Avatar`, `Role`, `account_status`, `rating`, `Note`, `hidden`, `rating_count`, `rating_sum`, `created_at`) VALUES
(1, 'admin1', 'admin1@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0379666888', 'https://res.cloudinary.com/dbaeafw6z/image/upload/v1764129518/avatars/yo1mfafinbrvmeahrpav.png', 1, 'active', NULL, 'pass-12345', 1, 0, 0, '2025-10-14 05:30:32'),
(2, 'admin2', 'admin2@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0379666999', NULL, 1, 'active', NULL, '', 1, 0, 0, '2025-10-14 05:30:32'),
(3, 'quanly1', 'quanly1@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234502', NULL, 2, 'active', NULL, '', 1, 0, 0, '2025-10-14 05:30:32'),
(4, 'quanly2', 'quanly2@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234503', NULL, 2, 'active', NULL, '', 1, 0, 0, '2025-10-14 05:30:32'),
(9, 'ketoan1', 'ketoan1@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234508', NULL, 5, 'active', NULL, 'test update', 1, 0, 0, '2025-10-14 05:30:32'),
(10, 'ketoan2', 'ketoan2@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234509', NULL, 5, 'active', NULL, '', 1, 0, 0, '2025-10-14 05:30:32'),
(11, 'shipper1', 'shipper1@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234510', NULL, 6, 'active', NULL, 'Go Vap-Binh Thanh', 1, 0, 0, '2025-10-14 05:30:32'),
(12, 'shipper2', 'shipper2@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234511', NULL, 6, 'active', NULL, 'Cu Chi - Hoc Mon', 1, 0, 0, '2025-10-14 05:30:32'),
(75, 'Tom', 'tom@gmail.com', '15de21c670ae7c3f6f3f1f37029303c9', '0979345532', NULL, 7, 'active', NULL, '', 1, 0, 0, '2025-10-14 05:30:32'),
(77, 'Dom', 'dom2@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0989777666', NULL, 2, 'active', NULL, '', 1, 0, 0, '2025-10-14 05:30:32'),
(139, 'shipper3', 'shipper3@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0379111222', NULL, 6, 'active', '4.50', '', 1, 0, 0, '2025-10-10 02:30:32'),
(141, 'shipper4', 'shipper4@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0379111000', 'https://res.cloudinary.com/dbaeafw6z/image/upload/v1764131072/avatars/user_141.jpg', 6, 'active', '4.00', '', 1, 4, 16, '2025-10-14 05:30:32'),
(157, 'shipper5', 'shipper5@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0379111333', NULL, 6, 'active', NULL, '', 1, 0, 0, '2025-10-14 05:30:32'),
(158, 'shipper6', 'shipper6@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0379111444', NULL, 6, 'active', NULL, '', 1, 0, 0, '2025-10-14 05:30:32'),
(159, 'shipper7', 'shipper7@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0936778998', NULL, 6, 'active', NULL, '', 1, 0, 0, '2025-10-14 05:30:32'),
(160, 'shipper8', 'shipper8@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0983557998', NULL, 6, 'active', NULL, '', 1, 0, 0, '2025-10-14 05:30:32'),
(161, 'shipper9', 'shipper9@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0912345678', NULL, 6, 'active', NULL, '', 1, 0, 0, '2025-10-14 05:30:32'),
(162, 'shipper10', 'shipper10@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0987654321', NULL, 6, 'active', NULL, '', 1, 0, 0, '2025-10-14 05:30:32'),
(163, 'shipper11', 'shipper11@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901122334', NULL, 6, 'active', NULL, '', 1, 0, 0, '2025-10-14 05:30:32'),
(164, 'shipper12', 'shipper12@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0934567890', NULL, 6, 'active', NULL, '', 1, 0, 0, '2025-10-14 05:30:32'),
(165, 'shipper13', 'shipper13@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0961234567', NULL, 6, 'active', NULL, '', 1, 0, 0, '2025-10-14 05:30:32'),
(166, 'shipper14', 'shipper14@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0979876543', NULL, 6, 'active', NULL, '', 1, 0, 0, '2025-10-14 05:30:32'),
(167, 'shipper15', 'shipper15@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0923456789', NULL, 6, 'active', NULL, '', 1, 0, 0, '2025-10-14 05:30:32'),
(168, 'shipper16', 'shipper16@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0941122334', NULL, 6, 'active', NULL, '', 1, 0, 0, '2025-10-14 05:30:32'),
(169, 'shipper17', 'shipper17@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0956789123', NULL, 6, 'active', NULL, '', 1, 0, 0, '2025-10-14 05:30:32'),
(170, 'shipper18', 'shipper18@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0998765432', NULL, 6, 'active', NULL, '', 1, 0, 0, '2025-10-14 05:30:32'),
(171, 'shipper19', 'shipper19@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0909988776', NULL, 6, 'active', NULL, '', 1, 0, 0, '2025-10-14 05:30:32'),
(172, 'shipper20', 'shipper20@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0933322110', NULL, 6, 'active', NULL, '', 1, 0, 0, '2025-10-14 05:30:32'),
(185, 'Nguyễn Khách Hàng', 'khachhangnguyen@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0979730421', NULL, 7, 'active', NULL, '', 1, 0, 0, '2025-10-14 05:30:32'),
(187, 'Trần Khách Hàng', 'tranKH@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0979730422', NULL, 7, 'active', NULL, '', 1, 0, 0, '2025-10-14 05:30:32'),
(188, 'Nguyễn Văn Shipper', 'nvshipper@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0379111555', NULL, 6, 'active', NULL, '', 1, 0, 0, '2025-10-14 09:01:50'),
(189, 'Nguyen Van B Test New', 'guest_1760679602@fake.local', '$2y$10$Boj3qaANVn6tB.s4.l6jLuZwRI1zevbaEpnniDoJFZRyRt9/nuy1G', '0989789021', NULL, 7, 'active', NULL, '', 1, 0, 0, '2025-10-17 05:40:02'),
(190, 'Nguyen Van B Test New 2', 'guest_1760680322@fake.local', '$2y$10$D2GnXU9TeYTFiA.h/kQMHehxG18E7ZN3aKz.qzF6S/Kyh7kSmihf2', '0989789028', NULL, 7, 'active', NULL, '', 1, 0, 0, '2025-10-17 05:52:02'),
(191, 'Nguyễn Văn Ba', 'nguyenba@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0379111666', NULL, 6, 'active', NULL, '', 1, 0, 0, '2025-10-20 16:25:07'),
(192, 'test3210', 'ts321@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0987987909', NULL, 6, 'active', NULL, '', 1, 0, 0, '2025-10-31 02:54:39'),
(194, 'Trong Phat Le', 'trongphat@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0379974903', NULL, 7, 'active', NULL, '', 1, 0, 0, '2025-10-31 03:10:49'),
(196, 'Test Shipper Image2', 'shipperanh@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0379974000', 'https://res.cloudinary.com/dbaeafw6z/image/upload/v1764087923/avatars/nwpsa5c8qwc5m5neddiz.png', 6, 'active', NULL, 'test', 1, 0, 0, '2025-11-25 16:10:06'),
(197, 'testSerc2', 'tsr@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0989777882', 'https://res.cloudinary.com/dbaeafw6z/image/upload/v1764216117/avatars/nesh7mvbnufwxjfvezkg.png', 2, 'active', NULL, 'testS', 1, 0, 0, '2025-11-27 04:00:38');

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
(6, 191, '54Y-66872', 'Honda Vision', 'motorbike', 1),
(7, 192, '49E-30762', 'Honda Vision', 'motorbike', 1),
(9, 196, '36E-12352', 'Honda Wave 110', 'motorbike', 1);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `CustomerID` (`CustomerID`),
  ADD KEY `ShipperID` (`ShipperID`);

--
-- Chỉ mục cho bảng `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_order_rating` (`order_id`),
  ADD KEY `fk_ratings_shipper` (`shipper_id`);

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
  ADD KEY `Role` (`Role`);

--
-- Chỉ mục cho bảng `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `license_plate_unique` (`license_plate`),
  ADD KEY `shipper_id` (`shipper_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2147483648;

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
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=316;

--
-- AUTO_INCREMENT cho bảng `transactions`
--
ALTER TABLE `transactions`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=198;

--
-- AUTO_INCREMENT cho bảng `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `users` (`ID`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`ShipperID`) REFERENCES `users` (`ID`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `fk_ratings_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ratings_shipper` FOREIGN KEY (`shipper_id`) REFERENCES `users` (`ID`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`Role`) REFERENCES `roles` (`ID`);

--
-- Các ràng buộc cho bảng `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_ibfk_1` FOREIGN KEY (`shipper_id`) REFERENCES `users` (`ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
