<?php
// FILE: api/user/update_user.php (PHIÊN BẢN HOÀN CHỈNH & TỐI ƯU)
header('Content-Type: application/json; charset=utf-8');
include_once('../../config/database.php');

$db = new clsKetNoi();
$conn = $db->moKetNoi();

// 1. Kiểm tra phương thức
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Phương thức không hợp lệ']);
    $db->dongKetNoi($conn);
    exit;
}

// 2. Lấy và làm sạch dữ liệu
// Các trường bắt buộc
if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo json_encode(['success' => false, 'error' => 'Thiếu ID người dùng']);
    exit;
}

$id       = intval($_POST['id']);
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$phone    = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$email    = isset($_POST['email']) ? trim($_POST['email']) : '';
$role     = isset($_POST['role']) ? intval($_POST['role']) : 0;

// Các trường tùy chọn (có thể rỗng)
$note         = isset($_POST['note']) ? trim($_POST['note']) : '';
$avatar_url   = isset($_POST['avatar_url']) ? trim($_POST['avatar_url']) : null;
$password     = isset($_POST['password']) ? trim($_POST['password']) : '';

// 3. Validate dữ liệu cơ bản
if (empty($username) || empty($phone) || empty($email) || $role <= 0) {
    echo json_encode(['success' => false, 'error' => 'Vui lòng điền đầy đủ các trường bắt buộc']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Email không hợp lệ']);
    exit;
}

// 4. Kiểm tra Email trùng lặp (trừ chính user này ra)
$stmt_check = $conn->prepare("SELECT ID FROM users WHERE Email = ? AND ID != ?");
$stmt_check->bind_param("si", $email, $id);
$stmt_check->execute();
$stmt_check->store_result();
if ($stmt_check->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'Email này đã được sử dụng bởi người khác!']);
    $stmt_check->close();
    exit;
}
$stmt_check->close();

// Bắt đầu Transaction để đảm bảo cả User và Xe đều được cập nhật hoặc không cái nào cả
$conn->begin_transaction();

try {
    // 5. Xây dựng câu truy vấn Cập nhật User động
    // (Chỉ cập nhật Password nếu người dùng có nhập)
    
    $sql = "UPDATE users SET Username=?, Email=?, PhoneNumber=?, Role=?, Note=?,  Avatar=?";
    $types = "sssiss";
    $params = [$username, $email, $phone, $role, $note, $avatar_url];

    if (!empty($password)) {
        $sql .= ", Password=?";
        $types .= "s";
        $params[] = md5($password); // Mã hóa MD5 (theo hệ thống cũ của bạn)
    }

    $sql .= " WHERE ID=?";
    $types .= "i";
    $params[] = $id;

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params); // Sử dụng toán tử splat (...) để unpack mảng

    if (!$stmt->execute()) {
        throw new Exception("Lỗi cập nhật User: " . $stmt->error);
    }
    $stmt->close();

    // 6. Xử lý thông tin xe (Chỉ nếu là Shipper - Role 6)
    if ($role == 6 && isset($_POST['license_plate'])) {
        $license = trim($_POST['license_plate']);
        $model = isset($_POST['vehicle_model']) ? trim($_POST['vehicle_model']) : '';

        // Kiểm tra xem shipper này đã có xe chưa
        $check_veh = $conn->query("SELECT id FROM vehicles WHERE shipper_id = $id");
        
        if ($check_veh->num_rows > 0) {
            // Đã có -> UPDATE
            $stmt_veh = $conn->prepare("UPDATE vehicles SET license_plate=?, model=? WHERE shipper_id=?");
            $stmt_veh->bind_param("ssi", $license, $model, $id);
        } else {
            // Chưa có -> INSERT
            $stmt_veh = $conn->prepare("INSERT INTO vehicles (shipper_id, license_plate, model, is_active) VALUES (?, ?, ?, 1)");
            $stmt_veh->bind_param("iss", $id, $license, $model);
        }

        if (!$stmt_veh->execute()) {
             throw new Exception("Lỗi cập nhật Xe: " . $stmt_veh->error);
        }
        $stmt_veh->close();
    }

    // Nếu mọi thứ ok, Commit giao dịch
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Cập nhật thành công']);

} catch (Exception $e) {
    // Nếu có lỗi, Rollback (hoàn tác) mọi thay đổi
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$db->dongKetNoi($conn);
?>