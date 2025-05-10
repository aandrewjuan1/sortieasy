<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Summary Report</title>
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
        .channel {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .in_store { background-color: #4e73df; color: white; }
        .online { background-color: #1cc88a; color: white; }
        .phone { background-color: #36b9cc; color: white; }
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
        <table>
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
                    <td>{{ $data['count'] }}</td>
                    <td>${{ number_format($data['revenue'], 2) }}</td>
                    <td>{{ number_format(($data['revenue'] / $totalRevenue) * 100, 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Recent Sales</div>
        <table>
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
                    <td>{{ $sale->quantity }}</td>
                    <td>${{ number_format($sale->unit_price, 2) }}</td>
                    <td>${{ $sale->formatted_total }}</td>
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
</body>
</html>
