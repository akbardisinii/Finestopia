<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Allocation Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Laporan Pengalokasian Keuangan</h1>
    <h2>User: {{ $user->name }}</h2>
    
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Jumlah</th>
                <th>Persentase</th>
            </tr>
        </thead>
        <tbody>
        @foreach($allocation['allocations'] as $category => $data)
    <tr>
        <td>{{ ucfirst($category) }}</td>
        <td>Rp {{ number_format($data['amount'], 0, ',', '.') }}</td>
        <td>{{ number_format($data['percentage'], 2) }}%</td>
    </tr>
@endforeach
        </tbody>
        <tfoot>
            <tr>
            <th>Total</th>
            <th>Rp {{ number_format($allocation['total'], 0, ',', '.') }}</th>
            <th>100%</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>