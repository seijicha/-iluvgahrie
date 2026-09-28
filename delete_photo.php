<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['filename'])) {
        // basename() prevents directory traversal attacks
        $filename = basename($_POST['filename']);
        $uploadDir = 'uploads/';
        $filePath = $uploadDir . $filename;

        require_once 'db.php';

        try {
            // Delete from database
            $stmt = $pdo->prepare("DELETE FROM `media` WHERE `filename` = ?");
            $stmt->execute([$filename]);

            // Ensure the file exists and delete it
            if (file_exists($filePath) && is_file($filePath)) {
                if (unlink($filePath)) {
                    echo json_encode(['status' => 'success', 'message' => 'File deleted successfully.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to delete file from disk.']);
                }
            } else {
                // If the file is missing from disk but we deleted the db record, we can still report success
                echo json_encode(['status' => 'success', 'message' => 'File record deleted, but file was not found on disk.']);
            }
        } catch (\PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No filename provided.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
