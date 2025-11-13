<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 40px;
            line-height: 1.6;
        }
        h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 30px;
            text-align: center;
        }
        .category-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .category-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 2px solid #333;
        }
        .beverage-item {
            margin-bottom: 8px;
            padding-left: 20px;
        }
        .beverage-name {
            font-weight: 500;
        }
        .beverage-count {
            color: #666;
        }
        .category-total {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>

    @foreach ($statsByCategory as $categoryData)
        <div class="category-section">
            <div class="category-title">
                {{ $categoryData['category'] }}
            </div>

            @foreach ($categoryData['beverages'] as $beverage)
                <div class="beverage-item">
                    <span class="beverage-name">{{ $beverage['name'] }}</span>
                    <span class="beverage-count"> - {{ $beverage['count'] }} préférence(s)</span>
                </div>
            @endforeach

            <div class="category-total">
                Total {{ $categoryData['category'] }} : {{ $categoryData['total'] }} préférence(s)
            </div>
        </div>
    @endforeach
</body>
</html>

