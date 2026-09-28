<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'iluvgahrie';

try {
    // Connect without database to create it if necessary
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Create Database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$db`");

    // Create Notes Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `notes` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `timestamp` BIGINT NOT NULL,
            `text` TEXT NOT NULL,
            `author` VARCHAR(255) NOT NULL,
            `color` VARCHAR(50) DEFAULT 'pastel-pink',
            `audio` VARCHAR(255) DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Create Media Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `media` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `filename` VARCHAR(255) NOT NULL,
            `type` VARCHAR(50) NOT NULL,
            `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    $messages = ["Database and tables created successfully."];

    // Migrate notes.json to database
    $notesFile = 'notes.json';
    if (file_exists($notesFile)) {
        $notesData = json_decode(file_get_contents($notesFile), true);
        if (is_array($notesData) && !empty($notesData)) {
            // Check if notes table is empty before migrating
            $stmt = $pdo->query("SELECT COUNT(*) FROM `notes`");
            if ($stmt->fetchColumn() == 0) {
                $insertNote = $pdo->prepare("INSERT INTO `notes` (`timestamp`, `text`, `author`, `color`, `audio`) VALUES (?, ?, ?, ?, ?)");
                $migratedCount = 0;
                foreach ($notesData as $note) {
                    $audio = !empty($note['audio']) ? $note['audio'] : null;
                    $insertNote->execute([
                        $note['timestamp'] ?? time(),
                        $note['text'] ?? '',
                        $note['author'] ?? 'Sweetheart',
                        $note['color'] ?? 'pastel-pink',
                        $audio
                    ]);
                    $migratedCount++;
                }
                $messages[] = "Migrated $migratedCount notes from notes.json.";
            } else {
                $messages[] = "Notes table already has data, skipping notes.json migration.";
            }
        }
    }

    // Migrate uploads directory to database
    $uploadDir = 'uploads/';
    if (is_dir($uploadDir)) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM `media`");
        if ($stmt->fetchColumn() == 0) {
            $insertMedia = $pdo->prepare("INSERT INTO `media` (`filename`, `type`) VALUES (?, ?)");
            $migratedMedia = 0;
            if ($dh = opendir($uploadDir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file != "." && $file != ".." && is_file($uploadDir . $file)) {
                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        // Determine type (skip audio for media gallery)
                        if (in_array($ext, ['mp3', 'wav', 'm4a']) || strpos(strtolower($file), 'audio') !== false) {
                            continue;
                        }
                        
                        $type = 'image';
                        if (in_array($ext, ['mp4', 'webm', 'ogg', 'mov'])) {
                            $type = 'video';
                        }
                        
                        $insertMedia->execute([$file, $type]);
                        $migratedMedia++;
                    }
                }
                closedir($dh);
            }
            $messages[] = "Migrated $migratedMedia files from uploads/ to media table.";
        } else {
            $messages[] = "Media table already has data, skipping uploads/ migration.";
        }
    }

    echo "<h3>Setup Complete</h3><ul>";
    foreach ($messages as $msg) {
        echo "<li>$msg</li>";
    }
    echo "</ul><br><a href='index.php'>Go to Album</a>";

} catch (\PDOException $e) {
    die("Setup failed: " . $e->getMessage());
}
?>
