<?php
// 食品管理 API 和頁面整合
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
                $stmt = $pdo->query("SELECT * FROM food ORDER BY todate ASC");
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
            } catch(Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;
            
        case 'updateFood':
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
                $id = $_POST['id'] ?? '';
                
                if (empty($id)) {
                    throw new Exception('缺少必要的 ID 參數');
                }
                
                $stmt = $pdo->prepare("UPDATE food SET name=?, todate=?, amount=?, photo=?, price=?, shop=?, photohash=? WHERE id=?");
                $stmt->execute([$name, $todate, $amount, $photo, $price, $shop, $photohash, $id]);
                
                echo json_encode(['success' => true, 'message' => '食品更新成功']);
            } catch(PDOException $e) {
                echo json_encode(['success' => false, 'message' => '更新食品失敗: ' . $e->getMessage()]);
            } catch(Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;
            
        case 'deleteFood':
            try {
                $id = $_POST['id'] ?? '';
                
                if (empty($id)) {
                    throw new Exception('缺少必要的 ID 參數');
                }
                
                $stmt = $pdo->prepare("DELETE FROM food WHERE id=?");
                $stmt->execute([$id]);
                
                echo json_encode(['success' => true, 'message' => '食品刪除成功']);
            } catch(PDOException $e) {
                echo json_encode(['success' => false, 'message' => '刪除食品失敗: ' . $e->getMessage()]);
            } catch(Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => '無效的操作']);
    }
    exit;
}

