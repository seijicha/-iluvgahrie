<?php
require_once 'db.php';

try {
    $stmt = $pdo->query("SELECT * FROM `notes` WHERE `audio` IS NOT NULL AND `audio` != '' ORDER BY `timestamp` DESC");
    $audioNotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (\PDOException $e) {
    $audioNotes = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AUDIO NOTES — CHA & GAHRIE</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400;1,600&family=Dancing+Script:wght@700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <script>
        (function () {
            var navEntries = performance.getEntriesByType ? performance.getEntriesByType("navigation") : [];
            var isReload = (navEntries.length > 0 && navEntries[0].type === "reload") || (performance.navigation && performance.navigation.type === 1);
            if (isReload) {
                window.location.replace('index.php');
            }
        })();
    </script>
</head>

<body>
    <!-- Editorial Top Navigation Header -->
    <header class="editorial-header">
        <div class="header-inner">
            <a href="index.php?page=story" class="brand-monogram" id="brandLogo">
                <span class="monogram-dot">.</span><span class="monogram-initials">CG</span>
                <span class="monogram-names">CHA & GAHRIE</span>
            </a>

            <nav class="editorial-nav">
                <a href="index.php?page=story" class="nav-btn" id="navPictures">OUR STORY</a>
                <a href="moments.php" class="nav-btn" id="navMoments">MOMENTS</a>
                <a href="notes.php" class="nav-btn" id="navNotes">NOTES</a>
                <a href="audio_notes.php" class="nav-btn active">AUDIO NOTES</a>
            </nav>

            <div class="header-actions">
                <div id="loveTimer" class="editorial-timer">
                    <span class="timer-heart-dot">♥</span>
                    <span class="timer-label">LOVING EACH OTHER:</span>
                    <span id="timeTogether" class="timer-value">Loading...</span>
                </div>

                <button id="musicToggleBtn" class="music-toggle-btn" title="Toggle Love Song">
                    <span class="music-note-icon">♫</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="editorial-main">
        <section class="notes-page-wrapper" style="display: block;">
            <div class="moments-header-card" style="max-width: 680px; margin-bottom: 25px;">
                <header>
                    <h1 class="gallery-section-title">Audio Notes</h1>
                    <p class="gallery-section-subtitle">Listen to our recorded love messages and voice notes.</p>
                </header>
            </div>

            <!-- Audio Notes Grid -->
            <div class="photo-grid" style="margin-top: 20px;">
                <?php if (empty($audioNotes)): ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 40px 20px; color: #888;">
                        <p style="font-size: 1.1rem; margin-bottom: 12px;">No voice audio notes recorded yet ♫</p>
                        <a href="notes.php" class="cute-btn" style="display: inline-block; padding: 10px 24px; text-decoration: none; border-radius: 30px;">Record a Voice Note in Sweet Notes</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($audioNotes as $index => $note): ?>
                        <?php 
                            $audioId = "audioNote_" . $index;
                            $btnId = "audioBtn_" . $index;
                            $noteColor = !empty($note['color']) ? htmlspecialchars($note['color']) : 'pastel-pink';
                            $author = !empty($note['author']) ? htmlspecialchars($note['author']) : 'Sweetheart';
                            $text = !empty($note['text']) ? htmlspecialchars_decode($note['text']) : '';
                            $audioSrc = "uploads/" . htmlspecialchars($note['audio']);
                            $dateStr = date('F j, Y, g:i a', $note['timestamp']);
                        ?>
                        <div class="note-card <?php echo $noteColor; ?>" data-timestamp="<?php echo $note['timestamp']; ?>">
                            <?php if ($text): ?>
                                <p class="note-text"><?php echo nl2br(htmlspecialchars($text)); ?></p>
                            <?php endif; ?>

                            <!-- Audio tag & Control Button -->
                            <div class="note-audio" style="margin: 12px 0;">
                                <audio id="<?php echo $audioId; ?>" src="<?php echo $audioSrc; ?>" preload="auto"></audio>
                                <div style="display: flex; align-items: center; gap: 12px; background: rgba(255, 255, 255, 0.85); padding: 10px 14px; border-radius: 14px; border: 1.5px solid rgba(255, 107, 129, 0.25);">
                                    <button id="<?php echo $btnId; ?>" class="custom-voice-play-round" onclick="toggleAudio('<?php echo $audioId; ?>', '<?php echo $btnId; ?>')">Play</button>
                                    <span style="font-weight: 600; color: #ff6b81; font-size: 0.88rem;">Voice Note ♫</span>
                                </div>
                            </div>

                            <div class="note-author">— <?php echo $author; ?></div>
                            <div class="note-date"><?php echo $dateStr; ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- Background Music -->
    <?php
    $bgSong = file_exists('lover_song.m4a') ? 'lover_song.m4a' : (file_exists('lover_song.mp3') ? 'lover_song.mp3' : 'lover_song.m4a');
    $bgMime = pathinfo($bgSong, PATHINFO_EXTENSION) === 'm4a' ? 'audio/mp4' : 'audio/mpeg';
    $bgVer = @filemtime($bgSong) ?: time();
    ?>
    <audio id="bgMusic" loop preload="auto">
        <source src="<?php echo $bgSong; ?>?v=<?php echo $bgVer; ?>" type="<?php echo $bgMime; ?>">
    </audio>

    <script src="script.js?v=<?php echo time(); ?>"></script>
    <script>
        function toggleAudio(audioId = "myAudio", buttonId = "audioButton") {
            var audio = document.getElementById(audioId);
            var button = document.getElementById(buttonId);
            var bgMusic = document.getElementById("bgMusic");

            if (!audio || !button) return;

            // Pause all other audio elements on the page
            document.querySelectorAll('audio').forEach(function(a) {
                if (a !== audio && a.id !== 'bgMusic' && !a.paused) {
                    a.pause();
                }
            });
            document.querySelectorAll('button[id^="audioBtn_"], #audioButton').forEach(function(b) {
                if (b !== button) {
                    b.innerHTML = "Play";
                }
            });

            if (audio.paused) {
                if (bgMusic && !bgMusic.paused) {
                    bgMusic.pause();
                }
                audio.play();
                button.innerHTML = "Pause";
            } else {
                audio.pause();
                button.innerHTML = "Play";
            }

            audio.onended = function() {
                button.innerHTML = "Play";
            };
        }
    </script>
</body>

</html>
