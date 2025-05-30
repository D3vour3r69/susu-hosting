<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Служебная записка</title>
    <style>
        /* Подключение шрифтов для DomPDF */
        @font-face {
            font-family: 'Times New Roman';
            font-style: normal;
            font-weight: normal;
            src: url({{ storage_path('fonts/times-new-roman.ttf') }}) format('truetype');
        }

        @font-face {
            font-family: 'Times New Roman';
            font-style: bold;
            font-weight: 700;
            src: url('{{ storage_path('fonts/times-new-roman-bold.ttf') }}') format('truetype');
        }

        body {
            font-family: 'Times New Roman', serif !important;
            margin: 1.5cm;
            font-size: 14pt;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo {
            width: 3cm;
            height: auto;
            margin-bottom: 10px;
        }

        .ministry {
            text-transform: uppercase;
            margin: 5px 0;
        }

        .university {
            font-weight: bold;
            margin: 5px 0;
        }

        .document-title {
            text-align: center;
            font-size: 16pt;
            margin: 25px 0;
        }

        .underline {
            border-bottom: 1px solid black;
            display: inline-block;
            width: 100%;
            margin: 15px 0;
        }

        .work-description {
            margin: 20px 0;
        }

        .signature-block {
            margin-top: 30px;
        }

        .contact-info {
            margin-top: 25px;
        }

        .financing-section {
            margin-top: 35px;
        }
    </style>
</head>
<body>
<!-- Шапка документа -->
<div class="header">
    <img src="{{ storage_path('app/public/logo.png') }}" class="logo" alt="Логотип">
    <p class="ministry">
        МИНИСТЕРСТВО НАУКИ И ВЫСШЕГО ОБРАЗОВАНИЯ<br>
        РОССИЙСКОЙ ФЕДЕРАЦИИ
    </p>

    <p class="university">
        ЮЖНО-УРАЛЬСКИЙ<br>
        ГОСУДАРСТВЕННЫЙ УНИВЕРСИТЕТ
    </p>

    <p class="department">
        {{ $department ?? 'НАЗВАНИЕ ПОДРАЗДЕЛЕНИЯ' }}
    </p>
</div>

<!-- Основное содержимое -->
<div class="content">
    <div class="document-title">
        СЛУЖЕБНАЯ ЗАПИСКА<br>
        от «{{ date('d.m.Y') }}»
    </div>

    <div class="work-description">
        <p><strong>О выполнении работ</strong></p>
        <p>Прошу выполнить хостинг сайта:</p>

        <div class="underline">{{ $work_description ?? '____________________________________________________' }}</div>
        <div class="underline">{{ $additional_info ?? '____________________________________________________' }}</div>

        <p>(подключить к сети компьютеры ({{ $computers_count ?? '__' }} шт.) в ауд./корп. {{ $location ?? '____' }},
            установить кабель-канал, выделить IP-адреса ({{ $ip_count ?? '__' }} шт.) и т.п.)</p>
    </div>

    <!-- Подпись и контакты -->
    <div class="signature-block">
        Руководитель _____________________ /{{ $director_name ?? 'Ф.И.О.' }}/
    </div>

    <div class="contact-info">
        <p><strong>Контактные данные ответственного работника:</strong></p>
        <p>Ф.И.О. {{ $responsible_name ?? '__________________________' }}</p>
        <p>Тел.: {{ $phone ?? '__________________________' }}</p>
        <p>E-mail: {{ $email ?? '__________@susu.ru' }}</p>
    </div>

    <!-- Финансирование -->
    <div class="financing-section">
        <p><strong>Подтверждение источника финансирования:</strong></p>
        <p>Начальник ПФО УНИД _________________________</p>
        <p>Начальник УПЭД _____________________________</p>
        <p>Проректор по экономическим вопросам __________</p>
    </div>
</div>
</body>
</html>
