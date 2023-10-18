<?php
// Xác định thư mục để lưu trữ tập tin tải lên
$targetDir = "./uploads/";

// Lấy tên tập tin và đường dẫn tạm thời
$fileName = $_FILES["upload"]["name"];
$tempFilePath = $_FILES["upload"]["tmp_name"];

// Xác định đường dẫn đầy đủ của tập tin đích
$targetPath = $targetDir . $fileName;

// Lấy phần mở rộng của tập tin
$fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

// Kiểm tra nếu là tập tin hợp lệ (chỉ png hoặc jpg)
if ($fileExtension == "png" || $fileExtension == "jpg") {
    // Di chuyển tập tin từ thư mục tạm thời đến thư mục đích
    if (move_uploaded_file($tempFilePath, $targetPath)) {
        // Trả về phản hồi JSON
        echo json_encode(array(
            "uploaded" => true,
            "url" => "http://localhost:5000/" . $fileName
        ));
    } else {
        // Trả về phản hồi JSON nếu có lỗi khi di chuyển tập tin
        echo json_encode(array(
            "uploaded" => false,
            "error" => "Could not move the file"
        ));
    }
} else {
    // Trả về phản hồi JSON nếu tập tin không hợp lệ
    echo json_encode(array(
        "uploaded" => false,
        "error" => "Invalid file format"
    ));
}