// 如果不是 POST 請求，顯示食品管理頁面
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🍽️ 食品管理系統</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
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

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .main-title {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .main-logo {
            width: 60px;
            height: 60px;
            background: #96ceb4;
            border-radius: 12px;
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .subtitle {
            font-size: 18px;
            opacity: 0.9;
            margin-bottom: 30px;
        }

        .controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .search-box {
            padding: 12px 20px;
            border: none;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 16px;
            min-width: 300px;
            backdrop-filter: blur(10px);
        }

        .search-box::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .add-btn {
            padding: 12px 25px;
            background: #96ceb4;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .add-btn:hover {
            background: #85b8a3;
        }

        .food-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }

        .food-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 20px;
            border-left: 4px solid #96ceb4;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .food-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .food-card.expired {
            border-left-color: #ff6b6b;
        }

        .food-card.expiring-soon {
            border-left-color: #ffa726;
        }

        .food-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #FFD700;
        }

        .food-image {
            width: 100%;
            max-width: 200px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            cursor: pointer;
            transition: transform 0.3s;
        }

        .food-image:hover {
            transform: scale(1.05);
        }

        .food-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            opacity: 0.8;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .detail-value {
            font-weight: bold;
            font-size: 16px;
        }

        .detail-value.price {
            color: #4ecdc4;
        }

        .detail-value.expired {
            color: #ff6b6b;
        }

        .detail-value.expiring-soon {
            color: #ffa726;
        }

        .detail-value.normal {
            color: #4ecdc4;
        }

        .food-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .action-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 8px;
            padding: 8px 12px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .action-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .edit-btn:hover {
            background: rgba(69, 183, 209, 0.3);
        }

        .delete-btn:hover {
            background: rgba(255, 107, 107, 0.3);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            opacity: 0.7;
        }

        .empty-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 12px 20px;
            border-radius: 25px;
            text-decoration: none;
            backdrop-filter: blur(10px);
            transition: background-color 0.3s;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* 平板版本 (768px - 1024px) */
        @media (max-width: 1024px) and (min-width: 769px) {
            .container {
                padding: 0 30px;
            }
            
            .food-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 25px;
            }
            
            .main-title {
                font-size: 42px;
            }
        }

        /* 手機版本 (最大 768px) */
        @media (max-width: 768px) {
            .container {
                padding: 0 15px;
            }
            
            .main-title {
                font-size: 32px;
                flex-direction: column;
                text-align: center;
            }
            
            .main-logo {
                margin-right: 0;
                margin-bottom: 10px;
                width: 50px;
                height: 50px;
                font-size: 20px;
            }
            
            .subtitle {
                font-size: 16px;
            }

            .controls {
                flex-direction: column;
                align-items: stretch;
                gap: 15px;
            }

            .search-box {
                min-width: auto;
                width: 100%;
                padding: 15px 20px;
                font-size: 16px;
            }
            
            .add-btn {
                padding: 15px 25px;
                font-size: 16px;
                width: 100%;
            }

            .food-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .food-card {
                padding: 20px 15px;
            }
            
            .food-name {
                font-size: 18px;
            }

            .food-details {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            
            .food-image {
                max-width: 150px;
                height: 120px;
            }
            
            .back-btn {
                position: relative;
                top: auto;
                left: auto;
                margin-bottom: 20px;
                display: inline-block;
                width: auto;
            }
        }

        /* 模態框樣式 */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal-content {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            padding: 0;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .modal-header h3 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }

        .close-btn {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s;
        }

        .close-btn:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .food-form {
            padding: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 14px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 16px;
            backdrop-filter: blur(10px);
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 0 2px rgba(150, 206, 180, 0.5);
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
        }

        .cancel-btn,
        .save-btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .cancel-btn {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .cancel-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .save-btn {
            background: #96ceb4;
            color: white;
        }

        .save-btn:hover {
            background: #85b8a3;
            transform: translateY(-1px);
        }

        /* 小手機版本 (最大 480px) */
        @media (max-width: 480px) {
            .container {
                padding: 0 10px;
            }
            
            .main-title {
                font-size: 28px;
            }
            
            .main-logo {
                width: 45px;
                height: 45px;
                font-size: 18px;
            }
            
            .subtitle {
                font-size: 14px;
            }
            
            .food-card {
                padding: 15px 12px;
            }
            
            .food-name {
                font-size: 16px;
            }
            
            .food-image {
                max-width: 120px;
                height: 100px;
            }
            
            .detail-label {
                font-size: 12px;
            }
            
            .detail-value {
                font-size: 14px;
            }
            
            .action-btn {
                padding: 6px 10px;
                font-size: 14px;
            }

            .modal-content {
                width: 95%;
                margin: 10px;
                max-height: 95vh;
            }
            
            .modal-header {
                padding: 20px;
            }
            
            .food-form {
                padding: 20px;
            }

            .form-actions {
                flex-direction: column;
                gap: 12px;
            }

            .cancel-btn, .save-btn {
                width: 100%;
                padding: 15px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <a href="index.php" class="back-btn">← 返回主系統</a>
    
    <div id="app">
        <div class="container">
            <div class="header">
                <h1 class="main-title">
                    <div class="main-logo">🍽️</div>
                    食品管理系統
                </h1>
                <p class="subtitle">管理您的食品庫存和到期日期</p>
            </div>

            <div class="controls">
                <input 
                    type="text" 
                    class="search-box" 
                    placeholder="搜尋食品名稱或商店..." 
                    v-model="searchQuery"
                    @input="filterFoods"
                >
                <button class="add-btn" @click="showAddModal = true">
                    ➕ 新增食品
                </button>
            </div>

            <div class="food-grid" v-if="filteredFoods.length > 0">
                <div 
                    class="food-card" 
                    v-for="(food, index) in filteredFoods" 
                    :key="index"
                    :class="{ 'expired': isExpired(food.todate), 'expiring-soon': isExpiringSoon(food.todate) }"
                >
                    <div class="food-name">{{ food.name }}</div>
                    
                    <img 
                        v-if="food.photo" 
                        :src="food.photo" 
                        :alt="food.name"
                        class="food-image"
                        @click="viewImage(food.photo)"
                        @error="handleImageError"
                    >
                    
                    <div class="food-details">
                        <div class="detail-item">
                            <span class="detail-label">到期日期:</span>
                            <span class="detail-value" :class="getDateClass(food.todate)">{{ formatDate(food.todate) }}</span>
                        </div>
                        <div class="detail-item" v-if="food.amount">
                            <span class="detail-label">數量:</span>
                            <span class="detail-value">{{ food.amount }}</span>
                        </div>
                        <div class="detail-item" v-if="food.price && food.price > 0">
                            <span class="detail-label">價格:</span>
                            <span class="detail-value price">NT$ {{ food.price }}</span>
                        </div>
                        <div class="detail-item" v-if="food.shop">
                            <span class="detail-label">商店:</span>
                            <span class="detail-value">{{ food.shop }}</span>
                        </div>
                    </div>
                    
                    <div class="food-actions">
                        <button class="action-btn edit-btn" @click="editFood(food, index)">✏️</button>
                        <button class="action-btn delete-btn" @click="deleteFood(index)">🗑️</button>
                    </div>
                </div>
            </div>

            <div class="empty-state" v-else>
                <div class="empty-icon">🍽️</div>
                <h3>暫無食品記錄</h3>
                <p>點擊「新增食品」開始管理您的食品庫存</p>
            </div>

            <!-- 新增/編輯食品模態框 -->
            <div class="modal-overlay" v-if="showAddModal || showEditModal" @click="closeModals">
                <div class="modal-content" @click.stop>
                    <div class="modal-header">
                        <h3>{{ showEditModal ? '編輯食品' : '新增食品' }}</h3>
                        <button class="close-btn" @click="closeModals">✕</button>
                    </div>
                    <form @submit.prevent="saveFood" class="food-form">
                        <div class="form-group">
                            <label>食品名稱 *</label>
                            <input type="text" v-model="currentFood.name" required>
                        </div>
                        <div class="form-group">
                            <label>到期日期 *</label>
                            <input type="date" v-model="currentFood.todate" required>
                        </div>
                        <div class="form-group">
                            <label>數量</label>
                            <input type="number" v-model="currentFood.amount" min="0" placeholder="選填">
                        </div>
                        <div class="form-group">
                            <label>價格 (NT$)</label>
                            <input type="number" v-model="currentFood.price" min="0" placeholder="選填">
                        </div>
                        <div class="form-group">
                            <label>商店</label>
                            <input type="text" v-model="currentFood.shop" placeholder="購買商店">
                        </div>
                        <div class="form-group">
                            <label>圖片網址</label>
                            <input type="url" v-model="currentFood.photo" placeholder="https://example.com/image.jpg">
                            <div v-if="currentFood.photo" style="margin-top: 10px;">
                                <img 
                                    :src="currentFood.photo" 
                                    alt="預覽圖片"
                                    style="max-width: 100px; height: auto; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"
                                    @error="handleImageError"
                                >
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="cancel-btn" @click="closeModals">取消</button>
                            <button type="submit" class="save-btn">{{ showEditModal ? '更新' : '新增' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const { createApp } = Vue;

        createApp({
            data() {
                return {
                    foods: [],
                    filteredFoods: [],
                    searchQuery: '',
                    showAddModal: false,
                    showEditModal: false,
                    currentFood: {
                        name: '',
                        todate: '',
                        amount: '',
                        photo: '',
                        price: '',
                        shop: ''
                    },
                    editingIndex: -1
                }
            },
            mounted() {
                this.loadFoods();
            },
            methods: {
                async loadFoods() {
                    try {
                        const formData = new FormData();
                        formData.append('action', 'getFoods');
                        
                        const response = await fetch('foods.php', {
                            method: 'POST',
                            body: formData
                        });
                        const data = await response.json();
                        
                        if (data.success) {
                            this.foods = data.data || [];
                            this.filteredFoods = [...this.foods];
                        } else {
                            console.error('載入食品失敗:', data.message);
                            this.foods = [];
                            this.filteredFoods = [];
                        }
                    } catch (error) {
                        console.error('載入食品失敗:', error);
                        this.foods = [];
                        this.filteredFoods = [];
                    }
                },
                filterFoods() {
                    if (!this.searchQuery.trim()) {
                        this.filteredFoods = [...this.foods];
                    } else {
                        this.filteredFoods = this.foods.filter(food => 
                            food.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            (food.shop && food.shop.toLowerCase().includes(this.searchQuery.toLowerCase()))
                        );
                    }
                },
                async deleteFood(index) {
                    if (!confirm('確定要刪除這個食品嗎？')) return;
                    
                    try {
                        const food = this.filteredFoods[index];
                        const formData = new FormData();
                        formData.append('action', 'deleteFood');
                        formData.append('name', food.name);
                        formData.append('todate', food.todate);
                        
                        const response = await fetch('foods.php', {
                            method: 'POST',
                            body: formData
                        });
                        const data = await response.json();
                        
                        if (data.success) {
                            await this.loadFoods();
                            alert('食品刪除成功！');
                        } else {
                            alert('刪除失敗: ' + data.message);
                        }
                    } catch (error) {
                        console.error('刪除食品失敗:', error);
                        alert('刪除失敗，請稍後再試');
                    }
                },
                viewImage(imageSrc) {
                    if (imageSrc) {
                        window.open(imageSrc, '_blank');
                    }
                },
                handleImageError(event) {
                    event.target.style.display = 'none';
                },
                formatDate(dateString) {
                    if (!dateString || dateString === '0000-00-00') return '未設定';
                    
                    try {
                        let date;
                        if (typeof dateString === 'string' && dateString.match(/^\d{4}-\d{2}-\d{2}$/)) {
                            const parts = dateString.split('-');
                            date = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
                        } else {
                            date = new Date(dateString);
                        }
                        
                        if (isNaN(date.getTime())) {
                            return '日期格式錯誤';
                        }
                        
                        const year = date.getFullYear();
                        if (year < 1900 || year > 2100) {
                            return '日期錯誤';
                        }
                        
                        return date.toLocaleDateString('zh-TW');
                    } catch (error) {
                        return '日期錯誤';
                    }
                },
                isExpired(dateString) {
                    if (!dateString || dateString === '0000-00-00') return false;
                    
                    let date;
                    if (typeof dateString === 'string' && dateString.match(/^\d{4}-\d{2}-\d{2}$/)) {
                        date = new Date(dateString + 'T00:00:00');
                    } else {
                        date = new Date(dateString);
                    }
                    
                    if (isNaN(date.getTime()) || date.getFullYear() === 1970) return false;
                    
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    return date < today;
                },
                isExpiringSoon(dateString) {
                    if (!dateString || dateString === '0000-00-00') return false;
                    
                    let date;
                    if (typeof dateString === 'string' && dateString.match(/^\d{4}-\d{2}-\d{2}$/)) {
                        date = new Date(dateString + 'T00:00:00');
                    } else {
                        date = new Date(dateString);
                    }
                    
                    if (isNaN(date.getTime()) || date.getFullYear() === 1970) return false;
                    
                    const today = new Date();
                    const sevenDaysLater = new Date(today.getTime() + 7 * 24 * 60 * 60 * 1000);
                    today.setHours(0, 0, 0, 0);
                    return date >= today && date <= sevenDaysLater;
                },
                getDateClass(dateString) {
                    if (this.isExpired(dateString)) return 'expired';
                    if (this.isExpiringSoon(dateString)) return 'expiring-soon';
                    return 'normal';
                },
                editFood(food, index) {
                    this.currentFood = { ...food };
                    this.editingIndex = index;
                    this.showEditModal = true;
                },
                async saveFood() {
                    try {
                        const formData = new FormData();
                        formData.append('action', this.showEditModal ? 'updateFood' : 'addFood');
                        formData.append('name', this.currentFood.name);
                        formData.append('todate', this.currentFood.todate);
                        formData.append('amount', this.currentFood.amount);
                        formData.append('photo', this.currentFood.photo);
                        formData.append('price', this.currentFood.price);
                        formData.append('shop', this.currentFood.shop);
                        
                        if (this.showEditModal) {
                            formData.append('id', this.currentFood.id);
                        }
                        
                        const response = await fetch('foods.php', {
                            method: 'POST',
                            body: formData
                        });
                        const data = await response.json();
                        
                        if (data.success) {
                            await this.loadFoods();
                            this.closeModals();
                            alert(this.showEditModal ? '食品更新成功！' : '食品新增成功！');
                        } else {
                            alert('操作失敗: ' + data.message);
                        }
                    } catch (error) {
                        console.error('保存食品失敗:', error);
                        alert('操作失敗，請稍後再試');
                    }
                },
                closeModals() {
                    this.showAddModal = false;
                    this.showEditModal = false;
                    this.currentFood = {
                        name: '',
                        todate: '',
                        amount: '',
                        photo: '',
                        price: '',
                        shop: ''
                    };
                    this.editingIndex = -1;
                }
            }
        }).mount('#app');
    </script>
</body>
</html>