<?php
// Arquivo de Conexão com o Banco de Dados (usando PDO)

// --- CONFIGURE SEUS DADOS AQUI ---
$host = '127.0.0.1';
$port = '3307';
$db_name = 'BancoPub'; // Verifique se 'teste_login' é o nome correto do seu banco de dados
$user = 'root';
$pass = '1234'; // Em XAMPP, a senha do root geralmente é vazia
$charset = 'utf8mb4';
// ------------------------------------

// DSN (Data Source Name) - A string de conexão do PDO
$dsn = "mysql:host=$host;port=$port;dbname=$db_name;charset=$charset";

// Opções do PDO para melhor tratamento de erros e segurança
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
];

try {
   
    
    // Cria a instância do PDO para conexão com o banco de dados
    $pdo = new PDO($dsn, $user, $pass, $options);

} // Dentro de config/db.php
catch (\PDOException $e) {
    
    // O código de log continua o mesmo
    $log_path = __DIR__ . '/../logs'; 
    $log_file = $log_path . '/app.log';

    // 2. Cria a pasta de logs se ela não existir.
    if (!is_dir($log_path)) {
        mkdir($log_path, 0755, true);
    }

    $error_message = "[" . date('Y-m-d H:i:s') . "] Erro de conexão com o banco de dados: " . $e->getMessage() . PHP_EOL;
    file_put_contents($log_file, $error_message, FILE_APPEND | LOCK_EX);

   
  
    throw new \PDOException("Erro fatal: Não foi possível conectar ao banco de dados.", (int)$e->getCode(), $e);
}// Dentro de config/db.php

 catch (\PDOException $e) {
    
    //local das logs
    $log_path = __DIR__ . '/../logs'; 
    $log_file = $log_path . '/app.log';
     // 2. Cria a pasta de logs se ela não existir.
    if (!is_dir($log_path)) {
        mkdir($log_path, 0755, true);
    }

    $error_message = "[" . date('Y-m-d H:i:s') . "] Erro de conexão com o banco de dados: " . $e->getMessage() . PHP_EOL;

    // 4. Salva a mensagem no arquivo de log.
    //    FILE_APPEND: Garante que a nova mensagem seja adicionada ao final do arquivo, sem apagar o conteúdo antigo.
    //    LOCK_EX: Garante que apenas um processo escreva no arquivo por vez, evitando corrupção do log.
    file_put_contents($log_file, $error_message, FILE_APPEND | LOCK_EX);

  
   // erro fatal: Não foi possível conectar ao banco de dados.
    throw new \PDOException("Erro fatal: Não foi possível conectar ao banco de dados.", (int)$e->getCode(), $e);
}
?>
