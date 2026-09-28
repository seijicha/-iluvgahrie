<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OUR MOMENTS — CHA & GAHRIE</title>
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
                <a href="moments.php" class="nav-btn active" id="navMoments">MOMENTS</a>
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
        <section class="moments-page-wrapper" id="momentsContainer" style="display: block;">
            <!-- Moments Header Card & Unified Uploader -->
            <div class="moments-header-card" id="mediaContainer">
                <header>
                    <h1 class="gallery-section-title">Our Moments</h1>
                    <p class="gallery-section-subtitle">Click Photographs or Video Memories below to open and view our
                        uploads.</p>
                </header>

                <div class="upload-box-wrapper">
                    <form id="uploadForm" enctype="multipart/form-data" style="margin: 0;">
                        <label for="photoUpload" class="cute-btn moments-upload-btn">
                            + Upload Memories (Photos & Videos)
                        </label>
                        <input type="file" id="photoUpload" name="photo[]" accept="image/*,video/*" multiple
                            style="display: none;">
                    </form>
                </div>
            </div>

            <!-- View Filter Pills Bar -->
            <div class="moments-filter-bar">
                <button class="filter-pill-btn active" id="filterAllBtn">All Moments</button>
                <button class="filter-pill-btn" id="filterPhotosBtn">Photographs</button>
                <button class="filter-pill-btn" id="filterVideosBtn">Video Memories</button>
            </div>

            <!-- Active Gallery Sections -->
            <div class="moments-display-area" id="momentsDisplayArea">
                <!-- Category 1: Photographs Section -->
                <div class="media-category-group" id="photosGroup" style="display: block;">
                    <div class="category-header-bar">
                        <h2 class="category-title">
                            Photographs
                            <span class="category-badge" id="photoCount">0</span>
                        </h2>
                        <span class="category-hint">Click any photo to expand full view</span>
                    </div>
                    <div class="photo-grid" id="photoGrid">
                        <!-- Photographs injected here -->
                    </div>
                </div>

                <!-- Category 2: Video Memories Section -->
                <div class="media-category-group" id="videosGroup" style="display: block;">
                    <div class="category-header-bar">
                        <h2 class="category-title">
                            Video Memories
                            <span class="category-badge" id="videoCount">0</span>
                        </h2>
                        <span class="category-hint">Click any video to play full screen</span>
                    </div>
                    <div class="photo-grid videos-grid" id="videoGrid">
                        <!-- Videos injected here -->
                    </div>
                </div>
            </div>
        </section>

        <!-- Upload Status -->
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

    <!-- Modal for Expanded Media (Click to open full photo or video) -->
    <div id="imageModal" class="modal">
        <span class="close-modal" id="closeModal">&times;</span>
        <img class="modal-content" id="expandedImg" style="display: none;">
        <video class="modal-content" id="expandedVideo" controls style="display: none;"></video>
        <button id="prevBtn" class="lightbox-arrow left-arrow">&#10094;</button>
        <button id="nextBtn" class="lightbox-arrow right-arrow">&#10095;</button>
    </div>

    <!-- Custom Context Menu for Delete -->
    <div id="customContextMenu" class="context-menu" style="display: none;">
        <button id="deleteMomentBtn" class="context-btn">Delete Memory</button>
        <button id="deleteNoteBtn" class="context-btn" style="display: none;">Delete Note</button>
    </div>

    <script src="script.js?v=<?php echo time(); ?>"></script>
</body>

</html>