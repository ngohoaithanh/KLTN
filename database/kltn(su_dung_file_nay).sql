-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 04, 2025 lúc 06:57 PM
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
(9178848, 185, NULL, 'Vinschool, Nguyễn Hữu Cảnh, Bến Nghé, Quận 1, Hồ Chí Minh', '10.7862422', '106.7114781', 'Khoa Cơ Khí - IUH, Đại học Công nghiệp Tp.Hồ Chí Minh, 12 Nguyễn Văn Bảo, Phường 4, Gò Vấp, Hồ Chí Minh', '10.8221072', '106.6879015', 'Tom', 'pending', '0.00', '0.00', NULL, '1.00', '18000.00', '2025-09-17 04:03:20', '2025-10-04 04:33:52', 'Hàng điện tử', '0912345000', 1),
(9182385, 185, NULL, '66 D. Lê Lợi, Phường 1, Gò Vấp, Hồ Chí Minh 700000, Việt Nam', '10.8205291', '106.6863567', '66b Nguyễn Sỹ Sách, Phường 15, Tân Bình, Hồ Chí Minh 70000, Việt Nam', '10.8199509', '106.6358395', 'Nguyễn Lâm', 'pending', '0.00', '0.00', NULL, '1.00', '18000.00', '2025-09-18 10:33:01', NULL, 'Hàng điện tử', '0999888909', 1),
(9186174, 185, NULL, '167/2/5 Ngô Tất Tố, P. 22, Phường 22, Bình Thạnh, Hồ Chí Minh 700000, Việt Nam', '10.7911801', '106.7148782', 'Khoa Cơ Khí - IUH, Đại học Công nghiệp Tp.Hồ Chí Minh, 12 Nguyễn Văn Bảo, Phường 4, Gò Vấp, Hồ Chí Minh', '10.8221072', '106.6879015', 'Trần An', 'pending', '0.00', '0.00', NULL, '2.00', '18000.00', '2025-09-18 10:45:51', '2025-10-04 16:00:44', 'Hàng dễ vỡ', '0912098002', 1),
(9186919, 185, NULL, '144 Xuân Thủy, Dịch Vọng Hậu, Cầu Giấy, Hà Nội', '21.0368282', '105.7820251', '222 Trần Duy Hưng, Cầu Giấy', '21.0069095', '105.7933494', 'Lê Phong', 'pending', '0.00', '0.00', NULL, '1.00', '18000.00', '2025-09-18 13:53:32', NULL, 'Hàng dễ vỡ', '0921876987', 1),
(9221121, 185, 141, 'Trạm ép giấy Xuân Trường, Nguyễn Văn Quỳ, Tân Thuận Đông, Quận 7, Hồ Chí Minh', '10.7429218', '106.7390444', 'Sân Bay Tân Sơn Nhất - Trường Sơn, Cảng hàng không Quốc tế Tân Sơn Nhất, Phường 2, Tân Bình, Hồ Chí Minh', '10.8156395', '106.6638113', 'Lê Anh', 'pending', '0.00', '0.00', NULL, '1.00', '18000.00', '2025-09-21 17:38:24', '2025-10-04 15:29:28', 'Hàng dễ vỡ', '0934999210', 1),
(9229334, 185, NULL, 'Trạm ép giấy Xuân Trường, Nguyễn Văn Quỳ, Tân Thuận Đông, Quận 7, Hồ Chí Minh', '10.7429218', '106.7390444', 'Chợ Thủ Đức B, Đoàn Công Hớn, Trường Thọ, Thủ Đức, Hồ Chí Minh', '10.8502291', '106.7557012', 'Trần Lam', 'pending', '0.00', '0.00', NULL, '2.00', '18000.00', '2025-09-21 17:40:03', '2025-10-04 04:29:10', '', '0924666892', 1),
(10046774, 185, 141, '81 Đ. Võ Duy Ninh, Phường 22, Bình Thạnh, Hồ Chí Minh 90000, Việt Nam', '10.7919236', '106.7159995', 'Nguyễn Văn Bảo/Số 12 ĐH Công Nghiệp, Phường 1, Gò Vấp, Hồ Chí Minh 71408, Việt Nam', '10.8221589', '106.6868454', 'Nguyễn Sa', 'accepted', '0.00', '0.00', NULL, '1.00', '18000.00', '2025-10-04 06:44:46', '2025-10-04 16:31:44', 'Tập tài liệu', '0900000878', 1),
(10046898, 185, 141, 'Katinat, 91 Đồng Khởi, Bến Nghé, Quận 1, Hồ Chí Minh', '10.7747667', '106.7043670', '66B Nguyễn Sỹ Sách, Phường 15, Tân Bình, Hồ Chí Minh', '10.8199447', '106.6358023', 'Lê Lam', 'accepted', '0.00', '0.00', NULL, '0.50', '15000.00', '2025-10-04 04:15:03', '2025-10-04 16:56:15', 'Hàng dễ vỡ', '0909000231', 1);

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
(139, 20.9828611, 105.6960604, NULL, NULL, NULL, 'offline', '2025-10-02 11:14:53'),
(141, 10.7695004, 106.7160039, NULL, NULL, NULL, 'online', '2025-10-04 16:56:57'),
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
(144, 9221121, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-02 11:16:27'),
(145, 9221121, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-02 11:19:44'),
(146, 9229334, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-02 11:19:45'),
(147, 9221121, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-02 11:25:17'),
(148, 9229334, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-02 11:27:40'),
(149, 9221121, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-02 17:22:35'),
(150, 9229334, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-02 17:23:43'),
(151, 9221121, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-02 17:38:31'),
(152, 9229334, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-02 17:38:36'),
(153, 9178848, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-02 17:40:11'),
(154, 9221121, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-02 17:40:13'),
(155, 9186174, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-02 17:41:53'),
(156, 9178848, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-02 17:42:20'),
(157, 9178848, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-02 17:44:27'),
(158, 9186174, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-02 17:44:28'),
(159, 9178848, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-02 17:50:09'),
(160, 9178848, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-02 18:05:40'),
(161, 9186174, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-02 18:07:10'),
(162, 9221121, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 03:52:28'),
(163, 9178848, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 03:56:29'),
(164, 9186174, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 03:58:14'),
(165, 9178848, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 03:58:59'),
(166, 9186174, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 04:05:17'),
(167, 9178848, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 04:08:20'),
(168, 10046898, 'Đơn hàng đã được tạo.', NULL, '2025-10-04 04:15:03'),
(169, 10046898, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 04:21:50'),
(170, 9178848, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 04:27:26'),
(171, 9229334, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 04:29:10'),
(172, 9178848, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 04:32:33'),
(173, 9178848, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 04:33:52'),
(174, 10046898, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 04:34:59'),
(175, 9186174, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 04:36:42'),
(176, 10046898, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 04:38:26'),
(177, 9221121, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 06:41:22'),
(178, 10046774, 'Đơn hàng đã được tạo.', NULL, '2025-10-04 06:44:46'),
(179, 9186174, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 06:45:44'),
(180, 10046898, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 07:39:17'),
(181, 9186174, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 07:42:29'),
(182, 9186174, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 09:57:03'),
(183, 10046774, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 10:21:29'),
(184, 10046774, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 10:24:40'),
(185, 9186174, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 10:36:03'),
(186, 9186174, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 10:39:35'),
(187, 10046774, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 10:44:49'),
(188, 9186174, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 14:33:15'),
(189, 10046774, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 14:41:30'),
(190, 9186174, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 14:46:27'),
(191, 10046774, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 14:48:31'),
(192, 10046774, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 14:51:48'),
(193, 10046898, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 14:53:22'),
(194, 10046774, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 14:54:55'),
(195, 9221121, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 15:29:28'),
(196, 9186174, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 15:32:32'),
(197, 10046774, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 15:33:22'),
(198, 10046898, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 15:34:23'),
(199, 10046774, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 15:41:51'),
(200, 9186174, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 16:00:44'),
(201, 10046774, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 16:18:17'),
(202, 10046898, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 16:30:35'),
(203, 10046774, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 16:31:44'),
(204, 10046898, 'Shipper 141 đã nhận đơn.', NULL, '2025-10-04 16:56:15');

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
(13, 'khachhang1', 'khachhang1@gmail.com', '12345', '0901234512', 7, NULL, '', NULL, 1),
(14, 'khachhang2', 'khachhang2@gmail.com', '12345', '0901234513', 7, NULL, '', NULL, 1),
(57, 'Lê Ngọc Anh', 'dom@gmail.com', '123', '0379974902', 2, NULL, '', NULL, 1),
(75, 'Tom', 'tom@gmail.com', '15de21c670ae7c3f6f3f1f37029303c9', '0979345532', 7, NULL, '', NULL, 1),
(77, 'Dom', 'dom2@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0989777666', 2, NULL, '', NULL, 1),
(100, 'QuanlyKho3', 'quanly3@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0983251777', 4, NULL, 'QL3', 4, 1),
(115, 'Minh An', 'guest_1747204004@fake.local', '$2y$10$UpLgDgkisMiZwLZSg2RLnOk.fG/pf7DPRGZ/prpmuMuTvOqxEZr4q', '0918578624', 7, NULL, '', NULL, 1),
(138, 'Hana', 'guest_1747242397@fake.local', '$2y$10$lz2KUtvHVAKtwbo6KN4wzOYyk7rEh7dlbbrUVg84MP.fJybg3cHgG', '0379974903', 7, NULL, '', NULL, 1),
(139, 'shipper3', 'shipper3@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0379111222', 6, NULL, '', 1, 1),
(141, 'shipper4', 'shipper4@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0379111000', 6, '4.00', '', 1, 1),
(142, 'Kanse', 'guest_1747387724@fake.local', '$2y$10$jzDzJzD8XxMwIw8/Bm43fOdTn/4SHiC6W2iiXeJVaZzmS5gVctNr.', '0983277111', 7, NULL, '', NULL, 1),
(143, 'Thanh Tâm', 'guest_1747388287@fake.local', '827ccb0eea8a706c4c34a16891f84e7b', '0976887662', 7, NULL, '', NULL, 1),
(144, 'Phúc An', 'guest_1747388744@fake.local', '$2y$10$llSpQEwSAUaJ64pGoUijeelrqGTNQPXq22S3ZR9JkP/OYicSGm.x.', '0973672111', 7, NULL, '', NULL, 1),
(145, 'Lê Khánh', 'guest_1747388905@fake.local', '$2y$10$0iP7mKv7LCFLHzjigKU9.uhXJ31JBSEmbqGgBGASps4kFoZV5AlIi', '0983277663', 7, NULL, '', NULL, 1),
(146, 'Như Lan', 'guest_1747407274@fake.local', '$2y$10$S7AR7JE8OZ9IuKQckVUPZeHv7OJFFuhjXivTYeKHcJ9mTGiBIR.9q', '0379974903', 7, NULL, '', NULL, 1),
(147, 'Minh An', 'guest_1747407480@fake.local', '$2y$10$vQApE0djxkJVKiOLh/OOoeSxNuH.jZykJxX2gYxNyp8zJfnAjm722', '0379974903', 7, NULL, '', NULL, 1),
(149, 'Khánh Hân', 'guest_1747558978@fake.local', '$2y$10$mYUbeK.r/TrcDz803YvhFOyBRcYdSEQrYdjApS8cjEHzsVsnAcpGi', '0934222999', 7, NULL, '', NULL, 1),
(150, 'Lê Như', 'guest_1747559548@fake.local', '$2y$10$qVyf1MnQFpqX107f96JEz..cF2qKJv82XxN90F4zkfqHjBxV5N7va', '0987666767', 7, NULL, '', NULL, 1),
(151, 'Hải Phúc', 'guest_1747934992@fake.local', '$2y$10$tXeQFoEy/xuYgao5233kMOb3CK/hoyb5clwQJCRPuRocsRQ4KdYdW', '0379974903', 7, NULL, '', NULL, 1),
(152, 'Trúc Linh', 'guest_1748280386@fake.local', '$2y$10$O0MOne.no1KwE4GdXbQmd.VAL6CqFfx/8sz15nYgnhRdY08jVINWW', '0983777123', 7, NULL, '', NULL, 1),
(153, 'Bảo Phúc', 'guest_1748280539@fake.local', '$2y$10$i9jf7VxsS6JIk97cTHFMj.DR68oUN1ZLY2oyc3YUzicXEHwOJsMgS', '0936123777', 7, NULL, '', NULL, 1),
(154, 'Ngọc Linh', 'guest_1748281492@fake.local', '$2y$10$QQ/MbFoivsXAu4vsbvouce60/HvX6PcPOUS8U/pRXDXG7Aa94Ap6e', '0967888321', 7, NULL, '', NULL, 1),
(155, 'Văn Nghĩa', 'guest_1748314842@fake.local', '$2y$10$VVpiSd.9BcCCmWX9i2cqWeiAeL6aVtqYkNH7wO7E8wCHWrGOzpE1W', '0983145772', 7, NULL, '', NULL, 1),
(156, 'Ngọc Thư', 'guest_1748314990@fake.local', '$2y$10$ku.bnr0zTrHIyACsFSnEvO4oeJddYJg.RIL9cYdT6fEC0tOkzE84i', '0983146772', 7, NULL, '', NULL, 1),
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
(175, 'Nhan vien test', 'nhanvientest@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0987123456', 6, NULL, '', 1, 1),
(176, 'Ngo Hoai Thanh', 'guest_1748346432@fake.local', '$2y$10$Qro619oLJ4mMNNFBN3beFuFYSMbebc8hYZUURZ4F1nsxBXrzevrH2', '0379974903', 7, NULL, '', NULL, 1),
(177, 'tes1', 'guest_1751680549@fake.local', '$2y$10$38z06uh09hxpczYLIareRePVUobEzgEg9cAX6D2lZe/rVZA/HwtX6', '0379974903', 7, NULL, '', NULL, 1),
(178, 'Test 1', 'guest_1754572285@fake.local', '$2y$10$5X.WsMWGKNCV1hPqj0VYE.NZnt1u.KMcagfy7Qwhfd7nRkqED.kka', '0987654321', 7, NULL, '', NULL, 1),
(179, 'Teo', 'guest_1757563799@fake.local', '$2y$10$H91uXRC1GoAHsnbTALEcNOFlAmbHmYCQSqpv32EAIrOt0tC3XkKUK', '0983120666', 7, NULL, '', NULL, 1),
(180, 'Nguyen Van A11', 'guest_1757564518@fake.local', '$2y$10$/ANQM3tARtetFk/vN/c/J.yXkjd2OAdjkAFdH2oAYhIlLX29U.Wta', '0901234567', 7, NULL, '', NULL, 1),
(181, 'Ty2', 'guest_1757578369@fake.local', '$2y$10$74iLNWR.T6Wqc91Hf03yQuqPaCflfITj6IRvqePLkN02h8vmrUGx6', '0934686767', 7, NULL, '', NULL, 1),
(182, 'Thanh2', 'guest_1757581456@fake.local', '$2y$10$gxPyeFy6UnQLugKhUnqoSOz.Xb9XG6U3XUhyiZthEV/otVSGaGxaC', '0932456777', 7, NULL, '', NULL, 1),
(183, 'Nguyen Van A123', 'guest_1757581581@fake.local', '$2y$10$cq2W0Fi31U57uXdwHFH0qeRQwAcXsxp7oDMDEZvD0jQJc.z5igUPO', '0901234567', 7, NULL, '', NULL, 1),
(184, 'Nguyen Van B Test', 'guest_1757581725@fake.local', '$2y$10$1UIJY49qYMzNcCO6jV3FAOT3RWxxkMg55gk62oSLAs4TVZjH1YBBa', '0901234567', 7, NULL, '', NULL, 1),
(185, 'Nguyễn Khách Hàng', 'khachhangnguyen@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0979730421', 7, NULL, '', NULL, 1),
(186, 'Mina', 'guest_1758078873@fake.local', '$2y$10$gSCDDM9NpneAIBOcbY7/PeKjt1yfHolRctllvwBBj96AXvfedH72O', '0987776322', 7, NULL, '', NULL, 1);

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
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=205;

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
-- Các ràng buộc cho bảng `warehouses`
--
ALTER TABLE `warehouses`
  ADD CONSTRAINT `fk_manager_id` FOREIGN KEY (`manager_id`) REFERENCES `users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
