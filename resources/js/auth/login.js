/**
 * Login Page JavaScript
 * - Toggle password visibility
 * - Form submission with loading state
 */

document.addEventListener('DOMContentLoaded', () => {
    // ── Toggle Password Visibility ──────────────────
    const togglePwBtn = document.getElementById('togglePw');
    const pwInput     = document.getElementById('password');
    const eyeIcon     = document.getElementById('eyeIcon');

    if (togglePwBtn && pwInput && eyeIcon) {
        togglePwBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const isText = pwInput.type === 'text';
            pwInput.type = isText ? 'password' : 'text';
            eyeIcon.className = isText ? 'bi bi-eye' : 'bi bi-eye-slash';
        });
    }

    // ── Form Loading State ──────────────────────────
    const loginForm  = document.getElementById('loginForm');
    const btnMasuk   = document.getElementById('btnMasuk');
    const btnText    = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');

    if (loginForm && btnMasuk && btnText && btnSpinner) {
        loginForm.addEventListener('submit', () => {
            btnMasuk.disabled = true;
            btnText.classList.add('d-none');
            btnSpinner.classList.remove('d-none');
        });
    }
});
