<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Denda</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .header { text-align: center; margin-bottom: 24px; }
        .header h1 { margin: 0; font-size: 22px; }
        .header p { margin: 4px 0 0; font-size: 14px; }
        .summary { margin-bottom: 20px; }
        .summary .item { margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ccc; padding: 8px 10px; font-size: 12px; }
        th { background: #f7f7f7; text-align: left; }
        .small { font-size: 11px; color: #555; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Denda</h1>
        <p>{{ now()->format('d F Y H:i') }}</p>
    </div>

    <div class="summary">
        <div class="item"><strong>Total Denda:</strong> {{ number_format($summary['total_penalties'] ?? 0) }}</div>
        <div class="item"><strong>Denda Belum Bayar:</strong> {{ number_format($summary['unpaid_count'] ?? 0) }}</div>
        <div class="item"><strong>Denda Sudah Bayar:</strong> {{ number_format($summary['paid_count'] ?? 0) }}</div>
        <div class="item"><strong>Total Nominal Belum Bayar:</strong> Rp {{ number_format(($summary['unpaid_amount'] ?? 0) / 100, 0, ',', '.') }}</div>
        <div class="item"><strong>Total Nominal Sudah Bayar:</strong> Rp {{ number_format(($summary['paid_amount'] ?? 0) / 100, 0, ',', '.') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>User</th>
                <th>Booking</th>
                <th>Kondisi</th>
                <th>Denda</th>
                <th>Status</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
                @php
                    $payable = $payment->payable;
                    $user = $payable?->user;
                    $condition = data_get($payment->meta, 'issue_condition', $payable?->issue_condition);
                    $conditionLabel = $condition ? ucfirst($condition) : '-';
                    $bookCode = data_get($payment->meta, 'book_code', $payable?->book_code);
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $user?->name ?? '-' }}</td>
                    <td>{{ $bookCode ?? '-' }}</td>
                    <td>{{ $conditionLabel }}</td>
                    <td>Rp {{ number_format($payment->amount / 100, 0, ',', '.') }}</td>
                    <td>{{ ucfirst($payment->status) }}</td>
                    <td>{{ $payment->created_at->format('d-m-Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="small">Tidak ada data denda untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
