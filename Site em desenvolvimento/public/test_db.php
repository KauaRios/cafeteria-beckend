<?php
// Script para testar a conexão PDO do arquivo config/db.php

// Define o charset para garantir que os acentos apareçam corretamente
header('Content-Type: text/html; charset=utf-8');
echo "<h1>Teste de Conexão PDO</h1>";

try {
    // 1. Tenta incluir o arquivo que deve criar a variável $pdo
    require_once __DIR__ . '/../config/db.php';

    // 2. Se a inclusão funcionou e não deu erro, $pdo deve existir
    if (isset($pdo) && $pdo instanceof PDO) {
        echo "<p style='color: green; font-weight: bold;'>✔️ SUCESSO: A variável \$pdo foi criada e é um objeto PDO.</p>";

        // 3. Tenta fazer uma consulta simples para confirmar que a conexão está ativa
        $stmt = $pdo->query("SELECT 'Conexão com o banco de dados funcionando!' AS message");
        $result = $stmt->fetch();

        echo "<p style='color: green; font-weight: bold;'>✔️ SUCESSO TOTAL: " . htmlspecialchars($result['message']) . "</p>";

    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ FALHA: O arquivo 'db.php' foi incluído, mas não criou a variável \$pdo corretamente.</p>";
    }

} catch (Throwable $e) {
    // Pega qualquer tipo de erro (incluindo erros de require ou de PDO)
    echo "<p style='color: red; font-weight: bold;'>❌ FALHA CRÍTICA: Ocorreu um erro.</p>";
    echo "<pre style='background-color: #ffebeb; border: 1px solid red; padding: 10px;'>" . $e->getMessage() . "</pre>";
}
?>