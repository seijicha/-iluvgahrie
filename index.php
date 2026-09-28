<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHA & GAHRIE — OUR LOVE STORY</title>
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
                if (window.history && window.history.replaceState) {
                    window.history.replaceState({}, document.title, window.location.pathname);
                }
            } else {
                var urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('page') || urlParams.has('nav')) {
                    document.documentElement.classList.add('album-already-entered');
                }
            }
        })();
    </script>
</head>

<body>
    <!-- Homepage Overlay (Preserved with transition!) -->
    <div id="homepageOverlay" class="homepage-overlay">
        <div class="homepage-content">
            <h1 class="homepage-title">Our Love Album</h1>
            <p class="homepage-subtitle">A collection of our favorite moments together.</p>
            <button class="cute-btn enter-btn" id="enterBtn">Enter Our Album</button>
        </div>
    </div>

    <!-- Editorial Top Navigation Header -->
    <header class="editorial-header">
        <div class="header-inner">
            <div class="brand-monogram" id="brandLogo">
                <span class="monogram-dot">.</span><span class="monogram-initials">CG</span>
                <span class="monogram-names">CHA & GAHRIE</span>
            </div>

            <nav class="editorial-nav">
                <a href="index.php?page=story" class="nav-btn active" id="navPictures">OUR STORY</a>
                <a href="moments.php" class="nav-btn" id="navMoments">MOMENTS</a>
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
        <!-- ============================================================
             1. OUR STORY (Home View with Hero Arches)
             ============================================================ -->
        <section class="editorial-hero" id="heroSection">
            <div class="hero-left-column">
                <div class="hero-typography">
                    <h1 class="hero-save-dates">
                        <span>FOREVER</span>
                        <span>WITH</span>
                        <span>YOU</span>
                    </h1>
                    <p class="hero-subtitle">CHA & GAHRIE • OUR LOVE STORY</p>
                </div>

                <!-- Circular Rotating Badge in bottom-left -->
                <div class="circular-stamp-container" id="stampBadge" title="Click for Love Sparks & Music!">
                    <div class="circular-stamp">
                        <svg viewBox="0 0 100 100" class="rotating-text-svg">
                            <path id="circleTextPath" d="M 50, 50 m -37, 0 a 37,37 0 1,1 74,0 a 37,37 0 1,1 -74,0"
                                fill="none" />
                            <text>
                                <textPath href="#circleTextPath" startOffset="0%">
                                    CHA & GAHRIE • YOU ARE LOVED • FOREVER & ALWAYS •
                                </textPath>
                            </text>
                        </svg>
                        <div class="stamp-center-heart">♥</div>
                    </div>
                </div>
            </div>

            <div class="hero-arches-column">
                <!-- Arch 1 -->
                <div class="arch-card" id="archCard1" data-index="0">
                    <div class="arch-media-frame">
                        <img src="uploads/photo_6185714979374306348_y.jpg" alt="Our Sweet Ride" id="archImg1">
                    </div>
                    <div class="arch-connector-stem"></div>
                    <div class="arch-info">
                        <span class="arch-date">26 JUNE 2026</span>
                        <h2 class="arch-caption">THE DAY WE MET</h2>
                    </div>
                </div>

                <!-- Arch 2 -->
                <div class="arch-card" id="archCard2" data-index="1">
                    <div class="arch-media-frame">
                        <img src="uploads/photo_6185714979374306346_y.jpg" alt="Our Mirror Moments" id="archImg2">
                    </div>
                    <div class="arch-connector-stem"></div>
                    <div class="arch-info">
                        <span class="arch-date">SWEETEST MOMENTS</span>
                        <h2 class="arch-caption">STORIES OPENING</h2>
                    </div>
                </div>

                <!-- Arch 3 -->
                <div class="arch-card" id="archCard3" data-index="2">
                    <div class="arch-media-frame">
                        <img src="uploads/photo_6185714979374306347_y.jpg" alt="Proud of You Always" id="archImg3">
                    </div>
                    <div class="arch-connector-stem"></div>
                    <div class="arch-info">
                        <span class="arch-date">FOREVER & ALWAYS</span>
                        <h2 class="arch-caption">THE NEXT JOURNEY</h2>
                    </div>
                </div>
            </div>
        </section>

        <!-- Curved 3D Photo Strip (Transitions right to left on scroll) -->
        <section class="photo-strip-section" id="photoStripSection">
            <div class="photo-strip-header">
                <span class="strip-eyebrow">A GLIMPSE INTO OUR DAYS</span>
                <h2 class="strip-title">Cherished Stills</h2>
            </div>
            <div class="photo-strip-viewport">
                <div class="photo-strip-track" id="photoStripTrack">
                    <!-- Set 1 -->
                    <div class="strip-card"><img src="uploads/strip_photo_1.jpg" alt="Memory" loading="lazy"></div>
                    <div class="strip-card"><img src="uploads/strip_photo_2.jpg" alt="Memory" loading="lazy"></div>
                    <div class="strip-card"><img src="uploads/strip_photo_3.jpg" alt="Memory" loading="lazy"></div>
                    <div class="strip-card"><img src="uploads/strip_photo_4.jpg" alt="Memory" loading="lazy"></div>
                    <div class="strip-card"><img src="uploads/strip_photo_5.jpg" alt="Memory" loading="lazy"></div>
                    <!-- Set 2 -->
                    <div class="strip-card"><img src="uploads/strip_photo_1.jpg" alt="Memory" loading="lazy"></div>
                    <div class="strip-card"><img src="uploads/strip_photo_2.jpg" alt="Memory" loading="lazy"></div>
                    <div class="strip-card"><img src="uploads/strip_photo_3.jpg" alt="Memory" loading="lazy"></div>
                    <div class="strip-card"><img src="uploads/strip_photo_4.jpg" alt="Memory" loading="lazy"></div>
                    <div class="strip-card"><img src="uploads/strip_photo_5.jpg" alt="Memory" loading="lazy"></div>
                    <!-- Set 3 -->
                    <div class="strip-card"><img src="uploads/strip_photo_1.jpg" alt="Memory" loading="lazy"></div>
                    <div class="strip-card"><img src="uploads/strip_photo_2.jpg" alt="Memory" loading="lazy"></div>
                    <div class="strip-card"><img src="uploads/strip_photo_3.jpg" alt="Memory" loading="lazy"></div>
                    <div class="strip-card"><img src="uploads/strip_photo_4.jpg" alt="Memory" loading="lazy"></div>
                    <div class="strip-card"><img src="uploads/strip_photo_5.jpg" alt="Memory" loading="lazy"></div>
                    <!-- Set 4 -->
                    <div class="strip-card"><img src="uploads/strip_photo_1.jpg" alt="Memory" loading="lazy"></div>
                    <div class="strip-card"><img src="uploads/strip_photo_2.jpg" alt="Memory" loading="lazy"></div>
                    <div class="strip-card"><img src="uploads/strip_photo_3.jpg" alt="Memory" loading="lazy"></div>
                    <div class="strip-card"><img src="uploads/strip_photo_4.jpg" alt="Memory" loading="lazy"></div>
                    <div class="strip-card"><img src="uploads/strip_photo_5.jpg" alt="Memory" loading="lazy"></div>
                    <!-- Set 5 (Edge-to-edge continuous buffer) -->
                    <div class="strip-card"><img src="uploads/strip_photo_1.jpg" alt="Memory" loading="lazy"></div>
                    <div class="strip-card"><img src="uploads/strip_photo_2.jpg" alt="Memory" loading="lazy"></div>
                    <div class="strip-card"><img src="uploads/strip_photo_3.jpg" alt="Memory" loading="lazy"></div>
                    <div class="strip-card"><img src="uploads/strip_photo_4.jpg" alt="Memory" loading="lazy"></div>
                    <div class="strip-card"><img src="uploads/strip_photo_5.jpg" alt="Memory" loading="lazy"></div>
                </div>
            </div>
        </section>

        <!-- Story Preview Card on Home -->
        <section class="story-preview-wrapper" id="storyPreviewSection">
            <div class="story-preview-card">
                <span class="story-card-tag">A LOVE LETTER TO US</span>
                <p class="story-preview-text">"You make my life feel lighter and brighter. Here's to all our memories
                    together, and to growing old by your side forever."</p>
                <a href="moments.php" class="cute-btn explore-btn" id="exploreMomentsBtn"
                    style="text-decoration: none !important;">Explore Moments Gallery →</a>
            </div>
        </section>

        <!-- ============================================================
             2. MOMENTS PAGE (Images & Videos Uploaded Together, Separated)
             ============================================================ -->
        <section class="moments-page-wrapper" id="momentsContainer" style="display: none;">
            <div class="moments-header-card" id="mediaContainer">
                <header>
                    <h2 class="gallery-section-title">Our Moments</h2>
                    <p class="gallery-section-subtitle">Every photograph and video of our journey together.</p>
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

            <!-- Category 1: Photographs -->
            <div class="media-category-group" id="photosGroup">
                <div class="category-header-bar">
                    <h3 class="category-title">
                        Photographs
                        <span class="category-badge" id="photoCount">0</span>
                    </h3>
                    <span class="category-hint">Our captured still moments</span>
                </div>
                <div class="photo-grid" id="photoGrid">
                    <!-- Photographs injected here -->
                </div>
            </div>

            <!-- Category 2: Video Memories -->
            <div class="media-category-group" id="videosGroup">
                <div class="category-header-bar">
                    <h3 class="category-title">
                        Video Memories
                        <span class="category-badge" id="videoCount">0</span>
                    </h3>
                    <span class="category-hint">Our live moments in motion</span>
                </div>
                <div class="photo-grid videos-grid" id="videoGrid">
                    <!-- Videos injected here -->
                </div>
            </div>
        </section>



        <!-- ============================================================
             4. NOTES PAGE
             ============================================================ -->
        <section class="notes-page-wrapper" id="notesContainer" style="display: none;">
            <div class="container" id="notesHeaderContainer">
                <header>
                    <h2 class="gallery-section-title">Sweet Notes</h2>
                    <p class="gallery-section-subtitle">Leave love messages and voice notes for each other.</p>
                </header>
            </div>

            <div class="note-input-container">
                <textarea id="newNoteText" placeholder="Write a sweet note..."></textarea>
                <div class="note-input-row">
                    <input type="text" id="noteAuthor" placeholder="Your Name">
                    <div class="note-color-picker">
                        <label>
                            <input type="radio" name="noteColor" value="pastel-pink" checked>
                            <span class="color-dot pink"></span>
                        </label>
                        <label>
                            <input type="radio" name="noteColor" value="pastel-blue">
                            <span class="color-dot blue"></span>
                        </label>
                        <label>
                            <input type="radio" name="noteColor" value="pastel-yellow">
                            <span class="color-dot yellow"></span>
                        </label>
                        <label>
                            <input type="radio" name="noteColor" value="pastel-purple">
                            <span class="color-dot purple"></span>
                        </label>
                    </div>
                    <div class="audio-input-section">
                        <div class="recorder-controls">
                            <button id="recordBtn" class="audio-upload-btn">Record Audio Voice Note</button>
                            <button id="stopBtn" class="audio-upload-btn stop-recording-btn"
                                style="display: none;">Stop Recording</button>
                            <div id="audioDisplay" class="audio-display"></div>
                        </div>
                    </div>
                    <button id="saveNoteBtn" class="cute-btn save-note-btn">Save Sweet Note</button>
                </div>

            <div id="notesGrid" class="photo-grid"></div>
        </section>

        <!-- Uploading Status -->
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

    <!-- Custom Context Menu -->
    <div id="customContextMenu" class="context-menu" style="display: none;">
        <button id="deleteMomentBtn" class="context-btn">Delete Memory</button>
        <button id="deleteNoteBtn" class="context-btn">Delete Note</button>
    </div>

    <script src="script.js?v=<?php echo time(); ?>"></script>
</body>

</html>