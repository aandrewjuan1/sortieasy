<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Supplier Overview Report</title>
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
        <table>
            <thead>
                <tr>
                    <th>Supplier Name</th>
                    <th>Number of Products</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topSuppliers as $supplier)
                <tr>
                    <td>{{ $supplier->name }}</td>
                    <td>{{ $supplier->products_count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Recent Deliveries</div>
        <table>
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
                    <td>{{ $delivery->product->supplier->name }}</td>
                    <td>{{ $delivery->delivery_date->format('Y-m-d') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
