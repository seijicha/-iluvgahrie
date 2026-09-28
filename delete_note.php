<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$timestamp = $_POST['timestamp'] ?? '';

if (empty($timestamp)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing parameter']);
    exit;
}

require_once 'db.php';

try {
    // First, find the note to see if it has an audio file
    $stmt = $pdo->prepare("SELECT `audio` FROM `notes` WHERE `timestamp` = ?");
    $stmt->execute([$timestamp]);
    $note = $stmt->fetch();

    if ($note) {
        // If it has audio, delete the audio file
        if (!empty($note['audio'])) {
            $audioPath = 'uploads/' . basename($note['audio']);
            if (file_exists($audioPath)) {
                unlink($audioPath);
            }
        }

        // Delete the note from the database
        $delStmt = $pdo->prepare("DELETE FROM `notes` WHERE `timestamp` = ?");
        $delStmt->execute([$timestamp]);
        
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Note not found']);
    }
} catch (\PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>