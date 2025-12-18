<?php
session_start();
include_once 'backend/config/database.php';

$database = new Database();
$db = $database->getConnection();

// 處理表單提交
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $query = "INSERT INTO food (name, todate, amount, price, shop, photo, photohash) 
                         VALUES (:name, :todate, :amount, :price, :shop, :photo, :photohash)";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':name', $_POST['name']);
                $stmt->bindParam(':todate', $_POST['todate']);
                $stmt->bindParam(':amount', $_POST['amount']);
                $stmt->bindParam(':price', $_POST['price']);
                $stmt->bindParam(':shop', $_POST['shop']);
                $stmt->bindParam(':photo', $_POST['photo']);
                $stmt->bindParam(':photohash', $_POST['photohash']);
                
                if ($stmt->execute()) {
                    $message = "食品新增成功！";
                } else {
                    $error = "新增失敗，請重試。";
                }
                break;
                
            case 'delete':
                $query = "DELETE FROM food WHERE name = :name";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':name', $_POST['name']);
                
                if ($stmt->execute()) {
                    $message = "食品刪除成功！";
                } else {
                    $error = "刪除失敗，請重試。";
                }
                break;
        }
    }
}

// 獲取所有食品
$query = "SELECT * FROM food ORDER BY todate DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$foods = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🍔 食品管理 - 鋒兒AI資訊系統</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Microsoft JhengHei', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: white;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.2rem;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            gap: 1.5rem;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: background 0.3s;
        }

        .nav-links a:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.9);
            color: #333;
        }

        .form-group textarea {
            height: 100px;
            resize: vertical;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .nutrition-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s;
        }

        .btn-primary {
            background: #4CAF50;
            color: white;
        }

        .btn-primary:hover {
            background: #45a049;
        }

        .btn-danger {
            background: #f44336;
            color: white;
        }

        .btn-danger:hover {
            background: #da190b;
        }

        .food-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .food-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 1.5rem;
            border-radius: 12px;
            transition: transform 0.3s;
        }

        .food-card:hover {
            transform: translateY(-5px);
        }

        .food-card h3 {
            color: #FFD700;
            margin-bottom: 0.5rem;
            font-size: 1.3rem;
        }

        .food-category {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .food-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #4CAF50;
            margin-bottom: 0.5rem;
        }

        .food-rating {
            color: #FFD700;
            margin-bottom: 0.5rem;
        }

        .food-stock {
            margin-bottom: 1rem;
        }

        .stock-high {
            color: #4CAF50;
        }

        .stock-medium {
            color: #ff9800;
        }

        .stock-low {
            color: #f44336;
        }

        .nutrition-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
        }

        .nutrition-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background: rgba(76, 175, 80, 0.2);
            border: 1px solid #4CAF50;
        }

        .alert-error {
            background: rgba(244, 67, 54, 0.2);
            border: 1px solid #f44336;
        }

        .status-available {
            color: #4CAF50;
        }

        .status-unavailable {
            color: #f44336;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <span>🤖</span>
            <span>鋒兒AI資訊系統</span>
        </div>
        <div class="nav-links">
            <a href="index.php">🏠 首頁</a>
            <a href="subscriptions.php">💳 訂閱管理</a>
            <a href="foods.php">🍔 食品管理</a>
            <a href="backend/test.php">🔧 系統測試</a>
        </div>
    </nav>

    <div class="container">
        <div class="header">
            <h1>🍔 食品管理</h1>
            <p>管理宮廷風味食品庫存</p>
        </div>

        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card">
            <h2>新增食品</h2>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-row">
                    <div class="form-group">
                        <label>食品名稱</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>商店</label>
                        <input type="text" name="shop" required>
                    </div>
                    <div class="form-group">
                        <label>價格 (NT$)</label>
                        <input type="number" name="price" required>
                    </div>
                    <div class="form-group">
                        <label>數量</label>
                        <input type="number" name="amount" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>到期日期</label>
                        <input type="date" name="todate" required>
                    </div>
                    <div class="form-group">
                        <label>照片檔名</label>
                        <input type="text" name="photo" placeholder="例: food.jpg">
                    </div>
                    <div class="form-group">
                        <label>照片雜湊</label>
                        <input type="text" name="photohash" placeholder="照片雜湊值">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">新增食品</button>
            </form>
        </div>

        <div class="card">
            <h2>食品列表</h2>
            <div class="food-grid">
                <?php foreach ($foods as $food): ?>
                    <?php 
                    $amount_class = '';
                    if ($food['amount'] > 20) $amount_class = 'stock-high';
                    elseif ($food['amount'] > 5) $amount_class = 'stock-medium';
                    else $amount_class = 'stock-low';
                    ?>
                    <div class="food-card">
                        <h3><?php echo htmlspecialchars($food['name']); ?></h3>
                        <div class="food-category"><?php echo htmlspecialchars($food['shop']); ?></div>
                        <div class="food-price">NT$ <?php echo number_format($food['price']); ?></div>
                        <div class="food-stock <?php echo $amount_class; ?>">
                            📦 數量: <?php echo $food['amount']; ?> 份
                        </div>
                        <div class="food-rating">
                            📅 到期日: <?php echo $food['todate']; ?>
                        </div>
                        
                        <?php if ($food['photo']): ?>
                            <div style="margin: 1rem 0; opacity: 0.9;">
                                📷 照片: <?php echo htmlspecialchars($food['photo']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" style="margin-top: 1rem;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="name" value="<?php echo htmlspecialchars($food['name']); ?>">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('確定要刪除此食品嗎？')">刪除</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>