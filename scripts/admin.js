// Функция для открытия модального окна с чатом для ответа клиенту
function openClientResponseModal(ticketId) {
    // Устанавливаем ID тикета в модальном окне
    document.getElementById('client-ticket-id-input').value = ticketId;
    
    // Загружаем сообщения чата
    loadClientChatMessages(ticketId);
    
    // Открываем модальное окно
    MicroModal.show('client-response-modal');
}

// Загрузка сообщений чата для ответа клиенту
function loadClientChatMessages(ticketId) {
    const chatMessages = document.getElementById('client-chat-messages');
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
    
    // Обработка отправки сообщения от администратора
    const chatForm = document.getElementById('client-chat-form');
    chatForm.onsubmit = function(e) {
        e.preventDefault();
        
        const messageInput = document.getElementById('client-chat-message');
        const message = messageInput.value.trim();
        
        if (!message) {
            return;
        }
        
        // Отправляем сообщение
        const formData = new FormData(chatForm);
        // Добавляем параметр sender_type = 'admin', так как это сообщение от администратора
        formData.append('sender_type', 'admin');
        
        sendMessage(formData, messageInput, chatMessages);
    };
    
    // Обработка отправки сообщения от пользователя
    const userMessageBtn = document.querySelector('.user-message-btn');
    if (userMessageBtn) {
        userMessageBtn.addEventListener('click', function() {
            const messageInput = document.getElementById('client-chat-message');
            const message = messageInput.value.trim();
            
            if (!message) {
                return;
            }
            
            // Отправляем сообщение
            const formData = new FormData(chatForm);
            // Добавляем параметр sender_type = 'user', так как это сообщение от пользователя
            formData.append('sender_type', 'user');
            
            sendMessage(formData, messageInput, chatMessages);
        });
    }
}

// Функция для отправки сообщения
function sendMessage(formData, messageInput, chatMessages) {
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

// Функция для отображения модального окна ответа
function showReplyModal(ticketId, clientId) {
    // Вместо старого модального окна используем новое с чатом
    openClientResponseModal(ticketId);
}

// Инициализация MicroModal для модальных окон
document.addEventListener('DOMContentLoaded', function() {
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
    
    // Находим все кнопки "Ответить" и добавляем обработчик
    const replyButtons = document.querySelectorAll('.reply-btn, button[data-micromodal-trigger="client-response-modal"]');
    replyButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Получаем ID тикета из атрибута data-ticket-id или из ближайшего элемента с классом ticket-id
            const ticketId = this.getAttribute('data-ticket-id') || 
                             this.closest('.ticket-card')?.querySelector('.ticket-id')?.textContent.replace('#', '') ||
                             this.closest('tr')?.querySelector('.ticket-id')?.textContent.replace('#', '');
            
            if (ticketId) {
                openClientResponseModal(ticketId);
            } else {
                console.error('Не удалось определить ID тикета');
            }
        });
    });
});

// Функция для отправки ответа клиенту (устаревшая)
function sendReply(event) {
    // Удаляем эту функцию, так как теперь используется новый механизм чата
} 