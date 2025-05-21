<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f8f8;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
        }
        .content {
            padding: 20px;
            background-color: #ffffff;
            border-radius: 5px;
            margin-top: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .credentials {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Bem-vindo, {{ $funcionario->user->nome }}!</h1>
        </div>

        <div class="content">
            <p>Estamos empolgados em tê-lo(a) como parte da nossa equipe! Você foi convidado(a) para acessar nosso sistema.</p>

            <p>Para seu primeiro acesso, utilize as credenciais abaixo:</p>

            <div class="credentials">
                <p><strong>CPF:</strong> {{ $funcionario->user->cpf }}</p>
                <p><strong>Senha:</strong> {{ $password }}</p>
            </div>

            <p>Por motivos de segurança, recomendamos que você altere sua senha após o primeiro login.</p>

            <a href="{{ url('/login') }}" class="button">Acessar o Sistema</a>

            <p>Se precisar de ajuda ou tiver alguma dúvida, nossa equipe de suporte está à disposição.</p>

            <p>Atenciosamente,<br>
            Equipe {{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>
