<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Служебная записка №{{ $application->id }}</title>
    <div class="logo-container">
        <img src="{{ public_path('storage/susu_logo.png') }}" class="logo" alt="Логотип">
    </div>
    <style>
        @page {
            margin: 20px 25px;
        }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            /*line-height: 1.5;*/
            /*margin: 0;*/
            /*padding: 0;*/
        }

        .header-container {

            flex-direction:row;
        }

        .university-block {
            text-align: center;
            display: inline-block;
            width: 50%;

        }

        .address-block {
            display: inline-block;
            width: 35%;

        }

        .document-title {
            text-align: left;
            font-size: 12pt;
            font-weight: bold;

            text-transform: uppercase;
            margin: 25px 0 15px 0;
        }

        .document-info {
            text-align: left;
            margin-bottom: 20px;
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 10px;
            text-align: left;
            padding-left: 35px;
        }

        .works-description {
            margin: 15px 0 30px 0;
            min-height: 150px;
            padding-left: 35px;
        }

        .signature-block {
            margin-top: 40px;
            margin-bottom: 20px;
            padding-left: 135px;
        }

        .contact-info {
            margin-top: 30px;
            padding-left: 35px;
        }

        .ministry {
            font-size: 8pt;
            text-transform: uppercase;
            margin-bottom: 0;
            line-height: 1.2;
        }

        .university {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 0;
            line-height: 1.3;
        }

        .department {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 15px;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .underline {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 200px;
            margin: 0 5px;
        }

        .work-item {
            margin-bottom: 8px;
            padding-left: 20px;
            text-indent: -20px;
        }

        .notes-section {
            margin-top: 20px;
            font-style: italic;
        }

        .contact-field {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 250px;
            margin-left: 10px;
            padding-bottom: 2px;
        }

        .address-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .logo {
            margin-top: 120px;
            display: block;
            padding-left: 180px;
        }

        .content-wrapper {
            padding-left: 35px;
        }
    </style>

</head>
<body>

<div class="header-container">

    <div class="university-block">
        <div class="ministry">
            министерство науки и высшего образования
        </div>
        <div class="ministry">
            российской федерации
        </div>
        <div class="university">
            ЮЖНО-УРАЛЬСКИЙ
        </div>
        <div class="university">
            ГОСУДАРСТВЕННЫЙ
        </div>
        <div class="university">
            УНИВЕРСИТЕТ
        </div>
        <div class="department">
            {{ $application->unit->name }}
        </div>
    </div>

    <div class="address-block">

        <div class="address-title">
            {{ $application->head->address_title }}
        </div>

        <div class="address-name">
            {{ $application->head->full_name }}
        </div>
    </div>
</div>

<div class="document-title">
    служебная записка
</div>

<div class="document-info">
    <span class="underline">{{ now()->format('d.m.Y') }}</span> № <span class="underline">{{ $application->id }}</span>
</div>

<div class="section-title">
    О выполнении работ
</div>

<div class="content-wrapper">
    <p>Прошу выполнить следующие работы по настройке и обслуживанию хостинг-сервисов со следующими технологиями:</p>
</div>

<div class="works-description">
    @foreach($application->featureItems as $item)
        <div class="work-item">{{ $item->name }}</div>
    @endforeach

    @if($application->notes)
        <div class="notes-section">
            {{ $application->notes }}
        </div>
    @endif
</div>

<div class="signature-block">
    <div>Руководитель <span class="underline"></span> /{{ $application->unit->head->name ?? '________________' }}/</div>
</div>

<div class="contact-info">
    <p>Контактные данные ответственного работника:</p>
    <p>
        Ф.И.О. <span class="contact-field">{{ $application->responsible->name }}</span>
    </p>
    <p>
        Тел.: <span class="contact-field"></span>
    </p>
    <p>
        E-mail: <span class="contact-field">{{ $application->responsible->email }}</span>
    </p>
</div>
</body>
</html>
