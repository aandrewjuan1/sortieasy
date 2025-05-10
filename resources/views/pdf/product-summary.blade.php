<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Product Summary Report</title>
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
        <h1>Product Summary Report</h1>
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <div class="summary-box">
        <p>Total Products: {{ $totalProducts }}</p>
        <p>Total Stock Items: {{ $totalStocks }}</p>
    </div>

    <div class="section">
        <div class="section-title">Critical Stock Products</div>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity in Stock</th>
                    <th>Safety Stock</th>
                </tr>
            </thead>
            <tbody>
                @foreach($criticalStockProducts as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->quantity_in_stock }}</td>
                    <td>{{ $product->safety_stock }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Low Stock Products</div>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity in Stock</th>
                    <th>Reorder Threshold</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lowStockProducts as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->quantity_in_stock }}</td>
                    <td>{{ $product->reorder_threshold }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Out of Stock Products</div>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Reorder Threshold</th>
                </tr>
            </thead>
            <tbody>
                @foreach($outOfStockProducts as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->reorder_threshold }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Overstocked Products</div>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity in Stock</th>
                </tr>
            </thead>
            <tbody>
                @foreach($overstockedProducts as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->quantity_in_stock }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
