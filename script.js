// ===== Air-e Payment Portal - Interactive Features =====

document.addEventListener('DOMContentLoaded', function () {
    // ===== reCAPTCHA Checkbox Loading Animation =====
    const recaptchaCheckbox = document.getElementById('recaptcha');

    if (recaptchaCheckbox) {
        recaptchaCheckbox.addEventListener('click', function (e) {
            // If already checked, allow unchecking
            if (this.checked) {
                return;
            }

            // Prevent default checking
            e.preventDefault();

            // Add loading class
            this.classList.add('loading');
            this.disabled = true;

            // Simulate loading for 1.5 seconds
            setTimeout(() => {
                this.classList.remove('loading');
                this.checked = true;
                this.disabled = false;
            }, 1500);
        });
    }

    // ===== Mobile Menu Toggle =====
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');

    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');

            // Animate hamburger menu
            this.classList.toggle('active');
        });
    }

    // ===== Navigation Active State =====
    const navItems = document.querySelectorAll('.nav-item');

    navItems.forEach(item => {
        item.addEventListener('click', function (e) {
            // Remove active class from all items
            navItems.forEach(nav => nav.classList.remove('active'));

            // Add active class to clicked item
            this.classList.add('active');
        });
    });

    // ===== Payment Card Interactions =====
    const paymentCards = document.querySelectorAll('.payment-card');

    paymentCards.forEach(card => {
        card.addEventListener('click', function () {
            // Remove selected state from all cards
            paymentCards.forEach(c => c.classList.remove('selected'));

            // Add selected state to clicked card
            this.classList.add('selected');
        });
    });

    // ===== Form Validation (Visual Only) =====
    const submitButton = document.querySelector('.btn-submit');

    if (submitButton && recaptchaCheckbox) {
        submitButton.addEventListener('click', function (e) {
            e.preventDefault();

            if (!recaptchaCheckbox.checked) {
                // Show visual feedback
                const captchaSection = document.querySelector('.captcha-section');
                captchaSection.style.animation = 'shake 0.5s';

                setTimeout(() => {
                    captchaSection.style.animation = '';
                }, 500);

                // You can add a toast notification here
                showNotification('Por favor, complete el reCAPTCHA', 'warning');
            } else {
                // Visual feedback for successful submission
                showNotification('Procesando pago...', 'success');

                // Here you would normally submit the form
                // For now, just visual feedback
                submitButton.innerHTML = '<span>Procesando...</span>';
                submitButton.disabled = true;

                setTimeout(() => {
                    submitButton.innerHTML = 'PAGAR';
                    submitButton.disabled = false;
                }, 2000);
            }
        });
    }

    // ===== Smooth Scroll for Navigation =====
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));

            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // ===== Notification System =====
    function showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotification = document.querySelector('.notification');
        if (existingNotification) {
            existingNotification.remove();
        }

        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;

        // Add styles
        notification.style.cssText = `
            position: fixed;
            top: 80px;
            right: 20px;
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

        document.body.appendChild(notification);

        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // ===== Add CSS Animations =====
    const style = document.createElement('style');
    style.textContent = `
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
        
        .payment-card.selected {
            border-color: #0052A3;
            box-shadow: 0 0 0 3px rgba(0, 82, 163, 0.2);
        }
        
        .menu-toggle.active span:nth-child(1) {
            transform: rotate(45deg) translate(6px, 6px);
        }
        
        .menu-toggle.active span:nth-child(2) {
            opacity: 0;
        }
        
        .menu-toggle.active span:nth-child(3) {
            transform: rotate(-45deg) translate(6px, -6px);
        }
    `;
    document.head.appendChild(style);

    // ===== Responsive Behavior =====
    let resizeTimer;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            // Expand sidebar on desktop view
            if (window.innerWidth > 1024) {
                sidebar.classList.remove('collapsed');
                menuToggle.classList.remove('active');
            }
        }, 250);
    });
});
