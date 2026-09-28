<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $text = $_POST['text'] ?? '';
    $author = $_POST['author'] ?? '';
    $color = $_POST['color'] ?? 'pastel-pink';
    $hasAudio = isset($_FILES['audio']) && $_FILES['audio']['error'] === UPLOAD_ERR_OK;

    if (empty($text) && !$hasAudio) {
        echo json_encode(['status' => 'error', 'message' => 'Missing text or audio.']);
        exit;
    }
    if (empty($author)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing author field.']);
        exit;
    }

    $audioFile = null;

    if ($hasAudio) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileTmpPath = $_FILES['audio']['tmp_name'];
        $fileName = $_FILES['audio']['name'];
        
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExt = ['mp3', 'wav', 'm4a', 'ogg', 'webm', 'mp4', 'aac'];

        if (in_array($ext, $allowedExt)) {
            $newFileName = 'audio_' . uniqid() . '.' . $ext;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $audioFile = $newFileName;
            }
        }
    }

    require_once 'db.php';
    
    try {
        $stmt = $pdo->prepare("INSERT INTO `notes` (`timestamp`, `text`, `author`, `color`, `audio`) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            time(),
            htmlspecialchars($text, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($author, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($color, ENT_QUOTES, 'UTF-8'),
            $audioFile
        ]);
        echo json_encode(['status' => 'success']);
    } catch (\PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}
echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
?>