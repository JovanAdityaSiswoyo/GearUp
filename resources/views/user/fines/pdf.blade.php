<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Denda - {{ $user->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .summary h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #333;
        }
        .summary-grid {
            display: table;
            width: 100%;
        }
        .summary-item {
            display: table-cell;
            padding: 5px;
            text-align: center;
        }
        .summary-item .value {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        .status-paid {
            background-color: #d4edda;
            color: #155724;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        .amount {
            font-weight: bold;
            color: #dc3545;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Denda</h1>
        <p>Nama: {{ $user->name }}</p>
        <p>Email: {{ $user->email }}</p>
        <p>Tanggal Laporan: {{ now()->format('d M Y H:i') }}</p>
    </div>

    <div class="summary">
        <h3>Ringkasan Denda</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div>Total Denda</div>
                <div class="value">{{ $fines->count() }}</div>
            </div>
            <div class="summary-item">
                <div>Pending</div>
                <div class="value">{{ $fines->where('status', 'pending')->count() }}</div>
            </div>
            <div class="summary-item">
                <div>Lunas</div>
                <div class="value">{{ $fines->where('status', 'paid')->count() }}</div>
            </div>
            <div class="summary-item">
                <div>Total Tagihan</div>
                <div class="value">Rp {{ number_format($fines->where('status', 'pending')->sum('amount') / 100, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Jenis Denda</th>
                <th>Jumlah</th>
                <th>Status</th>
                <th>Tanggal Dibuat</th>
                <th>Referensi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($fines as $index => $fine)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ ucfirst($fine->method) }}</td>
                <td class="amount">Rp {{ number_format($fine->amount, 0, ',', '.') }}</td>
                <td>
                    @if($fine->status === 'pending')
                        <span class="status-pending">Pending</span>
                    @else
                        <span class="status-paid">Lunas</span>
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($fine->created_at)->format('d M Y H:i') }}</td>
                <td>{{ $fine->provider_ref ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada {{ now()->format('d M Y H:i:s') }}</p>
        <p>GearUp - Sistem Manajemen Peralatan</p>
    </div>
</body>
</html>