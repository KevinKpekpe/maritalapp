<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 40px;
            line-height: 1.6;
        }
        h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 30px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .number {
            text-align: center;
            width: 50px;
        }
        .name {
            width: auto;
        }
        .type {
            width: 100px;
            text-align: center;
        }
        .table-name {
            width: 200px;
        }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>

    <table>
        <thead>
            <tr>
                <th class="number">N°</th>
                <th class="name">Nom de l'invité</th>
                <th class="type">Type</th>
                <th class="table-name">Table</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($guests as $index => $guest)
                <tr>
                    <td class="number">{{ $index + 1 }}</td>
                    <td class="name">{{ $guest->display_name }}</td>
                    <td class="type">{{ $guest->type === 'couple' ? 'Couple' : 'Solo' }}</td>
                    <td class="table-name">{{ $guest->table ? $guest->table->name : 'Non assigné' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

