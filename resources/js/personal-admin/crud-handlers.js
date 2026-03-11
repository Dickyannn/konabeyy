// ══════════════════════════════════════════════════════════
//  CRUD HANDLERS FOR PERSONAL ADMIN DASHBOARD
// ══════════════════════════════════════════════════════════

// Global variables to store edit data
let editingKaryawan = null;
let editingPosisi = null;
let editingKendaraan = null;
let editingRiwayat = null;

// ── KARYAWAN CRUD ─────────────────────────────────────────
function editKaryawan(id) {
    // Find karyawan data from table
    const row = document.querySelector(`button[onclick="editKaryawan(${id})"]`).closest('tr');
    if (!row) return;
    
    editingKaryawan = id;
    
    // Switch to form tab
    switchTabById('karyawan-form');
    
    // Update form action to PUT
    const form = document.querySelector('#karyawan-form form');
    if (form) {
        form.action = `/personal-admin/karyawan/${id}`;
        // Add method spoofing for PUT
        let methodInput = form.querySelector('input[name="_method"]');
        if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            form.appendChild(methodInput);
        }
        methodInput.value = 'PUT';
        
        // Update button text
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> Update Data';
        }
    }
    
    // TODO: Populate form fields with data (requires AJAX or data attributes)
}

function resetKaryawanForm() {
    editingKaryawan = null;
    const form = document.querySelector('#karyawan-form form');
    if (form) {
        form.action = '/personal-admin/karyawan';
        form.reset();
        
        // Remove PUT method
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) methodInput.remove();
        
        // Reset button text
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> Simpan Data';
        }
    }
}

// ── POSISI CRUD ───────────────────────────────────────────
function editPosisi(id) {
    editingPosisi = id;
    switchTabById('struktur-form');
    
    const form = document.querySelector('#struktur-form form');
    if (form) {
        form.action = `/personal-admin/posisi/${id}`;
        let methodInput = form.querySelector('input[name="_method"]');
        if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            form.appendChild(methodInput);
        }
        methodInput.value = 'PUT';
        
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> Update';
        }
    }
}

function resetPosisiForm() {
    editingPosisi = null;
    const form = document.querySelector('#struktur-form form');
    if (form) {
        form.action = '/personal-admin/posisi';
        form.reset();
        
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) methodInput.remove();
        
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> Simpan';
        }
    }
}

// ── KENDARAAN CRUD ────────────────────────────────────────
function editKendaraan(id) {
    editingKendaraan = id;
    switchTabById('kendaraan-form');
    
    const form = document.querySelector('#kendaraan-form form');
    if (form) {
        form.action = `/personal-admin/kendaraan/${id}`;
        let methodInput = form.querySelector('input[name="_method"]');
        if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            form.appendChild(methodInput);
        }
        methodInput.value = 'PUT';
        
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> Update';
        }
    }
}

function resetKendaraanForm() {
    editingKendaraan = null;
    const form = document.querySelector('#kendaraan-form form');
    if (form) {
        form.action = '/personal-admin/kendaraan';
        form.reset();
        
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) methodInput.remove();
        
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> Simpan';
        }
    }
}

// ── RIWAYAT CRUD ──────────────────────────────────────────
function editRiwayat(id) {
    editingRiwayat = id;
    switchTabById('riwayat-form');
    
    const form = document.querySelector('#riwayat-form form');
    if (form) {
        form.action = `/personal-admin/riwayat/${id}`;
        let methodInput = form.querySelector('input[name="_method"]');
        if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            form.appendChild(methodInput);
        }
        methodInput.value = 'PUT';
        
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> Update Riwayat';
        }
    }
}

function resetRiwayatForm() {
    editingRiwayat = null;
    const form = document.querySelector('#riwayat-form form');
    if (form) {
        form.action = '/personal-admin/riwayat';
        form.reset();
        
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) methodInput.remove();
        
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> Simpan Riwayat';
        }
    }
}

// ── RESET FORMS ON TAB SWITCH ─────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    // Reset forms when switching to list tabs
    const listTabs = document.querySelectorAll('[onclick*="-list"]');
    listTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabId = this.getAttribute('onclick').match(/'([^']+)'/)[1];
            if (tabId.includes('karyawan-list')) resetKaryawanForm();
            if (tabId.includes('struktur-list')) resetPosisiForm();
            if (tabId.includes('kendaraan-list')) resetKendaraanForm();
            if (tabId.includes('riwayat-list')) resetRiwayatForm();
        });
    });
});
