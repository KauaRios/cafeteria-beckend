<?php
// A primeira coisa em qualquer página que usa sessão
session_start();

// 1. VERIFICAÇÃO DE SEGURANÇA
// Se a variável de sessão 'user_id' NÃO EXISTE, significa que o usuário não está logado.
if (!isset($_SESSION["user_id"])) {
    // Redireciona o usuário para a página de login
    header("Location: frontend/index.html");
    exit; // Garante que o resto do script não seja executado
}

// Se o script chegou até aqui, o usuário está logado.

// Pega os dados do usuário da sessão para facilitar o uso.
// Usamos htmlspecialchars para evitar falhas de segurança (XSS).
$user_name = htmlspecialchars($_SESSION["user_name"]);
$user_email = htmlspecialchars($_SESSION["user_email"]);
$user_id = htmlspecialchars($_SESSION["user_id"]);


// --- DADOS DE EXEMPLO (Simulando um banco de dados) ---

// Lista de cafés disponíveis
$cafes = [
    [
        "nome" => "Espresso Clássico",
        "descricao" => "Um shot intenso e aromático do mais puro café.",
        "preco" => "7.50",
        "imagem_url" => "https://images.unsplash.com/photo-1579992305312-300e84242642?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=MnwzNjUyOXwwfDF8c2VhcmNofDE0fHxlc3ByZXNzb3xlbnwwfHx8fDE2NzI4NjQyMDI&ixlib=rb-4.0.3&q=80&w=400"
    ],
    [
        "nome" => "Cappuccino Cremoso",
        "descricao" => "Espresso, leite vaporizado e uma generosa camada de espuma.",
        "preco" => "12.00",
        "imagem_url" => "https://images.unsplash.com/photo-1557142046-c704a3adf364?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=MnwzNjUyOXwwfDF8c2VhcmNofDV8fGNhcHB1Y2Npbm98ZW58MHx8fHwxNjcyODY0MjM0&ixlib=rb-4.0.3&q=80&w=400"
    ],
    [
        "nome" => "Latte Macchiato",
        "descricao" => "Leite vaporizado 'manchado' com um toque de espresso.",
        "preco" => "13.50",
        "imagem_url" => "https://images.unsplash.com/photo-1593443320739-7eb5ce19b3d1?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=MnwzNjUyOXwwfDF8c2VhcmNofDEyfHxsYXR0ZXxlbnwwfHx8fDE2NzI4NjQyNjQ&ixlib=rb-4.0.3&q=80&w=400"
    ],
    [
        "nome" => "Café Gelado (Cold Brew)",
        "descricao" => "Café extraído a frio por 12 horas, resultando em uma bebida suave e menos ácida.",
        "preco" => "15.00",
        "imagem_url" => "https://images.unsplash.com/photo-1592663527359-cf6642f54cff?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=MnwzNjUyOXwwfDF8c2VhcmNofDF8fGNvbGQlMjBicmV3fGVufDB8fHx8MTY3Mjg2NDI5Mw&ixlib=rb-4.0.3&q=80&w=400"
    ]
];

// Lista de pedidos recentes do usuário (simulação)
$pedidos_recentes = [
    ["id" => "PED003", "item" => "Cappuccino Cremoso", "data" => "20/09/2025", "status" => "Entregue"],
    ["id" => "PED002", "item" => "Pão de Queijo", "data" => "18/09/2025", "status" => "Entregue"],
    ["id" => "PED001", "item" => "Espresso Clássico", "data" => "15/09/2025", "status" => "Cancelado"]
];

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafeteria Dev - Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --cor-primaria: #6F4E37; /* Marrom Café */
            --cor-secundaria: #F5DEB3; /* Bege Trigo */
            --cor-fundo: #FDFBF6;
            --cor-texto: #3B3B3B;
            --cor-sucesso: #28a745;
            --cor-erro: #dc3545;
            --sombra: 0 4px 12px rgba(0,0,0,0.1);
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--cor-fundo);
            margin: 0;
            color: var(--cor-texto);
        }

        .header {
            background-color: var(--cor-primaria);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .header h1 {
            margin: 0;
            font-size: 1.5em;
        }
        .header .user-info a {
            color: var(--cor-secundaria);
            text-decoration: none;
            font-weight: bold;
            margin-left: 20px;
        }
        .header .user-info a:hover {
            text-decoration: underline;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        h2 {
            color: var(--cor-primaria);
            border-bottom: 2px solid var(--cor-secundaria);
            padding-bottom: 10px;
            margin-top: 40px;
        }

        .grid-cafes {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
        }

        .card-cafe {
            background: #fff;
            border-radius: 10px;
            box-shadow: var(--sombra);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-cafe:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        .card-cafe img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .card-cafe-content {
            padding: 15px;
        }
        .card-cafe-content h3 {
            margin-top: 0;
            margin-bottom: 10px;
        }
        .card-cafe-content p {
            font-size: 0.9em;
            margin-bottom: 15px;
        }
        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .preco {
            font-size: 1.2em;
            font-weight: bold;
            color: var(--cor-primaria);
        }
        .btn-pedir {
            background-color: var(--cor-primaria);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .btn-pedir:hover {
            background-color: #5a3f2c;
        }
        
        .lista-pedidos {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: var(--sombra);
        }
        .pedido-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .pedido-item:last-child {
            border-bottom: none;
        }
        .status {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8em;
            font-weight: bold;
            color: #fff;
        }
        .status.entregue { background-color: var(--cor-sucesso); }
        .status.cancelado { background-color: var(--cor-erro); }

    </style>
</head>
<body>
    
    <header class="header">
        <h1>Cafeteria Dev</h1>
        <div class="user-info">
            <span>Bem-vindo(a), <strong><?php echo $user_name; ?></strong>!</span>
            <a href="auth/">Sair</a>
        </div>
    </header>

    <div class="container">
        <h2>Nosso Cardápio</h2>
        <div class="grid-cafes">
            <?php foreach ($cafes as $cafe): ?>
                <div class="card-cafe">
                    <img src="<?php echo htmlspecialchars($cafe['imagem_url']); ?>" alt="Imagem de <?php echo htmlspecialchars($cafe['nome']); ?>">
                    <div class="card-cafe-content">
                        <h3><?php echo htmlspecialchars($cafe['nome']); ?></h3>
                        <p><?php echo htmlspecialchars($cafe['descricao']); ?></p>
                        <div class="card-footer">
                            <span class="preco">R$ <?php echo number_format($cafe['preco'], 2, ',', '.'); ?></span>
                            <button class="btn-pedir">Pedir Agora</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <h2>Seus Pedidos Recentes</h2>
        <div class="lista-pedidos">
            <?php if (empty($pedidos_recentes)): ?>
                <p>Você ainda não fez nenhum pedido.</p>
            <?php else: ?>
                <?php foreach ($pedidos_recentes as $pedido): ?>
                    <div class="pedido-item">
                        <div>
                            <strong><?php echo htmlspecialchars($pedido['item']); ?></strong><br>
                            <small>Data: <?php echo htmlspecialchars($pedido['data']); ?> | Pedido: <?php echo htmlspecialchars($pedido['id']); ?></small>
                        </div>
                        <span class="status <?php echo strtolower(htmlspecialchars($pedido['status'])); ?>">
                            <?php echo htmlspecialchars($pedido['status']); ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
    </div>

</body>
</html>