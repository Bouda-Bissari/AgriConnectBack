<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .content {
            background-color: #ffffff;
            padding: 30px;
            margin: 40px auto;
            border-radius: 10px;
            max-width: 600px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header img {
            max-width: 120px;
        }
        .greeting {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }
        .content p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
            color: #555;
        }
        .button {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #218838;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #777;
        }
        .subcopy {
            font-size: 12px;
            color: #999;
            margin-top: 20px;
            text-align: center;
        }
        .subcopy a {
            color: #28a745;
            text-decoration: none;
        }
        .subcopy a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="header">
            <img src="{{ asset('storage/images/logo.png') }}" alt="Agriconnect Logo">
        </div>

        <div class="greeting">
            @if (!empty($greeting))
                {{ $greeting }}
            @else
                @lang('Bonjour!')
            @endif
        </div>

        @foreach ($introLines as $line)
            <p>{{ $line }}</p>
        @endforeach

        @isset($actionText)
            <a href="{{ $actionUrl }}" class="button">{{ $actionText }}</a>
        @endisset

        @foreach ($outroLines as $line)
            <p>{{ $line }}</p>
        @endforeach

        <div class="footer">
            @if (!empty($salutation))
                <p>{{ $salutation }}</p>
            @else
                <p>@lang('Cordialement'),<br>{{ config('app.name') }}</p>
            @endif
        </div>

        @isset($actionText)
            <div class="subcopy">
                @lang(
                    "Si vous avez des difficultés à cliquer sur le bouton \":actionText\", copiez et collez l'URL ci-dessous\n".
                    'dans votre navigateur web:',
                    ['actionText' => $actionText]
                )
                <br><a href="{{ $actionUrl }}">{{ $displayableActionUrl }}</a>
            </div>
        @endisset
    </div>
</body>
</html>
