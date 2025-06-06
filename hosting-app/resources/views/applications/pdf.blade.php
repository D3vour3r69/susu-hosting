<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Служебная записка №{{ $application->id }}</title>
    <style>
        @page {
            margin: 20px 25px;
        }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        /* Контейнер для всего контента */
        .content-wrapper {
            position: relative;
            width: 100%;

        }

        /* Блок адресата - позиционирование как в документе */
        .address-block {
            position: absolute;
            top: 0;
            right: 0;
            width: 65%;
            margin-bottom: 30px;
        }

        .address-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .address-name {
            margin-top: 5px;
        }

        /* Логотип - позиционирование как в документе */


        .logo {
            height: 80px;
            width: auto;
        }

        /* Основной заголовок документа */
        .header {
            text-align: left;
            margin-top: 100px; /* Отступ для места под адресата и лого */
            margin-bottom: 20px;
        }

        .ministry {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 0;
            line-height: 1.2;
            text-align: center;
        }

        .university {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 0;
            line-height: 1.3;
            text-align: center;
        }

        .department {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 15px;
            margin-bottom: 20px;
            line-height: 1.2;
            text-align: center;
        }

        .document-title {
            font-size: 12pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            margin: 25px 0 15px 0;
        }

        .document-info {
            text-align: center;
            margin-bottom: 20px;
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }

        .works-description {
            margin: 15px 0 30px 0;
            min-height: 150px;
        }

        .signature-block {
            margin-top: 40px;
            margin-bottom: 20px;
        }

        .signature-line {
            width: 60%;
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
        }

        .contact-info {
            margin-top: 30px;
        }

        .contact-field {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 250px;
            margin-left: 10px;
            padding-bottom: 2px;
        }

        .funding-confirmation {
            margin-top: 40px;
        }

        .confirmation-item {
            margin-bottom: 20px;
        }

        .confirmation-line {
            width: 80%;
            border-bottom: 1px solid #000;
            margin-top: 5px;
        }

        .work-item {
            margin-bottom: 8px;
            padding-left: 20px;
            text-indent: -20px;
        }

        .work-item::before {
            content: "• ";
            padding-right: 5px;
        }

        .notes-section {
            margin-top: 20px;
            font-style: italic;
        }

        .underline {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 200px;
            margin: 0 5px;
        }
    </style>
</head>
<body>
<div class="content-wrapper">
    <!-- Блок адресата - левый верхний угол -->
    @if($head)
        <div class="address-block">
            <div class="address-title">
                {{ $head->address_title }}
            </div>
            <div class="address-name">
                {{ $head->full_name }}
            </div>
        </div>
    @endif


    <div class="logo-container">
        <img src="{{ public_path('storage/logo.png') }}" class="logo" alt="Логотип">
    </div>


    <div class="header">
        <div class="ministry">
            министерство науки и высшего образования
        </div>
        <div class="ministry">
            российской федерации
        </div>
        <div class="university">
            южно-уральский
        </div>
        <div class="university">
            государственный университет
        </div>
        <div class="department">
            {{ $application->unit->name }}
        </div>
    </div>

    <!-- Остальная часть документа -->
    <div class="document-title">
        служебная записка
    </div>

    <div class="document-info">
        <span class="underline">{{ now()->format('d.m.Y') }}</span> № <span class="underline">{{ $application->id }}</span>
    </div>

    <div class="section-title">
        О выполнении работ
    </div>

    <p>Прошу выполнить следующие работы по настройке и обслуживанию хостинг-сервисов со следующими технологиями:</p>

    <div class="works-description">
        @foreach($application->featureItems as $item)
            <div class="work-item">{{ $item->name }}</div>
        @endforeach

        @if($application->notes)
            <div class="notes-section">
{{--                <strong>Дополнительные примечания:</strong><br>--}}
                {{ $application->notes }}
            </div>
        @endif
    </div>

    <div class="signature-block">
        <div class="signature-line"></div>
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


</div>
</body>
</html>
