<?php session_start();

require_once 'api/auth/AuthCheck.php';

AuthCheck('', 'login.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/settings.css">
    <link rel="stylesheet" href="styles/pages/products.css">
    <link rel="stylesheet" href="styles/modules/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="styles/modules/micromodal.css">
    <title>CRM | Товары</title>
</head>
<body>
    <header class="header">
        <div class="container">
            <p class="header__admin">ФИО</p>
            <ul class="header__links">
                <li><a href="#">Клиенты</a></li>
                <li><a href="#">Товары</a></li>
                <li><a href="#">Заказы</a></li>
            </ul>
            <a href="#" class="header__logout">Выйти</a>
        </div>
    </header>
    <main class="main">
        <section class="main__filters">
            <div class="container">
                <form action="" class="main__form">
                    <label class="main__label" for="search">Поиск по названию</label>
                    <input class="main__input" type="text" id="search" name="search" placeholder="Товар">
                    <select class="main__select" name="sort" id="sort">
                        <option value="0">Название</option>
                        <option value="1">Цена</option>
                        <option value="2">Количество</option>
                    </select>
                    <select class="main__select" name="sort" id="sort">
                        <option value="0">По возрастанию</option>
                        <option value="1">По убыванию</option>
                    </select>
                </form>
            </div>
        </section>
        <section class="main__clients">
            <div class="container">
                <h2 class="main__clients__title">Список товаров</h2>
                <button onclick="MicroModal.show('add-modal')" class="main__clients__add"><i class="fa fa-plus-circle"></i></button>
                <table>
                    <thead>
                        <th>ИД</th>
                        <th>Название</th>
                        <th>Описание</th>
                        <th>Цена</th>
                        <th>Количество</th>
                        <th>QR код</th>
                        <th>Редактировать</th>
                        <th>Удалить</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Футболка</td>
                            <td>Белая футболка XL</td>
                            <td>1000₽</td>
                            <td>50</td>
                            <td><i class="fa fa-qrcode" onclick="MicroModal.show('qr-modal')"></i></td>
                            <td><i class="fa fa-pencil" onclick="MicroModal.show('edit-modal')"></i></td>
                            <td><i class="fa fa-trash" onclick="MicroModal.show('delete-modal')"></i></td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>Футболка</td>
                            <td>Белая футболка XL</td>
                            <td>1000₽</td>
                            <td>50</td>
                            <td><i class="fa fa-qrcode" onclick="MicroModal.show('qr-modal')"></i></td>
                            <td><i class="fa fa-pencil" onclick="MicroModal.show('edit-modal')"></i></td>
                            <td><i class="fa fa-trash" onclick="MicroModal.show('delete-modal')"></i></td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>Футболка</td>
                            <td>Белая футболка XL</td>
                            <td>1000₽</td>
                            <td>50</td>
                            <td><i class="fa fa-qrcode" onclick="MicroModal.show('qr-modal')"></i></td>
                            <td><i class="fa fa-pencil" onclick="MicroModal.show('edit-modal')"></i></td>
                            <td><i class="fa fa-trash" onclick="MicroModal.show('delete-modal')"></i></td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>Футболка</td>
                            <td>Белая футболка XL</td>
                            <td>1000₽</td>
                            <td>50</td>
                            <td><i class="fa fa-qrcode" onclick="MicroModal.show('qr-modal')"></i></td>
                            <td><i class="fa fa-pencil" onclick="MicroModal.show('edit-modal')"></i></td>
                            <td><i class="fa fa-trash" onclick="MicroModal.show('delete-modal')"></i></td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>Футболка</td>
                            <td>Белая футболка XL</td>
                            <td>1000₽</td>
                            <td>50</td>
                            <td><i class="fa fa-qrcode" onclick="MicroModal.show('qr-modal')"></i></td>
                            <td><i class="fa fa-pencil" onclick="MicroModal.show('edit-modal')"></i></td>
                            <td><i class="fa fa-trash" onclick="MicroModal.show('delete-modal')"></i></td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>Футболка</td>
                            <td>Белая футболка XL</td>
                            <td>1000₽</td>
                            <td>50</td>
                            <td><i class="fa fa-qrcode" onclick="MicroModal.show('qr-modal')"></i></td>
                            <td><i class="fa fa-pencil" onclick="MicroModal.show('edit-modal')"></i></td>
                            <td><i class="fa fa-trash" onclick="MicroModal.show('delete-modal')"></i></td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>Футболка</td>
                            <td>Белая футболка XL</td>
                            <td>1000₽</td>
                            <td>50</td>
                            <td><i class="fa fa-qrcode" onclick="MicroModal.show('qr-modal')"></i></td>
                            <td><i class="fa fa-pencil" onclick="MicroModal.show('edit-modal')"></i></td>
                            <td><i class="fa fa-trash" onclick="MicroModal.show('delete-modal')"></i></td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>Футболка</td>
                            <td>Белая футболка XL</td>
                            <td>1000₽</td>
                            <td>50</td>
                            <td><i class="fa fa-qrcode" onclick="MicroModal.show('qr-modal')"></i></td>
                            <td><i class="fa fa-pencil" onclick="MicroModal.show('edit-modal')"></i></td>
                            <td><i class="fa fa-trash" onclick="MicroModal.show('delete-modal')"></i></td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>Футболка</td>
                            <td>Белая футболка XL</td>
                            <td>1000₽</td>
                            <td>50</td>
                            <td><i class="fa fa-qrcode" onclick="MicroModal.show('qr-modal')"></i></td>
                            <td><i class="fa fa-pencil" onclick="MicroModal.show('edit-modal')"></i></td>
                            <td><i class="fa fa-trash" onclick="MicroModal.show('delete-modal')"></i></td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>Футболка</td>
                            <td>Белая футболка XL</td>
                            <td>1000₽</td>
                            <td>50</td>
                            <td><i class="fa fa-qrcode" onclick="MicroModal.show('qr-modal')"></i></td>
                            <td><i class="fa fa-pencil" onclick="MicroModal.show('edit-modal')"></i></td>
                            <td><i class="fa fa-trash" onclick="MicroModal.show('delete-modal')"></i></td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>Футболка</td>
                            <td>Белая футболка XL</td>
                            <td>1000₽</td>
                            <td>50</td>
                            <td><i class="fa fa-qrcode" onclick="MicroModal.show('qr-modal')"></i></td>
                            <td><i class="fa fa-pencil" onclick="MicroModal.show('edit-modal')"></i></td>
                            <td><i class="fa fa-trash" onclick="MicroModal.show('delete-modal')"></i></td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>Футболка</td>
                            <td>Белая футболка XL</td>
                            <td>1000₽</td>
                            <td>50</td>
                            <td><i class="fa fa-qrcode" onclick="MicroModal.show('qr-modal')"></i></td>
                            <td><i class="fa fa-pencil" onclick="MicroModal.show('edit-modal')"></i></td>
                            <td><i class="fa fa-trash" onclick="MicroModal.show('delete-modal')"></i></td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>Футболка</td>
                            <td>Белая футболка XL</td>
                            <td>1000₽</td>
                            <td>50</td>
                            <td><i class="fa fa-qrcode" onclick="MicroModal.show('qr-modal')"></i></td>
                            <td><i class="fa fa-pencil" onclick="MicroModal.show('edit-modal')"></i></td>
                            <td><i class="fa fa-trash" onclick="MicroModal.show('delete-modal')"></i></td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>Футболка</td>
                            <td>Белая футболка XL</td>
                            <td>1000₽</td>
                            <td>50</td>
                            <td><i class="fa fa-qrcode" onclick="MicroModal.show('qr-modal')"></i></td>
                            <td><i class="fa fa-pencil" onclick="MicroModal.show('edit-modal')"></i></td>
                            <td><i class="fa fa-trash" onclick="MicroModal.show('delete-modal')"></i></td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>Футболка</td>
                            <td>Белая футболка XL</td>
                            <td>1000₽</td>
                            <td>50</td>
                            <td><i class="fa fa-qrcode" onclick="MicroModal.show('qr-modal')"></i></td>
                            <td><i class="fa fa-pencil" onclick="MicroModal.show('edit-modal')"></i></td>
                            <td><i class="fa fa-trash" onclick="MicroModal.show('delete-modal')"></i></td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>Футболка</td>
                            <td>Белая футболка XL</td>
                            <td>1000₽</td>
                            <td>50</td>
                            <td><i class="fa fa-qrcode" onclick="MicroModal.show('qr-modal')"></i></td>
                            <td><i class="fa fa-pencil" onclick="MicroModal.show('edit-modal')"></i></td>
                            <td><i class="fa fa-trash" onclick="MicroModal.show('delete-modal')"></i></td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>Футболка</td>
                            <td>Белая футболка XL</td>
                            <td>1000₽</td>
                            <td>50</td>
                            <td><i class="fa fa-qrcode" onclick="MicroModal.show('qr-modal')"></i></td>
                            <td><i class="fa fa-pencil" onclick="MicroModal.show('edit-modal')"></i></td>
                            <td><i class="fa fa-trash" onclick="MicroModal.show('delete-modal')"></i></td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>Футболка</td>
                            <td>Белая футболка XL</td>
                            <td>1000₽</td>
                            <td>50</td>
                            <td><i class="fa fa-qrcode" onclick="MicroModal.show('qr-modal')"></i></td>
                            <td><i class="fa fa-pencil" onclick="MicroModal.show('edit-modal')"></i></td>
                            <td><i class="fa fa-trash" onclick="MicroModal.show('delete-modal')"></i></td>
                        </tr>
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
                Добавление товара
              </h2>
              <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
            </header>
            <main class="modal__content" id="modal-1-content">
                <form class="modal__form">
                    <div class="modal__form-group">
                        <label for="name">Название</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="modal__form-group">
                        <label for="description">Описание</label>
                        <textarea id="description" name="description" required></textarea>
                    </div>
                    <div class="modal__form-group">
                        <label for="price">Цена</label>
                        <input type="number" id="price" name="price" min="0" step="0.01" required>
                    </div>
                    <div class="modal__form-group">
                        <label for="quantity">Количество</label>
                        <input type="number" id="quantity" name="quantity" min="0" required>
                    </div>
                    <div class="modal__form-actions">
                        <button type="submit" class="modal__btn">Создать</button>
                        <button type="button" class="modal__btn" data-micromodal-close>Отменить</button>
                    </div>
                </form>
            </main>
          </div>
        </div>
      </div>

      <div class="modal micromodal-slide" id="edit-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
          <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
            <header class="modal__header">
              <h2 class="modal__title" id="modal-1-title">
                Редактировать товар
              </h2>
              <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
            </header>
            <main class="modal__content" id="modal-1-content">
                <form class="modal__form">
                    <div class="modal__form-group">
                        <label for="name">Название</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="modal__form-group">
                        <label for="description">Описание</label>
                        <textarea id="description" name="description" required></textarea>
                    </div>
                    <div class="modal__form-group">
                        <label for="price">Цена</label>
                        <input type="number" id="price" name="price" min="0" step="0.01" required>
                    </div>
                    <div class="modal__form-group">
                        <label for="quantity">Количество</label>
                        <input type="number" id="quantity" name="quantity" min="0" required>
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

      <div class="modal micromodal-slide" id="qr-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
          <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
            <header class="modal__header">
              <h2 class="modal__title" id="modal-1-title">
                QR код товара
              </h2>
              <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
            </header>
            <main class="modal__content" id="modal-1-content">
                <div class="qr-code">
                    <!-- Здесь будет отображаться QR код -->
                </div>
                <button class="modal__btn" onclick="downloadQR()">Скачать</button>
            </main>
          </div>
        </div>
      </div>

    <!-- модальные окна скрипт -->
    <script defer src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>
    <script defer src="scripts/initClientsModal.js"></script>
    
</body>
</html>