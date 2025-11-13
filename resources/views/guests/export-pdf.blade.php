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
            line-height: 1.8;
        }
        h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 30px;
            text-align: center;
        }
        .list-item {
            margin-bottom: 10px;
            padding-left: 10px;
        }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>

    <div class="list">
        @foreach ($guests as $index => $guest)
            <div class="list-item">
                {{ $index + 1 }}. {{ $guest->display_name }}
            </div>
        @endforeach
    </div>
</body>
</html>

