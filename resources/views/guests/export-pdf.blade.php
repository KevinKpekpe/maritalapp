<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', serif;
            font-size: 11px;
            margin: 0;
            padding: 30px;
            line-height: 1.6;
            color: #000000;
            background-color: #ffffff;
        }
        h1 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 25px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .main-container {
            width: 100%;
            border-collapse: collapse;
        }
        .main-container td {
            width: 50%;
            padding: 0 15px;
            vertical-align: top;
        }
        .table-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .table-header {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
            padding: 5px 0;
            border-bottom: 1.5px solid #000000;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .guests-list {
            padding: 0;
            list-style: disc;
            list-style-position: outside;
            margin: 0 0 0 15px;
        }
        .guest-item {
            padding: 4px 0;
            margin-bottom: 2px;
            font-size: 10px;
            line-height: 1.5;
        }
        .guest-name {
            font-weight: normal;
            color: #000000;
        }
        .guest-phone {
            color: #000000;
        }
        @media print {
            body {
                padding: 25px;
            }
            .table-section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>

    @php
        $tablesArray = $guestsByTable->toArray();
        $totalTables = count($tablesArray);
        $tablesPerColumn = ceil($totalTables / 2);
        $leftColumn = array_slice($tablesArray, 0, $tablesPerColumn);
        $rightColumn = array_slice($tablesArray, $tablesPerColumn);
    @endphp

    <table class="main-container">
        <tr>
            <td>
                @foreach ($leftColumn as $group)
                    <div class="table-section">
                        <div class="table-header">
                            {{ $group['table'] }}
                        </div>
                        <ul class="guests-list">
                            @foreach ($group['guests'] as $guest)
                                <li class="guest-item">
                                    <span class="guest-name">
                                        @if ($guest->type === 'couple')
                                            Couple {{ $guest->display_name }}
                                        @else
                                            {{ $guest->display_name }}
                                        @endif
                                    </span>
                                    <span class="guest-phone">: {{ $guest->phone }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </td>
            <td>
                @foreach ($rightColumn as $group)
                    <div class="table-section">
                        <div class="table-header">
                            {{ $group['table'] }}
                        </div>
                        <ul class="guests-list">
                            @foreach ($group['guests'] as $guest)
                                <li class="guest-item">
                                    <span class="guest-name">
                                        @if ($guest->type === 'couple')
                                            Couple {{ $guest->display_name }}
                                        @else
                                            {{ $guest->display_name }}
                                        @endif
                                    </span>
                                    <span class="guest-phone">: {{ $guest->phone }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </td>
        </tr>
    </table>
</body>
</html>
