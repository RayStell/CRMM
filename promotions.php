<?php session_start();

if (isset($_GET['do']) && $_GET['do'] === 'logout') {
    require_once 'api/auth/LogoutUser.php';
    require_once 'api/DB.php';

    LogoutUser('login.php', $DB, $_SESSION['token']);

    exit;
}

require_once 'api/auth/AuthCheck.php';
require_once 'api/helpers/InputDefaultValue.php';
require_once 'api/DB.php';

AuthCheck('', 'login.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/settings.css">
    <link rel="stylesheet" href="styles/pages/clients.css">
    <link rel="stylesheet" href="styles/pages/tech.css">
    <link rel="stylesheet" href="styles/modules/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="styles/modules/micromodal.css">
    <link rel="stylesheet" href="styles/modules/support.css">
    <title>CRM | Акции</title>
</head>
<body>
    <header class="header">
        <div class="container">
            <p class="header__admin">
                <?php 
                    require 'api/DB.php';
                    require_once 'api/clients/AdminName.php';
                    require_once 'api/helpers/getUserType.php';

                    $adminName = AdminName($_SESSION['token'], $DB);
                    $userType = getUserType($DB);
                    echo "$adminName <span style='color: green'>($userType)</span>";
                ?>
            </p>
            <ul class="header__links">
                <li><a href="clients.php">Клиенты</a></li>
                <li><a href="product.php">Товары</a></li>
                <li><a href="orders.php">Заказы</a></li>
                <li><a href="promotions.php" class="active">Акции</a></li>
                <?php
                    if ($userType === 'tech') {
                        echo '<li><a href="tech.php">Обращение пользователя</a></li>';
                    }
                ?>
            </ul>
            <a href="?do=logout" class="header__logout">Выйти</a>
        </div>
    </header>
    <main class="main">
        <div class="container">
            <div class="tickets-header">
                <h1 class="tickets-title">Акции</h1>
                <div class="tickets-filters">
                    <button class="ticket-filter <?php echo (!isset($_GET['status']) || $_GET['status'] === 'all') ? 'active' : ''; ?>" onclick="window.location.href='?status=all'">
                        Все
                    </button>
                    <button class="ticket-filter <?php echo (isset($_GET['status']) && $_GET['status'] === 'active') ? 'active' : ''; ?>" onclick="window.location.href='?status=active'">
                        Активные
                    </button>
                    <button class="ticket-filter <?php echo (isset($_GET['status']) && $_GET['status'] === 'ended') ? 'active' : ''; ?>" onclick="window.location.href='?status=ended'">
                        Завершенные
                    </button>
                </div>
                <button class="main__clients__add" onclick="MicroModal.show('add-modal')"><i class="fa fa-plus-circle"></i></button>
            </div>

            <div class="tickets-grid">
                <?php
                    // Получаем текущую страницу
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $perPage = 12; // Количество акций на странице
                    $offset = ($page - 1) * $perPage;

                    // Формируем условие для фильтрации по статусу
                    $statusFilter = '';
                    if (isset($_GET['status'])) {
                        if ($_GET['status'] === 'active') {
                            $statusFilter = "WHERE uses < max_uses AND (cancel_at > NOW() OR cancel_at IS NULL)";
                        } elseif ($_GET['status'] === 'ended') {
                            $statusFilter = "WHERE uses >= max_uses OR (cancel_at <= NOW() AND cancel_at IS NOT NULL)";
                        }
                    }

                    // Получаем общее количество акций
                    $countQuery = "SELECT COUNT(*) as count FROM promotions $statusFilter";
                    $totalPromotions = $DB->query($countQuery)->fetch()['count'];
                    $totalPages = ceil($totalPromotions / $perPage);

                    // Получаем акции для текущей страницы
                    $query = "SELECT * FROM promotions $statusFilter ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";
                    $promotions = $DB->query($query)->fetchAll();

                    foreach ($promotions as $promo) {
                        $isActive = $promo['uses'] < $promo['max_uses'] && 
                                  ($promo['cancel_at'] > date('Y-m-d H:i:s') || $promo['cancel_at'] === null);
                        
                        $statusClass = $isActive ? 'work' : 'complete';
                        $statusText = $isActive ? 'Активна' : 'Завершена';
                        $statusIcon = $isActive ? 'check-circle' : 'times-circle';

                        echo "
                        <div class='ticket-card'>
                            <div class='ticket-header'>
                                <span class='ticket-id'>#" . $promo['id'] . "</span>
                                <span class='ticket-type tech'>" . htmlspecialchars($promo['code_promo']) . "</span>
                            </div>
                            <div class='ticket-message'>
                                <h3>" . htmlspecialchars($promo['title']) . "</h3>
                                <p>" . htmlspecialchars($promo['body']) . "</p>
                            </div>
                            <div class='ticket-info'>
                                <span><i class='fa fa-percent'></i>Скидка: " . $promo['discount'] . "%</span>
                                <span><i class='fa fa-refresh'></i>Использований: " . $promo['uses'] . " из " . $promo['max_uses'] . "</span>";
                                
                        if ($promo['path_to_image']) {
                            echo "<div class='ticket-files'>
                                    <a href='javascript:void(0)' onclick='showFile(\"" . htmlspecialchars($promo['path_to_image']) . "\", \"image\")' class='ticket-file'>
                                        <i class='fa fa-file-image-o'></i>
                                        Изображение акции
                                    </a>
                                </div>";
                        }

                        echo "<div class='ticket-status-container'>
                                <span class='ticket-status " . $statusClass . "'>
                                    <i class='fa fa-" . $statusIcon . "'></i>
                                    " . $statusText . "
                                </span>
                            </div>
                            </div>
                            <div class='ticket-date'>
                                <i class='fa fa-calendar'></i> 
                                Создано: " . date('d.m.Y H:i', strtotime($promo['created_at'])) . "
                                " . ($promo['cancel_at'] ? "<br><i class='fa fa-calendar-times-o'></i> До: " . date('d.m.Y H:i', strtotime($promo['cancel_at'])) : "") . "
                            </div>
                            <div style='display: flex; gap: 10px; margin-top: 10px;'>
                                <button class='reply-btn' onclick='editPromotion(" . json_encode($promo) . ")'><i class='fa fa-edit'></i> Редактировать</button>
                                <button class='reply-btn' style='background-color: #f44336;' onclick='deletePromotion(" . $promo['id'] . ")'><i class='fa fa-trash'></i> Удалить</button>
                            </div>
                        </div>";
                    }
                ?>
            </div>

            <?php if ($totalPages > 1): ?>
            <div class="pagination-container">
                <?php
                    // Сохраняем параметры фильтрации для пагинации
                    $filterParams = isset($_GET['status']) ? '&status=' . $_GET['status'] : '';

                    // Кнопка "Предыдущая"
                    $prevDisabled = ($page <= 1) ? " disabled" : "";
                    echo "<a href='?page=" . ($page - 1) . $filterParams . "'$prevDisabled>
                            <i class='fa fa-arrow-left'></i>
                          </a>";

                    // Номера страниц
                    echo "<div class='pagination'>";
                    for ($i = 1; $i <= $totalPages; $i++) {
                        $activeClass = ($i === $page) ? " class='active'" : "";
                        echo "<a href='?page=$i$filterParams'$activeClass>$i</a>";
                    }
                    echo "</div>";

                    // Кнопка "Следующая"
                    $nextDisabled = ($page >= $totalPages) ? " disabled" : "";
                    echo "<a href='?page=" . ($page + 1) . $filterParams . "'$nextDisabled>
                            <i class='fa fa-arrow-right'></i>
                          </a>";
                ?>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Модальное окно для создания акции -->
    <div class="modal micromodal-slide" id="add-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true">
                <header class="modal__header">
                    <h2 class="modal__title">Создание акции</h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content">
                    <form action="api/promotions/AddPromotion.php" method="POST" enctype="multipart/form-data" class="modal__form">
                        <div class="modal__form-group">
                            <label for="code_promo">Промокод</label>
                            <input type="text" id="code_promo" name="code_promo" required placeholder="Введите промокод...">
                        </div>
                        <div class="modal__form-group">
                            <label for="title">Заголовок</label>
                            <input type="text" id="title" name="title" required placeholder="Введите заголовок акции...">
                        </div>
                        <div class="modal__form-group">
                            <label for="body">Описание</label>
                            <textarea id="body" name="body" required placeholder="Введите описание акции..."></textarea>
                        </div>
                        <div class="modal__form-group">
                            <label for="discount">Скидка (%)</label>
                            <input type="number" id="discount" name="discount" required min="0" max="100" placeholder="Введите размер скидки...">
                        </div>
                        <div class="modal__form-group">
                            <label for="max_uses">Максимальное количество использований</label>
                            <input type="number" id="max_uses" name="max_uses" required min="1" placeholder="Введите максимальное количество использований...">
                        </div>
                        <div class="modal__form-group">
                            <label for="cancel_at">Дата окончания</label>
                            <input type="datetime-local" id="cancel_at" name="cancel_at">
                        </div>
                        <div class="modal__form-group">
                            <label for="image">Изображение акции</label>
                            <input type="file" id="image" name="image" accept="image/*">
                        </div>
                        <div class="modal__form-actions">
                            <button type="submit" class="modal__btn modal__btn-primary">Создать</button>
                            <button type="button" class="modal__btn" data-micromodal-close>Отменить</button>
                        </div>
                    </form>
                </main>
            </div>
        </div>
    </div>

    <!-- Модальное окно для редактирования акции -->
    <div class="modal micromodal-slide" id="edit-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true">
                <header class="modal__header">
                    <h2 class="modal__title">Редактирование акции</h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content">
                    <form action="api/promotions/EditPromotion.php" method="POST" enctype="multipart/form-data" class="modal__form">
                        <input type="hidden" id="edit_id" name="id">
                        <div class="modal__form-group">
                            <label for="edit_code_promo">Промокод</label>
                            <input type="text" id="edit_code_promo" name="code_promo" required placeholder="Введите промокод...">
                        </div>
                        <div class="modal__form-group">
                            <label for="edit_title">Заголовок</label>
                            <input type="text" id="edit_title" name="title" required placeholder="Введите заголовок акции...">
                        </div>
                        <div class="modal__form-group">
                            <label for="edit_body">Описание</label>
                            <textarea id="edit_body" name="body" required placeholder="Введите описание акции..."></textarea>
                        </div>
                        <div class="modal__form-group">
                            <label for="edit_discount">Скидка (%)</label>
                            <input type="number" id="edit_discount" name="discount" required min="0" max="100" placeholder="Введите размер скидки...">
                        </div>
                        <div class="modal__form-group">
                            <label for="edit_max_uses">Максимальное количество использований</label>
                            <input type="number" id="edit_max_uses" name="max_uses" required min="1" placeholder="Введите максимальное количество использований...">
                        </div>
                        <div class="modal__form-group">
                            <label for="edit_cancel_at">Дата окончания</label>
                            <input type="datetime-local" id="edit_cancel_at" name="cancel_at">
                        </div>
                        <div class="modal__form-group">
                            <label for="edit_image">Изображение акции</label>
                            <input type="file" id="edit_image" name="image" accept="image/*">
                        </div>
                        <div class="modal__form-actions">
                            <button type="submit" class="modal__btn modal__btn-primary">Сохранить</button>
                            <button type="button" class="modal__btn" data-micromodal-close>Отменить</button>
                        </div>
                    </form>
                </main>
            </div>
        </div>
    </div>

    <!-- Модальное окно для просмотра файлов -->
    <div class="modal micromodal-slide" id="file-preview-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container modal__container--large" role="dialog" aria-modal="true">
                <header class="modal__header">
                    <h2 class="modal__title">Просмотр изображения</h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content" id="file-preview-content">
                    <!-- Контент будет добавлен динамически -->
                </main>
            </div>
        </div>
    </div>

    <script defer src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            MicroModal.init({
                openTrigger: 'data-micromodal-trigger',
                closeTrigger: 'data-micromodal-close',
                disableFocus: true,
                disableScroll: true,
                awaitOpenAnimation: true,
                awaitCloseAnimation: true
            });
        });

        function showFile(filePath, type) {
            const container = document.getElementById('file-preview-content');
            container.innerHTML = '';

            if (type === 'image') {
                const img = document.createElement('img');
                img.src = filePath;
                img.style.maxWidth = '100%';
                img.style.height = 'auto';
                container.appendChild(img);
            }

            MicroModal.show('file-preview-modal');
        }

        function editPromotion(promo) {
            document.getElementById('edit_id').value = promo.id;
            document.getElementById('edit_code_promo').value = promo.code_promo;
            document.getElementById('edit_title').value = promo.title;
            document.getElementById('edit_body').value = promo.body;
            document.getElementById('edit_discount').value = promo.discount;
            document.getElementById('edit_max_uses').value = promo.max_uses;
            if (promo.cancel_at) {
                document.getElementById('edit_cancel_at').value = promo.cancel_at.slice(0, 16);
            }
            MicroModal.show('edit-modal');
        }

        function deletePromotion(id) {
            if (confirm('Вы уверены, что хотите удалить эту акцию?')) {
                window.location.href = `api/promotions/DeletePromotion.php?id=${id}`;
            }
        }
    </script>
</body>
</html> 