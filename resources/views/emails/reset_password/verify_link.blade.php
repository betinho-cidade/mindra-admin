<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitação de Troca de Senha</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f4f4f4;
            color: #333333;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #004aad;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px;
            line-height: 1.6;
        }
        .content p {
            margin: 0 0 15px;
        }
        .content .highlight {
            font-weight: bold;
            color: #004aad;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 20px 0;
            background-color: #004aad;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }
        .button:hover {
            background-color: #003080;
        }
        .footer {
            background-color: #f4f4f4;
            padding: 10px;
            text-align: center;
            font-size: 12px;
            color: #666666;
        }
        @media only screen and (max-width: 600px) {
            .container {
                width: 100%;
                border-radius: 0;
            }
            .header h1 {
                font-size: 20px;
            }
            .content {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Solicitação de Troca de Senha</h1>
        </div>
        <div class="content">
            <p>Prezado(a) <span class="highlight">{{ $funcionario }}</span>,</p>
            <p>Recebemos sua solicitação para redefinição de senha do sistema. Para prosseguir com a troca de senha, clique no botão abaixo:</p>
            <form name="reset-password" action="{{ route('reset.password', compact('token')) }}" method="GET">
                @csrf
                <button type="submit" class="button">Redefinir Senha</button>
            </form>
            <p><strong>Importante:</strong></p>
            <p>
                - Caso não tenha solicitado a troca de senha, ignore este e-mail ou entre em contato com nossa equipe imediatamente.
            </p>
            <p>Para dúvidas ou suporte, entre em contato com a equipe de suporte pelo e-mail <a href="mailto:suporte@mindra.com.br">suporte@mindra.com.br</a></p>
            <p>Atenciosamente,<br>
            <span class="highlight">MINDRA</span><br>
            Equipe de Suporte Técnico</p>
        </div>
        <div class="footer">
            <p>Este é um e-mail automático, por favor, não responda diretamente. Para suporte, contate <a href="mailto:suporte@mindra.com.br">suporte@mindra.com.br</a>.</p>
            <p>© 2025 Mindra. Todos os direitos reservados.</p>
        </div>
    </div>
</body>
</html>

