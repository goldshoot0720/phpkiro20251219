<?php
// 資料庫連接設定
$host = '127.0.0.1';
$port = '3306';
$dbname = 'goldshoot0720';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("資料庫連接失敗: " . $e->getMessage());
}

// 處理AJAX請求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'getFoods':
            try {
                $stmt = $pdo->query("SELECT *, ROW_NUMBER() OVER (ORDER BY todate ASC) as id FROM food ORDER BY todate ASC");
                $foods = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                $price = $_POST['price'] ?? null;
                $price = ($price === '' || $price === null) ? null : (int)$price;
                $shop = $_POST['shop'] ?? '';
                $photohash = $_POST['photohash'] ?? '';
                
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
                $price = $_POST['price'] ?? null;
                $price = ($price === '' || $price === null) ? null : (int)$price;
                $shop = $_POST['shop'] ?? '';
                $photohash = $_POST['photohash'] ?? '';
                
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