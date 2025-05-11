<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transaction Summary Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }
        .header h1 {
            color: #2d3748;
            font-size: 24px;
            margin: 0;
            padding: 0;
        }
        .header p {
            color: #718096;
            margin: 5px 0 0;
        }
        .summary-box {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .summary-box p {
            margin: 5px 0;
            color: #4a5568;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e2e8f0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
        }
        th, td {
            padding: 8px 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
            word-wrap: break-word;
        }
        th {
            background-color: #f7fafc;
            font-weight: bold;
            color: #4a5568;
            white-space: nowrap;
        }
        td {
            color: #4a5568;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .transaction-type {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        .purchase { background-color: #c6f6d5; color: #2f855a; }
        .sale { background-color: #dbeafe; color: #2c5282; }
        .return { background-color: #fefcbf; color: #975a16; }
        .adjustment { background-color: #e9d8fd; color: #6b46c1; }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-yellow {
            color: #d69e2e;
        }
        .text-red {
            color: #c53030;
        }
        .text-green {
            color: #2f855a;
        }
        .text-blue {
            color: #2c5282;
        }
        .text-purple {
            color: #6b46c1;
        }
        .product-name {
            font-weight: 500;
            color: #2d3748;
        }
        .user-name {
            color: #4a5568;
        }
        .quantity {
            font-weight: 500;
        }
        .quantity.positive {
            color: #2f855a;
        }
        .quantity.negative {
            color: #c53030;
        }
        /* Column widths for specific tables */
        .volume-table th:nth-child(1),
        .volume-table td:nth-child(1) { width: 40%; }
        .volume-table th:nth-child(2),
        .volume-table td:nth-child(2) { width: 30%; }
        .volume-table th:nth-child(3),
        .volume-table td:nth-child(3) { width: 30%; }

        .transactions-table th:nth-child(1),
        .transactions-table td:nth-child(1) { width: 15%; }
        .transactions-table th:nth-child(2),
        .transactions-table td:nth-child(2) { width: 20%; }
        .transactions-table th:nth-child(3),
        .transactions-table td:nth-child(3) { width: 35%; }
        .transactions-table th:nth-child(4),
        .transactions-table td:nth-child(4) { width: 15%; }
        .transactions-table th:nth-child(5),
        .transactions-table td:nth-child(5) { width: 15%; }
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
        <table class="volume-table">
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
                    <td class="text-right">{{ $transactionVolume[$type] ?? 0 }}</td>
                    <td class="text-right">{{ number_format(($transactionVolume[$type] ?? 0) / array_sum($transactionVolume) * 100, 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Recent Transactions</div>
        <table class="transactions-table">
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
                    <td>
                        <div class="product-name">{{ $transaction->product->name }}</div>
                    </td>
                    <td class="text-right">
                        <span class="quantity {{ $transaction->quantity > 0 ? 'positive' : 'negative' }}">
                            {{ $transaction->quantity }}
                        </span>
                    </td>
                    <td>
                        <span class="user-name">{{ $transaction->user->name }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Notes</div>
        <ul style="color: #4a5568; margin: 0; padding-left: 20px;">
            <li>Transaction types are color-coded for easy identification</li>
            <li>Positive quantities indicate stock additions (purchases, returns)</li>
            <li>Negative quantities indicate stock reductions (sales, adjustments)</li>
            <li>Percentages are calculated based on total transaction volume</li>
        </ul>
    </div>
</body>
</html>
