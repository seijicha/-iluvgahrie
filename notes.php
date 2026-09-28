<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWEET NOTES — CHA & GAHRIE</title>
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
                <a href="notes.php" class="nav-btn active" id="navNotes">NOTES</a>
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
        <section class="notes-page-wrapper" id="notesContainer" style="display: block;">
            <div class="moments-header-card" id="notesHeaderCard" style="max-width: 680px;">
                <header>
                    <h1 class="gallery-section-title">Sweet Notes</h1>
                    <p class="gallery-section-subtitle">Leave love messages and audio voice notes for each other.</p>
                </header>

                <div class="note-input-container">
                    <textarea id="newNoteText" placeholder="Write a sweet love note..."></textarea>
                    <div class="note-input-row">
                        <input type="text" id="noteAuthor" placeholder="Your Name (Cha or Gahrie)">
                        <div class="note-color-picker">
                            <label title="Pastel Pink">
                                <input type="radio" name="noteColor" value="pastel-pink" checked>
                                <span class="color-dot pink"></span>
                            </label>
                            <label title="Pastel Blue">
                                <input type="radio" name="noteColor" value="pastel-blue">
                                <span class="color-dot blue"></span>
                            </label>
                            <label title="Pastel Yellow">
                                <input type="radio" name="noteColor" value="pastel-yellow">
                                <span class="color-dot yellow"></span>
                            </label>
                            <label title="Pastel Purple">
                                <input type="radio" name="noteColor" value="pastel-purple">
                                <span class="color-dot purple"></span>
                            </label>
                        </div>
                    </div>

                    <div class="audio-input-section">
                        <div class="recorder-controls">
                            <button id="recordBtn" class="audio-upload-btn">Record Audio Voice Note</button>
                            <button id="stopBtn" class="audio-upload-btn"
                                style="display: none; background-color: #ff6b81;">Stop Recording</button>
                            <div id="audioDisplay" class="audio-display"></div>
                        </div>
                    </div>

                    <button id="saveNoteBtn" class="cute-btn save-note-btn">Save Sweet Note</button>
                </div>
            </div>

            <!-- Notes List Grid -->
            <div id="notesGrid" class="photo-grid" style="margin-top: 30px;">
                <!-- Notes injected dynamically -->
            </div>
        </section>

        <!-- Loading Status -->
        <div id="loading" style="display: none; text-align: center; color: #ff6b81; font-weight: 600; padding: 25px;">
            Uploading memories...
        </div>
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

    <!-- Modal for Expanded Media -->
    <div id="imageModal" class="modal">
        <span class="close-modal" id="closeModal">&times;</span>
        <img class="modal-content" id="expandedImg" style="display: none;">
        <video class="modal-content" id="expandedVideo" controls style="display: none;"></video>
        <button id="prevBtn" class="lightbox-arrow left-arrow">&#10094;</button>
        <button id="nextBtn" class="lightbox-arrow right-arrow">&#10095;</button>
    </div>

    <!-- Custom Context Menu for Delete -->
    <div id="customContextMenu" class="context-menu" style="display: none;">
        <button id="deleteMomentBtn" class="context-btn" style="display: none;">Delete Memory</button>
        <button id="deleteNoteBtn" class="context-btn">Delete Note</button>
    </div>

    <script src="script.js?v=<?php echo time(); ?>"></script>
</body>

</html>
