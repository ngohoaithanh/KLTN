-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th9 11, 2025 lúc 08:12 AM
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
-- Cơ sở dữ liệu: `qlgh`
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
(6, 5161592, '200000.00', 'pending', '2025-05-16 14:47:40'),
(7, 5162479, '150000.00', 'pending', '2025-05-26 17:41:45'),
(8, 5185301, '120000.00', 'collected', '2025-05-27 03:20:00'),
(9, 5183559, '120000.00', 'pending', '2025-05-18 09:12:28'),
(10, 5237944, '150000.00', 'pending', '2025-05-26 17:41:41'),
(11, 5275074, '120000.00', 'pending', '2025-05-26 17:26:27'),
(12, 5273122, '200000.00', 'pending', '2025-05-26 17:28:59'),
(13, 5273180, '198000.00', 'pending', '2025-05-26 17:44:52'),
(14, 5278850, '150000.00', 'pending', '2025-05-27 03:00:42'),
(15, 5271702, '200000.00', 'pending', '2025-05-27 03:03:10'),
(16, 5277755, '198000.00', 'settled', '2025-05-27 11:53:10'),
(17, 7057340, '200000.00', 'pending', '2025-07-05 01:55:49'),
(18, 8073892, '199000.00', 'pending', '2025-08-07 13:11:25'),
(19, 9115378, '150000.00', 'pending', '2025-09-11 04:21:58');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `ID` int(11) NOT NULL,
  `CustomerID` int(11) NOT NULL,
  `ShipperID` int(11) DEFAULT NULL,
  `Pick_up_address` varchar(255) NOT NULL,
  `Delivery_address` varchar(255) NOT NULL,
  `Recipient` varchar(100) DEFAULT NULL,
  `Status` enum('pending','received','in_warehouse','out_of_warehouse','in_transit','delivered','delivery_failed','returned','cancelled') DEFAULT 'pending',
  `COD_amount` decimal(10,2) DEFAULT 0.00,
  `WarehouseID` int(11) NOT NULL,
  `Weight` decimal(10,2) DEFAULT NULL,
  `ShippingFee` decimal(10,2) DEFAULT 0.00,
  `Created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `Note` varchar(255) DEFAULT NULL,
  `RecipientPhone` varchar(20) DEFAULT NULL,
  `hidden` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`ID`, `CustomerID`, `ShipperID`, `Pick_up_address`, `Delivery_address`, `Recipient`, `Status`, `COD_amount`, `WarehouseID`, `Weight`, `ShippingFee`, `Created_at`, `Note`, `RecipientPhone`, `hidden`) VALUES
(5161592, 145, NULL, 'Japan', 'Thái Văn Lung, Q1', 'Giang Anh', 'in_warehouse', '120000.00', 1, '1.00', '15000.00', '2025-05-16 09:48:25', '', '0967555444', 1),
(5162479, 147, NULL, 'to 11 ap thanh hoa', '12 Nguyễn Văn Bảo, Phường 1, Gò Vấp, Hồ Chí Minh', 'Ngọc Bình', 'pending', '150000.00', 1, '1.00', '15000.00', '2025-05-16 14:58:00', '', '0903288333', 1),
(5183559, 150, NULL, '3202 Hannah Stree', '12 Nguyễn Văn Bảo, Phường 1, Gò Vấp, Hồ Chí Minh', 'Hoài Ngọc', 'pending', '120000.00', 1, '2.00', '18000.00', '2025-05-18 09:12:28', '', '0978675212', 1),
(5185301, 149, 139, 'B74 Bis Tô Ký, P. Đông Hưng Thuận, Q.12, Ho Chi Minh', '268-270 Phan Xích Long, Phường 7, Phú Nhuận, Hồ Chí Minh', 'Lê Bảo', 'delivered', '120000.00', 1, '1.00', '15000.00', '2025-05-18 09:02:58', '', '0976555721', 1),
(5237944, 151, NULL, 'to 11 ap thanh hoa', '12 Nguyễn Văn Bảo, Phường 1, Gò Vấp, Hồ Chí Minh', 'Lâm Ngọc', 'pending', '150000.00', 1, '2.00', '18000.00', '2025-05-22 17:29:52', '', '0974678214', 1),
(5271702, 156, NULL, 'Đường Tân Kỳ Tân Quý, Quận Tân Phú, Hồ Chí Minh', 'đường cách mạng tháng 8, phường 11, quận 3, TP HCM', 'Lâm Minh', 'pending', '200000.00', 4, '2.00', '18000.00', '2025-05-27 03:03:10', '', '0943226668', 1),
(5273122, 153, NULL, 'Phạm Văn Đồng, Q. Bình Thạnh, Tp. Hồ Chí Minh', 'Đ. Thái Văn Lung, Phường Bến Nghé, Quận 1, Hồ Chí Minh', 'Vũ Khánh Tâm', 'in_warehouse', '200000.00', 2, '2.00', '18000.00', '2025-05-26 17:28:59', '', '0934777888', 1),
(5273180, 154, NULL, 'Hóc Môn, TP Hồ Chí Minh', 'Bình Quới, Bình Thạnh', 'Vũ Hải', 'pending', '198000.00', 4, '1.00', '18000.00', '2025-05-26 17:44:52', '', '0932667812', 1),
(5275074, 152, NULL, '238/3/8 Đông Thạnh, Hóc Môn, TP Hồ Chí Minh', '280 Đ. Cộng Hòa, Phường 13, Tân Bình, Hồ Chí Minh', 'Thu Thảo', 'pending', '120000.00', 2, '1.00', '18000.00', '2025-05-26 17:26:26', '', '09437772123', 1),
(5277755, 176, 11, 'to 11 ap thanh hoa', '12 Nguyễn Văn Bảo, Phường 1, Gò Vấp, Hồ Chí Minh', 'Dc Alvin', 'delivered', '198000.00', 1, '2.00', '18000.00', '2025-05-27 11:47:12', '', '0338322433', 1),
(5278850, 155, NULL, '206 Đ. 3 Tháng 2,  Quận 10, TP Hồ Chí Minh', 'Đường Lê Thánh Tôn, Bến Nghé, Quận 1, Hồ Chí Minh', 'Hải Như', 'pending', '150000.00', 2, '1.00', '18000.00', '2025-05-27 03:00:42', '', '0934889668', 1),
(7057340, 177, NULL, 'to 11 ap thanh hoa', 'Binh long', 'tes2', 'pending', '200000.00', 1, '1.00', '18000.00', '2025-07-05 01:55:49', '', '0987333411', 0),
(8073892, 178, 141, 'Tp Ho Chi Minh', 'Ha Noi', 'Nguyen Van A', 'out_of_warehouse', '199000.00', 1, '1.00', '18000.00', '2025-08-07 13:11:25', '', '0967888999', 1),
(9111043, 179, NULL, 'Tan Binh', 'Go Vap', 'Ti', 'pending', '0.00', 1, '1.00', '18000.00', '2025-09-11 04:09:59', '', '0983221999', 0),
(9115378, 180, NULL, '12 Nguyen Van Bao, Go Vap, TP.HCM', '35 Hai Ba Trung, Quan 1, TP.HCM', 'Tran Thi B', 'pending', '150000.00', 1, '1.20', '18000.00', '2025-09-11 04:21:57', 'Giao trong giờ hành chính', '0912345678', 1);

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

--
-- Đang đổ dữ liệu cho bảng `order_warehouse_tracking`
--

INSERT INTO `order_warehouse_tracking` (`ID`, `OrderID`, `WarehouseID`, `Handled_by`, `ActionType`, `Timestamp`, `Note`) VALUES
(67, 5161592, 1, 1, 'import', '2025-05-27 00:49:04', NULL),
(68, 5273122, 2, 1, 'import', '2025-05-27 00:49:17', NULL),
(69, 5185301, 1, 7, 'import', '2025-05-27 10:17:06', NULL),
(70, 5185301, 1, 7, 'export', '2025-05-27 10:17:18', NULL),
(71, 5277755, 1, 1, 'import', '2025-05-27 18:48:39', NULL),
(72, 5277755, 1, 1, 'export', '2025-05-27 18:49:45', NULL),
(73, 8073892, 1, 1, 'import', '2025-08-07 20:14:05', NULL),
(74, 8073892, 1, 1, 'export', '2025-08-07 20:14:42', NULL);

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
(55, 5161592, 'Đơn hàng đã được tạo.', NULL, '2025-05-16 09:48:25'),
(57, 5162479, 'Đơn hàng đã được tạo.', NULL, '2025-05-16 14:58:00'),
(63, 5162479, 'Đơn hàng đã được tiếp nhận', NULL, '2025-05-16 15:43:01'),
(73, 5185301, 'Đơn hàng đã được tiếp nhận', NULL, '2025-05-18 09:03:59'),
(79, 5183559, 'Đơn hàng đã được tạo.', NULL, '2025-05-18 09:12:28'),
(80, 5183559, 'Đơn hàng đã được tiếp nhận', NULL, '2025-05-18 09:12:36'),
(90, 5237944, 'Đơn hàng đã được tạo.', NULL, '2025-05-22 17:29:52'),
(91, 5237944, 'Đơn hàng đã được tiếp nhận', NULL, '2025-05-22 17:30:11'),
(96, 5275074, 'Đơn hàng đã được tạo.', NULL, '2025-05-26 17:26:26'),
(97, 5161592, 'Đơn hàng đã được tiếp nhận', NULL, '2025-05-26 17:27:27'),
(99, 5273122, 'Đơn hàng đã được tạo.', NULL, '2025-05-26 17:28:59'),
(100, 5273180, 'Đơn hàng đã được tạo.', NULL, '2025-05-26 17:44:52'),
(101, 5161592, 'Đơn hàng đã được tiếp nhận', NULL, '2025-05-26 17:49:04'),
(102, 5161592, 'Đơn hàng đã được nhập kho', NULL, '2025-05-26 17:49:09'),
(103, 5273122, 'Đơn hàng đã được tiếp nhận', NULL, '2025-05-26 17:49:17'),
(104, 5273122, 'Đơn hàng đã được nhập kho', NULL, '2025-05-26 17:49:22'),
(105, 5278850, 'Đơn hàng đã được tạo.', NULL, '2025-05-27 03:00:42'),
(106, 5271702, 'Đơn hàng đã được tạo.', NULL, '2025-05-27 03:03:10'),
(107, 5185301, 'Đơn hàng đã được tiếp nhận', NULL, '2025-05-27 03:17:06'),
(108, 5185301, 'Đơn hàng đã được nhập kho', NULL, '2025-05-27 03:17:11'),
(109, 5185301, 'Đơn hàng đã được xuất kho và đang được sắp xếp shipper giao hàng', NULL, '2025-05-27 03:17:18'),
(110, 5185301, 'Đơn hàng đang trên đường giao, vui lòng chú ý điện thoại', NULL, '2025-05-27 03:18:18'),
(111, 5185301, 'Đơn hàng đã giao thành công', NULL, '2025-05-27 03:20:00'),
(112, 5277755, 'Đơn hàng đã được tạo.', NULL, '2025-05-27 11:47:12'),
(113, 5277755, 'Đơn hàng đã được tiếp nhận', NULL, '2025-05-27 11:48:39'),
(114, 5277755, 'Đơn hàng đã được nhập kho', NULL, '2025-05-27 11:48:44'),
(115, 5277755, 'Đơn hàng đã được xuất kho và đang được sắp xếp shipper giao hàng', NULL, '2025-05-27 11:49:45'),
(116, 5277755, 'Đơn hàng đang trên đường giao, vui lòng chú ý điện thoại', NULL, '2025-05-27 11:50:45'),
(117, 5277755, 'Đơn hàng đã giao thành công', NULL, '2025-05-27 11:51:19'),
(118, 7057340, 'Đơn hàng đã được tạo.', NULL, '2025-07-05 01:55:49'),
(119, 8073892, 'Đơn hàng đã được tạo.', NULL, '2025-08-07 13:11:25'),
(120, 8073892, 'Đơn hàng đã được tiếp nhận', NULL, '2025-08-07 13:14:05'),
(121, 8073892, 'Đơn hàng đã được nhập kho', NULL, '2025-08-07 13:14:10'),
(122, 8073892, 'Đơn hàng đã được xuất kho và đang được sắp xếp shipper giao hàng', NULL, '2025-08-07 13:14:42'),
(123, 8073892, 'Đơn hàng đang trên đường giao, vui lòng chú ý điện thoại', NULL, '2025-08-07 13:15:42'),
(124, 9111043, 'Đơn hàng đã được tạo.', NULL, '2025-09-11 04:09:59'),
(125, 9115378, 'Đơn hàng đã được tạo.', NULL, '2025-09-11 04:21:57');

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
(12, 5185301, 139, 'collect_cod', '120000.00', 'completed', NULL, '2025-05-27 03:20:00'),
(13, 5277755, 11, 'collect_cod', '198000.00', 'completed', NULL, '2025-05-27 11:51:19'),
(14, 5277755, 1, 'pay_cod', '198000.00', 'completed', NULL, '2025-05-27 11:53:10');

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
  `Note` varchar(255) NOT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `hidden` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`ID`, `Username`, `Email`, `Password`, `PhoneNumber`, `Role`, `Note`, `warehouse_id`, `hidden`) VALUES
(1, 'admin1', 'admin1@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234500', 1, 'pass-12345', NULL, 1),
(2, 'admin2', 'admin2@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234501', 1, '', NULL, 1),
(3, 'quanly1', 'quanly1@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234502', 2, '', NULL, 1),
(4, 'quanly2', 'quanly2@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234503', 2, '', NULL, 1),
(5, 'nhanvientiepnhan1', 'nhanvientiepnhan1@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234504', 3, '', 1, 1),
(6, 'nhanvientiepnhan2', 'nhanvientiepnhan2@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234505', 3, '', NULL, 1),
(7, 'quanlykho1', 'quanlykho1@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234506', 4, '', 1, 1),
(8, 'quanlykho2', 'quanlykho2@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234507', 4, '', NULL, 1),
(9, 'ketoan1', 'ketoan1@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234508', 5, '', NULL, 1),
(10, 'ketoan2', 'ketoan2@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234509', 5, '', NULL, 1),
(11, 'shipper1', 'shipper1@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234510', 6, 'Go Vap-Binh Thanh', 1, 1),
(12, 'shipper2', 'shipper2@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901234511', 6, 'Cu Chi - Hoc Mon', 1, 1),
(13, 'khachhang1', 'khachhang1@gmail.com', '12345', '0901234512', 7, '', NULL, 1),
(14, 'khachhang2', 'khachhang2@gmail.com', '12345', '0901234513', 7, '', NULL, 1),
(57, 'Lê Ngọc Anh', 'dom@gmail.com', '123', '0379974902', 2, '', NULL, 1),
(73, 'Junior', 'thanhhuykks1403@gmail.com', '202cb962ac59075b964b07152d234b70', '0379974903', 7, 'pass-123', NULL, 1),
(75, 'Tom', 'tom@gmail.com', '15de21c670ae7c3f6f3f1f37029303c9', '0979345532', 7, '', NULL, 1),
(77, 'Dom', 'dom2@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0989777666', 2, '', NULL, 1),
(100, 'QuanlyKho3', 'quanly3@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0983251777', 4, 'QL3', 4, 1),
(115, 'Minh An', 'guest_1747204004@fake.local', '$2y$10$UpLgDgkisMiZwLZSg2RLnOk.fG/pf7DPRGZ/prpmuMuTvOqxEZr4q', '0918578624', 7, '', NULL, 1),
(138, 'Hana', 'guest_1747242397@fake.local', '$2y$10$lz2KUtvHVAKtwbo6KN4wzOYyk7rEh7dlbbrUVg84MP.fJybg3cHgG', '0379974903', 7, '', NULL, 1),
(139, 'shipper3', 'shipper3@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0983129887', 6, '', 1, 1),
(141, 'shipper4', 'shipper4@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0983128777', 6, '', 1, 1),
(142, 'Kanse', 'guest_1747387724@fake.local', '$2y$10$jzDzJzD8XxMwIw8/Bm43fOdTn/4SHiC6W2iiXeJVaZzmS5gVctNr.', '0983277111', 7, '', NULL, 1),
(143, 'Thanh Tâm', 'guest_1747388287@fake.local', '$2y$10$Z0P3ddVLMdc0rKfHQ9oac.a6rUlYAnMxlKDDP5kw/ER5jMRXt.fVe', '0976887662', 7, '', NULL, 1),
(144, 'Phúc An', 'guest_1747388744@fake.local', '$2y$10$llSpQEwSAUaJ64pGoUijeelrqGTNQPXq22S3ZR9JkP/OYicSGm.x.', '0973672111', 7, '', NULL, 1),
(145, 'Lê Khánh', 'guest_1747388905@fake.local', '$2y$10$0iP7mKv7LCFLHzjigKU9.uhXJ31JBSEmbqGgBGASps4kFoZV5AlIi', '0983277663', 7, '', NULL, 1),
(146, 'Như Lan', 'guest_1747407274@fake.local', '$2y$10$S7AR7JE8OZ9IuKQckVUPZeHv7OJFFuhjXivTYeKHcJ9mTGiBIR.9q', '0379974903', 7, '', NULL, 1),
(147, 'Minh An', 'guest_1747407480@fake.local', '$2y$10$vQApE0djxkJVKiOLh/OOoeSxNuH.jZykJxX2gYxNyp8zJfnAjm722', '0379974903', 7, '', NULL, 1),
(149, 'Khánh Hân', 'guest_1747558978@fake.local', '$2y$10$mYUbeK.r/TrcDz803YvhFOyBRcYdSEQrYdjApS8cjEHzsVsnAcpGi', '0934222999', 7, '', NULL, 1),
(150, 'Lê Như', 'guest_1747559548@fake.local', '$2y$10$qVyf1MnQFpqX107f96JEz..cF2qKJv82XxN90F4zkfqHjBxV5N7va', '0987666767', 7, '', NULL, 1),
(151, 'Hải Phúc', 'guest_1747934992@fake.local', '$2y$10$tXeQFoEy/xuYgao5233kMOb3CK/hoyb5clwQJCRPuRocsRQ4KdYdW', '0379974903', 7, '', NULL, 1),
(152, 'Trúc Linh', 'guest_1748280386@fake.local', '$2y$10$O0MOne.no1KwE4GdXbQmd.VAL6CqFfx/8sz15nYgnhRdY08jVINWW', '0983777123', 7, '', NULL, 1),
(153, 'Bảo Phúc', 'guest_1748280539@fake.local', '$2y$10$i9jf7VxsS6JIk97cTHFMj.DR68oUN1ZLY2oyc3YUzicXEHwOJsMgS', '0936123777', 7, '', NULL, 1),
(154, 'Ngọc Linh', 'guest_1748281492@fake.local', '$2y$10$QQ/MbFoivsXAu4vsbvouce60/HvX6PcPOUS8U/pRXDXG7Aa94Ap6e', '0967888321', 7, '', NULL, 1),
(155, 'Văn Nghĩa', 'guest_1748314842@fake.local', '$2y$10$VVpiSd.9BcCCmWX9i2cqWeiAeL6aVtqYkNH7wO7E8wCHWrGOzpE1W', '0983145772', 7, '', NULL, 1),
(156, 'Ngọc Thư', 'guest_1748314990@fake.local', '$2y$10$ku.bnr0zTrHIyACsFSnEvO4oeJddYJg.RIL9cYdT6fEC0tOkzE84i', '0983146772', 7, '', NULL, 1),
(157, 'shipper5', 'shipper5@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0983167552', 6, '', 2, 1),
(158, 'shipper6', 'shipper6@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0974665338', 6, '', 2, 1),
(159, 'shipper7', 'shipper7@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0936778998', 6, '', 2, 1),
(160, 'shipper8', 'shipper8@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0983557998', 6, '', 2, 1),
(161, 'shipper9', 'shipper9@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0912345678', 6, '', 4, 1),
(162, 'shipper10', 'shipper10@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0987654321', 6, '', 4, 1),
(163, 'shipper11', 'shipper11@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0901122334', 6, '', 4, 1),
(164, 'shipper12', 'shipper12@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0934567890', 6, '', 4, 1),
(165, 'shipper13', 'shipper13@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0961234567', 6, '', 5, 1),
(166, 'shipper14', 'shipper14@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0979876543', 6, '', 5, 1),
(167, 'shipper15', 'shipper15@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0923456789', 6, '', 5, 1),
(168, 'shipper16', 'shipper16@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0941122334', 6, '', 5, 1),
(169, 'shipper17', 'shipper17@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0956789123', 6, '', 3, 1),
(170, 'shipper18', 'shipper18@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0998765432', 6, '', 3, 1),
(171, 'shipper19', 'shipper19@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0909988776', 6, '', 3, 1),
(172, 'shipper20', 'shipper20@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0933322110', 6, '', 3, 1),
(175, 'Nhan vien test', 'nhanvientest@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '0987123456', 6, '', 1, 1),
(176, 'Ngo Hoai Thanh', 'guest_1748346432@fake.local', '$2y$10$Qro619oLJ4mMNNFBN3beFuFYSMbebc8hYZUURZ4F1nsxBXrzevrH2', '0379974903', 7, '', NULL, 1),
(177, 'tes1', 'guest_1751680549@fake.local', '$2y$10$38z06uh09hxpczYLIareRePVUobEzgEg9cAX6D2lZe/rVZA/HwtX6', '0379974903', 7, '', NULL, 1),
(178, 'Test 1', 'guest_1754572285@fake.local', '$2y$10$5X.WsMWGKNCV1hPqj0VYE.NZnt1u.KMcagfy7Qwhfd7nRkqED.kka', '0987654321', 7, '', NULL, 1),
(179, 'Teo', 'guest_1757563799@fake.local', '$2y$10$H91uXRC1GoAHsnbTALEcNOFlAmbHmYCQSqpv32EAIrOt0tC3XkKUK', '0983120666', 7, '', NULL, 1),
(180, 'Nguyen Van A11', 'guest_1757564518@fake.local', '$2y$10$/ANQM3tARtetFk/vN/c/J.yXkjd2OAdjkAFdH2oAYhIlLX29U.Wta', '0901234567', 7, '', NULL, 1);

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
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT cho bảng `transactions`
--
ALTER TABLE `transactions`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=181;

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
