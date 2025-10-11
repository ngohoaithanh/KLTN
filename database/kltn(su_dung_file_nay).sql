-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 11, 2025 lúc 06:31 PM
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
  `hidden` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`ID`, `CustomerID`, `ShipperID`, `Pick_up_address`, `Pick_up_lat`, `Pick_up_lng`, `Delivery_address`, `Delivery_lat`, `Delivery_lng`, `Recipient`, `status`, `COD_amount`, `CODFee`, `WarehouseID`, `Weight`, `ShippingFee`, `Created_at`, `Accepted_at`, `Note`, `RecipientPhone`, `hidden`) VALUES
(9175208, 185, NULL, 'Khoa Cơ Khí - IUH, Đại học Công nghiệp Tp.Hồ Chí Minh, 12 Nguyễn Văn Bảo, Phường 4, Gò Vấp, Hồ Chí Minh', '10.8221072', '106.6879015', 'Vinhomes Grand Park, Long Bình, Thủ Đức, Hồ Chí Minh', '10.8429630', '106.8407200', 'Zaa', 'pending', '0.00', '0.00', NULL, '1.20', '18000.00', '2025-09-17 04:53:35', NULL, 'Hàng dễ vỡ', '0998998999', 1),
(9178848, 185, 139, 'Vinschool, Nguyễn Hữu Cảnh, Bến Nghé, Quận 1, Hồ Chí Minh', '10.7862422', '106.7114781', 'Khoa Cơ Khí - IUH, Đại học Công nghiệp Tp.Hồ Chí Minh, 12 Nguyễn Văn Bảo, Phường 4, Gò Vấp, Hồ Chí Minh', '10.8221072', '106.6879015', 'Tom', 'delivery_failed', '0.00', '0.00', NULL, '1.00', '18000.00', '2025-09-17 04:03:20', '2025-10-09 04:07:35', 'Hàng điện tử', '0912345000', 1),
(9182385, 185, NULL, '66 D. Lê Lợi, Phường 1, Gò Vấp, Hồ Chí Minh 700000, Việt Nam', '10.8205291', '106.6863567', '66b Nguyễn Sỹ Sách, Phường 15, Tân Bình, Hồ Chí Minh 70000, Việt Nam', '10.8199509', '106.6358395', 'Nguyễn Lâm', 'pending', '0.00', '0.00', NULL, '1.00', '18000.00', '2025-09-18 10:33:01', NULL, 'Hàng điện tử', '0999888909', 1),
(9186174, 185, 141, '167/2/5 Ngô Tất Tố, P. 22, Phường 22, Bình Thạnh, Hồ Chí Minh 700000, Việt Nam', '10.7911801', '106.7148782', 'Khoa Cơ Khí - IUH, Đại học Công nghiệp Tp.Hồ Chí Minh, 12 Nguyễn Văn Bảo, Phường 4, Gò Vấp, Hồ Chí Minh', '10.8221072', '106.6879015', 'Trần An', 'accepted', '0.00', '0.00', NULL, '2.00', '18000.00', '2025-09-18 10:45:51', '2025-10-11 10:28:12', 'Hàng dễ vỡ', '0912098002', 1),
(9186919, 185, NULL, '144 Xuân Thủy, Dịch Vọng Hậu, Cầu Giấy, Hà Nội', '21.0368282', '105.7820251', '222 Trần Duy Hưng, Cầu Giấy', '21.0069095', '105.7933494', 'Lê Phong', 'pending', '0.00', '0.00', NULL, '1.00', '18000.00', '2025-09-18 13:53:32', NULL, 'Hàng dễ vỡ', '0921876987', 1),
(9221121, 185, 141, 'Trạm ép giấy Xuân Trường, Nguyễn Văn Quỳ, Tân Thuận Đông, Quận 7, Hồ Chí Minh', '10.7429218', '106.7390444', 'Sân Bay Tân Sơn Nhất - Trường Sơn, Cảng hàng không Quốc tế Tân Sơn Nhất, Phường 2, Tân Bình, Hồ Chí Minh', '10.8156395', '106.6638113', 'Lê Anh', 'delivered', '0.00', '0.00', NULL, '1.00', '18000.00', '2025-09-21 17:38:24', '2025-10-11 09:43:24', 'Hàng dễ vỡ', '0934999210', 1),
(9229334, 185, NULL, 'Trạm ép giấy Xuân Trường, Nguyễn Văn Quỳ, Tân Thuận Đông, Quận 7, Hồ Chí Minh', '10.7429218', '106.7390444', 'Chợ Thủ Đức B, Đoàn Công Hớn, Trường Thọ, Thủ Đức, Hồ Chí Minh', '10.8502291', '106.7557012', 'Trần Lam', 'pending', '0.00', '0.00', NULL, '2.00', '18000.00', '2025-09-21 17:40:03', '2025-10-04 04:29:10', '', '0924666892', 1),
(10046774, 185, NULL, '81 Đ. Võ Duy Ninh, Phường 22, Bình Thạnh, Hồ Chí Minh 90000, Việt Nam', '10.7919236', '106.7159995', 'Nguyễn Văn Bảo/Số 12 ĐH Công Nghiệp, Phường 1, Gò Vấp, Hồ Chí Minh 71408, Việt Nam', '10.8221589', '106.6868454', 'Nguyễn Sa', 'pending', '0.00', '0.00', NULL, '1.00', '18000.00', '2025-10-04 06:44:46', '2025-10-04 16:31:44', 'Tập tài liệu', '0900000878', 1),
(10046898, 185, 141, 'Katinat, 91 Đồng Khởi, Bến Nghé, Quận 1, Hồ Chí Minh', '10.7747667', '106.7043670', '66B Nguyễn Sỹ Sách, Phường 15, Tân Bình, Hồ Chí Minh', '10.8199447', '106.6358023', 'Lê Lam', 'delivered', '0.00', '0.00', NULL, '0.50', '15000.00', '2025-10-04 04:15:03', '2025-10-11 02:57:25', 'Hàng dễ vỡ', '0909000231', 1),
(10067527, 185, NULL, 'Katinat Phan Văn Trị, 18A Đ. Phan Văn Trị, Phường 1, Gò Vấp, Hồ Chí Minh, Việt Nam', NULL, NULL, 'Cheese Coffee, 190C Đ. Phan Văn Trị, Phường 14, Bình Thạnh, Hồ Chí Minh, Việt Nam', NULL, NULL, 'Nguyen Bao', 'pending', '0.00', '0.00', NULL, '0.30', '15000.00', '2025-10-06 01:14:57', NULL, 'Tai lieu', '0989878465', 1);

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
(139, 10.7693005, 106.7160034, NULL, NULL, NULL, 'online', '2025-10-09 04:08:52'),
(141, 10.7980003, 106.7050018, NULL, NULL, NULL, 'online', '2025-10-11 10:32:06'),
(157, 20.9828608, 105.6960607, NULL, NULL, NULL, 'offline', '2025-10-02 11:14:58');

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
(231, 9186174, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-11 10:28:12');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `transactions`
--

CREATE TABLE `transactions` (
  `ID` int(11) NOT NULL,
  `OrderID` int(11) DEFAULT NULL,
  `UserID` int(11) NOT NULL,
  `Type` enum('collect_cod','pay_cod','salary','shipping_fee','other') NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `Status` enum('pending','completed','failed') DEFAULT 'pending',
  `Note` varchar(255) DEFAULT NULL,
  `Created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `transactions`
--

INSERT INTO `transactions` (`ID`, `OrderID`, `UserID`, `Type`, `Amount`, `Status`, `Note`, `Created_at`) VALUES
(12, NULL, 139, 'collect_cod', '120000.00', 'completed', NULL, '2025-05-27 03:20:00'),
(13, NULL, 11, 'collect_cod', '198000.00', 'completed', NULL, '2025-05-27 11:51:19'),
(14, NULL, 1, 'pay_cod', '198000.00', 'completed', NULL, '2025-05-27 11:53:10');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `PhoneNumber` varchar(255) DEFAULT NULL,
  `Role` int(11) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT NULL CHECK (`rating` between 0.00 and 5.00),
  `Note` varchar(255) NOT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `hidden` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`ID`, `Username`, `Email`, `Password`, `PhoneNumber`, `Role`, `rating`, `Note`, `warehouse_id`, `hidden`) VALUES
(1, 'admin1', 'admin1@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0379666888', 1, NULL, 'pass-12345', NULL, 1),
(2, 'admin2', 'admin2@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234501', 1, NULL, '', NULL, 1),
(3, 'quanly1', 'quanly1@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234502', 2, NULL, '', NULL, 1),
(4, 'quanly2', 'quanly2@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234503', 2, NULL, '', NULL, 1),
(5, 'nhanvientiepnhan1', 'nhanvientiepnhan1@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234504', 3, NULL, '', 1, 1),
(6, 'nhanvientiepnhan2', 'nhanvientiepnhan2@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234505', 3, NULL, '', NULL, 1),
(7, 'quanlykho1', 'quanlykho1@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234506', 4, NULL, '', 1, 1),
(8, 'quanlykho2', 'quanlykho2@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234507', 4, NULL, '', NULL, 1),
(9, 'ketoan1', 'ketoan1@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234508', 5, NULL, '', NULL, 1),
(10, 'ketoan2', 'ketoan2@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234509', 5, NULL, '', NULL, 1),
(11, 'shipper1', 'shipper1@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234510', 6, NULL, 'Go Vap-Binh Thanh', 1, 1),
(12, 'shipper2', 'shipper2@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234511', 6, NULL, 'Cu Chi - Hoc Mon', 1, 1),
(75, 'Tom', 'tom@gmail.com', '15de21c670ae7c3f6f3f1f37029303c9', '0979345532', 7, NULL, '', NULL, 1),
(77, 'Dom', 'dom2@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0989777666', 2, NULL, '', NULL, 1),
(100, 'QuanlyKho3', 'quanly3@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0983251777', 4, NULL, 'QL3', 4, 1),
(139, 'shipper3', 'shipper3@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0379111222', 6, '4.50', '', 1, 1),
(141, 'shipper4', 'shipper4@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0379111000', 6, '4.50', '', 1, 1),
(157, 'shipper5', 'shipper5@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0983167552', 6, NULL, '', 2, 1),
(158, 'shipper6', 'shipper6@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0974665338', 6, NULL, '', 2, 1),
(159, 'shipper7', 'shipper7@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0936778998', 6, NULL, '', 2, 1),
(160, 'shipper8', 'shipper8@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0983557998', 6, NULL, '', 2, 1),
(161, 'shipper9', 'shipper9@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0912345678', 6, NULL, '', 4, 1),
(162, 'shipper10', 'shipper10@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0987654321', 6, NULL, '', 4, 1),
(163, 'shipper11', 'shipper11@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901122334', 6, NULL, '', 4, 1),
(164, 'shipper12', 'shipper12@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0934567890', 6, NULL, '', 4, 1),
(165, 'shipper13', 'shipper13@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0961234567', 6, NULL, '', 5, 1),
(166, 'shipper14', 'shipper14@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0979876543', 6, NULL, '', 5, 1),
(167, 'shipper15', 'shipper15@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0923456789', 6, NULL, '', 5, 1),
(168, 'shipper16', 'shipper16@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0941122334', 6, NULL, '', 5, 1),
(169, 'shipper17', 'shipper17@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0956789123', 6, NULL, '', 3, 1),
(170, 'shipper18', 'shipper18@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0998765432', 6, NULL, '', 3, 1),
(171, 'shipper19', 'shipper19@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0909988776', 6, NULL, '', 3, 1),
(172, 'shipper20', 'shipper20@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0933322110', 6, NULL, '', 3, 1),
(185, 'Nguyễn Khách Hàng', 'khachhangnguyen@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0979730421', 7, NULL, '', NULL, 1);

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
(1, 141, '93E-30690', 'Yamaha Sirius', 'motorbike', 1),
(2, 139, '59E-04963', 'Wave RSX', 'motorbike', 1);

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
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

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
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `trackings`
--
ALTER TABLE `trackings`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=232;

--
-- AUTO_INCREMENT cho bảng `transactions`
--
ALTER TABLE `transactions`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=187;

--
-- AUTO_INCREMENT cho bảng `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
