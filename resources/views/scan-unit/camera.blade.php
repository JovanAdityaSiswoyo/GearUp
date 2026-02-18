<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan QR Code</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .scanner-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 24px;
            max-width: 500px;
            width: 100%;
        }

        h2 {
            font-size: 24px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 16px;
            text-align: center;
        }

        .video-container {
            position: relative;
            width: 100%;
            margin-bottom: 24px;
            border-radius: 8px;
            overflow: hidden;
            background: #000;
        }

        video, canvas {
            width: 100%;
            display: block;
            border-radius: 8px;
        }

        .status {
            text-align: center;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 14px;
            font-weight: 500;
        }

        .status.waiting {
            background: #e0f2fe;
            color: #0369a1;
        }

        .status.success {
            background: #dcfce7;
            color: #166534;
        }

        .status.error {
            background: #fee2e2;
            color: #991b1b;
        }

        .results {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
            margin-top: 16px;
            min-height: 80px;
        }

        .results-text {
            font-size: 14px;
            color: #6b7280;
            text-align: center;
        }

        .serial-display {
            font-size: 18px;
            font-weight: 600;
            color: #059669;
            text-align: center;
            margin-top: 8px;
            font-family: 'Monaco', 'Courier New', monospace;
        }

        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 16px;
        }

        button {
            flex: 1;
            padding: 12px 16px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        button.primary {
            background: #3b82f6;
            color: white;
        }

        button.primary:hover {
            background: #2563eb;
        }

        button.secondary {
            background: #e5e7eb;
            color: #374151;
        }

        button.secondary:hover {
            background: #d1d5db;
        }

        button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .info-box {
            background: #fef3c7;
            border: 1px solid #fcd34d;
            border-radius: 6px;
            padding: 12px;
            font-size: 12px;
            color: #92400e;
            margin-bottom: 16px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="scanner-container">
        <h2>📱 Scan QR Code</h2>
        
        <div class="info-box">
            Arahkan kamera ke label QR code milik unit. Pastikan pencahayaan cukup untuk hasil optimal.
        </div>

        <div class="status waiting" id="status">
            ⏳ Menunggu QR code...
        </div>

        <div class="video-container">
            <video id="video" autoplay playsinline></video>
            <canvas id="canvas" style="display: none;"></canvas>
        </div>

        <div class="results">
            <div class="results-text" id="results">
                Hasil scan akan muncul di sini
            </div>
            <div class="serial-display" id="serialDisplay"></div>
        </div>

        <div class="button-group">
            <button class="secondary" onclick="window.close()">Tutup</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');
        const resultsElement = document.getElementById('results');
        const statusElement = document.getElementById('status');
        const serialDisplay = document.getElementById('serialDisplay');
        let isProcessing = false;

        // Request camera access
        navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'environment' }
        }).then(stream => {
            video.srcObject = stream;
            video.addEventListener('play', () => {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                scanQR();
            });
        }).catch(err => {
            statusElement.className = 'status error';
            statusElement.textContent = '❌ Akses kamera ditolak. Periksa izin browser.';
            resultsElement.textContent = 'Error: ' + err.message;
        });

        function scanQR() {
            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const code = jsQR(imageData.data, imageData.width, imageData.height);

                if (code) {
                    const data = code.data;
                    
                    // Extract unit ID from /scan-unit/{unit-id}
                    const match = data.match(/\/scan-unit\/([a-f0-9\-]+)$/i);
                    
                    if (match && match[1]) {
                        const unitId = match[1];
                        
                        // Extract serial from unit details via API
                        fetch(`/api/units/${unitId}`)
                            .then(r => r.json())
                            .then(unit => {
                                statusElement.className = 'status success';
                                statusElement.textContent = '✅ QR Code Scanned!';
                                resultsElement.textContent = `Unit ID: ${unitId}`;
                                serialDisplay.textContent = unit.serial_number || 'N/A';
                                
                                // Send back to parent window
                                if (window.parent !== window) {
                                    window.parent.postMessage({
                                        action: 'qr_scanned',
                                        serial: unit.serial_number
                                    }, '*');
                                }
                                
                                // Auto-close after 1.5 seconds
                                setTimeout(() => {
                                    if (window.parent !== window) {
                                        window.close();
                                    }
                                }, 1500);
                            })
                            .catch(err => {
                                // If API fails, use serial from QR directly if available
                                statusElement.className = 'status success';
                                statusElement.textContent = '✅ QR Code Scanned!';
                                serialDisplay.textContent = unitId;
                                
                                if (window.parent !== window) {
                                    window.parent.postMessage({
                                        action: 'qr_scanned',
                                        serial: unitId
                                    }, '*');
                                }
                                
                                setTimeout(() => {
                                    if (window.parent !== window) {
                                        window.close();
                                    }
                                }, 1500);
                            });
                    } else {
                        statusElement.className = 'status error';
                        statusElement.textContent = '❌ QR Code tidak valid untuk unit';
                        resultsElement.textContent = 'Data: ' + data.substring(0, 50) + '...';
                    }
                }
            }
            
            requestAnimationFrame(scanQR);
        }
    </script>
</body>
</html>
