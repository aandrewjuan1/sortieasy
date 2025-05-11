<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Logistics Summary Report</title>
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
        .status {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        .pending { background-color: #fefcbf; color: #975a16; }
        .in_transit { background-color: #dbeafe; color: #2c5282; }
        .delivered { background-color: #c6f6d5; color: #2f855a; }
        .cancelled { background-color: #fed7d7; color: #c53030; }
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
        /* Column widths for specific tables */
        .shipments-table th:nth-child(1),
        .shipments-table td:nth-child(1) { width: 30%; }
        .shipments-table th:nth-child(2),
        .shipments-table td:nth-child(2) { width: 20%; }
        .shipments-table th:nth-child(3),
        .shipments-table td:nth-child(3) { width: 20%; }

        .deliveries-table th:nth-child(1),
        .deliveries-table td:nth-child(1) { width: 40%; }
        .deliveries-table th:nth-child(2),
        .deliveries-table td:nth-child(2) { width: 25%; }
        .deliveries-table th:nth-child(3),
        .deliveries-table td:nth-child(3) { width: 20%; }
        .deliveries-table th:nth-child(4),
        .deliveries-table td:nth-child(4) { width: 15%; }
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
        <table class="shipments-table">
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
                    <td class="text-right">{{ $data['count'] }}</td>
                    <td class="text-right">{{ number_format($data['percentage'], 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Upcoming Deliveries (Next 7 Days)</div>
        <table class="deliveries-table">
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
                    <td class="text-right">{{ $delivery->days_until }} days</td>
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
        <table class="deliveries-table">
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
                    <td class="text-right text-red">{{ $shipment->days_late }} days</td>
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

    <div class="section">
        <div class="section-title">Notes</div>
        <ul style="color: #4a5568; margin: 0; padding-left: 20px;">
            <li>Status colors indicate the current state of each shipment</li>
            <li>Late shipments are those that have exceeded their expected delivery date</li>
            <li>Upcoming deliveries are scheduled for the next 7 days</li>
            <li>Percentages are calculated based on total shipments in the last 30 days</li>
        </ul>
    </div>
</body>
</html>
