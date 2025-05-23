<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autorização para Realização de Avaliação - {{ $campanha_empresa->campanha->titulo }}</title>
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
            <h1>Autorização para Avaliação - {{ $campanha_empresa->campanha->titulo }}</h1>
        </div>
        <div class="content">
            <p>Prezado(a) <span class="highlight">{{ $funcionario->user->nome }}</span>,</p>
            <p>Informamos que você foi autorizado(a) a realizar a avaliação referente à campanha <span class="highlight">{{ $campanha_empresa->campanha->titulo }}</span>.</p>
            <p><strong>Detalhes da Avaliação:</strong></p>
            <p>
                - <strong>Período:</strong> De <span class="highlight">{{ $campanha_empresa->campanha->data_inicio_formatada }}</span> até <span class="highlight">{{ $campanha_empresa->campanha->data_fim_formatada }}</span><br>
                - <strong>Empresa:</strong> {{ $campanha_empresa->empresa->nome }}<br>
            </p>
            <p>Para acessar a avaliação, clique no botão abaixo:</p>
            <a href="[Link para Avaliação]" class="button">Acessar Avaliação</a>
            <p>Caso tenha dúvidas ou precise de suporte, entre em contato com a equipe de Recursos Humanos pelo e-mail <a href="mailto:{{ $campanha_empresa->empresa->email }}">{{ $campanha_empresa->empresa->email }}</a>.</p>
            <p>Atenciosamente,<br>
            <span class="highlight">{{ $campanha_empresa->empresa->nome }}</span><br>
            Equipe de Recursos Humanos</p>
        </div>
        <div class="footer">
            <p>Este é um e-mail automático, por favor, não responda diretamente. Para suporte, contate <a href="mailto:[E-mail de Suporte]">[E-mail de Suporte]</a>.</p>
            <p>&copy; 2025 Mindra. Todos os direitos reservados.</p>
        </div>
    </div>
</body>
</html>
