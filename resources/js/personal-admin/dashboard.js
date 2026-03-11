/* ═══════════════════════════════════════════════════════
   PERSONAL ADMIN DASHBOARD JAVASCRIPT
   ═══════════════════════════════════════════════════════ */

const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const BASE_URL = '{{ route("personal-admin.dashboard") }}'.replace('/personal-admin/dashboard', '');

// ───────────────────────────────────────────────────────
// KARYAWAN DATA MANAGEMENT
// ───────────────────────────────────────────────────────

/** Load karyawan data from API */
function loadKaryawanData(page = 1) {
    const search = document.getElementById('searchKaryawan')?.value || '';
    const golongan = document.getElementById('filterGolongan')?.value || '';
    const unit = document.getElementById('filterUnit')?.value || '';

    fetch(`/personal-admin/karyawan?page=${page}&search=${search}&golongan=${golongan}&unit=${unit}`)
        .then(r => r.json())
        .then(data => {
            renderKaryawanTable(data.data.data);
            renderPagination('karyawanPagination', data.data);
        })
        .catch(err => console.error('Error loading karyawan:', err));
}

/** Render karyawan table */
function renderKaryawanTable(karyawans) {
    const tbody = document.getElementById('karyawanTableBody');
    
    if (!karyawans || karyawans.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-muted py-4">
                    <i class="bi bi-inbox me-2"></i>Belum ada data
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = karyawans.map(k => `
        <tr>
            <td><strong>${k.nip}</strong></td>
            <td>${k.nama_karyawan}</td>
            <td>${k.golongan?.kode_golongan || '-'}</td>
            <td>${k.unit?.nama_pt || '-'}</td>
            <td>
                <span class="status-badge ${k.is_active ? 'status-aktif' : 'status-nonaktif'}">
                    ${k.is_active ? 'Aktif' : 'Nonaktif'}
                </span>
            </td>
            <td>${formatDate(k.tanggal_masuk)}</td>
            <td>
                <div class="d-flex gap-1">
                    <button onclick="editKaryawan(${JSON.stringify(k).replace(/"/g, '&quot;')})" class="btn-action btn-edit" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button onclick="deleteKaryawan(${k.id})" class="btn-action btn-delete" title="Hapus">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

/** Render pagination */
function renderPagination(containerId, pagination) {
    const container = document.getElementById(containerId);
    if (!pagination.links || pagination.links.length <= 2) {
        container.innerHTML = '';
        return;
    }

    let html = '';
    pagination.links.forEach(link => {
        if (link.label.includes('Previous')) {
            html += `<button onclick="loadKaryawanData(${link.url?.split('page=')[1] || 1})" ${!link.url ? 'disabled' : ''}>← Sebelumnya</button>`;
        } else if (link.label.includes('Next')) {
            html += `<button onclick="loadKaryawanData(${link.url?.split('page=')[1] || 1})" ${!link.url ? 'disabled' : ''}>Selanjutnya →</button>`;
        } else {
            const page = parseInt(link.label);
            if (!isNaN(page)) {
                const active = link.active ? 'active' : '';
                html += `<button onclick="loadKaryawanData(${page})" class="${active}">${page}</button>`;
            }
        }
    });

    container.innerHTML = html;
}

/** Edit karyawan - populate form */
function editKaryawan(data) {
    document.getElementById('karyawanId').value = data.id;
    document.getElementById('nipKaryawan').value = data.nip;
    document.getElementById('namaKaryawan').value = data.nama_karyawan;
    document.getElementById('nikKaryawan').value = data.nik || '';
    document.getElementById('tglLahirKaryawan').value = data.tanggal_lahir || '';
    document.getElementById('jenisKelaminKaryawan').value = data.jenis_kelamin || '';
    document.getElementById('statusKawinKaryawan').value = data.id_status_kawin || '';
    document.getElementById('emailKaryawan').value = data.email || '';
    document.getElementById('noTelpKaryawan').value = data.nomor_telepon || '';
    document.getElementById('alamatKaryawan').value = data.alamat || '';
    document.getElementById('golonganKaryawan').value = data.id_golongan || '';
    document.getElementById('unitKaryawan').value = data.id_unit || '';
    document.getElementById('tglMasukKaryawan').value = data.tanggal_masuk || '';
    document.getElementById('statusKarKaryawan').value = data.id_status_karyawan || '';
    document.getElementById('isActiveKaryawan').checked = data.is_active ?? true;

    document.getElementById('modalKaryawanTitle').textContent = 'Edit Karyawan';
    document.getElementById('formKaryawan').setAttribute('action', `/personal-admin/karyawan/${data.id}`);
    document.getElementById('formKaryawan').setAttribute('method', 'POST');

    // Add hidden _method field for PUT request
    let methodInput = document.getElementById('methodInput');
    if (!methodInput) {
        methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.id = 'methodInput';
        methodInput.name = '_method';
        document.getElementById('formKaryawan').appendChild(methodInput);
    }
    methodInput.value = 'PUT';

    new bootstrap.Modal(document.getElementById('modalKaryawan')).show();
}

/** Reset form for new karyawan */
function resetFormKaryawan() {
    document.getElementById('formKaryawan').reset();
    document.getElementById('karyawanId').value = '';
    document.getElementById('isActiveKaryawan').checked = true;

    document.getElementById('modalKaryawanTitle').textContent = 'Tambah Karyawan';
    document.getElementById('formKaryawan').setAttribute('action', '/personal-admin/karyawan');
    document.getElementById('formKaryawan').setAttribute('method', 'POST');

    // Remove _method field
    const methodInput = document.getElementById('methodInput');
    if (methodInput) methodInput.remove();

    // Clear error messages
    const errorAlert = document.getElementById('formErrorAlert');
    if (errorAlert) {
        errorAlert.classList.add('d-none');
        document.getElementById('formErrorList').innerHTML = '';
    }
}

/** Delete karyawan with confirmation */
function deleteKaryawan(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus karyawan ini?')) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/personal-admin/karyawan/${id}`;
    form.innerHTML = `
        <input type="hidden" name="_token" value="${CSRF_TOKEN}">
        <input type="hidden" name="_method" value="DELETE">
    `;
    document.body.appendChild(form);
    form.submit();
}

/** Handle form submission */
function handleFormSubmit(e) {
    e.preventDefault();

    const formData = new FormData(document.getElementById('formKaryawan'));
    const action = document.getElementById('formKaryawan').getAttribute('action');
    const method = document.getElementById('formKaryawan').getAttribute('method') || 'POST';

    const isUpdate = method === 'POST' && document.getElementById('methodInput')?.value === 'PUT';
    
    fetch(action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json',
        },
        body: formData,
    })
    .then(r => {
        if (!r.ok) return r.json().then(err => { throw err; });
        return r.json();
    })
    .then(data => {
        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('modalKaryawan'))?.hide();
        
        // Show success message (reload page to show message)
        window.location.reload();
    })
    .catch(err => {
        const errorAlert = document.getElementById('formErrorAlert');
        const errorList = document.getElementById('formErrorList');
        
        if (err.errors) {
            errorList.innerHTML = Object.values(err.errors)
                .flat()
                .map(e => `<li>${e}</li>`)
                .join('');
        } else {
            errorList.innerHTML = `<li>${err.message || 'Terjadi kesalahan'}</li>`;
        }
        
        errorAlert.classList.remove('d-none');
    });
}

// ───────────────────────────────────────────────────────
// EVENT LISTENERS
// ───────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', () => {
    // Load initial data
    loadKaryawanData();

    // Search and filter
    document.getElementById('searchKaryawan')?.addEventListener('input', () => loadKaryawanData());
    document.getElementById('filterGolongan')?.addEventListener('change', () => loadKaryawanData());
    document.getElementById('filterUnit')?.addEventListener('change', () => loadKaryawanData());

    // Form submission
    document.getElementById('formKaryawan')?.addEventListener('submit', handleFormSubmit);

    // Close error alert
    document.querySelectorAll('.alert .btn-close').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.alert').remove();
        });
    });
});

// ───────────────────────────────────────────────────────
// UTILITY FUNCTIONS
// ───────────────────────────────────────────────────────

/** Format date to DD/MM/YYYY */
function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { 
        year: 'numeric', 
        month: '2-digit', 
        day: '2-digit' 
    });
}

/** Show notification */
function showNotification(message, type = 'success') {
    const alertClass = `alert-${type}`;
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show mt-3" role="alert">
            <i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    document.querySelector('.page-wrapper').insertAdjacentHTML('afterbegin', alertHtml);
}
