<?php
// Inicia a sessão para gerenciar o estado do usuário
session_start();
// Importa o arquivo de configuração do banco de dados (onde está a conexão PDO)
require_once __DIR__ . '/../config/db.php';
// Importa funções auxiliares, como a função de registrar logs de eventos
require_once __DIR__ . '/../config/funcoes.php';


// Request method POST indica que o formulário de login foi submetido
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Verifica se os campos de email e senha foram preenchidos
    if (!empty($_POST['email_login']) && !empty($_POST['password'])) {
        $email = $_POST ['email_login'];
        $password = $_POST ['senha_hash'];
        // Tenta buscar o usuário no banco de dados
        try {
            $stmt = $pdo->prepare("SELECT * FROM Usuario WHERE email_login = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verifica se o usuário foi encontrado e se a senha está correta
            if ($user && password_verify($password, $user['PASSWORD'])) {

                $_SESSION['user_id'] = $user['cpf'];
                $_SESSION['user_name'] = $user['nome'];
                $_SESSION['user_email'] = $user['email_login'];

                // Registra no log que o login foi bem-sucedido
                registrar_log($pdo, 'INFO', 'Login bem-sucedido', ['user_id' => $user['cpf'], 'email' => $email]);
                //redireciona para a página inicial do sistema
                header("Location: ../dashboard.php");
                exit;
                // Se o login falhar, registra no log e informa o usuário
            } else {
                registrar_log($pdo, 'WARNING', 'Tentativa de login falhou', ['email' => $email, 'ip' => $_SERVER['REMOTE_ADDR']]);
                // Mensagem genérica para não revelar se o email ou a senha estão incorretos
                $error = "Email e/ou senha inválido(s).";
                header("Location: ../frontend/index.html?error=" . urlencode($error));
                exit;
            }
            // Fim do try

            // Captura erros de banco de dados
        } catch (PDOException $e) {
            registrar_log($pdo, 'ERROR', 'Erro de banco de dados no login', ['error_message' => $e->getMessage()]);
            // Mensagem genérica de erro para o usuário
            $error = "Error no sistema. Tente novamente mais tarde.";
            header("Location: ..frontend/index.html?error=" . urlencode($error));
            exit;
        }
        // Fim do if dos campos preenchidos

        // Se os campos não foram preenchidos, informa o usuário
    } else {
        $error = "Por favor, preencha todos os campos.";
        header("Location: ../frontend/index.html?error=" . urlencode($error));
        exit;
    }
    // Fim do if do método POST

    // Se o acesso não for via POST, redireciona para a página inicial
} else {
    header("Location: ../frontend/index.html");
    exit;
}