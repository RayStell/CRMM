<?php session_start();




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
    <link rel="stylesheet" href="styles/modules/support.css">
    <title>CRM | Клиенты</title>
</head>
<body>
    <header class="header">
        <div class="container">
            <p class="header__admin">
                <?php 
                    require 'api/DB.php';
                    require_once 'api/clients/AdminName.php';
                    require_once 'api/helpers/getUserType.php';

                    echo AdminName($_SESSION['token'], $DB);
                    $userType = getUserType($DB);
                    echo " <span style='color: green'>($userType)</span>";
                ?>
            </p>
            <ul class="header__links">
                <li><a href="clients.php" class="active">Клиенты</a></li>
                <li><a href="product.php">Товары</a></li>
                <li><a href="orders.php">Заказы</a></li>
                <li><a href="promotions.php">Акции</a></li>
                <?php
                    require_once 'api/helpers/getUserType.php';
                    $userType = getUserType($DB);
                    if ($userType === 'tech') {
                        echo '<li><a href="tech.php">Обращение пользователя</a></li>';
                    }
                ?>
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
                <?php
                    if (isset($_GET['edit-user']) && !empty($_GET['edit-user'])) {
                        $userId = (int)$_GET['edit-user'];
                        $stmt = $DB->prepare("SELECT name, email, phone FROM clients WHERE id = ?");
                        $stmt->execute([$userId]);
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    }

                    // Сохраняем параметры поиска для формы
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
                    if (isset($_GET['page'])) {
                        $searchParams .= '&page=' . urlencode($_GET['page']);
                    }
                ?>
                <form class="modal__form" method="POST" action="api/clients/EditClient.php?<?php echo ltrim($searchParams, '&'); ?>">
                    <input type="hidden" name="id" value="<?php echo $userId ?? ''; ?>">
                    <div class="modal__form-group">
                        <label for="fullname">ФИО</label>
                        <input type="text" id="fullname" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>">
                    </div>
                    <div class="modal__form-group">
                        <label for="email">Почта</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                    </div>
                    <div class="modal__form-group">
                        <label for="phone">Телефон</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
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
                            <input type="text" id="header" name="header" value="Дорогие коллеги!">
                        </div>
                        <div class="modal__form-group">
                            <label for="main">Тело письма</label>
                            <textarea id="main" name="main" rows="5">Компания «Сибирский гостинец» - это российский производитель натуральных продуктов из экологически чистого сырья. Мы перерабатываем и реализуем дикорастущие лесные ягоды с применением инновационных технологий сублимации, а также выпускаем снековую продукцию (кедровый орех и сушеные грибы).

Мы работаем с 2012 года, но уже наладили взаимовыгодные партнёрские отношения с крупными российскими торговыми сетями: «Азбука Вкуса», «Бахетле», «Звездный», «Лэнд», «Табрис» и другие. Нас ценят за высокое качество продукта и строгое соблюдение сроков. А мы ценим своих партнеров и всегда рады новым!

Больше полезной информации о нашей компании и продукте вы найдете в презентации во вложении (либо по <a href="#">ссылке</a>).</textarea>
                        </div>
                        <div class="modal__form-group">
                            <label for="footer">Футер</label>
                            <input type="text" id="footer" name="footer" value="(3462) 77-40-59<br>
