<?php
header('Content-Type: application/json');

require_once 'db.php';

try {
    $stmt = $pdo->query("SELECT * FROM `notes` ORDER BY `timestamp` DESC");
    $notes = $stmt->fetchAll();
    echo json_encode(['status' => 'success', 'notes' => $notes]);
} catch (\PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
