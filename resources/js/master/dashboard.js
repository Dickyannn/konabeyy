/**
 * Master Dashboard JavaScript
 * Handles CRUD operations and form interactions
 */

document.addEventListener('DOMContentLoaded', function () {
    initializeEventListeners();
    formatCurrency();
});

// ───────────────────────────────────────────────────────
//  EVENT LISTENERS
// ───────────────────────────────────────────────────────
function initializeEventListeners() {
    // Close modals on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal.show').forEach(m => {
                const modal = bootstrap.Modal.getInstance(m);
                if (modal) modal.hide();
            });
        }
    });

    // Form submission handlers
    setupFormHandlers();
}

// ───────────────────────────────────────────────────────
//  MASTER GOLONGAN FUNCTIONS
// ───────────────────────────────────────────────────────
function editGolongan(data) {
    document.getElementById('methodGolongan').value = 'PUT';
    document.getElementById('idGolongan').value = data.id;
    document.getElementById('kodeGolongan').value = data.kode_golongan;
    document.getElementById('namaGolongan').value = data.nama_golongan;
    document.getElementById('gajiMin').value = data.gaji_pokok_min || '';
    document.getElementById('gajiMax').value = data.gaji_pokok_max || '';
    document.getElementById('deskGolongan').value = data.deskripsi || '';
    
    document.getElementById('formGolongan').action = `/master/golongan/${data.id}`;
    document.getElementById('btnGolongan').textContent = 'Simpan Perubahan';
    document.getElementById('kodeGolongan').focus();
}

function resetFormGolongan() {
    document.getElementById('methodGolongan').value = 'POST';
    document.getElementById('formGolongan').reset();
    document.getElementById('formGolongan').action = '{{ route("master.golongan.store") }}';
    document.getElementById('btnGolongan').textContent = 'Tambah Golongan';
}

function deleteGolongan(id) {
    if (confirm('Yakin ingin nonaktifkan golongan ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/master/golongan/${id}`;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = document.querySelector('meta[name="csrf-token"]').content;
        
        form.appendChild(methodInput);
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// ───────────────────────────────────────────────────────
//  CAR ALLOWANCE FUNCTIONS
// ───────────────────────────────────────────────────────
function resetFormCA() {
    document.getElementById('methodCA').value = 'POST';
    document.getElementById('formCA').reset();
}

function deleteCarAllowance(id) {
    if (confirm('Yakin ingin nonaktifkan car allowance ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/master/car-allowance/${id}`;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = document.querySelector('meta[name="csrf-token"]').content;
        
        form.appendChild(methodInput);
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// ───────────────────────────────────────────────────────
//  USER FUNCTIONS
// ───────────────────────────────────────────────────────
function editUser(data) {
    document.getElementById('methodUser').value = 'PUT';
    document.getElementById('idUser').value = data.id;
    document.getElementById('namaUser').value = data.nama;
    document.getElementById('emailUser').value = data.email;
    document.getElementById('roleUser').value = data.id_role || '';
    
    const unitSelect = document.querySelectorAll('select[name="id_unit"]');
    if (unitSelect.length > 0) {
        unitSelect[0].value = data.id_unit || '';
    }
    
    const passwordField = document.getElementById('passwordUser');
    passwordField.value = '';
    passwordField.placeholder = 'Kosongkan jika tidak ingin mengubah password';
    
    const pwRequired = document.getElementById('pwRequired');
    if (pwRequired) pwRequired.style.display = 'none';
    
    document.getElementById('formUser').action = `/master/users/${data.id}`;
    document.getElementById('btnUser').textContent = 'Simpan Perubahan';
}

function resetFormUser() {
    document.getElementById('methodUser').value = 'POST';
    document.getElementById('formUser').reset();
    document.getElementById('formUser').action = '{{ route("master.users.store") }}';
    
    const passwordField = document.getElementById('passwordUser');
    passwordField.placeholder = 'Min 8 karakter';
    
    const pwRequired = document.getElementById('pwRequired');
    if (pwRequired) pwRequired.style.display = 'inline';
    
    document.getElementById('btnUser').textContent = 'Tambah User';
}

function toggleUserActive(userId) {
    if (confirm('Yakin ingin mengubah status user ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/master/users/${userId}/toggle-active`;
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = document.querySelector('meta[name="csrf-token"]').content;
        
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function resetUserPassword(userId) {
    if (confirm('Reset password user ini? Password akan diubah menjadi Password123')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/master/users/${userId}/reset-password`;
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = document.querySelector('meta[name="csrf-token"]').content;
        
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// ───────────────────────────────────────────────────────
//  UNIT PT FUNCTIONS
// ───────────────────────────────────────────────────────
function deleteUnitPt(id) {
    if (confirm('Yakin ingin nonaktifkan unit/PT ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/master/unit-pt/${id}`;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = document.querySelector('meta[name="csrf-token"]').content;
        
        form.appendChild(methodInput);
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// ───────────────────────────────────────────────────────
//  FORM HANDLING
// ───────────────────────────────────────────────────────
function setupFormHandlers() {
    // Add loading state to form submissions
    const forms = document.querySelectorAll('form[id^="form"]');
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            const btn = this.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                const spinner = btn.querySelector('.spinner-border');
                if (spinner) spinner.style.display = 'inline-block';
            }
        });
    });
}

// ───────────────────────────────────────────────────────
//  CURRENCY FORMATTING
// ───────────────────────────────────────────────────────
function formatCurrency() {
    const currencyInputs = document.querySelectorAll('input[data-format="currency"]');
    
    currencyInputs.forEach(input => {
        input.addEventListener('blur', function () {
            let value = this.value.replace(/[^\d]/g, '');
            if (value) {
                this.value = formatNumber(value);
            }
        });
        
        input.addEventListener('focus', function () {
            this.value = this.value.replace(/[^\d]/g, '');
        });
    });
}

function formatNumber(num) {
    return parseInt(num).toLocaleString('id-ID');
}

// ───────────────────────────────────────────────────────
//  MODAL CLOSE ON SUCCESS
// ───────────────────────────────────────────────────────
window.addEventListener('load', function () {
    const successMessage = document.querySelector('[role="alert"].alert-success-hrms');
    if (successMessage) {
        // Close all open modals
        document.querySelectorAll('.modal.show').forEach(m => {
            const modal = bootstrap.Modal.getInstance(m);
            if (modal) modal.hide();
        });
    }
});

// ───────────────────────────────────────────────────────
//  HELPER FUNCTIONS
// ───────────────────────────────────────────────────────
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Teks berhasil disalin!');
    }).catch(() => {
        alert('Gagal menyalin teks');
    });
}

function showConfirmation(message, callback) {
    if (confirm(message)) {
        callback();
    }
}
