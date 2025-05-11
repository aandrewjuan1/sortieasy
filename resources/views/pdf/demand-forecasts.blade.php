<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Demand Forecasts Report</title>
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
        .filters {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .filters p {
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
        .product-name {
            font-weight: bold;
            color: #2d3748;
        }
        .product-sku {
            color: #718096;
            font-size: 11px;
        }
        .forecast-value {
            color: #d69e2e;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #718096;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Demand Forecasts Report</h1>
        <p>Generated on: {{ $generatedAt }}</p>
    </div>

    @if($filters['search'] || $filters['product'] || $filters['dateRange'])
        <div class="filters">
            <p><strong>Applied Filters:</strong></p>
            @if($filters['search'])
                <p>Search: "{{ $filters['search'] }}"</p>
            @endif
            @if($filters['product'])
                <p>Product: {{ $filters['product'] }}</p>
            @endif
            @if($filters['dateRange'])
                <p>Date Range: {{ $filters['dateRange'] }}</p>
            @endif
        </div>
    @endif

    <div class="section">
        <div class="section-title">Forecast Details</div>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Forecast Date</th>
                    <th>Forecasted Quantity</th>
                    <th>Days Until Forecast</th>
                </tr>
            </thead>
            <tbody>
                @forelse($forecasts as $forecast)
                    <tr>
                        <td>
                            <div class="product-name">{{ $forecast->product->name }}</div>
                        </td>
                        <td>
                            <div class="product-sku">{{ $forecast->product->sku }}</div>
                        </td>
                        <td>{{ $forecast->forecast_date->format('Y-m-d') }}</td>
                        <td class="forecast-value">{{ number_format($forecast->predicted_quantity, 0) }}</td>
                        <td class="days-until-forecast">{{ $forecast->forecast_date->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">No forecasts found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Notes</div>
        <ul style="color: #4a5568; margin: 0; padding-left: 20px;">
            <li>Forecast dates represent when the predicted demand is expected to occur</li>
            <li>Forecasted quantities are based on historical data and market trends</li>
            <li>Days until forecast shows the relative time until the forecasted demand</li>
            <li>All quantities are rounded to the nearest whole number</li>
        </ul>
    </div>
</body>
</html>
