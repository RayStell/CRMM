<?php session_start();

// ТЕх.поддержка
// Пользователь : создание тикета, общение с тех.поддержкой, закрытие тикета.
// Тех.поддержка : принятие тикета, общение с пользователем, закрытие тикета.

if (isset($_GET['do']) && $_GET['do'] === 'logout') {
    require_once 'api/auth/LogoutUser.php';
    require_once 'api/DB.php';

    LogoutUser('login.php', $DB, $_SESSION['token']);

    exit;
}

require_once 'api/auth/AuthCheck.php';
require_once 'api/helpers/InputDefaultValue.php';
require_once 'api/clients/ClientsSearch.php';

AuthCheck('', 'login.php');

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>CRM | Клиенты</title>
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
            </ul>
            <a href="?do=logout" class="header__logout">Выйти</a>
        </div>
    </header>
    <main class="main">
        <section class="main__filters">
            <div class="container">
                <form action="" method="GET" class="main__form">
                    <select class="main__select" name="search_name" id="search_name">
                        <option value="name" <?php echo ($_GET['search_name'] ?? '') === 'name' ? 'selected' : ''; ?>>Поиск по имени</option>
                        <option value="email" <?php echo ($_GET['search_name'] ?? '') === 'email' ? 'selected' : ''; ?>>Поиск по почте</option>
                    </select>
                    <input <?php InputDefaultValue('search', ''); ?> class="main__input" type="text" id="search" name="search" placeholder="Александр">
                    <select class="main__select" name="sort" id="sort">
                        <option value="0" <?php echo ($_GET['sort'] ?? '') === '0' ? 'selected' : ''; ?>>По умолчанию</option>
                        <option value="1" <?php echo ($_GET['sort'] ?? '') === '1' ? 'selected' : ''; ?>>По возрастанию</option>
                        <option value="2" <?php echo ($_GET['sort'] ?? '') === '2' ? 'selected' : ''; ?>>По убыванию</option>
                    </select>
                    <button type="submit">Поиск</button>
                    <a href="?" class="main__reset">Сбросить</a>
                </form>
            </div>
        </section>
        <section class="main__clients">
            <div class="container">
                <h2 class="main__clients__title">Список клиентов</h2>
                <button class="main__clients__add" onclick="MicroModal.show('add-modal')"><i class="fa fa-plus-circle"></i></button>
                <?php
                    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $maxClients = 5;

                    $countClients = $DB->query("
                    SELECT COUNT(*) as count FROM clients")
                    ->fetchAll()[0]['count'];

                    $maxPage = ceil($countClients / $maxClients);
                    $minPage = 1;

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

                    // Normalize currentPage
                    if ($currentPage < $minPage || !is_numeric($currentPage)) {
                        $currentPage = $minPage;
                        header("Location: ?page=$currentPage" . $searchParams);
                        exit;
                    }
                    if ($currentPage > $maxPage) {
                        $currentPage = $maxPage;
                        header("Location: ?page=$currentPage" . $searchParams);
                        exit;
                    }
                    
                    // Wrap pagination in container
                    echo "<div class='pagination-container'>";
                    
                    // Always show prev button, but disable if on first page
                    $prevDisabled = ($currentPage <= $minPage) ? " disabled" : "";
                    $Prev = $currentPage - 1;
                    echo "<a href='?page=$Prev" . $searchParams . "'$prevDisabled><i class='fa fa-arrow-left' aria-hidden='true'></i></a>";

                    // Show numbered pagination buttons
                    echo "<div class='pagination'>";
                    for ($i = 1; $i <= $maxPage; $i++) {
                        $activeClass = ($i === $currentPage) ? " class='active'" : "";
                        echo "<a href='?page=$i" . $searchParams . "'$activeClass>$i</a>";
                    }
                    echo "</div>";

                    // Always show next button, but disable if on last page
                    $nextDisabled = ($currentPage >= $maxPage) ? " disabled" : "";
                    $Next = $currentPage + 1;
                    echo "<a href='?page=$Next" . $searchParams . "'$nextDisabled><i class='fa fa-arrow-right' aria-hidden='true'></i></a>";

                    echo "</div>"; // Close pagination-container
                ?>
                <table>
                    <thead>
                        <th>ИД</th>
                        <th>ФИО</th>
                        <th>Почта</th>
                        <th>Телефон</th>
                        <th>День рождения</th>
                        <th>Дата создания</th>
                        <th>История заказов</th>
                        <th>Редактировать</th>
                        <th>Удалить</th>
                    </thead>
                    <tbody>
                        <?php
                            require 'api/DB.php';
                            require_once('api/clients/OutputClients.php');
                            require_once('api/clients/ClientsSearch.php');

                            $clients = ClientsSearch($_GET, $DB);
                        
                            OutputClients($clients);

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
                Добавить клиента
              </h2>
              <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
            </header>
            <main class="modal__content" id="modal-1-content">
                <form action="api/clients/AddClients.php" method="POST" class="modal__form">
                    <div class="modal__form-group">
                        <label for="fullname">ФИО</label>
                        <input type="text" id="fullname" name="fullname">
                    </div>
                    <div class="modal__form-group">
                        <label for="email">Почта</label>
                        <input type="email" id="email" name="email">
                    </div>
                    <div class="modal__form-group">
                        <label for="phone">Телефон</label>
                        <input type="tel" id="phone" name="phone">
                    </div>
                    <div class="modal__form-group">
                        <label for="birthday">День рождения</label>
                        <input type="date" id="birthday" name="birthday">
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
          <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
            <header class="modal__header">
              <h2 class="modal__title" id="modal-1-title">
                Вы уверены, что хотите удалить клиента?
              </h2>
              <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
            </header>
            <main class="modal__content" id="modal-1-content">
                <button class="modal__btn danger">Удалить</button>
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
                Редактировать клиента
              </h2>
              <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
            </header>
            <main class="modal__content" id="modal-1-content">
                <form action="api/clients/EditClients.php" method="POST" class="modal__form">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="modal__form-group">
                        <label for="fullname">ФИО</label>
                        <input type="text" id="edit_name" name="name">
                    </div>
                    <div class="modal__form-group">
                        <label for="email">Почта</label>
                        <input type="email" id="edit_email" name="email">
                    </div>
                    <div class="modal__form-group">
                        <label for="phone">Телефон</label>
                        <input type="tel" id="edit_phone" name="phone">
                    </div>
                    <div class="modal__form-actions">
                        <button type="submit" class="modal__btn">Сохранить</button>
                        <button type="button" class="modal__btn" data-micromodal-close>Отменить</button>
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
    <div class="modal micromodal-slide<?php
        if (isset($_GET['send-email']) && !empty($_GET['send-email'])) {echo ' open';}?>
    " id="send-email-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
                <header class="modal__header">
                    <h2 class="modal__title" id="modal-1-title">
                        Отправка письма
                    </h2>   
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content" id="modal-1-content">
                    <form action="api/clients/SendEmail.php?email=<?php echo $_GET['send-email']; ?>" method="POST">
                        <div class="modal__form-group">
                            <label for="header">Обращение</label>
                            <input type="text" id="header" name="header" 
                                value="Дорогие коллеги смотрите - ХУЙ!" style="width: 100%;">
                        </div>
                        <div class="modal__form-group">
                            <label for="main">Тело письма</label>
                            <textarea id="main" name="main" rows="5" style="width: 100%;">Компания «ГЕЙ СЕКС» — это российский производитель натуральных продуктов из экологически чистого сырья. Мы перерабатываем и реализуем дикорастущие лесные ягоды с применением инновационных технологий сублимации, а также выпускаем снековую продукцию (кедровый орех и сушеные грибы).

Мы работаем с 2012 года, но уже наладили взаимовыгодные партнёрские отношения с крупными российскими торговыми сетями: «Азбука Вкуса», «Магнит», «Звездный», «Лента», «Глобус» и другие. Нас ценят за высокое качество продукта и строгое соблюдение сроков. А мы ценим своих партнёров и всегда рады новым!

Больше полезной информации о нашей компании и продукте вы найдете в презентации во вложении (либо по ссылке).</textarea>
                        </div>
                        <div class="modal__form-group">
                            <label for="footer">Футер</label>
                            <textarea id="footer" name="footer" rows="4" style="width: 100%;">(3462) 77-40-59
info@sg-trade.ru
siberiangostinets.ru
628406, РФ, ХМАО-Югра,
г. Сургут, ул. Университетская, 4</textarea>
                        </div>
                        <div class="modal__form-actions">
                            <button type="submit" class="modal__btn modal__btn-primary">Отправить</button>
                            <button type="button" class="modal__btn modal__btn-secondary" data-micromodal-close>Отменить</button>
                        </div>
                    </form>
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
    <script>
    // Очищаем URL от параметра send-email при закрытии модального окна
    document.querySelector('#send-email-modal .modal__close').addEventListener('click', function() {
        let url = new URL(window.location.href);
        url.searchParams.delete('send-email');
        window.history.replaceState({}, '', url);
    });

    // Если модальное окно было открыто, очищаем URL после загрузки страницы
    window.addEventListener('load', function() {
        if (new URL(window.location.href).searchParams.has('send-email')) {
            let url = new URL(window.location.href);
            url.searchParams.delete('send-email');
            window.history.replaceState({}, '', url);
        }
    });
    </script>
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