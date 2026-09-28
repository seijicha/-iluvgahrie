<?php
header('Content-Type: application/json');

require_once 'db.php';

try {
    $stmt = $pdo->query("SELECT `filename` FROM `media` ORDER BY `uploaded_at` DESC");
    $mediaRecords = $stmt->fetchAll();
    
    $photos = [];
    foreach ($mediaRecords as $record) {
        $photos[] = $record['filename'];
    }
    
    echo json_encode(['status' => 'success', 'photos' => $photos]);
} catch (\PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>