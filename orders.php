<?php session_start();

if (isset($_GET['do']) && $_GET['do'] === 'logout') {
    require_once 'api/auth/LogoutUser.php';
    require_once 'api/DB.php';

    LogoutUser('login.php', $DB, $_SESSION['token']);

    exit;
}

require_once 'api/auth/AuthCheck.php';

AuthCheck('', 'login.php');

require_once 'api/helpers/InputDefaultValue.php';
require_once 'api/helpers/getUserType.php';

// Получаем тип пользователя
$userType = getUserType($_SESSION['token']);

// Обработка состояния статуса заказов
if (isset($_GET["search_status"])) {
    $_SESSION["search_status"] = $_GET["search_status"];
} else if (!isset($_SESSION["search_status"])) {
    $_SESSION["search_status"] = "all"; // По умолчанию показываем все заказы
}

// Обработка кнопки сброса
if (isset($_GET['reset'])) {
    $_SESSION["search_status"] = "all"; // По умолчанию показываем все заказы
    header("Location: orders.php");
    exit;
}

// Добавляем параметры в URL пагинации
$searchParams = '';
if (isset($_GET['search_name'])) {
    $searchParams .= '&search_name=' . urlencode($_GET['search_name']);
}
if (isset($_GET['search'])) {
    $searchParams .= '&search=' . urlencode($_GET['search']);
}
if (isset($_GET['sort'])) {
    $searchParams .= '&sort=' . urlencode($_GET['sort']);
}
if (isset($_SESSION['search_status'])) {
    $searchParams .= '&search_status=' . urlencode($_SESSION['search_status']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/settings.css">
    <link rel="stylesheet" href="styles/pages/clients.css">
    <link rel="stylesheet" href="styles/modules/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="styles/modules/micromodal.css">
    <title>CRM | Заказы</title>
    <style>
        .header__buttons {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header__support {
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        .header__support:hover {
            background-color: #45a049;
        }

        .support__btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 15px 25px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .support__btn:hover {
            background-color: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
        }

        .support__btn i {
            font-size: 20px;
        }

        .support__btn-container {
            position: fixed;
            bottom: 90px;
            right: 20px;
            width: 300px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: none;
            padding: 20px;
        }

        .support__btn-container.active {
            display: block;
        }

        .support__header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .support__header h3 {
            margin: 0;
            color: #333;
        }

        .support__close {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #666;
        }

        .support__btn-container form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .support__btn-container label {
            color: #666;
            margin-bottom: 5px;
        }

        .support__btn-container select,
        .support__btn-container textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .support__btn-container textarea {
            min-height: 100px;
            resize: vertical;
        }

        .file-input-container {
            position: relative;
            margin-bottom: 10px;
        }

        .file-name {
            margin-top: 5px;
            font-size: 12px;
            color: #666;
        }

        .support__btn-container button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .support__btn-container button[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <p class="header__admin">
                <?php 
                    require 'api/DB.php';
                    require_once 'api/clients/AdminName.php';

                    echo AdminName($_SESSION['token'], $DB);
                ?>
            </p>
            <ul class="header__links">
                <li><a href="clients.php">Клиенты</a></li>
                <li><a href="product.php">Товары</a></li>
                <li><a href="orders.php">Заказы</a></li>
                <?php
                if ($userType === 'tech'){
                    echo "<li><a href='tech.php'>Обращения пользователя</a></li>";
                }
                ?>
            </ul>
            <div class="header__buttons">
                <?php if ($userType === 'user'): ?>
                    <a href="user_tickets.php" class="header__support">Мои обращения</a>
                <?php endif; ?>
                <a href="?do=logout" class="header__logout">Выйти</a>
            </div>
        </div>
    </header>
    <main class="main">
        <section class="main__filters">
            <div class="container">
                <form action="" class="main__form">
                    <label class="main__label" for="search">Поиск по заказу</label>
                    <input <?php InputDefaultValue('search', ''); ?> class="main__input" type="text" id="search" name="search" placeholder="Поиск...">
                    <select class="main__select" name="search_name" id="search_name">
                        <option value="client.name" <?php echo ($_GET['search_name'] ?? '') === 'client.name' ? 'selected' : ''; ?>>По клиенту</option>
                        <option value="orders.id" <?php echo ($_GET['search_name'] ?? '') === 'orders.id' ? 'selected' : ''; ?>>По ID</option>
                        <option value="orders.order_date" <?php echo ($_GET['search_name'] ?? '') === 'orders.order_date' ? 'selected' : ''; ?>>По дате</option>
                        <option value="orders.total" <?php echo ($_GET['search_name'] ?? '') === 'orders.total' ? 'selected' : ''; ?>>По сумме</option>
                        <option value="orders.status" <?php echo ($_GET['search_name'] ?? '') === 'orders.status' ? 'selected' : ''; ?>>По статусу</option>
                    </select>
                    <select class="main__select" name="sort" id="sort">
                        <option value="0" <?php echo ($_GET['sort'] ?? '') === '0' ? 'selected' : ''; ?>>По умолчанию</option>
                        <option value="1" <?php echo ($_GET['sort'] ?? '') === '1' ? 'selected' : ''; ?>>По возрастанию</option>
                        <option value="2" <?php echo ($_GET['sort'] ?? '') === '2' ? 'selected' : ''; ?>>По убыванию</option>
                    </select>
                    <div class="filter-controls">
                        <label>
                            <select class="main__select" name="search_status" id="search_status">
                                <option value="all" <?php echo ($_SESSION["search_status"] === "all" ? "selected" : ""); ?>>Все заказы</option>
                                <option value="1" <?php echo ($_SESSION["search_status"] === "1" ? "selected" : ""); ?>>Активные заказы</option>
                                <option value="0" <?php echo ($_SESSION["search_status"] === "0" ? "selected" : ""); ?>>Неактивные заказы</option>
                            </select>
                        </label>
                    </div>
                    <button type="submit">Поиск</button>
                    <a href="?" class="main__reset" onclick="' . session_unset() . '">Сбросить</a>
                </form>
            </div>
        </section>
        <section class="main__clients">
            <div class="container">
                <h2 class="main__clients__title">Список заказов</h2>
                <div class="main__clients__controls">
                    <button class="main__clients__add" onclick="MicroModal.show('add-modal')"><i class="fa fa-plus-circle"></i></button>
                </div>
                <table>
                    <thead>
                        <th>ИД</th>
                        <th>Менеджер</th>
                        <th>ФИО</th>
                        <th>Дата заказа</th>
                        <th>Общая сумма</th>
                        <th>Состав заказа</th>
                        <th>Статус</th>
                        <th>Чек</th>
                        <th>Редактировать</th>
                        <th>Удалить</th>
                    </thead>
                    <tbody>
                        <?php
                            require 'api/DB.php';
                            require_once 'api/orders/OutputOrders.php';
                            require_once 'api/orders/OrdersSearch.php';

                            // Подсчет общего количества записей с учетом фильтров
                            $search = isset($_GET['search']) ? strtolower($_GET['search']) : '';
                            $whereClause = "";
                            if (!empty($search)) {
                                $whereClause = "WHERE (LOWER(clients.name) LIKE '%$search%' OR LOWER(products.name) LIKE '%$search%')";
                            }

                            // Добавляем условие статуса для подсчета
                            if ($_SESSION["search_status"] == '1') {
                                $whereClause = $whereClause ? $whereClause . " AND orders.status = '1'" : "WHERE orders.status = 1";
                            } elseif ($_SESSION["search_status"] == '0') {
                                $whereClause = $whereClause ? $whereClause . " AND orders.status = '0'" : "WHERE orders.status = 0";
                            }

                            $countQuery = "SELECT COUNT(DISTINCT orders.id) as count 
                                           FROM orders 
                                           JOIN clients ON orders.client_id = clients.id 
                                           JOIN order_items ON orders.id = order_items.order_id 
                                           JOIN products ON order_items.product_id = products.id 
                                           $whereClause";

                            $countOrders = $DB->query($countQuery)->fetchAll()[0]['count'];

                            $per_page = 5; // Количество записей на странице
                            $maxPage = ceil($countOrders / $per_page);
                            $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

                            // Проверка и корректировка текущей страницы
                            if ($currentPage < 1) {
                                $currentPage = 1;
                            } elseif ($currentPage > $maxPage) {
                                $currentPage = $maxPage;
                            }

                            // Build pagination URL with preserved search parameters
                            $searchParams = '';
                            if (isset($_GET['search_name'])) {
                                $searchParams .= '&search_name=' . urlencode($_GET['search_name']);
                            }
                            if (isset($_GET['search'])) {
                                $searchParams .= '&search=' . urlencode($_GET['search']);
                            }
                            if (isset($_GET['sort'])) {
                                $searchParams .= '&sort=' . urlencode($_GET['sort']);
                            }

                            // Wrap pagination in container
                            echo "<div class='pagination-container'>";
                            
                            // Кнопка "Предыдущая"
                            $prevDisabled = ($currentPage <= 1) ? " disabled" : "";
                            $prevPage = $currentPage - 1;
                            echo "<a href='?page=$prevPage$searchParams'$prevDisabled><i class='fa fa-arrow-left' aria-hidden='true'></i></a>";

                            // Номера страниц
                            echo "<div class='pagination'>";
                            for ($i = 1; $i <= $maxPage; $i++) {
                                $activeClass = ($i === $currentPage) ? " class='active'" : "";
                                echo "<a href='?page=$i$searchParams'$activeClass>$i</a>";
                            }
                            echo "</div>";

                            // Кнопка "Следующая"
                            $nextDisabled = ($currentPage >= $maxPage) ? " disabled" : "";
                            $nextPage = $currentPage + 1;
                            echo "<a href='?page=$nextPage$searchParams'$nextDisabled><i class='fa fa-arrow-right' aria-hidden='true'></i></a>";

                            echo "</div>";

                            $orders = OrdersSearch($_GET, $DB);
                            OutputOrders($orders);
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    <div class="modal micromodal-slide" id="add-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
          <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
            <header class="modal__header">
              <h2 class="modal__title" id="modal-1-title">
                Создание заказа
              </h2>
              <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
            </header>
            <main class="modal__content" id="modal-1-content">
                <form action="api/orders/AddOrders.php" method="POST" class="modal__form">
                    <div class="modal__form-group">
                        <label for="client">Клиент</label>
                        <select class="main__select" name="client" id="client">
                        <option value="new">Новый пользователь</option>
                            <?php
                                $users = $DB->query("SELECT id, name FROM clients")->fetchAll();
                                foreach ($users as $key => $user) {
                                    $id = $user['id'];
                                    $name = $user['name'];
                                    echo "<option value='$id'>$name</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="modal__form-group group-email" id="email-field">
                        <label for="email">Почта</label>
                        <input type="email" id="email" name="email" placeholder="Введите почту....">
                    </div>
                    <div class="modal__form-group">
                        <label for="products">Товар</label>
                        <select class="main__select" name="products[]" id="products" multiple>
                        <?php
                                $products = $DB->query("SELECT id, name, price, stock FROM products WHERE stock > 0")->fetchAll();
                                foreach ($products as $key => $product) {
                                    $id = $product['id'];
                                    $name = $product['name'];
                                    $price = $product['price'];
                                    $stock = $product['stock'];
                                    echo "<option value='$id'>$name - {$price}₽ - ({$stock} шт.)</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="modal__form-actions">
                        <button type="submit" class="modal__btn modal__btn-primary">Создать</button>
                        <button type="button" class="modal__btn modal__btn-secondary" data-micromodal-close>Отменить</button>
                    </div>
                </form>
            </main>
          </div>
        </div>
      </div>
      <div class="modal micromodal-slide" id="delete-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true">
                <header class="modal__header">
                    <h2 class="modal__title">Удалить заказ?</h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content">
                    <p>Вы уверены, что хотите удалить заказ?</p>
                    <button class="modal__btn modal__btn-danger">Удалить</button>
                    <button class="modal__btn" data-micromodal-close>Отменить</button>
                </main>
            </div>
        </div>
    </div>
    <div class="modal micromodal-slide" id="edit-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
          <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
            <header class="modal__header">
              <h2 class="modal__title" id="modal-1-title">
                Редактировать заказ
              </h2>
              <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
            </header>
            <main class="modal__content" id="modal-1-content">
                <form action="api/orders/EditOrders.php" method="POST" class="modal__form">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="modal__form-group">
                        <label for="edit_status">Статус заказа</label>
                        <select class="main__select" name="status" id="edit_status">
                            <option value="1">Активный</option>
                            <option value="0">Неактивный</option>
                        </select>
                    </div>
                    <div class="modal__form-actions">
                        <button type="submit" class="modal__btn modal__btn-primary">Сохранить</button>
                        <button type="button" class="modal__btn modal__btn-secondary" data-micromodal-close>Отменить</button>
                    </div>
                </form>
            </main>
          </div>
        </div>
      </div>
      <div class="modal micromodal-slide" id="history-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
                <header class="modal__header">
                    <h2 class="modal__title" id="modal-1-title">
                        История покупок
                    </h2>
                    <small>Фамилия Имя Отчество</small>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content" id="modal-1-content">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>ID заказа</th>
                                <th>Товар</th>
                                <th>Количество</th>
                                <th>Цена</th>
                                <th>Дата</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Товар 1</td>
                                <td>2</td>
                                <td>1000₽</td>
                                <td>12.01.2024</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Товар 2</td>
                                <td>1</td>
                                <td>500₽</td>
                                <td>15.01.2024</td>
                            </tr>
                        </tbody>
                    </table>
                </main>
            </div>
        </div>
    </div>
    <div class="modal micromodal-slide" id="details-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true">
                <header class="modal__header">
                    <h2 class="modal__title">Информация о заказе #<span id="order-id"></span></h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content">
                    <div class="order-details">
                        <p><strong>Клиент:</strong> <span id="client-name"></span></p>
                        <p><strong>Дата заказа:</strong> <span id="order-date"></span></p>
                        <p><strong>Общая сумма:</strong> <span id="order-total"></span>₽</p>
                        
                        <h3>Состав заказа:</h3>
                        <table class="details-table">
                            <thead>
                                <tr>
                                    <th>Товар</th>
                                    <th>Количество</th>
                                    <th>Цена</th>
                                    <th>Сумма</th>
                                </tr>
                            </thead>
                            <tbody id="order-items">
                            </tbody>
                        </table>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <div class="modal micromodal-slide
        <?php
        if (isset($_SESSION['orders_error']) && 
        !empty($_SESSION['orders_error'])) {
            echo 'open';
        }
        ?>
    " id="error-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
                <header class="modal__header">
                    <h2 class="modal__title" id="modal-1-title">
                        Ошибка!
                    </h2>   
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content" id="modal-1-content">
                <?php
                if (isset($_SESSION['orders_error'])
                && !empty($_SESSION['orders_error'])) {
                    echo $_SESSION['orders_error'];

                    $_SESSION['orders_error'] = '';
                }
                ?>
                </main>
            </div>
        </div>
    </div>
    <button class="support__btn" id="support-btn"><i class="fa fa-headphones"></i> Поддержка</button>
    <div class="support__btn-container" id="support-form">
        <div class="support__header">
            <h3>Техническая поддержка</h3>
            <button type="button" class="support__close" id="support-close"><i class="fa fa-times"></i></button>
        </div>
        <form action="api/tickets/CreateTicket.php" method="POST" enctype="multipart/form-data">
            <label for="support-type">Тип обращения</label>
            <select id="support-type" name="support-type">
                <option value="technical">Техническая неполадка</option>
                <option value="CRM">Проблемы с CRM</option>
            </select>
            <label for="support-message">Текст обращения</label>
            <textarea id="support-message" name="support-message"></textarea>
            <label for="files">Прикрепить файл</label>
            <div class="file-input-container">
                <input type="file" name="files" id="files">
                <div class="file-name" id="file-name"></div>
            </div>
            <button type="submit">Отправить</button>
        </form>
    </div>
    <script defer src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>
    <script defer src="scripts/initClientsModal.js"></script>
    <script defer src="scripts/orders.js"></script>
    <script>
    // Скрипт для отображения/скрытия формы поддержки
    document.addEventListener('DOMContentLoaded', function() {
        const supportBtn = document.getElementById('support-btn');
        const supportForm = document.getElementById('support-form');
        const supportClose = document.getElementById('support-close');
        
        supportBtn.addEventListener('click', function() {
            supportForm.classList.toggle('active');
        });
        
        supportClose.addEventListener('click', function() {
            supportForm.classList.remove('active');
        });
        
        // Закрытие формы при клике вне её
        document.addEventListener('click', function(event) {
            if (!supportForm.contains(event.target) && event.target !== supportBtn) {
                supportForm.classList.remove('active');
            }
        });
        
        // Отображение имени выбранного файла
        const fileInput = document.getElementById('files');
        const fileName = document.getElementById('file-name');
        
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                fileName.textContent = this.files[0].name;
            } else {
                fileName.textContent = '';
            }
        });
    });
    </script>
</body>
</html>