<a href='mailto:info@ws-trade.ru' style='color: blue; text-decoration: underline;'>info@ws-trade.ru</a><br>
<a href='https://сибирскийгостинец.рф' style='color: blue; text-decoration: underline;'>сибирскийгостинец.рф</a><br>
628406, РФ, ХМАО-Югра,<br>
г. Сургут, ул. Университетская, 4">
                        </div>
                        <div class="modal__form-actions">
                            <button type="submit" class="modal__btn modal__btn-primary">Отправить</button>
                            <button type="button" class="modal__btn modal__btn-secondary" data-micromodal-close>Отменить</button>
                        </div>
                    </form>

                    <?php 
                    if (isset($_GET['send-email']) && !empty($_GET['send-email'])) {
                        
                    }
                    ?>
                </main>
            </div>
        </div>
    </div>
    <div class="modal micromodal-slide" id="support-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
                <header class="modal__header">
                    <h2 class="modal__title" id="modal-1-title">
                        Обращение в техподдержку
                    </h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content" id="modal-1-content">
                    <form action="api/tickets/CreateTicket.php" method="POST" enctype="multipart/form-data">
                        <div class="form__group">
                            <label for="type">Тип проблемы</label>
                            <select name="type" id="type" required>
                                <option value="tech">Техническая неполадка</option>
                                <option value="crm">Проблема с CRM</option>
                            </select>
                        </div>
                        <div class="form__group">
                            <label for="message">Сообщение</label>
                            <textarea name="message" id="message" required></textarea>
                        </div>
                        <div class="form__group">
                            <label for="files">Прикрепить файлы</label>
                            <input type="file" name="files[]" id="files" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx">
                        </div>
                        <button type="submit" class="form__submit">Отправить</button>
                    </form>
                </main>
            </div>
        </div>
    </div>
    <div class="support-create-ticket">
        <div class="support__header">
            <h3>Техническая поддержка</h3>
            <button class="support__close" aria-label="Закрыть"><i class="fa fa-times"></i></button>
        </div>
        <div class="support__tabs">
            <button class="support__tab active" data-tab="create-ticket">Создать обращение</button>
            <button class="support__tab" data-tab="my-tickets">Мои обращения</button>
        </div>
        <div class="support__content active" id="create-ticket">
            <form action="api/tickets/CreateTicket.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="type">Тип обращения</label>
                    <select name="type" id="type" class="support-select" required>
                        <option value="tech">Техническая неполадка</option>
                        <option value="crm">Проблема с CRM</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="message">Текст обращения</label>
                    <textarea name="message" id="message" placeholder="Опишите вашу проблему..." required></textarea>
                </div>
                <div class="form-group">
                    <label for="files">Прикрепить файлы</label>
                    <input type="file" name="files[]" id="files" multiple>
                </div>
                <button type="submit" class="support-submit">Отправить обращение</button>
            </form>
        </div>
        <div class="support__content" id="my-tickets">
            <div class="my-tickets-container">
                <!-- Здесь будут отображаться карточки обращений пользователя -->
                <div class="loading-spinner">
                    <i class="fa fa-spinner fa-spin"></i> Загрузка обращений...
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно для чата по обращению -->
    <div class="modal micromodal-slide" id="ticket-chat-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-chat-title">
                <header class="modal__header">
                    <h2 class="modal__title" id="modal-chat-title">
                        Чат по обращению #<span id="chat-ticket-id"></span>
                    </h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content" id="modal-chat-content">
                    <div class="chat-messages" id="chat-messages">
                        <!-- Здесь будут сообщения чата -->
                    </div>
                    <form id="chat-form" class="chat-form">
                        <input type="hidden" id="chat-ticket-id-input" name="ticket_id" value="">
                        <div class="form-group">
                            <textarea name="message" id="chat-message" placeholder="Введите сообщение..." required></textarea>
                        </div>
                        <div class="chat-form-actions">
                            <button type="submit" class="chat-submit">Отправить как пользователь</button>
                            <button type="button" class="chat-submit admin-message-btn">Отправить как администратор</button>
                        </div>
                    </form>
                </main>
            </div>
        </div>
    </div>

    <!-- Модальное окно для ответа клиенту -->
    <div class="modal micromodal-slide" id="client-response-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-response-title">
                <header class="modal__header">
                    <h2 class="modal__title" id="modal-response-title">
                        Ответ клиенту
                    </h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content" id="modal-response-content">
                    <div class="chat-messages" id="client-chat-messages">
                        <!-- Здесь будут сообщения чата -->
                    </div>
                    <form id="client-chat-form" class="chat-form">
                        <input type="hidden" id="client-ticket-id-input" name="ticket_id" value="">
                        <div class="form-group">
                            <textarea name="message" id="client-chat-message" placeholder="Введите сообщение..." required></textarea>
                        </div>
                        <div class="chat-form-actions">
                            <button type="submit" class="chat-submit">Отправить как администратор</button>
                            <button type="button" class="chat-submit user-message-btn">Отправить как пользователь</button>
                        </div>
                    </form>
                </main>
            </div>
        </div>
    </div>

    <script defer src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>
    <script defer src="scripts/initClientsModal.js"></script>
    <script defer src="scripts/support.js"></script>

    <!-- техподдержка -->
    <button class="support-btn" title="Техническая поддержка">
        <i class="fa fa-question"></i>
    </button>
</body>
</html>