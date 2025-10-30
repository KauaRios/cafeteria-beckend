<?php 
session_start(); 
require_once __DIR__ . "/../config/db.php"; 
require_once __DIR__ . "/../config/funcoes.php";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (empty($name) || empty($email) || empty($password)) {
        $error = "Todos os campos são obrigatórios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Formato de email inválido.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id_usuario FROM Usuario WHERE email_login = :email");
            $stmt->execute([':email' => $email]);

            if ($stmt->fetch()) {
                registrar_log($pdo, 'WARNING', 'Tentativa de cadastro com email duplicado', ['email' => $email]);
                $error = "Ops! Parece que este email já está em uso.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepared("INSERT INTO Usuario (nome, email_login, senha_hash) VALUES (:name, :email, :password_hash)");

                $stmt->execute([
                    ':name' => $name,
                    ':email' => $email,
                    ':password_hash' => $hashedPassword
                ]);

                register_log($pdo, 'INFO', 'Novo usuário registrado', ['email' => $email]);
                header("Location: ../frontend/index.html?sucess=" . urlencode("Cadastro Realizado com sucesso! Faça seu login."));
                exit;
            }
        } catch (PDOException $e) {
            registrar_log($pdo, 'ERROR', 'Falha ao registrar usuário', ['error_message'=> $e->getMessage()]);
            $error = "Erro ao tentar registrar. Tente novamente.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Registrar</title>
</head>
<body>
    <h2>Registrar</h2>
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="name" placeholder="Nome" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"><br>
        <input type="email" name="email" placeholder="Email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"><br>
        <input type="password" name="password" placeholder="Senha" required><br>
        <button type="submit">Registrar</button>
    </form>
</body>
</html>