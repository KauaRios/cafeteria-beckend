<?php 
// Inicia a sessão para poder armazenar dados de usuário, mensagens, etc.
session_start(); 

// Importa o arquivo de configuração do banco de dados (onde está a conexão PDO)
require_once __DIR__ . "/../config/db.php"; 

// Importa funções auxiliares, como a função de registrar logs de eventos
require_once __DIR__ . "/../config/funcoes.php";

// Variável para armazenar mensagens de erro, caso algo dê errado
$error = "";

// Verifica se o formulário foi enviado via método POST (ou seja, o usuário clicou em "Registrar")
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Pega os dados enviados no formulário e remove espaços extras nas pontas
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Verifica se todos os campos foram preenchidos
    if (empty($name) || empty($email) || empty($password)) {
        $error = "Todos os campos são obrigatórios.";

    // Verifica se o email informado tem um formato válido
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Formato de email inválido.";

    } else {
        try {
            // Verifica no banco se já existe um usuário com o mesmo email
            $stmt = $pdo->prepare("SELECT id_usuario FROM Usuario WHERE email_login = :email");
            $stmt->execute([':email' => $email]);

            // Se encontrar o email, significa que já existe alguém com esse login
            if ($stmt->fetch()) {
                // Registra um log de aviso informando tentativa de cadastro com email duplicado
                registrar_log($pdo, 'WARNING', 'Tentativa de cadastro com email duplicado', ['email' => $email]);

                // Exibe mensagem de erro ao usuário
                $error = "Ops! Parece que este email já está em uso.";
            } else {
                // Criptografa a senha do usuário antes de salvar (por segurança)
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Prepara a query para inserir o novo usuário no banco de dados
                $stmt = $pdo->prepare("INSERT INTO Usuario (nome, email_login, senha_hash) VALUES (:name, :email, :password_hash)");

                // Executa a inserção dos dados no banco
                $stmt->execute([
                    ':name' => $name,
                    ':email' => $email,
                    ':password_hash' => $hashedPassword
                ]);

                // Registra no log que um novo usuário foi criado
                registrar_log($pdo, 'INFO', 'Novo usuário registrado', ['email' => $email]);

                // Redireciona o usuário para a página inicial com uma mensagem de sucesso
                header("Location: ../frontend/index.html?sucess=" . urlencode("Cadastro Realizado com sucesso! Faça seu login."));
                exit; // Encerra o script após o redirecionamento
            }
        } catch (PDOException $e) {
            // Se ocorrer algum erro no banco, registra o erro no log
            registrar_log($pdo, 'ERROR', 'Falha ao registrar usuário', ['error_message'=> $e->getMessage()]);
            // Mensagem amigável exibida para o usuário
            $error = "Erro ao tentar registrar. Tente novamente.";
        }
    }
}
?>

<!-- Parte visual da página -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Registrar</title>
</head>
<body>
    <h2>Registrar</h2>

    <!-- Caso exista alguma mensagem de erro, exibe aqui em vermelho -->
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <!-- Formulário de registro do usuário -->
    <form method="POST">
        <!-- Campo para nome -->
        <input 
            type="text" 
            name="name" 
            placeholder="Nome" 
            required 
            value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
        ><br>

        <!-- Campo para email -->
        <input 
            type="email" 
            name="email" 
            placeholder="Email" 
            required 
            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
        ><br>

        <!-- Campo para senha -->
        <input 
            type="password" 
            name="password" 
            placeholder="Senha" 
            required
        ><br>

        <!-- Botão para enviar o formulário -->
        <button type="submit">Registrar</button>
    </form>
</body>
</html>
