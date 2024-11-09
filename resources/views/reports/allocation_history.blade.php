<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Alokasi Keuangan</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
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
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Alokasi Keuangan</h2>
        <p>Tanggal: {{ date('d/m/Y', strtotime($date)) }}</p>
    </div>

    <table>
        <tr>
            <th>Total Dana</th>
            <td colspan="2">Rp {{ number_format($total, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Kategori</th>
            <th>Persentase</th>
            <th>Nominal</th>
        </tr>
        <tr>
            <td>Kebutuhan Primer</td>
            <td>{{ $primary_percentage }}%</td>
            <td>Rp {{ number_format($primary_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Kebutuhan Sekunder</td>
            <td>{{ $secondary_percentage }}%</td>
            <td>Rp {{ number_format($secondary_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Investasi</td>
            <td>{{ $investment_percentage }}%</td>
            <td>Rp {{ number_format($investment_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Pembayaran Utang</td>
            <td>{{ $debt_percentage }}%</td>
            <td>Rp {{ number_format($debt_amount, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div style="margin-top: 30px">
    <p><small>Dicetak pada: {{ \Carbon\Carbon::now('Asia/Jakarta')->format('d/m/Y H:i:s') }}</small></p>
    </div>
</body>
</html>