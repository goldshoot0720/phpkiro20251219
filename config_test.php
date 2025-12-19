<?php
// 配置測試頁面
require_once 'config.php';

$currentConfig = getCurrentConfig();
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>資料庫配置測試</title>
    <style>
        body {
            font-family: 'Microsoft JhengHei', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: white;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 30px;
        }
        .config-section {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 12px;
            margin: 20px 0;
        }
        .success {
            color: #4CAF50;
            font-weight: bold;
        }
        .error {
            color: #f44336;
            font-weight: bold;
        }
        .info {
            color: #2196F3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        th {
            background: rgba(255, 255, 255, 0.1);
            font-weight: bold;
        }
        .test-btn {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 5px;
        }
        .test-btn:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 資料庫配置測試</h1>
        
        <div class="config-section">
            <h2>當前環境資訊</h2>
            <table>
                <tr>
                    <th>項目</th>
                    <th>值</th>
                </tr>
                <tr>
                    <td>當前網址</td>
                    <td class="info"><?php echo $currentConfig['current_host']; ?></td>
                </tr>
                <tr>
                    <td>環境類型</td>
                    <td class="info"><?php echo $currentConfig['environment'] === 'local' ? '本地開發環境' : '遠端生產環境'; ?></td>
                </tr>
                <tr>
                    <td>資料庫主機</td>
                    <td><?php echo $currentConfig['database_host']; ?></td>
                </tr>
                <tr>
                    <td>資料庫名稱</td>
                    <td><?php echo $currentConfig['database_name']; ?></td>
                </tr>
                <tr>
                    <td>資料庫用戶</td>
                    <td><?php echo $currentConfig['database_user']; ?></td>
                </tr>
            </table>
        </div>

        <div class="config-section">
            <h2>資料庫連接測試</h2>
            <?php
            try {
                $pdo = getDatabase();
                echo "<p class='success'>✅ 資料庫連接成功！</p>";
                
                // 測試查詢
                $stmt = $pdo->query("SHOW TABLES");
                $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                echo "<h3>資料表列表:</h3>";
                echo "<ul>";
                foreach ($tables as $table) {
                    echo "<li>" . htmlspecialchars($table) . "</li>";
                }
                echo "</ul>";
                
                // 測試訂閱表
                if (in_array('subscription', $tables)) {
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM subscription");
                    $result = $stmt->fetch();
                    echo "<p class='info'>📋 訂閱記錄數量: " . $result['count'] . "</p>";
                }
                
                // 測試食品表
                if (in_array('food', $tables)) {
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM food");
                    $result = $stmt->fetch();
                    echo "<p class='info'>🍽️ 食品記錄數量: " . $result['count'] . "</p>";
                }
                
            } catch (Exception $e) {
                echo "<p class='error'>❌ 資料庫連接失敗: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
            ?>
        </div>

        <div class="config-section">
            <h2>功能測試</h2>
            <button class="test-btn" onclick="testSubscriptions()">測試訂閱管理</button>
            <button class="test-btn" onclick="testFoods()">測試食品管理</button>
            <button class="test-btn" onclick="location.href='index.php'">返回主頁</button>
            
            <div id="testResult" style="margin-top: 20px;"></div>
        </div>
    </div>

    <script>
        async function testSubscriptions() {
            const resultDiv = document.getElementById('testResult');
            resultDiv.innerHTML = '<p>測試訂閱管理功能...</p>';
            
            try {
                const formData = new FormData();
                formData.append('action', 'getSubscriptions');
                
                const response = await fetch('subscriptions.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="success">
                            <h3>✅ 訂閱管理測試成功</h3>
                            <p>找到 ${data.data.length} 筆訂閱記錄</p>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="error">
                            <h3>❌ 訂閱管理測試失敗</h3>
                            <p>${data.message}</p>
                        </div>
                    `;
                }
            } catch (error) {
                resultDiv.innerHTML = `
                    <div class="error">
                        <h3>❌ 訂閱管理測試錯誤</h3>
                        <p>${error.message}</p>
                    </div>
                `;
            }
        }

        async function testFoods() {
            const resultDiv = document.getElementById('testResult');
            resultDiv.innerHTML = '<p>測試食品管理功能...</p>';
            
            try {
                const formData = new FormData();
                formData.append('action', 'getFoods');
                
                const response = await fetch('foods_api.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="success">
                            <h3>✅ 食品管理測試成功</h3>
                            <p>找到 ${data.data.length} 筆食品記錄</p>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="error">
                            <h3>❌ 食品管理測試失敗</h3>
                            <p>${data.message}</p>
                        </div>
                    `;
                }
            } catch (error) {
                resultDiv.innerHTML = `
                    <div class="error">
                        <h3>❌ 食品管理測試錯誤</h3>
                        <p>${error.message}</p>
                    </div>
                `;
            }
        }
    </script>
</body>
</html>