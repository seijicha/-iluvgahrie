<?php
ini_set('upload_max_filesize', '512M');
ini_set('post_max_size', '512M');
ini_set('memory_limit', '512M');
ini_set('max_execution_time', '300');

header('Content-Type: application/json');

$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['photo'])) {
        $uploadedFiles = [];
        $errors = [];
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'heic', 'bmp', 'mp4', 'webm', 'ogg', 'mov', 'm4v', 'avi', 'mkv', '3gp'];

        $files = $_FILES['photo'];
        $isMultiple = is_array($files['name']);
        $count = $isMultiple ? count($files['name']) : 1;

        require_once 'db.php';
        $insertMedia = $pdo->prepare("INSERT INTO `media` (`filename`, `type`) VALUES (?, ?)");

        for ($i = 0; $i < $count; $i++) {
            $error = $isMultiple ? $files['error'][$i] : $files['error'];

            if ($error === UPLOAD_ERR_OK) {
                $fileTmpPath = $isMultiple ? $files['tmp_name'][$i] : $files['tmp_name'];
                $fileName = $isMultiple ? $files['name'][$i] : $files['name'];
                $fileType = $isMultiple ? $files['type'][$i] : $files['type'];

                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if (in_array($ext, $allowed_extensions)) {
                    $cleanOriginalName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($fileName));
                    $newFileName = uniqid() . '_' . $cleanOriginalName;
                    $destPath = $uploadDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        try {
                            $videoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'm4v', 'avi', 'mkv', '3gp'];
                            $type = in_array($ext, $videoExtensions) || (strpos($fileType, 'video') !== false) ? 'video' : 'image';
                            $insertMedia->execute([$newFileName, $type]);
                            $uploadedFiles[] = $newFileName;
                        } catch (\PDOException $e) {
                            $errors[] = "Database error for file $fileName: " . $e->getMessage();
                        }
                    } else {
                        $errors[] = "Error saving file: $fileName";
                    }
                } else {
                    $errors[] = "Unsupported format (.$ext) for file: $fileName";
                }
            } else if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
                $errors[] = "File is too large. Max allowed upload size is 512MB.";
            } else if ($error !== UPLOAD_ERR_NO_FILE) {
                $errors[] = "Upload failed with code $error.";
            }
        }

        if (count($uploadedFiles) > 0) {
            echo json_encode(['status' => 'success', 'message' => count($uploadedFiles) . ' file(s) uploaded successfully.', 'files' => $uploadedFiles, 'errors' => $errors]);
        } else {
            echo json_encode(['status' => 'error', 'message' => count($errors) > 0 ? implode(', ', $errors) : 'No valid files were uploaded.', 'errors' => $errors]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No file uploaded or file exceeded POST size limits.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>