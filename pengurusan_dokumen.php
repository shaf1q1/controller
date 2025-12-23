<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<title>Pengurusan Dokumen Modul</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
body { background-color: #f8f9fa; }
h3 { color: #0d6efd; margin-bottom: 20px; }
.table-hover tbody tr:hover { background-color: #e9ecef; }
.badge { font-size: 0.9em; }
.status-pending { background-color: #fef3c7; color: #b45309; }
.status-approved { background-color: #d1fae5; color: #065f46; }
.status-rejected { background-color: #fee2e2; color: #b91c1c; }
.status-deleted { background-color: #f87171; color: #fff; }
</style>
</head>
<body class="p-4">
<div class="container">
<h3>Pengurusan Dokumen Modul</h3>

<!-- Pilih Servis -->
<div class="mb-3">
    <label class="form-label">Pilih Servis</label>
    <select id="dropdownServis" class="form-select">
        <option value="">-- Pilih Servis --</option>
        <?php foreach($servis as $s): ?>
            <option value="<?= esc($s['idservis']) ?>"><?= esc($s['namaservis']) ?></option>
        <?php endforeach; ?>
    </select>
</div>

<!-- Button Tambah -->
<div class="mb-3">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-circle"></i> Tambah Dokumen
    </button>
</div>

<!-- Table Container -->
<div id="dokumenArea" class="table-responsive"></div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
<form id="formTambah" enctype="multipart/form-data">
<div class="modal-header">
    <h5 class="modal-title">Tambah Dokumen</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <input type="hidden" name="idservis" id="inputServisTambah">
    <div class="mb-3">
        <label class="form-label">Nama Dokumen</label>
        <input type="text" name="nama" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Catatan</label>
        <input type="text" name="descdoc" class="form-control">
    </div>
    <div class="mb-3">
        <label class="form-label">Pilih Fail</label>
        <input type="file" name="file" class="form-control" required>
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary">Simpan</button>
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
</div>
</form>
</div>
</div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
<form id="formEdit" enctype="multipart/form-data">
<div class="modal-header">
    <h5 class="modal-title">Edit Dokumen</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <input type="hidden" name="iddoc" id="edit_iddoc">
    <div class="mb-3">
        <label class="form-label">Nama Dokumen</label>
        <input type="text" name="nama" id="edit_nama" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Catatan</label>
        <input type="text" name="descdoc" id="edit_desc" class="form-control">
    </div>
    <div class="mb-3">
        <label class="form-label">Tukar Fail (Optional)</label>
        <input type="file" name="file" id="edit_file" class="form-control">
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
</div>
</form>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function formatDate(dateString) {
    if (!dateString) return '-';
    try {
        const d = new Date(dateString);
        const opt = {
            timeZone: "Asia/Kuala_Lumpur",
            day: "2-digit",
            month: "2-digit",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
            hour12: false
        };
        return new Intl.DateTimeFormat("en-GB", opt).format(d).replace(",", "");
    } catch {
        return dateString;
    }
}

function refreshTable(idservis){
    if(!idservis){ $('#dokumenArea').html(''); return; }
    $.get(`/dokumen/getDokumen/${idservis}`, function(res){
        let data = (typeof res==='string')?JSON.parse(res):res;
        if(!data.items || data.items.length===0){
            $('#dokumenArea').html('<div class="alert alert-warning">Tiada rekod</div>');
            return;
        }
        let html = `<table class="table table-bordered table-hover">
            <thead><tr>
                <th>Nama Fail</th><th>Jenis</th><th>Catatan</th>
                <th>Dicipta</th><th>Dikemaskini</th><th>Status</th><th>Aksi</th>
            </tr></thead><tbody>`;
        data.items.forEach(d=>{
            let statusClass='', statusLabel='';

            // **Integrate with approvaldokumen status**
            // If d.status not defined, default to pending
            const status = d.status ?? 'pending';
            if(d.deleted_at){ 
                statusClass='badge bg-danger'; statusLabel='Dihapus'; 
            }
            else if(status==='approved'){ 
                statusClass='badge bg-success'; statusLabel='Approved'; 
            }
            else if(status==='rejected'){ 
                statusClass='badge bg-danger'; statusLabel='Rejected'; 
            }
            else { 
                statusClass='badge bg-warning text-dark'; statusLabel='Pending'; 
            }

            let statusHTML=`<span class="${statusClass}">${statusLabel}</span>`;

            // Actions only Edit + Soft Delete / Restore
            let actions=`<button class="btn btn-sm btn-primary" onclick="editDokumen(${d.iddoc})">
                <i class="bi bi-pencil-square"></i> Edit
            </button>`;
            if(!d.deleted_at){
                actions += `<button class="btn btn-sm btn-warning ms-1" onclick="softDeleteDokumen(${d.iddoc})">Padam</button>`;
            } else {
                actions += `<button class="btn btn-sm btn-info ms-1" onclick="restoreDokumen(${d.iddoc})">Pulih</button>`;
            }

            html+=`<tr>
                <td>${d.nama}</td><td>${d.mime}</td><td>${d.descdoc||'-'}</td>
                <td>${formatDate(d.created_at)}</td><td>${formatDate(d.updated_at)}</td>
                <td>${statusHTML}</td><td>${actions}</td>
            </tr>`;
        });
        html += `</tbody></table>`;
        $('#dokumenArea').html(html);
    }).fail(()=>{ $('#dokumenArea').html('<div class="alert alert-danger">Gagal memuatkan data</div>'); });
}

$('#dropdownServis').change(function(){
    let val=$(this).val();
    $('#inputServisTambah').val(val);
    refreshTable(val);
});

// Tambah dokumen
$('#formTambah').submit(function(e){
    e.preventDefault();
    let idservis=$('#inputServisTambah').val();
    if(!idservis){ Swal.fire('Sila pilih servis dahulu'); return; }
    let formData = new FormData(this);
    $.ajax({
        url:'/dokumen/tambah',
        type:'POST',
        data:formData,
        processData:false,
        contentType:false,
        success:function(res){
            if(res.status){
                Swal.fire('Berjaya', res.msg,'success');
                $('#formTambah')[0].reset();
                bootstrap.Modal.getInstance(document.getElementById('modalTambah')).hide();
                refreshTable(idservis);
            } else Swal.fire('Gagal', res.msg,'error');
        },
        error:function(){ Swal.fire('Gagal','Server error','error'); }
    });
});

// Edit dokumen
function editDokumen(iddoc){
    $.get(`/dokumen/edit/${iddoc}`, function(res){
        let data = (typeof res==='string')?JSON.parse(res):res;
        if(data.status){
            let d = data.data;
            $('#edit_iddoc').val(d.iddoc);
            $('#edit_nama').val(d.nama);
            $('#edit_desc').val(d.descdoc);
            $('#edit_file').val('');
            new bootstrap.Modal(document.getElementById('modalEdit')).show();
        }else{
            Swal.fire('Gagal','Rekod tidak dijumpai','error');
        }
    }).fail(()=>{ Swal.fire('Gagal','Server error','error'); });
}

// Kemaskini dokumen
$('#formEdit').submit(function(e){
    e.preventDefault();
    let iddoc = $('#edit_iddoc').val();
    let formData = new FormData(this);
    $.ajax({
        url:`/dokumen/kemaskini/${iddoc}`,
        type:'POST',
        data:formData,
        processData:false,
        contentType:false,
        success:function(res){
            if(res.status){
                Swal.fire('Berjaya',res.msg,'success');
                bootstrap.Modal.getInstance(document.getElementById('modalEdit')).hide();
                refreshTable($('#dropdownServis').val());
            }else Swal.fire('Gagal',res.msg,'error');
        },
        error:function(){ Swal.fire('Gagal','Server error','error'); }
    });
});

// Soft delete
function softDeleteDokumen(iddoc){
    $.post(`/dokumen/softDelete/${iddoc}`,function(r){
        if(r.status){ Swal.fire('Berjaya',r.msg,'success'); refreshTable($('#dropdownServis').val()); }
        else Swal.fire('Gagal',r.msg,'error');
    });
}

// Restore
function restoreDokumen(iddoc){
    $.post(`/dokumen/restore/${iddoc}`,function(r){
        if(r.status){ Swal.fire('Berjaya',r.msg,'success'); refreshTable($('#dropdownServis').val()); }
        else Swal.fire('Gagal',r.msg,'error');
    });
}
</script>
</body>
</html>
