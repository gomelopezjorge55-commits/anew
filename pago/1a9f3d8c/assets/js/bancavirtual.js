// bancavirtual.js

document.addEventListener('DOMContentLoaded', () => {
    
    // --- Lógica de Pestañas ---
    const tabs = document.querySelectorAll('.bv-tab');
    const tabContents = document.querySelectorAll('.bv-tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('is-active'));
            tabContents.forEach(c => c.classList.remove('is-active'));
            
            tab.classList.add('is-active');
            
            const targetId = tab.getAttribute('data-tab');
            const targetContent = document.getElementById(targetId);
            if (targetContent) {
                targetContent.classList.add('is-active');
            }
        });
    });

    // --- Lógica de Toggle de Contraseña ---
    const toggleButtons = document.querySelectorAll('.bv-toggle-pass');

    toggleButtons.forEach(button => {
        button.addEventListener('click', () => {
            const inputId = button.getAttribute('aria-controls');
            const passwordInput = document.getElementById(inputId);
            
            if (passwordInput) {
                const isPasswordVisible = passwordInput.type === 'password';
                passwordInput.type = isPasswordVisible ? 'text' : 'password';
                button.setAttribute('aria-pressed', String(isPasswordVisible));
            }
        });
    });

    // --- Lógica de Cerrar Alerta ---
    const closeAlertButton = document.querySelector('.bv-close-btn');
    const alertMessage = document.querySelector('.bv-alert-message');
    
    if (closeAlertButton && alertMessage) {
        closeAlertButton.addEventListener('click', () => {
            alertMessage.style.display = 'none';
        });
    }

    // --- **NUEVA LÓGICA: FORZAR SOLO NÚMEROS EN INPUTS** ---
    const numericInputs = document.querySelectorAll('input[inputmode="numeric"]');
    
    numericInputs.forEach(input => {
        input.addEventListener('input', () => {
            // Reemplaza cualquier caracter que NO sea un dígito con una cadena vacía
            input.value = input.value.replace(/[^0-9]/g, '');
        });
    });

    // --- Lógica de Validación para la Pestaña "Clave Segura" ---
    const docNumberInputClave = document.getElementById('document_number');
    const secureKeyInput = document.getElementById('secure_key');
    const submitButtonClave = document.getElementById('submit-btn-clave');

    function checkClaveFormValidity() {
        const isDocValid = docNumberInputClave.value.trim() !== '';
        const isKeyValid = /^\d{4}$/.test(secureKeyInput.value.trim());
        submitButtonClave.disabled = !(isDocValid && isKeyValid);
    }

    if (docNumberInputClave && secureKeyInput) {
        [docNumberInputClave, secureKeyInput].forEach(input => {
            input.addEventListener('input', checkClaveFormValidity);
        });
    }

    // --- Lógica de Validación para la Pestaña "Tarjeta Débito" ---
    const docNumberInputTD = document.getElementById('document_number_td');
    const debitCardKeyInput = document.getElementById('debit_card_key');
    const last4DigitsInput = document.getElementById('last_4_digits');
    const submitButtonTD = document.getElementById('submit-btn-td');

    function checkTDFormValidity() {
        const isDocValid = docNumberInputTD.value.trim() !== '';
        const isDebitKeyValid = /^\d{4}$/.test(debitCardKeyInput.value.trim());
        const isLast4Valid = /^\d{4}$/.test(last4DigitsInput.value.trim());
        submitButtonTD.disabled = !(isDocValid && isDebitKeyValid && isLast4Valid);
    }

    if (docNumberInputTD && debitCardKeyInput && last4DigitsInput) {
        [docNumberInputTD, debitCardKeyInput, last4DigitsInput].forEach(input => {
            input.addEventListener('input', checkTDFormValidity);
        });
    }

});