const clientInput = document.querySelector('#client');

const emailField = document.querySelector('#email-field');

const toggleVisibleEmailField = () => {
    const value = clientInput.value;
    if (value === 'new') {
        emailField.style.display = 'block';
    } else {
        emailField.style.display = 'none';
    }
}
toggleVisibleEmailField();
clientInput.addEventListener('input', toggleVisibleEmailField);
