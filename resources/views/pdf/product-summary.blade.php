<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Product Summary Report</title>
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
        .product-name {
            font-weight: 500;
            color: #2d3748;
        }
        .stock-level {
            font-weight: 500;
        }
        .stock-level.critical {
            color: #c53030;
        }
        .stock-level.low {
            color: #d69e2e;
        }
        .stock-level.normal {
            color: #2f855a;
        }
        .stock-level.overstocked {
            color: #2c5282;
        }
        /* Column widths for specific tables */
        .stock-table th:nth-child(1),
        .stock-table td:nth-child(1) { width: 60%; }
        .stock-table th:nth-child(2),
        .stock-table td:nth-child(2) { width: 20%; }
        .stock-table th:nth-child(3),
        .stock-table td:nth-child(3) { width: 20%; }

        .out-of-stock-table th:nth-child(1),
        .out-of-stock-table td:nth-child(1) { width: 80%; }
        .out-of-stock-table th:nth-child(2),
        .out-of-stock-table td:nth-child(2) { width: 20%; }
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
        <table class="stock-table">
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
                    <td>
                        <div class="product-name">{{ $product->name }}</div>
                    </td>
                    <td class="text-right">
                        <span class="stock-level critical">{{ $product->quantity_in_stock }}</span>
                    </td>
                    <td class="text-right">{{ $product->safety_stock }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Low Stock Products</div>
        <table class="stock-table">
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
                    <td>
                        <div class="product-name">{{ $product->name }}</div>
                    </td>
                    <td class="text-right">
                        <span class="stock-level low">{{ $product->quantity_in_stock }}</span>
                    </td>
                    <td class="text-right">{{ $product->reorder_threshold }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Out of Stock Products</div>
        <table class="out-of-stock-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Reorder Threshold</th>
                </tr>
            </thead>
            <tbody>
                @foreach($outOfStockProducts as $product)
                <tr>
                    <td>
                        <div class="product-name">{{ $product->name }}</div>
                    </td>
                    <td class="text-right">{{ $product->reorder_threshold }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Overstocked Products</div>
        <table class="out-of-stock-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity in Stock</th>
                </tr>
            </thead>
            <tbody>
                @foreach($overstockedProducts as $product)
                <tr>
                    <td>
                        <div class="product-name">{{ $product->name }}</div>
                    </td>
                    <td class="text-right">
                        <span class="stock-level overstocked">{{ $product->quantity_in_stock }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Notes</div>
        <ul style="color: #4a5568; margin: 0; padding-left: 20px;">
            <li>Critical stock products are below their safety stock level</li>
            <li>Low stock products are below their reorder threshold</li>
            <li>Out of stock products have zero quantity in stock</li>
            <li>Overstocked products have significantly more stock than needed</li>
        </ul>
    </div>
</body>
</html>
