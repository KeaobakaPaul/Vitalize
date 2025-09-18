<?php
function db() {
    static $pdo;

    if ($pdo === null) {
        $config = require __DIR__ . '/config.php';
        $dbConf = $config['db'];

        $dsn = "mysql:host=127.0.0.1;port=3307;dbname=vitalize_db;charset=utf8mb4";

        try {
            $pdo = new PDO($dsn, $dbConf['user'], $dbConf['pass']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("DB connection failed: " . $e->getMessage());
        }
    }

    return $pdo;
}
