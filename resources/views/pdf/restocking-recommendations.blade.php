<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Restocking Recommendations Report</title>
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
        .status.critical {
            background-color: #fed7d7;
            color: #c53030;
        }
        .status.low {
            background-color: #fefcbf;
            color: #975a16;
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
        .text-yellow {
            color: #d69e2e;
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
        <h1>Restocking Recommendations Report</h1>
        <p>Generated on: {{ $generatedAt }}</p>
        @if($search)
            <p>Filtered by search: "{{ $search }}"</p>
        @endif
    </div>

    <div class="summary-box">
        <p>Total Products: {{ $totalProducts }}</p>
        <p>Products with Recommendations: {{ $productsWithRecommendations }}</p>
    </div>

    <div class="section">
        <div class="section-title">Restocking Recommendations</div>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Forecast</th>
                    <th>Current Stock</th>
                    <th>Safety Stock</th>
                    <th>Reorder Threshold</th>
                    <th>Recommended Order</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                    @if($product->restockingRecommendation)
                        @php
                            $recommendation = $product->restockingRecommendation;
                            $stockStatus = $recommendation->quantity_in_stock <= $product->safety_stock ? 'critical' :
                                         ($recommendation->quantity_in_stock <= $product->reorder_threshold ? 'low' : 'normal');
                        @endphp
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->sku }}</td>
                            <td class="text-right">{{ number_format($recommendation->total_forecasted_demand, 0) }}</td>
                            <td class="text-right">
                                <span class="status {{ $stockStatus }}">
                                    {{ $recommendation->quantity_in_stock }}
                                </span>
                            </td>
                            <td class="text-right">{{ $product->safety_stock }}</td>
                            <td class="text-right">{{ $product->reorder_threshold }}</td>
                            <td class="text-right text-yellow">
                                @if($recommendation->reorder_quantity > 0)
                                    <strong>{{ number_format($recommendation->reorder_quantity, 0) }}</strong>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Notes</div>
        <ul style="color: #4a5568; margin: 0; padding-left: 20px;">
            <li>Forecast represents the total forecasted demand over 30 days</li>
            <li>Safety stock is set to protect against unexpected demand spikes</li>
            <li>Reorder threshold is the level at which new orders should be placed</li>
            <li>Recommended order quantity is calculated based on current stock levels and forecasted demand</li>
        </ul>
    </div>
</body>
</html>
