<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Summary Report</title>
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
        .channel {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        .in_store { background-color: #dbeafe; color: #2c5282; }
        .online { background-color: #c6f6d5; color: #2f855a; }
        .phone { background-color: #fefcbf; color: #975a16; }
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
        /* Column widths for specific tables */
        .channel-table th:nth-child(1),
        .channel-table td:nth-child(1) { width: 25%; }
        .channel-table th:nth-child(2),
        .channel-table td:nth-child(2) { width: 20%; }
        .channel-table th:nth-child(3),
        .channel-table td:nth-child(3) { width: 25%; }
        .channel-table th:nth-child(4),
        .channel-table td:nth-child(4) { width: 15%; }

        .sales-table th:nth-child(1),
        .sales-table td:nth-child(1) { width: 15%; }
        .sales-table th:nth-child(2),
        .sales-table td:nth-child(2) { width: 30%; }
        .sales-table th:nth-child(3),
        .sales-table td:nth-child(3) { width: 10%; }
        .sales-table th:nth-child(4),
        .sales-table td:nth-child(4) { width: 15%; }
        .sales-table th:nth-child(5),
        .sales-table td:nth-child(5) { width: 15%; }
        .sales-table th:nth-child(6),
        .sales-table td:nth-child(6) { width: 15%; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sales Summary Report</h1>
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <div class="summary-box">
        <p>Total Sales Volume: {{ $totalVolume }}</p>
        <p>Total Revenue: ${{ number_format($totalRevenue, 2) }}</p>
    </div>

    <div class="section">
        <div class="section-title">Sales by Channel</div>
        <table class="channel-table">
            <thead>
                <tr>
                    <th>Channel</th>
                    <th>Sales Count</th>
                    <th>Revenue</th>
                    <th>Revenue %</th>
                </tr>
            </thead>
            <tbody>
                @foreach($salesByChannel as $channel => $data)
                <tr>
                    <td>
                        <span class="channel {{ $channel }}">
                            {{ match($channel) {
                                'in_store' => 'In-Store',
                                'online' => 'Online',
                                'phone' => 'Phone',
                                default => ucfirst($channel)
                            } }}
                        </span>
                    </td>
                    <td class="text-right">{{ $data['count'] }}</td>
                    <td class="text-right">${{ number_format($data['revenue'], 2) }}</td>
                    <td class="text-right">{{ number_format(($data['revenue'] / $totalRevenue) * 100, 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Recent Sales</div>
        <table class="sales-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                    <th>Channel</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentSales as $sale)
                <tr>
                    <td>{{ $sale->formatted_date }}</td>
                    <td>{{ $sale->product->name }}</td>
                    <td class="text-right">{{ $sale->quantity }}</td>
                    <td class="text-right">${{ number_format($sale->unit_price, 2) }}</td>
                    <td class="text-right text-green">${{ $sale->formatted_total }}</td>
                    <td>
                        <span class="channel {{ $sale->channel }}">
                            {{ App\Enums\SaleChannel::getLabel($sale->channel->value) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Notes</div>
        <ul style="color: #4a5568; margin: 0; padding-left: 20px;">
            <li>Sales volume represents the total number of transactions</li>
            <li>Revenue percentages are calculated based on total revenue</li>
            <li>Recent sales show the latest transactions across all channels</li>
            <li>Channel colors indicate the sales channel for each transaction</li>
        </ul>
    </div>
</body>
</html>
