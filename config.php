<?php
// 動態資料庫配置
function getDatabaseConfig() {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    
    if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
        // 本地開發環境
        return [
            'host' => '127.0.0.1',
            'port' => '3306',
            'dbname' => 'goldshoot0720',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8mb4'
        ];
    } else {
        // 遠端生產環境 (InfinityFree)
        return [
            'host' => 'sql301.infinityfree.com',
            'port' => '3306',
            'dbname' => 'if0_38435166_goldshoot0720',
            'username' => 'if0_38435166',
            'password' => 'gf0Tagood129',
            'charset' => 'utf8mb4'
        ];
    }
}

// 獲取資料庫連接
function getDatabase() {
    $config = getDatabaseConfig();
    
    try {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
        $pdo = new PDO($dsn, $config['username'], $config['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch(PDOException $e) {
        error_log("資料庫連接失敗: " . $e->getMessage());
        throw new Exception("資料庫連接失敗，請稍後再試");
    }
}

// 顯示當前配置資訊（僅用於調試）
function getCurrentConfig() {
    $config = getDatabaseConfig();
    $host = $_SERVER['HTTP_HOST'] ?? '';
    
    return [
        'current_host' => $host,
        'environment' => (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) ? 'local' : 'production',
        'database_host' => $config['host'],
        'database_name' => $config['dbname'],
        'database_user' => $config['username']
    ];
}
?>