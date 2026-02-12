<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Print QR Labels</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: white;
            padding: 0;
            margin-top: 120px;
        }

        .page {
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            padding: 10mm;
            background: white;
            page-break-after: always;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 5mm;
            align-content: start;
        }

        .label {
            width: 100%;
            aspect-ratio: 1;
            border: 1px dashed #ccc;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 4mm;
            background: white;
            page-break-inside: avoid;
        }

        .label-qr {
            width: 90%;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2mm;
        }

        .label-qr img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            max-width: 70px;
            max-height: 70px;
        }

        .label-qr svg {
            max-width: 70px;
            max-height: 70px;
        }

        .label-info {
            text-align: center;
            width: 100%;
            flex-shrink: 0;
        }

        .label-info .serial {
            font-size: 8px;
            font-weight: bold;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 2px;
        }

        .label-info .product {
            font-size: 6px;
            color: #666;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .page-counter {
            position: fixed;
            bottom: 10px;
            right: 20px;
            font-size: 10px;
            color: #999;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
                margin-top: 0 !important;
            }

            .page {
                margin: 0;
                padding: 10mm;
                page-break-after: always;
            }

            .page-counter {
                display: none;
            }

            .print-buttons {
                display: none !important;
            }

            .info-box {
                display: none !important;
            }
        }

        .print-buttons {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            background: white;
            padding: 10px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: 0.3s;
        }

        .btn-print {
            background: #10b981;
            color: white;
        }

        .btn-print:hover {
            background: #059669;
        }

        .btn-close {
            background: #ef4444;
            color: white;
        }

        .btn-close:hover {
            background: #dc2626;
        }

        .btn-settings {
            background: #3b82f6;
            color: white;
        }

        .btn-settings:hover {
            background: #2563eb;
        }

        .settings-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }

        .settings-modal.active {
            display: flex;
        }

        .settings-content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }

        .settings-content h3 {
            margin-bottom: 20px;
            font-size: 18px;
            color: #333;
        }

        .settings-group {
            margin-bottom: 15px;
        }

        .settings-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
            color: #555;
            font-weight: 500;
        }

        .settings-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .settings-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .settings-buttons button {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
        }

        .settings-buttons .save {
            background: #10b981;
            color: white;
        }

        .settings-buttons .cancel {
            background: #e5e7eb;
            color: #333;
        }

        .info-box {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: #e0f2fe;
            border: 1px solid #0284c7;
            border-radius: 4px;
            padding: 12px 16px;
            font-size: 12px;
            color: #0c4a6e;
            z-index: 999;
        }
    </style>
</head>
<body>
    <div class="print-buttons">
        <button class="btn btn-settings" onclick="toggleSettings()">⚙️ Settings</button>
        <button class="btn btn-print" onclick="window.print()">🖨️ Print</button>
        <button class="btn btn-close" onclick="window.close()">✕ Close</button>
    </div>

    <div id="settings-modal" class="settings-modal">
        <div class="settings-content">
            <h3>Print Label Settings</h3>
            <div class="settings-group">
                <label for="cols">Columns per page:</label>
                <input type="number" id="cols" value="4" min="1" max="10" onchange="applySettings()">
            </div>
            <div class="settings-group">
                <label for="label-size">Label size (mm):</label>
                <input type="number" id="label-size" value="50" min="20" max="100" onchange="applySettings()">
            </div>
            <div class="settings-buttons">
                <button class="save" onclick="saveSettings()">Apply</button>
                <button class="cancel" onclick="toggleSettings()">Cancel</button>
            </div>
        </div>
    </div>

    <div class="info-box">
        📋 Total labels: {{ $units->count() }} | 
        Page size: A4 (210×297mm) | 
        per page: 20 labels
    </div>

    <!-- Print Pages -->
    @php
        $labelsPerPage = 20; // 4 columns × 5 rows
        $pages = collect($units)->chunk($labelsPerPage);
    @endphp

    @foreach($pages as $pageIndex => $pageUnits)
    <div class="page" id="page-{{ $pageIndex + 1 }}">
        @foreach($pageUnits as $unit)
        <div class="label">
            <div class="label-qr">
                <img src="{{ $qrCodes[$unit->id] }}" alt="QR Code for {{ $unit->serial_number }}" style="width: 100%; height: 100%; object-fit: contain;">
            </div>
            <div class="label-info">
                <div class="serial">{{ $unit->serial_number }}</div>
                <div class="product">{{ substr($unit->product->name, 0, 12) }}</div>
            </div>
        </div>
        @endforeach
    </div>
    @endforeach

    <script>
        function toggleSettings() {
            const modal = document.getElementById('settings-modal');
            modal.classList.toggle('active');
        }

        function applySettings() {
            const cols = document.getElementById('cols').value;
            const size = document.getElementById('label-size').value;

            document.querySelectorAll('.page').forEach(page => {
                page.style.gridTemplateColumns = `repeat(${cols}, 1fr)`;
            });

            document.querySelectorAll('.label').forEach(label => {
                label.style.width = size + 'mm';
                label.style.height = size + 'mm';
            });
        }

        function saveSettings() {
            applySettings();
            toggleSettings();
        }

        // Auto-focus untuk print setelah load
        window.addEventListener('load', () => {
            setTimeout(() => {
                // Bisa auto-print kalau mau: window.print();
            }, 500);
        });
    </script>
</body>
</html>
