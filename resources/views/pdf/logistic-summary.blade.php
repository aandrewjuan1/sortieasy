<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Logistics Summary Report</title>
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
        .status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .pending { background-color: #fef3c7; color: #92400e; }
        .in_transit { background-color: #dbeafe; color: #1e40af; }
        .delivered { background-color: #dcfce7; color: #166534; }
        .cancelled { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Logistics Summary Report</h1>
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <div class="summary-box">
        <p>Total Shipments (Last 30 Days): {{ $totalShipments }}</p>
    </div>

    <div class="section">
        <div class="section-title">Shipments by Status</div>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Count</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($shipmentsByStatus as $status => $data)
                <tr>
                    <td>
                        <span class="status {{ $status }}">
                            {{ App\Enums\LogisticStatus::from($status)->label() }}
                        </span>
                    </td>
                    <td>{{ $data['count'] }}</td>
                    <td>{{ number_format($data['percentage'], 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Upcoming Deliveries (Next 7 Days)</div>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Delivery Date</th>
                    <th>Days Until</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($upcomingDeliveries as $delivery)
                <tr>
                    <td>{{ $delivery->product->name }}</td>
                    <td>{{ $delivery->formatted_date }}</td>
                    <td>{{ $delivery->days_until }} days</td>
                    <td>
                        <span class="status {{ $delivery->status->value }}">
                            {{ $delivery->status->label() }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Late Shipments</div>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Delivery Date</th>
                    <th>Days Late</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lateShipments as $shipment)
                <tr>
                    <td>{{ $shipment->product->name }}</td>
                    <td>{{ $shipment->delivery_date->format('M d, Y') }}</td>
                    <td>{{ $shipment->days_late }} days</td>
                    <td>
                        <span class="status {{ $shipment->status->value }}">
                            {{ $shipment->status->label() }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
