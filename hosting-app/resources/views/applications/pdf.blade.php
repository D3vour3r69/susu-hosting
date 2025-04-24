<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Служебная записка #{{ $application->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .logo { width: 100px; }
        .ministry { font-size: 14pt; text-transform: uppercase; }
        .document-title { font-size: 18pt; text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        td, th { border: 1px solid #ddd; padding: 8px; }
    </style>
</head>
<body>
<div class="header">
    <p class="ministry">
        МИНИСТЕРСТВО НАУКИ И ВЫСШЕГО ОБРАЗОВАНИЯ<br>
        РОССИЙСКОЙ ФЕДЕРАЦИИ
    </p>

    <p class="document-title">
        СЛУЖЕБНАЯ ЗАПИСКА №{{ $application->id }}
    </p>
</div>

<div class="content">
    <p><strong>Статус:</strong> {{ $application->status }}</p>
    <p><strong>Дата создания:</strong> {{ $application->created_at->format('d.m.Y') }}</p>

    <h3>Выбранные параметры:</h3>
    <table>
        <thead>
        <tr>
            <th>Категория</th>
            <th>Параметр</th>
        </tr>
        </thead>
        <tbody>
        @foreach($application->featureItems->groupBy('feature_id') as $group)
            @foreach($group as $item)
                <tr>
                    <td>{{ $item->feature->name }}</td>
                    <td>{{ $item->name }}</td>
                </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>

    @if($application->notes)
        <h3>Дополнительные заметки:</h3>
        <p>{{ $application->notes }}</p>
    @endif
</div>
</body>
</html>
