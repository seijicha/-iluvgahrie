document.addEventListener('DOMContentLoaded', () => {
    const bgMusic = document.getElementById('bgMusic');
    const navPictures = document.getElementById('navPictures');
    const navMoments = document.getElementById('navMoments') || document.getElementById('navVideos');
    const navNotes = document.getElementById('navNotes');
    const heroSection = document.getElementById('heroSection');
    const photoStripSection = document.getElementById('photoStripSection');
    const photoStripTrack = document.getElementById('photoStripTrack');
    const storyPreviewSection = document.getElementById('storyPreviewSection');
    const momentsContainer = document.getElementById('momentsContainer');
    const notesContainer = document.getElementById('notesContainer');
    const uploadLabel = document.getElementById('uploadLabel');
    const photoGrid = document.getElementById('photoGrid');
    const videoGrid = document.getElementById('videoGrid');
    const exploreMomentsBtn = document.getElementById('exploreMomentsBtn');
    const brandLogo = document.getElementById('brandLogo');
    const photoUpload = document.getElementById('photoUpload');
    const uploadForm = document.getElementById('uploadForm');
    const loadingText = document.getElementById('loading');
    const modal = document.getElementById("imageModal");
    const expandedImg = document.getElementById("expandedImg");
    const expandedVideo = document.getElementById("expandedVideo");
    const closeModal = document.getElementById("closeModal");
    const contextMenu = document.getElementById('customContextMenu');
    const deleteMomentBtn = document.getElementById('deleteMomentBtn');
    const deleteNoteBtn = document.getElementById('deleteNoteBtn');
    const enterBtn = document.getElementById('enterBtn');
    const homepageOverlay = document.getElementById('homepageOverlay');
    const timeTogether = document.getElementById('timeTogether');
    const notesGrid = document.getElementById('notesGrid');
    const saveNoteBtn = document.getElementById('saveNoteBtn');
    const newNoteText = document.getElementById('newNoteText');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const musicToggleBtn = document.getElementById('musicToggleBtn');

    // Navigation and page context detection
    let isReload = false;
    try {
        const navEntries = performance.getEntriesByType("navigation");
        if (navEntries.length > 0 && navEntries[0].type === "reload") {
            isReload = true;
        } else if (performance.navigation && performance.navigation.type === 1) {
            isReload = true;
        }
    } catch (e) {}

    const isMomentsPage = window.location.pathname.endsWith('moments.php');
    const isPhotosPage = window.location.pathname.endsWith('photographs.php');
    const isVideosPage = window.location.pathname.endsWith('videos.php');
    const isNotesPage = window.location.pathname.endsWith('notes.php');

    const urlParams = new URLSearchParams(window.location.search);
    const initialPage = urlParams.get('page');
    const hasNavParam = urlParams.has('page') || urlParams.has('nav');

    let allMedia = [];
    let activeLightboxList = [];
    let currentLightboxIndex = -1;
    let selectedMemoryFilename = null;
    let selectedNoteTimestamp = null;
    let favorites = new Set();

    const recordBtn = document.getElementById('recordBtn');
    const stopBtn = document.getElementById('stopBtn');
    const audioDisplay = document.getElementById('audioDisplay');
    let mediaRecorder = null;
    let audioStream = null;
    let audioChunks = [];
    let selectedAudioBlob = null;
    let selectedAudioExt = 'mp3';
    let recordTimerInterval = null;
    let recordSeconds = 0;
    let visualAudioCtx = null;
    let visualAnimId = null;

    const getSupportedAudioMimeType = () => {
        if (typeof MediaRecorder === 'undefined') return '';
        const types = [
            'audio/webm;codecs=opus',
            'audio/webm',
            'audio/mp4',
            'audio/aac',
            'audio/ogg;codecs=opus',
            'audio/ogg'
        ];
        for (const type of types) {
            if (MediaRecorder.isTypeSupported(type)) return type;
        }
        return '';
    };

    const formatTime = (totalSeconds) => {
        const mins = Math.floor(totalSeconds / 60);
        const secs = Math.floor(totalSeconds % 60);
        return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
    };

    class VoicePlayer {
        constructor(source, initialDuration = 0, onTimeUpdate = null, onEnded = null) {
            this.source = source;
            this.duration = (initialDuration && initialDuration > 0) ? initialDuration : 0;
            this.onTimeUpdate = onTimeUpdate;
            this.onEnded = onEnded;
            this.audioCtx = null;
            this.audioBuffer = null;
            this.sourceNode = null;
            this.startTime = 0;
            this.pausedAt = 0;
            this.isPlaying = false;
            this.animFrame = null;
            this.htmlAudio = null;
            this.initPromise = null;
        }

        async init() {
            if (this.initPromise) return this.initPromise;
            this.initPromise = (async () => {
                try {
                    let arrayBuffer;
                    if (this.source instanceof Blob) {
                        arrayBuffer = await this.source.arrayBuffer();
                    } else if (typeof this.source === 'string') {
                        const response = await fetch(this.source);
                        arrayBuffer = await response.arrayBuffer();
                    }

                    if (arrayBuffer && arrayBuffer.byteLength > 0) {
                        const AudioContextClass = window.AudioContext || window.webkitAudioContext;
                        if (!this.audioCtx) {
                            this.audioCtx = new AudioContextClass();
                        }
                        const bufferCopy = arrayBuffer.slice(0);
                        this.audioBuffer = await this.audioCtx.decodeAudioData(bufferCopy);
                        if (this.audioBuffer && this.audioBuffer.duration > 0) {
                            this.duration = this.audioBuffer.duration;
                        }
                    }
                } catch (err) {
                    console.warn('WebAudio decode fallback to HTMLAudio:', err);
                    try {
                        let url = '';
                        if (this.source instanceof Blob) {
                            url = URL.createObjectURL(this.source);
                        } else if (typeof this.source === 'string') {
                            url = this.source;
                        }
                        if (url) {
                            this.htmlAudio = new Audio(url);
                            this.htmlAudio.preload = 'auto';
                        }
                    } catch (e) {}
                }
            })();
            return this.initPromise;
        }

        async play() {
            if (!this.audioBuffer && !this.htmlAudio) {
                await this.init();
            }

            if (this.audioBuffer) {
                if (this.audioCtx.state === 'suspended') {
                    await this.audioCtx.resume();
                }
                if (this.sourceNode) {
                    try { this.sourceNode.stop(); } catch (e) {}
                    this.sourceNode.disconnect();
                    this.sourceNode = null;
                }

                this.sourceNode = this.audioCtx.createBufferSource();
                this.sourceNode.buffer = this.audioBuffer;
                this.sourceNode.connect(this.audioCtx.destination);

                const offset = this.pausedAt >= this.duration ? 0 : this.pausedAt;
                this.startTime = this.audioCtx.currentTime - offset;
                this.sourceNode.start(0, offset);
                this.isPlaying = true;

                this.sourceNode.onended = () => {
                    if (this.isPlaying && (this.audioCtx.currentTime - this.startTime >= this.duration - 0.08)) {
                        this.stop();
                        if (this.onEnded) this.onEnded();
                    }
                };

                this.startProgress();
            } else if (this.htmlAudio) {
                this.isPlaying = true;
                this.htmlAudio.currentTime = this.pausedAt;
                await this.htmlAudio.play();
                this.htmlAudio.ontimeupdate = () => {
                    if (this.onTimeUpdate) {
                        this.onTimeUpdate(this.htmlAudio.currentTime, this.htmlAudio.duration || this.duration);
                    }
                };
                this.htmlAudio.onended = () => {
                    this.stop();
                    if (this.onEnded) this.onEnded();
                };
            }
        }

        pause() {
            if (this.audioBuffer && this.isPlaying) {
                this.pausedAt = Math.min(this.duration, Math.max(0, this.audioCtx.currentTime - this.startTime));
                if (this.sourceNode) {
                    try { this.sourceNode.stop(); } catch (e) {}
                    this.sourceNode.disconnect();
                    this.sourceNode = null;
                }
                this.isPlaying = false;
                cancelAnimationFrame(this.animFrame);
            } else if (this.htmlAudio) {
                this.htmlAudio.pause();
                this.pausedAt = this.htmlAudio.currentTime;
                this.isPlaying = false;
            }
        }

        stop() {
            if (this.audioBuffer) {
                if (this.sourceNode) {
                    try { this.sourceNode.stop(); } catch (e) {}
                    this.sourceNode.disconnect();
                    this.sourceNode = null;
                }
                this.pausedAt = 0;
                this.isPlaying = false;
                cancelAnimationFrame(this.animFrame);
            } else if (this.htmlAudio) {
                this.htmlAudio.pause();
                this.htmlAudio.currentTime = 0;
                this.pausedAt = 0;
                this.isPlaying = false;
            }
            if (this.onTimeUpdate) {
                this.onTimeUpdate(0, this.duration);
            }
        }

        seek(posFraction) {
            const target = Math.max(0, Math.min(this.duration, posFraction * this.duration));
            this.pausedAt = target;
            if (this.isPlaying) {
                if (this.audioBuffer) {
                    if (this.sourceNode) {
                        try { this.sourceNode.stop(); } catch (e) {}
                        this.sourceNode.disconnect();
                        this.sourceNode = null;
                    }
                    this.sourceNode = this.audioCtx.createBufferSource();
                    this.sourceNode.buffer = this.audioBuffer;
                    this.sourceNode.connect(this.audioCtx.destination);
                    this.startTime = this.audioCtx.currentTime - target;
                    this.sourceNode.start(0, target);
                    this.sourceNode.onended = () => {
                        if (this.isPlaying && (this.audioCtx.currentTime - this.startTime >= this.duration - 0.08)) {
                            this.stop();
                            if (this.onEnded) this.onEnded();
                        }
                    };
                } else if (this.htmlAudio) {
                    this.htmlAudio.currentTime = target;
                }
            }
            if (this.onTimeUpdate) {
                this.onTimeUpdate(target, this.duration);
            }
        }

        startProgress() {
            cancelAnimationFrame(this.animFrame);
            const loop = () => {
                if (!this.isPlaying) return;
                const cur = Math.min(this.duration, Math.max(0, this.audioCtx.currentTime - this.startTime));
                if (this.onTimeUpdate) {
                    this.onTimeUpdate(cur, this.duration);
                }
                if (cur < this.duration) {
                    this.animFrame = requestAnimationFrame(loop);
                }
            };
            this.animFrame = requestAnimationFrame(loop);
        }
    }

    let activeCustomPlayer = null;
    let currentPreviewPlayer = null;

    const createCustomAudioPlayer = (source, defaultDurationText = 'Voice Note ♫', initialDurationSec = 0) => {
        const playerContainer = document.createElement('div');
        playerContainer.className = 'custom-voice-card-player';

        const playBtn = document.createElement('button');
        playBtn.type = 'button';
        playBtn.className = 'custom-voice-play-round';
        playBtn.innerHTML = '▶';
        playBtn.style.paddingLeft = '3px';

        const track = document.createElement('div');
        track.className = 'custom-voice-track';

        const barBg = document.createElement('div');
        barBg.className = 'custom-voice-bar-bg';

        const barFill = document.createElement('div');
        barFill.className = 'custom-voice-bar-fill';
        barBg.appendChild(barFill);

        const timeRow = document.createElement('div');
        timeRow.className = 'custom-voice-time-row';

        const curTime = document.createElement('span');
        curTime.textContent = '0:00';

        const durTime = document.createElement('span');
        durTime.textContent = initialDurationSec > 0 ? formatTime(initialDurationSec) : defaultDurationText;

        timeRow.appendChild(curTime);
        timeRow.appendChild(durTime);

        track.appendChild(barBg);
        track.appendChild(timeRow);

        playerContainer.appendChild(playBtn);
        playerContainer.appendChild(track);

        const onTimeUpdate = (currentTime, totalDuration) => {
            const cur = Math.max(0, currentTime || 0);
            const dur = totalDuration > 0 ? totalDuration : (initialDurationSec > 0 ? initialDurationSec : 0);
            if (dur > 0) {
                const pct = Math.min(100, Math.max(0, (cur / dur) * 100));
                barFill.style.width = `${pct}%`;
                curTime.textContent = formatTime(Math.floor(cur));
                durTime.textContent = formatTime(Math.floor(dur));
            } else {
                curTime.textContent = formatTime(Math.floor(cur));
            }
        };

        const onEnded = () => {
            playBtn.innerHTML = '▶';
            playBtn.style.paddingLeft = '3px';
            barFill.style.width = '0%';
            curTime.textContent = '0:00';
            if (activeCustomPlayer === voicePlayer) {
                activeCustomPlayer = null;
            }
            safeResumeBgMusic();
        };

        const voicePlayer = new VoicePlayer(source, initialDurationSec, onTimeUpdate, onEnded);

        voicePlayer.init().then(() => {
            if (voicePlayer.duration > 0) {
                durTime.textContent = formatTime(Math.floor(voicePlayer.duration));
            }
        }).catch(() => {});

        const stopPlaying = () => {
            voicePlayer.stop();
            playBtn.innerHTML = '▶';
            playBtn.style.paddingLeft = '3px';
            barFill.style.width = '0%';
            curTime.textContent = '0:00';
            if (activeCustomPlayer === voicePlayer) {
                activeCustomPlayer = null;
            }
        };

        playBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation();

            if (!voicePlayer.isPlaying) {
                if (activeCustomPlayer && activeCustomPlayer !== voicePlayer) {
                    activeCustomPlayer.stop();
                }

                if (bgMusic && !bgMusic.paused) {
                    bgMusic.pause();
                }

                activeCustomPlayer = voicePlayer;

                try {
                    await voicePlayer.play();
                    playBtn.innerHTML = '⏸';
                    playBtn.style.paddingLeft = '0px';
                } catch (err) {
                    console.error('Voice playback error:', err);
                    playBtn.innerHTML = '▶';
                    playBtn.style.paddingLeft = '3px';
                }
            } else {
                voicePlayer.pause();
                playBtn.innerHTML = '▶';
                playBtn.style.paddingLeft = '3px';
                if (activeCustomPlayer === voicePlayer) {
                    activeCustomPlayer = null;
                }
                safeResumeBgMusic();
            }
        });

        barBg.addEventListener('click', (e) => {
            e.stopPropagation();
            const rect = barBg.getBoundingClientRect();
            const pos = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
            voicePlayer.seek(pos);
        });

        return { container: playerContainer, player: voicePlayer, stopPlaying };
    };

    const renderRecordedAudioPlayer = (blob, ext, recordedDurationSec) => {
        selectedAudioBlob = blob;
        selectedAudioExt = ext;

        if (currentPreviewPlayer) {
            currentPreviewPlayer.stopPlaying();
            currentPreviewPlayer = null;
        }

        const totalSecs = Math.max(1, recordedDurationSec || 1);
        const formattedTotal = formatTime(totalSecs);

        if (audioDisplay) {
            audioDisplay.innerHTML = '';
            const playerObj = createCustomAudioPlayer(blob, formattedTotal, totalSecs);
            currentPreviewPlayer = playerObj;

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.id = 'removeAudioBtn';
            removeBtn.style.cssText = 'background: none; border: none; color: #ff6b81; font-size: 0.82rem; cursor: pointer; text-decoration: underline; align-self: flex-end; padding: 2px 4px; font-weight: 600; margin-top: 4px;';
            removeBtn.textContent = 'Remove Voice Note';
            removeBtn.addEventListener('click', () => {
                playerObj.stopPlaying();
                currentPreviewPlayer = null;
                selectedAudioBlob = null;
                audioDisplay.innerHTML = '';
                if (recordBtn) recordBtn.textContent = 'Record Audio Voice Note';
            });

            const wrap = document.createElement('div');
            wrap.style.cssText = 'display: flex; flex-direction: column; width: 100%; margin: 8px 0;';
            wrap.appendChild(playerObj.container);
            wrap.appendChild(removeBtn);
            audioDisplay.appendChild(wrap);
        }

        if (recordBtn) {
            recordBtn.textContent = 'Re-record Voice Note';
            recordBtn.style.display = 'block';
        }
        if (stopBtn) {
            stopBtn.style.display = 'none';
        }
    };

    const stopAudioRecording = () => {
        clearInterval(recordTimerInterval);
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
        }
    };

    if (recordBtn) {
        recordBtn.addEventListener('click', async () => {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert('Audio recording is not supported on this browser or requires a secure connection (localhost / https).');
                return;
            }

            // Temporarily pause background music so mic doesn't catch background audio
            if (bgMusic && !bgMusic.paused) {
                bgMusic.pause();
            }

            try {
                audioStream = await navigator.mediaDevices.getUserMedia({
                    audio: {
                        echoCancellation: true,
                        noiseSuppression: true,
                        autoGainControl: true
                    }
                });

                const mimeType = getSupportedAudioMimeType();
                const options = mimeType ? { mimeType } : {};
                try {
                    mediaRecorder = new MediaRecorder(audioStream, options);
                } catch (e) {
                    mediaRecorder = new MediaRecorder(audioStream);
                }

                audioChunks = [];

                mediaRecorder.ondataavailable = (event) => {
                    if (event.data && event.data.size > 0) {
                        audioChunks.push(event.data);
                    }
                };

                mediaRecorder.onstop = () => {
                    if (audioStream) {
                        audioStream.getTracks().forEach(track => track.stop());
                        audioStream = null;
                    }

                    const actualMime = mediaRecorder.mimeType || mimeType || 'audio/webm';
                    const ext = 'mp3';
                    const blob = new Blob(audioChunks, { type: actualMime });
                    
                    selectedAudioBlob = blob;
                    selectedAudioExt = ext;
                    const finalSecs = recordSeconds;

                    renderRecordedAudioPlayer(blob, ext, finalSecs);
                };

                mediaRecorder.start(250);

                recordBtn.style.display = 'none';
                stopBtn.style.display = 'block';
                recordSeconds = 0;
                stopBtn.textContent = 'Stop Recording (0:00)';
                recordTimerInterval = setInterval(() => {
                    recordSeconds++;
                    const mins = Math.floor(recordSeconds / 60);
                    const secs = recordSeconds % 60;
                    stopBtn.textContent = `Stop Recording (${mins}:${secs < 10 ? '0' : ''}${secs})`;
                }, 1000);

            } catch (error) {
                console.error('Microphone access error:', error);
                if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
                    alert('Microphone permission was denied.\n\nPlease click the lock / microphone icon on the left side of your browser address bar and choose "Allow", then try again.');
                } else if (error.name === 'NotFoundError' || error.name === 'DevicesNotFoundError') {
                    alert('No microphone was detected on your device. Please plug in a microphone or headset.');
                } else {
                    alert('Unable to access microphone: ' + (error.message || error.name || 'Please check browser permissions.'));
                }
            }
        });
    }

    if (stopBtn) {
        stopBtn.addEventListener('click', () => {
            stopAudioRecording();
        });
    }

    if (saveNoteBtn) {
        saveNoteBtn.addEventListener('click', async () => {
            const text = newNoteText ? newNoteText.value.trim() : '';
            const authorInput = document.getElementById('noteAuthor');
            const author = authorInput ? authorInput.value.trim() : '';
            const selectedColorInput = document.querySelector('input[name="noteColor"]:checked');
            const color = selectedColorInput ? selectedColorInput.value : 'pastel-pink';

            if (!text && !selectedAudioBlob) {
                alert('Please write something or record audio!');
                return;
            }
            if (!author) {
                alert('Please enter your name.');
                return;
            }

            try {
                const formData = new FormData();
                formData.append('text', text);
                formData.append('author', author);
                formData.append('color', color);
                if (selectedAudioBlob) {
                    formData.append('audio', selectedAudioBlob, 'audio_note.' + selectedAudioExt);
                }

                const response = await fetch('save_note.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.status === 'success') {
                    newNoteText.value = '';
                    if (authorInput) authorInput.value = '';
                    selectedAudioBlob = null;
                    if (audioDisplay) audioDisplay.innerHTML = '';
                    recordBtn.textContent = 'Record Audio Voice Note';
                    recordBtn.style.display = 'block';
                    stopBtn.style.display = 'none';
                    await loadNotes();
                } else {
                    alert(result.message || 'Error saving note.');
                }
            } catch (error) {
                console.error(error);
            }
        });
    }

    let isMusicMuted = localStorage.getItem('bgMusicMuted') === 'true';

    const updateMusicToggleUI = () => {
        if (!musicToggleBtn) return;
        const icon = musicToggleBtn.querySelector('.music-note-icon') || musicToggleBtn;
        if (isMusicMuted) {
            musicToggleBtn.classList.add('muted');
            musicToggleBtn.title = "Play Love Song";
            if (icon) icon.textContent = "🔇";
        } else {
            musicToggleBtn.classList.remove('muted');
            musicToggleBtn.title = "Mute Love Song";
            if (icon) icon.textContent = "♫";
        }
    };

    const restoreMusicPosition = () => {
        if (!bgMusic) return;
        const savedTime = parseFloat(sessionStorage.getItem('bgMusicCurrentTime') || '0');
        if (savedTime > 0 && isFinite(savedTime)) {
            try {
                bgMusic.currentTime = savedTime;
            } catch (e) {}
        }
    };

    const safeResumeBgMusic = () => {
        isMusicMuted = localStorage.getItem('bgMusicMuted') === 'true';
        if (bgMusic && !isMusicMuted && bgMusic.paused) {
            bgMusic.muted = false;
            bgMusic.play().then(() => updateMusicToggleUI()).catch(() => {});
        }
    };

    const tryStartMusic = () => {
        isMusicMuted = localStorage.getItem('bgMusicMuted') === 'true';
        if (!bgMusic || isMusicMuted) return;
        bgMusic.muted = false;
        restoreMusicPosition();
        bgMusic.play().then(() => {
            updateMusicToggleUI();
        }).catch(() => {
            // If browser autoplay policy requires user interaction on new page, resume on first interaction
            const onFirstInteraction = () => {
                isMusicMuted = localStorage.getItem('bgMusicMuted') === 'true';
                if (bgMusic && !isMusicMuted && bgMusic.paused) {
                    bgMusic.muted = false;
                    restoreMusicPosition();
                    bgMusic.play().then(() => updateMusicToggleUI()).catch(() => {});
                }
                window.removeEventListener('click', onFirstInteraction);
                window.removeEventListener('touchstart', onFirstInteraction);
                window.removeEventListener('scroll', onFirstInteraction);
                window.removeEventListener('keydown', onFirstInteraction);
            };
            window.addEventListener('click', onFirstInteraction, { once: true });
            window.addEventListener('touchstart', onFirstInteraction, { once: true });
            window.addEventListener('scroll', onFirstInteraction, { once: true });
            window.addEventListener('keydown', onFirstInteraction, { once: true });
        });
    };

    if (bgMusic) {
        bgMusic.volume = 0.55;
        bgMusic.muted = isMusicMuted;

        // Persist music position so navigation doesn't restart the song from 0:00
        bgMusic.addEventListener('timeupdate', () => {
            if (bgMusic.currentTime > 0) {
                sessionStorage.setItem('bgMusicCurrentTime', bgMusic.currentTime.toString());
            }
        });

        window.addEventListener('beforeunload', () => {
            if (bgMusic && bgMusic.currentTime > 0) {
                sessionStorage.setItem('bgMusicCurrentTime', bgMusic.currentTime.toString());
            }
        });

        bgMusic.addEventListener('play', () => {
            updateMusicToggleUI();
        });

        bgMusic.addEventListener('pause', () => {
            updateMusicToggleUI();
        });
    }

    if (musicToggleBtn && bgMusic) {
        musicToggleBtn.addEventListener('click', () => {
            if (bgMusic.paused || isMusicMuted) {
                isMusicMuted = false;
                localStorage.setItem('bgMusicMuted', 'false');
                bgMusic.muted = false;
                bgMusic.play().then(() => {
                    updateMusicToggleUI();
                }).catch(err => {
                    console.log('Audio playback error:', err);
                    updateMusicToggleUI();
                });
            } else {
                isMusicMuted = true;
                localStorage.setItem('bgMusicMuted', 'true');
                bgMusic.pause();
                updateMusicToggleUI();
            }
        });
    }

    updateMusicToggleUI();

    // Auto-resume music on moments and notes pages or when album was already entered
    const isMainOverlayVisible = homepageOverlay && homepageOverlay.style.display !== 'none' && !hasNavParam;
    if (!isMusicMuted && (!isMainOverlayVisible || isMomentsPage || isNotesPage)) {
        tryStartMusic();
    }

    // Favorites management functions
    const loadFavorites = () => {
        const stored = localStorage.getItem('photoFavorites');
        favorites = new Set(stored ? JSON.parse(stored) : []);
    };

    const saveFavorites = () => {
        localStorage.setItem('photoFavorites', JSON.stringify(Array.from(favorites)));
    };

    const toggleFavorite = (filename) => {
        if (favorites.has(filename)) {
            favorites.delete(filename);
        } else {
            favorites.add(filename);
        }
        saveFavorites();
        renderMedia();
    };

    const isFavorited = (filename) => {
        return favorites.has(filename);
    };

    loadFavorites();

    const isVideo = (filename) => {
        const nameLower = filename.toLowerCase();
        if (nameLower.includes('audio_note') || nameLower.includes('audio')) return false;
        const ext = filename.split('.').pop().toLowerCase();
        return ['mp4', 'webm', 'ogg', 'mov'].includes(ext);
    };

    const isAudio = (filename) => {
        const nameLower = filename.toLowerCase();
        if (nameLower.includes('audio_note') || nameLower.includes('audio')) return true;
        const ext = filename.split('.').pop().toLowerCase();
        return ['mp3', 'm4a', 'wav'].includes(ext);
    };

    const heroArchFilenames = [
        'photo_6185714979374306348_y.jpg',
        'photo_6185714979374306346_y.jpg',
        'photo_6185714979374306347_y.jpg'
    ];

    const setActiveNav = (activeBtn) => {
        [navPictures, navMoments, navNotes].forEach(btn => {
            if (btn) btn.classList.remove('active');
        });
        if (activeBtn) activeBtn.classList.add('active');
    };

    const showPage = (page) => {
        if (page === 'story') {
            sessionStorage.setItem('albumEntered', 'true');
            if (homepageOverlay) {
                homepageOverlay.classList.add('fade-out');
                homepageOverlay.style.display = 'none';
                homepageOverlay.style.pointerEvents = 'none';
            }
            if (heroSection) heroSection.style.display = 'grid';
            if (photoStripSection) photoStripSection.style.display = 'block';
            if (storyPreviewSection) storyPreviewSection.style.display = 'block';
            if (momentsContainer) momentsContainer.style.display = 'none';
            if (notesContainer) notesContainer.style.display = 'none';
            setActiveNav(navPictures);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            // Remove hero arches and photo strip when clicking to another page!
            if (heroSection) heroSection.style.display = 'none';
            if (photoStripSection) photoStripSection.style.display = 'none';
            if (storyPreviewSection) storyPreviewSection.style.display = 'none';

            if (page === 'moments') {
                if (momentsContainer) momentsContainer.style.display = 'block';
                if (notesContainer) notesContainer.style.display = 'none';
                setActiveNav(navMoments);
                renderMedia();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else if (page === 'notes') {
                if (momentsContainer) momentsContainer.style.display = 'none';
                if (notesContainer) notesContainer.style.display = 'block';
                setActiveNav(navNotes);
                loadNotes();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
    };

    if (navPictures) {
        navPictures.addEventListener('click', (e) => {
            if (window.location.pathname.endsWith('moments.php') || window.location.pathname.endsWith('notes.php')) {
                return; // Let normal navigation to index.php?page=story proceed
            }
            e.preventDefault();
            sessionStorage.setItem('albumEntered', 'true');
            if (homepageOverlay) {
                homepageOverlay.classList.add('fade-out');
                homepageOverlay.style.display = 'none';
                homepageOverlay.style.pointerEvents = 'none';
            }
            showPage('story');
        });
    }

    if (homepageOverlay) {
        if (hasNavParam && !isReload) {
            homepageOverlay.classList.add('fade-out');
            homepageOverlay.style.display = 'none';
            homepageOverlay.style.pointerEvents = 'none';
        } else {
            homepageOverlay.classList.remove('fade-out');
            homepageOverlay.style.display = 'flex';
            homepageOverlay.style.pointerEvents = 'auto';
            if (window.history && window.history.replaceState) {
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        }
    }

    if (initialPage && ['notes', 'moments', 'story'].includes(initialPage)) {
        showPage(initialPage);
    }

    if (brandLogo) {
        brandLogo.addEventListener('click', (e) => {
            if (!window.location.pathname.endsWith('moments.php') && !window.location.pathname.endsWith('notes.php')) {
                e.preventDefault();
                showPage('story');
            }
        });
    }

    const createMediaFrame = (media, currentList) => {
        const frame = document.createElement('div');
        frame.className = 'photo-frame';
        frame.setAttribute('data-filename', media);

        let mediaElement;
        if (isVideo(media)) {
            mediaElement = document.createElement('video');
            mediaElement.src = `uploads/${media}`;
            mediaElement.preload = "metadata";
            mediaElement.muted = true;
            frame.addEventListener('mouseenter', () => { mediaElement.play().catch(() => {}); });
            frame.addEventListener('mouseleave', () => { mediaElement.pause(); mediaElement.currentTime = 0; });
        } else {
            mediaElement = document.createElement('img');
            mediaElement.src = `uploads/${media}`;
            mediaElement.alt = "Our memory";
            mediaElement.loading = "lazy";
        }

        const caption = document.createElement('div');
        caption.className = 'photo-caption';
        caption.innerText = isVideo(media) ? "Video Clip" : "Memory";

        frame.appendChild(mediaElement);
        frame.appendChild(caption);

        // Click to open in lightbox
        frame.addEventListener('click', () => {
            activeLightboxList = currentList;
            currentLightboxIndex = currentList.indexOf(media);
            if (modal) modal.style.display = "flex";
            updateLightboxMedia();
        });

        // Context menu right-click for delete
        frame.addEventListener('contextmenu', (e) => {
            selectedMemoryFilename = media;
            selectedNoteTimestamp = null;
            positionContextMenu(e, frame);
            if (deleteMomentBtn) deleteMomentBtn.style.display = 'block';
            if (deleteNoteBtn) deleteNoteBtn.style.display = 'none';
        });

        return frame;
    };

    const renderMedia = () => {
        // Exclude hero arch photos
        const availableMedia = allMedia.filter(f => !heroArchFilenames.includes(f) && !isAudio(f));
        const photosList = availableMedia.filter(f => !isVideo(f));
        const videosList = availableMedia.filter(f => isVideo(f));

        const photoCount = document.getElementById('photoCount');
        const videoCount = document.getElementById('videoCount');
        const photoCountBadge = document.getElementById('photoCountBadge');
        const videoCountBadge = document.getElementById('videoCountBadge');

        if (photoCount) photoCount.textContent = photosList.length;
        if (videoCount) videoCount.textContent = videosList.length;
        if (photoCountBadge) photoCountBadge.textContent = `${photosList.length} Photos`;
        if (videoCountBadge) videoCountBadge.textContent = `${videosList.length} Videos`;

        // Render Photographs
        if (photoGrid) {
            photoGrid.innerHTML = '';
            if (photosList.length === 0) {
                photoGrid.innerHTML = '<div class="empty-media-card"><p>No photographs uploaded yet.</p></div>';
            } else {
                photosList.forEach(m => photoGrid.appendChild(createMediaFrame(m, photosList)));
            }
        }

        // Render Videos
        if (videoGrid) {
            videoGrid.innerHTML = '';
            if (videosList.length === 0) {
                videoGrid.innerHTML = '<div class="empty-media-card"><p>No video memories uploaded yet.</p></div>';
            } else {
                videosList.forEach(m => videoGrid.appendChild(createMediaFrame(m, videosList)));
            }
        }
    };

    // Interactive moments view filter tabs (All Moments / Photographs / Video Memories)
    const filterAllBtn = document.getElementById('filterAllBtn');
    const filterPhotosBtn = document.getElementById('filterPhotosBtn');
    const filterVideosBtn = document.getElementById('filterVideosBtn');
    const photosGroup = document.getElementById('photosGroup');
    const videosGroup = document.getElementById('videosGroup');

    const setMomentsView = (view) => {
        if (!photosGroup || !videosGroup) return;

        [filterAllBtn, filterPhotosBtn, filterVideosBtn].forEach(b => b && b.classList.remove('active'));

        if (view === 'photos') {
            photosGroup.style.display = 'block';
            videosGroup.style.display = 'none';
            if (filterPhotosBtn) filterPhotosBtn.classList.add('active');
        } else if (view === 'videos') {
            photosGroup.style.display = 'none';
            videosGroup.style.display = 'block';
            if (filterVideosBtn) filterVideosBtn.classList.add('active');
        } else {
            photosGroup.style.display = 'block';
            videosGroup.style.display = 'block';
            if (filterAllBtn) filterAllBtn.classList.add('active');
        }
    };

    if (filterPhotosBtn) filterPhotosBtn.addEventListener('click', () => setMomentsView('photos'));
    if (filterVideosBtn) filterVideosBtn.addEventListener('click', () => setMomentsView('videos'));
    if (filterAllBtn) filterAllBtn.addEventListener('click', () => setMomentsView('all'));

    const loadMedia = async () => {
        try {
            const response = await fetch('get_photos.php');
            const data = await response.json();
            if (data.status === 'success') {
                allMedia = data.photos;
                renderMedia();
            }
        } catch (error) {
            console.error('Error fetching media:', error);
        }
    };

    loadMedia();

    // Circular stamp click action
    const stampBadge = document.getElementById('stampBadge');
    if (stampBadge) {
        stampBadge.addEventListener('click', () => {
            createFloatingHearts();
            safeResumeBgMusic();
        });
    }

    // Clicking arch cards on OUR STORY to open in lightbox
    document.querySelectorAll('.arch-card').forEach((card) => {
        card.addEventListener('click', () => {
            const img = card.querySelector('img');
            if (img && img.src) {
                if (expandedVideo) expandedVideo.style.display = "none";
                if (expandedImg) {
                    expandedImg.src = img.src;
                    expandedImg.style.display = "block";
                }
                activeLightboxList = [img.src];
                currentLightboxIndex = 0;
                if (modal) modal.style.display = "flex";
            }
        });
    });

    // Smooth 60fps GPU Animation: Continuous ambient drift + Scroll-driven right-to-left slide
    if (photoStripTrack) {
        let currentStripX = 0;
        let targetStripX = 0;
        let autoDrift = 0;
        let isTrackHovered = false;
        const stripCards = Array.from(photoStripTrack.querySelectorAll('.strip-card'));

        photoStripTrack.addEventListener('mouseenter', () => { isTrackHovered = true; });
        photoStripTrack.addEventListener('mouseleave', () => { isTrackHovered = false; });

        const animatePhotoStrip = () => {
            if (photoStripSection && photoStripSection.style.display !== 'none') {
                const scrollY = window.pageYOffset || document.documentElement.scrollTop || 0;

                // Ambient gentle floating drift
                if (!isTrackHovered) {
                    autoDrift += 0.8;
                }

                // Every pixel of scroll down advances the strip from the right edge to the left edge
                targetStripX = (scrollY * 4.8) + autoDrift;

                // Silky smooth interpolation (lerp)
                currentStripX += (targetStripX - currentStripX) * 0.16;

                // Seamless infinite wrap calculation across repeating 5-card sets
                const totalWidth = photoStripTrack.scrollWidth;
                const singleSetWidth = totalWidth > 0 ? totalWidth / 5 : 1210;
                const displayX = ((currentStripX % singleSetWidth) + singleSetWidth) % singleSetWidth;

                // Glide smoothly from right edge across to left edge
                photoStripTrack.style.transform = `translate3d(-${displayX.toFixed(2)}px, 0, 0)`;

                // Zero-reflow 3D perspective calculation (pure math, no getBoundingClientRect)
                const cardPitch = stripCards.length > 0 ? (stripCards[0].offsetWidth || 220) + 22 : 242;
                const viewportCenter = window.innerWidth / 2;

                stripCards.forEach((card, index) => {
                    const cardCenter = (index * cardPitch) + (cardPitch / 2) - displayX;
                    if (cardCenter > -200 && cardCenter < window.innerWidth + 200) {
                        const distFromCenter = cardCenter - viewportCenter;
                        const normalized = Math.max(-1.3, Math.min(1.3, distFromCenter / (window.innerWidth * 0.45)));

                        const rotateY = normalized * -22;
                        const translateZ = (1 - Math.min(1, Math.abs(normalized))) * 45 - 20;
                        const scale = 1 - Math.min(1, Math.abs(normalized)) * 0.05;

                        card.style.transform = `rotateY(${rotateY.toFixed(2)}deg) translateZ(${translateZ.toFixed(1)}px) scale(${scale.toFixed(3)})`;
                    }
                });
            }
            requestAnimationFrame(animatePhotoStrip);
        };

        requestAnimationFrame(animatePhotoStrip);
    }

    if (photoStripTrack) {
        photoStripTrack.addEventListener('click', (e) => {
            const card = e.target.closest('.strip-card');
            if (card) {
                const img = card.querySelector('img');
                if (img && img.src) {
                    if (expandedVideo) expandedVideo.style.display = "none";
                    if (expandedImg) {
                        expandedImg.src = img.src;
                        expandedImg.style.display = "block";
                    }
                    activeLightboxList = [img.src];
                    currentLightboxIndex = 0;
                    if (modal) modal.style.display = "flex";
                }
            }
        });
    }

    const updateLightboxMedia = () => {
        if (!activeLightboxList || activeLightboxList.length === 0) return;
        if (currentLightboxIndex < 0 || currentLightboxIndex >= activeLightboxList.length) return;
        const media = activeLightboxList[currentLightboxIndex];

        if (isVideo(media)) {
            if (expandedImg) expandedImg.style.display = "none";
            if (expandedVideo) {
                expandedVideo.src = `uploads/${media}`;
                expandedVideo.style.display = "block";
                expandedVideo.play().catch(err => console.log(err));
            }
        } else {
            if (expandedVideo) {
                expandedVideo.pause();
                expandedVideo.style.display = "none";
            }
            if (expandedImg) {
                expandedImg.src = media.startsWith('uploads/') || media.startsWith('http') ? media : `uploads/${media}`;
                expandedImg.style.display = "block";
            }
        }
    };

    if (prevBtn) {
        prevBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (!activeLightboxList || activeLightboxList.length <= 1) return;
            currentLightboxIndex = (currentLightboxIndex - 1 + activeLightboxList.length) % activeLightboxList.length;
            updateLightboxMedia();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (!activeLightboxList || activeLightboxList.length <= 1) return;
            currentLightboxIndex = (currentLightboxIndex + 1) % activeLightboxList.length;
            updateLightboxMedia();
        });
    }

    if (photoUpload) {
        photoUpload.addEventListener('change', async (e) => {
            if (!e.target.files.length || !uploadForm) return;
            const formData = new FormData(uploadForm);
            if (loadingText) {
                loadingText.style.display = 'block';
                loadingText.textContent = `Uploading ${e.target.files.length} memory file(s)...`;
            }
            try {
                const response = await fetch('upload.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.status === 'success') {
                    await loadMedia();
                    if (!window.location.pathname.endsWith('moments.php')) {
                        showPage('moments');
                    }
                } else {
                    alert(result.message || 'Failed to upload file.');
                }
            } catch (error) {
                console.error(error);
                alert('An error occurred while uploading. Please check the file size.');
            } finally {
                if (loadingText) loadingText.style.display = 'none';
                photoUpload.value = '';
            }
        });
    }

    const positionContextMenu = (e, target) => {
        e.preventDefault();
        if (!contextMenu) return;
        contextMenu.style.display = 'block';
        const targetRect = target.getBoundingClientRect();
        const menuRect = contextMenu.getBoundingClientRect();
        contextMenu.style.left = `${targetRect.right + window.scrollX - menuRect.width - 10}px`;
        contextMenu.style.top = `${targetRect.top + window.scrollY + 10}px`;
    };

    document.addEventListener('contextmenu', (e) => {
        const target = e.target.closest('.photo-frame');
        if (target) {
            const filename = target.getAttribute('data-filename');
            if (filename) {
                selectedMemoryFilename = filename;
                selectedNoteTimestamp = null;
                if (deleteMomentBtn) deleteMomentBtn.style.display = 'block';
                if (deleteNoteBtn) deleteNoteBtn.style.display = 'none';
                positionContextMenu(e, target);
            }
        }
    });

    if (notesGrid) {
        notesGrid.addEventListener('contextmenu', (e) => {
            const card = e.target.closest('.note-card');
            if (card) {
                selectedNoteTimestamp = card.getAttribute('data-timestamp');
                selectedMemoryFilename = null;
                if (deleteMomentBtn) deleteMomentBtn.style.display = 'none';
                if (deleteNoteBtn) deleteNoteBtn.style.display = 'block';
                positionContextMenu(e, card);
            }
        });
    }

    document.addEventListener('click', () => {
        if (contextMenu) contextMenu.style.display = 'none';
    });

    if (deleteMomentBtn) {
        deleteMomentBtn.addEventListener('click', async () => {
            if (!selectedMemoryFilename) return;
            if (!confirm('Are you sure you want to delete this memory?')) return;
            try {
                const response = await fetch('delete_photo.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'filename=' + encodeURIComponent(selectedMemoryFilename)
                });
                const result = await response.json();
                if (result.status === 'success') await loadMedia();
                if (contextMenu) contextMenu.style.display = 'none';
            } catch (error) {
                console.error(error);
            }
        });
    }

    if (deleteNoteBtn) {
        deleteNoteBtn.addEventListener('click', async () => {
            if (!selectedNoteTimestamp) return;
            if (!confirm('Are you sure you want to delete this note?')) return;
            try {
                const response = await fetch('delete_note.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'timestamp=' + encodeURIComponent(selectedNoteTimestamp)
                });
                const result = await response.json();
                if (result.status === 'success') await loadNotes();
                if (contextMenu) contextMenu.style.display = 'none';
            } catch (error) {
                console.error(error);
            }
        });
    }

    const hideModal = () => {
        if (modal) modal.style.display = "none";
        if (expandedVideo) {
            expandedVideo.pause();
            expandedVideo.src = "";
        }
        safeResumeBgMusic();
    };

    if (closeModal) closeModal.addEventListener('click', hideModal);
    window.addEventListener('click', (e) => {
        if (e.target === modal) hideModal();
    });

    document.addEventListener('keydown', (e) => {
        if (!modal || modal.style.display !== 'flex') return;
        if (e.key === 'Escape') hideModal();
        if (e.key === 'ArrowLeft' && prevBtn) prevBtn.click();
        if (e.key === 'ArrowRight' && nextBtn) nextBtn.click();
    });

    if (expandedVideo && bgMusic) {
        expandedVideo.addEventListener('play', () => { bgMusic.pause(); });
        expandedVideo.addEventListener('pause', () => {
            if (modal && modal.style.display === "flex") safeResumeBgMusic();
        });
    }

    // Global audio control for voice note cards using event capturing
    document.addEventListener('play', (e) => {
        const target = e.target;
        if (target && target.tagName && target.tagName.toLowerCase() === 'audio' && target.id !== 'bgMusic') {
            if (bgMusic && !bgMusic.paused) {
                bgMusic.pause();
            }
        }
    }, true);

    document.addEventListener('pause', (e) => {
        const target = e.target;
        if (target && target.tagName && target.tagName.toLowerCase() === 'audio' && target.id !== 'bgMusic') {
            safeResumeBgMusic();
        }
    }, true);

    const createFloatingHearts = () => {
        const heartCount = 50;
        const container = document.createElement('div');
        container.style.position = 'fixed';
        container.style.top = '0';
        container.style.left = '0';
        container.style.width = '100vw';
        container.style.height = '100vh';
        container.style.pointerEvents = 'none';
        container.style.zIndex = '99999';
        document.body.appendChild(container);
        const emojis = ['❤️', '💖', '💕', '💗', '💓', '💝', '🌸', '💘', '🤍', '✨'];
        for (let i = 0; i < heartCount; i++) {
            const heart = document.createElement('div');
            heart.className = 'floating-heart';
            heart.innerHTML = emojis[Math.floor(Math.random() * emojis.length)];
            const left = Math.random() * 100;
            const size = Math.random() * 50 + 35;
            const delay = Math.random() * 0.9;
            const duration = Math.random() * 1.5 + 2.0;
            const sway = (Math.random() * 140) - 70;
            heart.style.setProperty('--left', `${left}vw`);
            heart.style.setProperty('--size', `${size}px`);
            heart.style.setProperty('--delay', `${delay}s`);
            heart.style.setProperty('--duration', `${duration}s`);
            heart.style.setProperty('--sway', `${sway}px`);
            container.appendChild(heart);
        }
        setTimeout(() => { container.remove(); }, 5000);
    };

    if (enterBtn && homepageOverlay) {
        enterBtn.addEventListener('click', () => {
            if (homepageOverlay.style.pointerEvents === 'none') return;
            homepageOverlay.style.pointerEvents = 'none';
            createFloatingHearts();
            homepageOverlay.classList.add('fade-out');
            setTimeout(() => {
                homepageOverlay.style.display = 'none';
            }, 800);
            safeResumeBgMusic();
        });
    }

    if (timeTogether) {
        const anniversaryDate = new Date('2026-04-22T00:00:00');
        const updateTimer = () => {
            const now = new Date();
            let diff = now - anniversaryDate;
            if (diff < 0) diff = 0;
            const seconds = Math.floor(diff / 1000) % 60;
            const minutes = Math.floor(diff / (1000 * 60)) % 60;
            const hours = Math.floor(diff / (1000 * 60 * 60)) % 24;
            const totalDays = Math.floor(diff / (1000 * 60 * 60 * 24));
            const months = Math.floor(totalDays / 30);
            const days = totalDays % 30;
            let parts = [];
            if (months > 0) parts.push(`${months} month${months !== 1 ? 's' : ''}`);
            if (days > 0) parts.push(`${days} day${days !== 1 ? 's' : ''}`);
            parts.push(`${hours}h ${minutes}m ${seconds}s`);
            timeTogether.textContent = parts.join(', ');
        };
        updateTimer();
        setInterval(updateTimer, 1000);
    }

    const loadNotes = async () => {
        if (!notesGrid) return;
        try {
            const response = await fetch('get_notes.php');
            const data = await response.json();
            if (data.status === 'success') {
                notesGrid.innerHTML = '';
                if (data.notes.length === 0) return;
                data.notes.forEach(note => {
                    const card = document.createElement('div');
                    const noteColorClass = note.color ? note.color : 'pastel-pink';
                    card.className = `note-card ${noteColorClass}`;
                    card.setAttribute('data-timestamp', note.timestamp);

                    const noteDate = new Date(note.timestamp * 1000);
                    const formattedDate = noteDate.toLocaleDateString('en-US', {
                        month: 'long', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit'
                    });

                    const textElement = document.createElement('p');
                    textElement.className = 'note-text';
                    const parser = new DOMParser();
                    textElement.textContent = parser.parseFromString(note.text, 'text/html').body.textContent;

                    const authorName = note.author || note.Author || note.name || "Sweetheart";
                    const authorElement = document.createElement('div');
                    authorElement.className = 'note-author';
                    authorElement.textContent = `— ${authorName}`;

                    const dateElement = document.createElement('div');
                    dateElement.className = 'note-date';
                    dateElement.textContent = formattedDate;

                    card.appendChild(textElement);

                    if (note.audio) {
                        const audioContainer = document.createElement('div');
                        audioContainer.className = 'note-audio';
                        const player = createCustomAudioPlayer('uploads/' + encodeURIComponent(note.audio), 'Voice Note ♫');
                        audioContainer.appendChild(player.container);
                        card.appendChild(audioContainer);
                    }

                    card.appendChild(authorElement);
                    card.appendChild(dateElement);
                    notesGrid.appendChild(card);
                });
            }
        } catch (error) {
            console.error(error);
        }
    };

    window.loadNotes = loadNotes;

    window.toggleAudio = function(audioId = "myAudio", buttonId = "audioButton") {
        var audio = typeof audioId === 'string' ? document.getElementById(audioId) : audioId;
        var button = typeof buttonId === 'string' ? document.getElementById(buttonId) : buttonId;
        var bgMusic = document.getElementById("bgMusic");

        if (!audio || !button) return;

        if (audio.paused) {
            document.querySelectorAll('audio').forEach(function(a) {
                if (a !== audio && a.id !== 'bgMusic' && !a.paused) {
                    a.pause();
                }
            });
            if (bgMusic && !bgMusic.paused) {
                bgMusic.pause();
            }
            audio.play().then(function() {
                button.innerHTML = "Pause";
            }).catch(function(err) {
                console.error("Playback error:", err);
            });
        } else {
            audio.pause();
            button.innerHTML = "Play";
            safeResumeBgMusic();
        }

        audio.onended = function() {
            button.innerHTML = "Play";
            safeResumeBgMusic();
        };
    };

    // Automatically load notes if notesGrid is present (e.g. on notes.php)
    if (notesGrid) {
        loadNotes();
    }

    const sparkleEmojis = ['✨', '💕', '💖', '🌸', '💗'];
    document.addEventListener('click', (e) => {
        const tag = e.target.tagName.toLowerCase();
        if (['button', 'input', 'label', 'textarea', 'a', 'select', 'video', 'audio'].includes(tag)) return;
        if (e.target.closest('.modal') || (contextMenu && e.target.closest('.context-menu')) || (homepageOverlay && e.target.closest('.homepage-overlay'))) return;

        const sparkle = document.createElement('div');
        sparkle.className = 'click-sparkle';
        sparkle.textContent = sparkleEmojis[Math.floor(Math.random() * sparkleEmojis.length)];
        sparkle.style.left = `${e.clientX}px`;
        sparkle.style.top = `${e.clientY}px`;
        document.body.appendChild(sparkle);
        setTimeout(() => sparkle.remove(), 800);
    });
});