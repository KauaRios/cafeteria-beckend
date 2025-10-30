<?php
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/funcoes.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!empty($_POST['email_login']) && !empty($_POST['password'])) {
        $email = $_POST ['email_login'];
        $password = $_POST ['senha_hash'];

        try {
            $stmt = $pdo->prepare("SELECT * FROM Usuario WHERE email_login = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['PASSWORD'])) {

                $_SESSION['user_id'] = $user['cpf'];
                $_SESSION['user_name'] = $user['nome'];
                $_SESSION['user_email'] = $user['email_login'];

                registrar_log($pdo, 'INFO', 'Login bem-sucedido', ['user_id' => $user['cpf'], 'email' => $email]);

                header("Location: ../dashboard.php");
                exit;
            } else {
                registrar_log($pdo, 'WARNING', 'Tentativa de login falhou', ['email' => $email, 'ip' => $_SERVER['REMOTE_ADDR']]);
                
                $error = "Email e/ou senha inválido(s).";
                header("Location: ../frontend/index.html?error=" . urlencode($error));
                exit;
            }
        } catch (PDOException $e) {
            registrar_log($pdo, 'ERROR', 'Erro de banco de dados no login', ['error_message' => $e->getMessage()]);

            $error = "Error no sistema. Tente novamente mais tarde.";
            header("Location: ..frontend/index.html?error=" . urlencode($error));
            exit;
        }
    } else {
        $error = "Por favor, preencha todos os campos.";
        header("Location: ../frontend/index.html?error=" . urlencode($error));
        exit;
    }
} else {
    header("Location: ../frontend/index.html");
    exit;
}