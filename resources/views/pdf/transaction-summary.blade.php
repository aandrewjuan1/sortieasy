<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transaction Summary Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .summary-box {
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .transaction-type {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .purchase { background-color: #dcfce7; color: #166534; }
        .sale { background-color: #dbeafe; color: #1e40af; }
        .return { background-color: #fef9c3; color: #854d0e; }
        .adjustment { background-color: #f3e8ff; color: #6b21a8; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Transaction Summary Report</h1>
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <div class="summary-box">
        <p>Total Transactions: {{ $totalTransactions }}</p>
    </div>

    <div class="section">
        <div class="section-title">Transaction Volume by Type</div>
        <table>
            <thead>
                <tr>
                    <th>Transaction Type</th>
                    <th>Volume</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactionTypes as $type)
                <tr>
                    <td>
                        <span class="transaction-type {{ $type }}">
                            {{ ucfirst($type) }}
                        </span>
                    </td>
                    <td>{{ $transactionVolume[$type] ?? 0 }}</td>
                    <td>{{ number_format(($transactionVolume[$type] ?? 0) / array_sum($transactionVolume) * 100, 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Recent Transactions</div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>User</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentTransactions as $transaction)
                <tr>
                    <td>{{ $transaction->formatted_date }}</td>
                    <td>
                        <span class="transaction-type {{ $transaction->type }}">
                            {{ $transaction->formatted_type }}
                        </span>
                    </td>
                    <td>{{ $transaction->product->name }}</td>
                    <td>{{ $transaction->quantity }}</td>
                    <td>{{ $transaction->user->name }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
