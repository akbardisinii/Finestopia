<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengeluaran</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
<h1>Laporan Pengeluaran</h1>
<h2>User: {{ $user->name }}</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Title</th>
                <th>Amount</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $expense)
            <tr>
                <td>{{ $expense->formatted_date }}</td>
                <td>{{ $expense->title }}</td>
                <td>Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                <td>{{ $expense->description }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2"><strong>Total Expense</strong></td>
                <td colspan="2"><strong>Rp {{ number_format($totalExpense, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>