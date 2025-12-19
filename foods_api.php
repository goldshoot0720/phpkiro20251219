<?php
// 引入動態資料庫配置
require_once 'config.php';

try {
    $pdo = getDatabase();
} catch(Exception $e) {
    die("資料庫連接失敗: " . $e->getMessage());
}

// 處理AJAX請求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'getFoods':
            try {
                $stmt = $pdo->query("SELECT name, todate, amount, photo, price, shop, photohash, ROW_NUMBER() OVER (ORDER BY todate ASC) as row_id FROM food ORDER BY todate ASC");
                $foods = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // 調試：記錄查詢結果
                error_log("Foods API - 查詢結果: " . json_encode($foods));
                
                // 確保資料類型正確
                foreach ($foods as &$food) {
                    // 確保數值欄位是正確的類型
                    $food['amount'] = $food['amount'] ? (int)$food['amount'] : null;
                    $food['price'] = $food['price'] ? (int)$food['price'] : 0;
                    
                    // 確保字串欄位不是 null
                    $food['name'] = $food['name'] ?? '';
                    $food['todate'] = $food['todate'] ?? '';
                    $food['photo'] = $food['photo'] ?? '';
                    $food['shop'] = $food['shop'] ?? '';
                    $food['photohash'] = $food['photohash'] ?? '';
                }
                
                echo json_encode(['success' => true, 'data' => $foods]);
            } catch(PDOException $e) {
                echo json_encode(['success' => false, 'message' => '獲取食品資料失敗: ' . $e->getMessage()]);
            }
            break;
            
        case 'addFood':
            try {
                $name = $_POST['name'] ?? '';
                $todate = $_POST['todate'] ?? '';
                $amount = $_POST['amount'] ?? null;
                $amount = ($amount === '' || $amount === null) ? null : (int)$amount;
                $photo = $_POST['photo'] ?? '';
                $price = $_POST['price'] ?? 0;
                $price = ($price === '' || $price === null) ? 0 : (int)$price;
                $shop = $_POST['shop'] ?? '';
                $photohash = ''; // 不再使用，保留空值
                
                // 調試：記錄接收到的資料
                error_log("AddFood - 接收到的資料: " . json_encode($_POST));
                error_log("AddFood - 處理後的資料: name=$name, todate=$todate, amount=$amount, price=$price, shop=$shop, photo=$photo");
                
                // 驗證日期格式
                if ($todate && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $todate)) {
                    throw new Exception("日期格式錯誤: $todate");
                }
                
                $stmt = $pdo->prepare("INSERT INTO food (name, todate, amount, photo, price, shop, photohash) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $todate, $amount, $photo, $price, $shop, $photohash]);
                
                echo json_encode(['success' => true, 'message' => '食品新增成功']);
            } catch(PDOException $e) {
                echo json_encode(['success' => false, 'message' => '新增食品失敗: ' . $e->getMessage()]);
            }
            break;
            
        case 'updateFood':
            try {
                $originalName = $_POST['originalName'] ?? '';
                $originalTodate = $_POST['originalTodate'] ?? '';
                $name = $_POST['name'] ?? '';
                $todate = $_POST['todate'] ?? '';
                $amount = $_POST['amount'] ?? null;
                $amount = ($amount === '' || $amount === null) ? null : (int)$amount;
                $photo = $_POST['photo'] ?? '';
                $price = $_POST['price'] ?? 0;
                $price = ($price === '' || $price === null) ? 0 : (int)$price;
                $shop = $_POST['shop'] ?? '';
                $photohash = ''; // 不再使用，保留空值
                
                $stmt = $pdo->prepare("UPDATE food SET name=?, todate=?, amount=?, photo=?, price=?, shop=?, photohash=? WHERE name=? AND todate=?");
                $stmt->execute([$name, $todate, $amount, $photo, $price, $shop, $photohash, $originalName, $originalTodate]);
                
                echo json_encode(['success' => true, 'message' => '食品更新成功']);
            } catch(PDOException $e) {
                echo json_encode(['success' => false, 'message' => '更新食品失敗: ' . $e->getMessage()]);
            }
            break;
            
        case 'deleteFood':
            try {
                $name = $_POST['name'] ?? '';
                $todate = $_POST['todate'] ?? '';
                
                $stmt = $pdo->prepare("DELETE FROM food WHERE name=? AND todate=?");
                $stmt->execute([$name, $todate]);
                
                echo json_encode(['success' => true, 'message' => '食品刪除成功']);
            } catch(PDOException $e) {
                echo json_encode(['success' => false, 'message' => '刪除食品失敗: ' . $e->getMessage()]);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => '無效的操作']);
    }
    exit;
}
?>