document.addEventListener('DOMContentLoaded', function() {
    const supportBtn = document.querySelector('.support-btn');
    const supportTicket = document.querySelector('.support-create-ticket');
    const closeBtn = document.querySelector('.support__close');
    const tabs = document.querySelectorAll('.support__tab');
    const contents = document.querySelectorAll('.support__content');
    let isOpen = false;

    function closeTicket() {
        isOpen = false;
        supportTicket.classList.remove('active');
    }

    // Открытие/закрытие модального окна по клику на кнопку
    supportBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        isOpen = !isOpen;
        supportTicket.classList.toggle('active', isOpen);
        
        // Если открыта вкладка "Мои обращения", загружаем список обращений
        if (isOpen && document.querySelector('.support__tab[data-tab="my-tickets"]').classList.contains('active')) {
            loadUserTickets();
        }
    });

    // Закрытие по крестику
    closeBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        closeTicket();
    });

    // Закрытие модального окна при клике вне его
    document.addEventListener('click', function(e) {
        if (isOpen && !supportTicket.contains(e.target) && e.target !== supportBtn) {
            closeTicket();
        }
    });

    // Предотвращение закрытия при клике внутри модального окна
    supportTicket.addEventListener('click', function(e) {
        e.stopPropagation();
    });

    // Обработка отправки формы
    const supportForm = supportTicket.querySelector('form');
    supportForm.addEventListener('submit', function(e) {
        // Здесь можно добавить валидацию формы
        const textarea = this.querySelector('textarea');
        if (!textarea.value.trim()) {
            e.preventDefault();
            textarea.classList.add('error');
            alert('Пожалуйста, опишите вашу проблему');
            return;
        }
    });

    // Анимация иконки при наведении
    supportBtn.addEventListener('mouseenter', function() {
        const icon = this.querySelector('i');
        icon.style.transform = 'scale(1.1) rotate(15deg)';
    });

    supportBtn.addEventListener('mouseleave', function() {
        const icon = this.querySelector('i');
        icon.style.transform = 'scale(1) rotate(0)';
    });
    
    // Переключение вкладок
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Удаляем активный класс у всех вкладок и контентов
            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));
            
            // Добавляем активный класс текущей вкладке
            this.classList.add('active');
            
            // Показываем соответствующий контент
            const tabId = this.getAttribute('data-tab');
            document.getElementById(tabId).classList.add('active');
            
            // Если выбрана вкладка "Мои обращения", загружаем список обращений
            if (tabId === 'my-tickets') {
                loadUserTickets();
            }
        });
    });
    
    // Функция для переключения на вкладку "Мои обращения"
    window.showMyTicketsWindow = function() {
        // Находим вкладку "Мои обращения" и имитируем клик по ней
        const myTicketsTab = document.querySelector('.support__tab[data-tab="my-tickets"]');
        if (myTicketsTab) {
            myTicketsTab.click();
        }
    };
    
    // Функция для переключения на вкладку "Создать обращение"
    window.showCreateTicketWindow = function() {
        // Находим вкладку "Создать обращение" и имитируем клик по ней
        const createTicketTab = document.querySelector('.support__tab[data-tab="create-ticket"]');
        if (createTicketTab) {
            createTicketTab.click();
        }
    };
    
    // Функция для открытия модального окна "Мои обращения"
    window.showMyTicketsModal = function() {
        // Закрываем модальное окно создания обращения
        MicroModal.close('support-modal');
        
        // Открываем модальное окно "Мои обращения"
        MicroModal.show('my-tickets-modal');
        
        // Загружаем список обращений
        loadTicketsForModal();
    };
    
    // Функция для открытия модального окна создания обращения
    window.showSupportModal = function() {
        // Закрываем модальное окно "Мои обращения"
        MicroModal.close('my-tickets-modal');
        
        // Открываем модальное окно создания обращения
        MicroModal.show('support-modal');
    };
    
    // Загрузка списка обращений пользователя для модального окна
    function loadTicketsForModal() {
        const ticketsList = document.getElementById('tickets-list');
        ticketsList.innerHTML = '<div class="loading-spinner"><i class="fa fa-spinner fa-spin"></i> Загрузка обращений...</div>';
        
        console.log('Загрузка обращений для модального окна...');
        
        fetch('api/tickets/GetUserTickets.php')
            .then(response => {
                console.log('Ответ получен:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Данные API:', data);
                
                if (data.success) {
                    if (data.tickets.length === 0) {
                        console.log('Обращений не найдено');
                        ticketsList.innerHTML = '<div class="no-tickets">У вас пока нет обращений</div>';
                        return;
                    }
                    
                    console.log(`Найдено ${data.tickets.length} обращений`);
                    ticketsList.innerHTML = '';
                    data.tickets.forEach(ticket => {
                        const ticketCard = createTicketCardForModal(ticket);
                        ticketsList.appendChild(ticketCard);
                    });
                } else {
                    console.error('Ошибка API:', data.error);
                    ticketsList.innerHTML = `<div class="no-tickets">Ошибка: ${data.error || 'Не удалось загрузить обращения'}</div>`;
                }
            })
            .catch(error => {
                console.error('Ошибка запроса:', error);
                ticketsList.innerHTML = '<div class="no-tickets">Ошибка при загрузке обращений</div>';
            });
    }
    
    // Создание карточки обращения для модального окна
    function createTicketCardForModal(ticket) {
        const card = document.createElement('div');
        card.className = 'ticket-card-mini';
        
        // Добавляем превью изображения, если есть
        let imagePreview = '';
        if (ticket.files && ticket.files.length > 0) {
            const firstImage = ticket.files[0];
            imagePreview = `<div class="ticket-image-preview">
                <img src="${firstImage.path}" alt="Превью">
            </div>`;
        }
        
        card.innerHTML = `
            <div class="ticket-header">
                <span class="ticket-id">#${ticket.id}</span>
                <span class="ticket-type ${ticket.type}">${ticket.typeText}</span>
            </div>
            <div class="ticket-message">${ticket.message}</div>
            ${imagePreview}
            <div class="ticket-info">
                <span class="ticket-status ${ticket.status}">
                    <i class="fa fa-${ticket.statusIcon}"></i>
                    ${ticket.statusText}
                </span>
                <span class="ticket-date">
                    <i class="fa fa-calendar"></i> 
                    ${ticket.created_at}
                </span>
            </div>
            <div class="ticket-actions">
                <button class="chat-btn" onclick="openTicketChatFromModal(${ticket.id})">
                    <i class="fa fa-comments"></i> Чат
                </button>
            </div>
        `;
        
        return card;
    }
    
    // Загрузка списка обращений пользователя для вкладки
    function loadUserTickets() {
        const ticketsContainer = document.querySelector('.my-tickets-container');
        ticketsContainer.innerHTML = '<div class="loading-spinner"><i class="fa fa-spinner fa-spin"></i> Загрузка обращений...</div>';
        
        console.log('Загрузка обращений для вкладки...');
        
        fetch('api/tickets/GetUserTickets.php')
            .then(response => {
                console.log('Ответ получен:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Данные API:', data);
                
                if (data.success) {
                    if (data.tickets.length === 0) {
                        console.log('Обращений не найдено');
                        ticketsContainer.innerHTML = '<div class="no-tickets">У вас пока нет обращений</div>';
                        return;
                    }
                    
                    console.log(`Найдено ${data.tickets.length} обращений`);
                    ticketsContainer.innerHTML = '';
                    data.tickets.forEach(ticket => {
                        const ticketCard = createTicketCard(ticket);
                        ticketsContainer.appendChild(ticketCard);
                    });
                } else {
                    console.error('Ошибка API:', data.error);
                    ticketsContainer.innerHTML = `<div class="no-tickets">Ошибка: ${data.error || 'Не удалось загрузить обращения'}</div>`;
                }
            })
            .catch(error => {
                console.error('Ошибка запроса:', error);
                ticketsContainer.innerHTML = '<div class="no-tickets">Ошибка при загрузке обращений</div>';
            });
    }
    
    // Создание карточки обращения для вкладки
    function createTicketCard(ticket) {
        const card = document.createElement('div');
        card.className = 'ticket-card-mini';
        
        // Добавляем превью изображения, если есть
        let imagePreview = '';
        if (ticket.files && ticket.files.length > 0) {
            const firstImage = ticket.files[0];
            imagePreview = `<div class="ticket-image-preview">
                <img src="${firstImage.path}" alt="Превью">
            </div>`;
        }
        
        card.innerHTML = `
            <div class="ticket-header">
                <span class="ticket-id">#${ticket.id}</span>
                <span class="ticket-type ${ticket.type}">${ticket.typeText}</span>
            </div>
            <div class="ticket-message">${ticket.message}</div>
            ${imagePreview}
            <div class="ticket-info">
                <span class="ticket-status ${ticket.status}">
                    <i class="fa fa-${ticket.statusIcon}"></i>
                    ${ticket.statusText}
                </span>
                <span class="ticket-date">
                    <i class="fa fa-calendar"></i> 
                    ${ticket.created_at}
                </span>
            </div>
            <div class="ticket-actions">
                <button class="chat-btn" onclick="openTicketChat(${ticket.id})">
                    <i class="fa fa-comments"></i> Чат
                </button>
            </div>
        `;
        
        return card;
    }
    
    // Инициализация MicroModal для модальных окон
    if (typeof MicroModal !== 'undefined') {
        MicroModal.init({
            openTrigger: 'data-micromodal-trigger',
            closeTrigger: 'data-micromodal-close',
            disableFocus: true,
            disableScroll: true,
            awaitOpenAnimation: true,
            awaitCloseAnimation: true
        });
    }
});

