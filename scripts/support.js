document.addEventListener('DOMContentLoaded', function() {
    const supportBtn = document.querySelector('.support-btn');
    const supportTicket = document.querySelector('.support-create-ticket');
    const closeBtn = document.querySelector('.support__close');
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
}); 