<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Supplier Overview Report</title>
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
        .supplier-name {
            font-weight: 500;
            color: #2d3748;
        }
        .product-count {
            color: #4a5568;
        }
        /* Column widths for specific tables */
        .suppliers-table th:nth-child(1),
        .suppliers-table td:nth-child(1) { width: 70%; }
        .suppliers-table th:nth-child(2),
        .suppliers-table td:nth-child(2) { width: 30%; }

        .deliveries-table th:nth-child(1),
        .deliveries-table td:nth-child(1) { width: 40%; }
        .deliveries-table th:nth-child(2),
        .deliveries-table td:nth-child(2) { width: 40%; }
        .deliveries-table th:nth-child(3),
        .deliveries-table td:nth-child(3) { width: 20%; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Supplier Overview Report</h1>
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <div class="summary-box">
        <p>Total Suppliers: {{ $totalSuppliers }}</p>
    </div>

    <div class="section">
        <div class="section-title">Top Suppliers</div>
        <table class="suppliers-table">
            <thead>
                <tr>
                    <th>Supplier Name</th>
                    <th>Number of Products</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topSuppliers as $supplier)
                <tr>
                    <td>
                        <div class="supplier-name">{{ $supplier->name }}</div>
                    </td>
                    <td class="text-right">
                        <span class="product-count">{{ $supplier->products_count }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Recent Deliveries</div>
        <table class="deliveries-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Supplier</th>
                    <th>Delivery Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentDeliveries as $delivery)
                <tr>
                    <td>{{ $delivery->product->name }}</td>
                    <td>
                        <div class="supplier-name">{{ $delivery->product->supplier->name }}</div>
                    </td>
                    <td>{{ $delivery->delivery_date->format('Y-m-d') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Notes</div>
        <ul style="color: #4a5568; margin: 0; padding-left: 20px;">
            <li>Top suppliers are ranked by the number of products they supply</li>
            <li>Recent deliveries show the latest shipments received from suppliers</li>
            <li>Supplier names are highlighted for better visibility</li>
            <li>All dates are shown in YYYY-MM-DD format</li>
        </ul>
    </div>
</body>
</html>