// Открытие чата по обращению из вкладки
function openTicketChat(ticketId) {
    // Устанавливаем ID тикета в модальном окне
    document.getElementById('chat-ticket-id').textContent = ticketId;
    document.getElementById('chat-ticket-id-input').value = ticketId;
    
    // Загружаем сообщения чата
    loadChatMessages(ticketId);
    
    // Открываем модальное окно
    MicroModal.show('ticket-chat-modal');
}

// Открытие чата по обращению из модального окна
function openTicketChatFromModal(ticketId) {
    // Закрываем модальное окно "Мои обращения"
    MicroModal.close('my-tickets-modal');
    
    // Устанавливаем ID тикета в модальном окне
    document.getElementById('chat-ticket-id').textContent = ticketId;
    document.getElementById('chat-ticket-id-input').value = ticketId;
    
    // Загружаем сообщения чата
    loadChatMessages(ticketId);
    
    // Открываем модальное окно
    MicroModal.show('ticket-chat-modal');
}

// Загрузка сообщений чата
function loadChatMessages(ticketId) {
    const chatMessages = document.getElementById('chat-messages');
    chatMessages.innerHTML = '<div class="loading-spinner"><i class="fa fa-spinner fa-spin"></i> Загрузка сообщений...</div>';
    
    fetch(`api/tickets/GetTicketChat.php?ticket_id=${ticketId}`)
        .then(response => response.json())
        .then(data => {
            console.log('Данные чата:', data);
            
            if (data.success) {
                chatMessages.innerHTML = '';
                
                if (data.messages.length === 0) {
                    chatMessages.innerHTML = '<div class="no-messages">Сообщений пока нет</div>';
                } else {
                    data.messages.forEach(message => {
                        const messageEl = document.createElement('div');
                        messageEl.className = `chat-message ${message.is_admin ? 'admin' : 'user'}`;
                        
                        messageEl.innerHTML = `
                            <div class="chat-message-content">${message.message}</div>
                            <div class="chat-message-meta">
                                ${message.sender_name} · ${message.created_at}
                            </div>
                        `;
                        
                        chatMessages.appendChild(messageEl);
                    });
                    
                    // Прокручиваем к последнему сообщению
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            } else {
                chatMessages.innerHTML = `<div class="error-message">Ошибка: ${data.error || 'Не удалось загрузить сообщения'}</div>`;
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
            chatMessages.innerHTML = '<div class="error-message">Ошибка при загрузке сообщений</div>';
        });
    
    // Обработка отправки сообщения от пользователя
    const chatForm = document.getElementById('chat-form');
    chatForm.onsubmit = function(e) {
        e.preventDefault();
        
        const messageInput = document.getElementById('chat-message');
        const message = messageInput.value.trim();
        
        if (!message) {
            return;
        }
        
        // Отправляем сообщение
        const formData = new FormData(chatForm);
        // Добавляем параметр sender_type = 'user', так как это сообщение от пользователя
        formData.append('sender_type', 'user');
        
        sendChatMessage(formData, messageInput, chatMessages);
    };
    
    // Обработка отправки сообщения от администратора
    const adminMessageBtn = document.querySelector('.admin-message-btn');
    if (adminMessageBtn) {
        adminMessageBtn.addEventListener('click', function() {
            const messageInput = document.getElementById('chat-message');
            const message = messageInput.value.trim();
            
            if (!message) {
                return;
            }
            
            // Отправляем сообщение
            const formData = new FormData(chatForm);
            // Добавляем параметр sender_type = 'admin', так как это сообщение от администратора
            formData.append('sender_type', 'admin');
            
            sendChatMessage(formData, messageInput, chatMessages);
        });
    }
}

// Функция для отправки сообщения в чате
function sendChatMessage(formData, messageInput, chatMessages) {
    fetch('api/tickets/AddMessage.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Ответ на отправку сообщения:', data);
        
        if (data.success) {
            // Очищаем поле ввода
            messageInput.value = '';
            
            // Добавляем сообщение в чат
            if (data.message) {
                const messageEl = document.createElement('div');
                messageEl.className = `chat-message ${data.message.is_admin ? 'admin' : 'user'}`;
                
                messageEl.innerHTML = `
                    <div class="chat-message-content">${data.message.message}</div>
                    <div class="chat-message-meta">
                        ${data.message.sender_name} · ${data.message.created_at}
                    </div>
                `;
                
                chatMessages.appendChild(messageEl);
                
                // Прокручиваем к последнему сообщению
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        } else {
            alert('Ошибка при отправке сообщения: ' + (data.error || 'Неизвестная ошибка'));
        }
    })
    .catch(error => {
        console.error('Ошибка:', error);
        alert('Ошибка при отправке сообщения');
    });
} 