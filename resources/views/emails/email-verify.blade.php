<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Confirmação de E-mail</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4a90e2;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 0.8em;
            color: #666;
        }
        .negrito {
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="header">
    <h1>Bem-vindo ao {{ config('app.name') }}!</h1>
</div>
<div class="content">
    <p>Olá, <span class="negrito"> {{ $user->name }}!</span></p>
    <p>Estamos muito felizes em tê-lo conosco. Seu cadastro foi realizado com sucesso e agora você faz parte da nossa comunidade.</p>
    <p>Para começar a usar nossos serviços, por favor, verifique seu e-mail clicando no botão abaixo:</p>
    <p>
        <a href="{{ $url }}" class="button">Verificar E-mail</a>
    </p>
    <p>Se o botão acima não funcionar, você pode copiar e colar o seguinte link no seu navegador:</p>
    <p>{{ $url }}</p>
    <p>Após a verificação, você poderá:</p>
    <ul>
        <li>Verificar seus dados e atualizá-los</li>
        <li>Explorar nossos recursos</li>
        <li>Conectar-se com outros usuários</li>
    </ul>
    <p>Se tiver qualquer dúvida, não hesite em nos contatar.</p>
</div>
<div class="footer">
    <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.</p>
    <p>Você está recebendo este e-mail porque se cadastrou em nossa plataforma.</p>
</div>
</body>
</html>
