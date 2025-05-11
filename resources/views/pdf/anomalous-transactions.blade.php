<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Anomalous Transactions Report</title>
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
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background-color: #f7fafc;
            font-weight: bold;
            color: #4a5568;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .status {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        .status.anomalous {
            background-color: #fed7d7;
            color: #c53030;
        }
        .status.normal {
            background-color: #c6f6d5;
            color: #2f855a;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-red {
            color: #c53030;
        }
        .text-green {
            color: #2f855a;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Anomalous Transactions Report</h1>
        <p>Generated on: {{ $generatedAt }}</p>
        @if($search)
            <p>Filtered by search: "{{ $search }}"</p>
        @endif
        @if($productFilter)
            <p>Filtered by product: {{ $productFilter }}</p>
        @endif
        @if($showOnlyAnomalies)
            <p>Showing only anomalous transactions</p>
        @endif
    </div>

    <div class="summary-box">
        <p>Total Anomalies Detected: {{ $totalAnomalies }}</p>
    </div>

    <div class="section">
        <div class="section-title">Anomalous Transactions</div>
        <table>
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Quantity</th>
                    <th>Transaction Date</th>
                    <th>Status</th>
                    <th>Anomaly Score</th>
                </tr>
            </thead>
            <tbody>
                @foreach($results as $result)
                    <tr>
                        <td>{{ $result->transaction_id }}</td>
                        <td>{{ $result->product->name }}</td>
                        <td>{{ $result->product->sku }}</td>
                        <td class="text-right">{{ $result->transaction->quantity }}</td>
                        <td>{{ $result->transaction->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <span class="status {{ $result->status->value === 'anomalous' ? 'anomalous' : 'normal' }}">
                                {{ ucfirst($result->status->value) }}
                            </span>
                        </td>
                        <td class="text-right">
                            @if($result->anomaly_score)
                                {{ number_format($result->anomaly_score, 2) }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Notes</div>
        <ul style="color: #4a5568; margin: 0; padding-left: 20px;">
            <li>Anomaly score represents the deviation from normal transaction patterns</li>
            <li>Higher scores indicate stronger anomalies</li>
            <li>Transactions are marked as anomalous when they significantly deviate from expected patterns</li>
            <li>Review these transactions carefully as they may indicate unusual activity</li>
        </ul>
    </div>
</body>
</html>
