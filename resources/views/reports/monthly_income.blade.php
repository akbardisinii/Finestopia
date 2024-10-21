<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Income Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Laporan Pendapatan Bulanan - {{ date('F Y', strtotime("{$year}-{$month}-01")) }}</h1>
    <h2>User: {{ $user->name }}</h2>
    
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Judul</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($incomes as $income)
            <tr>
                <td>{{ $income->formatted_date }}</td>
                <td>{{ $income->title }}</td>
                <td>Rp {{ number_format($income->amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2">Total Pendapatan</th>
                <th>Rp {{ number_format($totalIncome, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>