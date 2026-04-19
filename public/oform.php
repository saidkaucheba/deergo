<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$host = 'localhost'; $dbname = 'deergo'; $user = 'postgres'; $password = '';
try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Ошибка БД');
}

// Получаем типы доставки из БД
$types = $pdo->query("SELECT * FROM deliverytypes ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

// ОФОРМЛЕНИЕ ЗАКАЗА
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment      = trim($_POST['comment'] ?? '');
    $addr_from    = trim($_POST['addr_from'] ?? '');
    $addr_to      = trim($_POST['addr_to'] ?? '');
    $weight       = (int)($_POST['weight'] ?? 10);
    $type_id      = (int)($_POST['type_id'] ?? 1);
    $price        = (int)($_POST['price'] ?? 0);
    $user_id      = $_SESSION['user_id'];

    // Сохраняем адреса
    $stmt = $pdo->prepare("INSERT INTO addresses (userid, street, house) VALUES (?, ?, ?) RETURNING id");

    // Адрес «Откуда»
    $parts_from = explode(',', $addr_from, 2);
    $street_from = trim($parts_from[0] ?? $addr_from);
    $house_from  = trim($parts_from[1] ?? '1');
    $stmt->execute([$user_id, $street_from, $house_from]);
    $from_id = $stmt->fetchColumn();

    // Адрес «Куда»
    $parts_to = explode(',', $addr_to, 2);
    $street_to = trim($parts_to[0] ?? $addr_to);
    $house_to  = trim($parts_to[1] ?? '1');
    $stmt->execute([$user_id, $street_to, $house_to]);
    $to_id = $stmt->fetchColumn();

    // Статус «В обработке» — id=1
    $status_id = 1;

    // Генерируем номер заказа
    $order_num = 'DG-' . date('Ymd') . '-' . rand(1000, 9999);

    // Создаём заказ
    $ins = $pdo->prepare("
        INSERT INTO orders 
        (ordernumber, userid, shipmentaddressid, deliveryaddressid, deliverytypeid, statusid, weight, price, comment)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $ins->execute([$order_num, $user_id, $from_id, $to_id, $type_id, $status_id, $weight, $price, $comment]);

    // Возвращаем результат через JSON (для всплывашки в JS)
    header('Content-Type: application/json');
    echo json_encode(['ok' => true, 'order_num' => $order_num]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление доставки — DeerGo</title>
    <link rel="stylesheet" href="css/oform.css">
</head>
<body>

    <header class="header">
        <div class="header-left">
            <div class="logo-img">
                <img src="images/log.png" alt="логотип">
            </div>
            <span class="brand">DeerGo</span>
        </div>
        <nav class="nav-menu">
            <div class="tab-switch">
                <a href="index.php" class="tab-option">Главная</a>
                <a href="cour.php" class="tab-option">Для курьеров</a>
            </div>
            <a href="profil.php" class="profile-btn">Профиль</a>
        </nav>
    </header>

    <main class="main">
        <h1 class="page-title">Оформление доставки</h1>

        <div class="oform-layout">

            <!-- ЛЕВАЯ КОЛОНКА -->
            <div class="left-col">

                <div class="info-block">
                    <div class="info-label">Откуда</div>
                    <div class="info-value" id="show-from">—</div>
                </div>

                <div class="info-block">
                    <div class="info-label">Куда</div>
                    <div class="info-value" id="show-to">—</div>
                </div>

                <div class="info-block">
                    <div class="info-label">Вид доставки</div>
                    <div class="info-value" id="show-type">—</div>
                </div>

                <div class="info-block">
                    <div class="info-label">Вес</div>
                    <div class="info-value" id="show-weight">—</div>
                </div>

                <div class="field-box">
                    <input type="text" id="comment" placeholder="Комментарий к доставке (необязательно)">
                </div>

            </div>

            <!-- ПРАВАЯ КОЛОНКА -->
            <div class="right-col">

                <div class="price-block">
                    <div class="price-label">Стоимость доставки</div>
                    <div class="price-value" id="show-price">—</div>
                </div>

                <div class="payment-block">
                    <div class="info-label">Способ оплаты</div>
                    <div class="pay-options">
                        <label class="pay-option">
                            <input type="radio" name="payment" value="card" checked>
                            Банковская карта
                        </label>
                        <label class="pay-option">
                            <input type="radio" name="payment" value="cash">
                            Наличные
                        </label>
                        <label class="pay-option">
                            <input type="radio" name="payment" value="online">
                            Онлайн-перевод
                        </label>
                    </div>
                </div>

                <div class="btn-row">
                    <button class="btn" onclick="history.back()">Назад</button>
                    <button class="btn btn-pay" onclick="placeOrder()">Оплатить</button>
                </div>

            </div>
        </div>

        <!-- ДАННЫЕ ТИПОВ ДОСТАВКИ ДЛЯ JS -->
        <script>
        // Цены из БД
        const deliveryTypes = <?= json_encode($types) ?>;

        // Читаем сохранённый выбор из главной страницы
        const weight   = parseInt(sessionStorage.getItem('weight') || '10');
        const typeId   = parseInt(sessionStorage.getItem('typeId') || '2');
        const typeLabel = sessionStorage.getItem('typeLabel') || 'Легковая';
        const addrFrom = sessionStorage.getItem('addrFrom') || '';
        const addrTo   = sessionStorage.getItem('addrTo') || '';

        document.getElementById('show-from').textContent   = addrFrom || 'не указан';
        document.getElementById('show-to').textContent     = addrTo   || 'не указан';
        document.getElementById('show-type').textContent   = typeLabel;
        document.getElementById('show-weight').textContent = weight + ' кг';

        // Рассчитываем цену: базовая цена типа + вес * 10
        const typeData = deliveryTypes.find(t => t.id == typeId);
        const basePrice = typeData ? typeData.price : 300;
        const totalPrice = basePrice + weight * 10;
        document.getElementById('show-price').textContent = totalPrice + ' ₽';

        function placeOrder() {
            const comment = document.getElementById('comment').value;

            fetch('oform.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    comment: comment,
                    addr_from: addrFrom,
                    addr_to: addrTo,
                    weight: weight,
                    type_id: typeId,
                    price: totalPrice
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.ok) {
                    alert('Доставка оформлена!\nНомер заказа: ' + data.order_num + '\n\nСкоро она будет передана в работу.');
                    window.location.href = 'tekuch.php';
                }
            })
            .catch(() => {
                alert('Произошла ошибка. Попробуйте снова.');
            });
        }
        </script>

    </main>

</body>
</html>