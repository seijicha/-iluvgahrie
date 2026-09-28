<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VIDEO MEMORIES — CHA & GAHRIE</title>
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
                <a href="photographs.php" class="nav-btn" id="navPhotographs">PHOTOGRAPHS</a>
                <a href="videos.php" class="nav-btn active" id="navVideos">VIDEOS</a>
                <a href="notes.php" class="nav-btn" id="navNotes">NOTES</a>
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
        <section class="moments-page-wrapper" id="videosContainer" style="display: block;">
            <!-- Header & Uploader -->
            <div class="moments-header-card" id="mediaContainer">
                <header>
                    <h1 class="gallery-section-title">Video Memories</h1>
                    <p class="gallery-section-subtitle">Our live clips and moving moments together.</p>
                </header>

                <div class="upload-box-wrapper">
                    <label for="photoUpload" class="cute-btn moments-upload-btn">
                        + Upload Video Clips
                    </label>
                    <p class="upload-note-text">Select video clips from your device to add to our video memories</p>
                </div>
            </div>

            <!-- Dedicated Video Memories Grid -->
            <div class="media-category-group" id="videosGroup">
                <div class="category-header-bar">
                    <h2 class="category-title">
                        All Video Memories
                        <span class="category-badge" id="videoCount">0</span>
                    </h2>
                    <span class="category-hint">Our live moments in motion</span>
                </div>
                <div class="photo-grid videos-grid" id="videoGrid">
                    <!-- Videos injected here -->
                </div>
            </div>
        </section>

        <!-- Upload Status -->
        <div id="loading" style="display: none; text-align: center; color: #ff6b81; font-weight: 600; padding: 25px;">
            Uploading memories...
        </div>
    </main>

    <!-- Background Music -->
    <audio id="bgMusic" loop>
        <source src="lover_song.mp3" type="audio/mpeg">
    </audio>

    <!-- Modal for Expanded Media -->
    <div id="imageModal" class="modal">
        <span class="close-modal" id="closeModal">&times;</span>
        <img class="modal-content" id="expandedImg" style="display: none;">
        <video class="modal-content" id="expandedVideo" controls style="display: none;"></video>
        <button id="prevBtn" class="lightbox-arrow left-arrow">&#10094;</button>
        <button id="nextBtn" class="lightbox-arrow right-arrow">&#10095;</button>
    </div>

    <!-- Custom Context Menu -->
    <div id="customContextMenu" class="context-menu" style="display: none;">
        <button id="deleteMomentBtn" class="context-btn">Delete Memory</button>
        <button id="deleteNoteBtn" class="context-btn" style="display: none;">Delete Note</button>
    </div>

    <script src="script.js?v=<?php echo time(); ?>"></script>
</body>

</html>
