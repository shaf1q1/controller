<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<title>Pengurusan Dokumen Modul</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<style>
body { background-color: #f0f8ff; }
h3 { color: #1e3a8a; margin-bottom: 20px; }
.table-responsive { margin-top: 20px; }
table.table thead { background-color: #cfe2ff; color: #0d3b66; cursor: pointer; }
table.table tbody tr:hover { background-color: #e6f0ff; }
.form-control, .form-select { border-radius: 0.375rem; }
.btn-primary { background-color: #3b82f6; border-color: #3b82f6; }
.btn-warning { background-color: #facc15; border-color: #facc15; }
.btn-danger { background-color: #ef4444; border-color: #ef4444; }
.alert { margin-top: 10px; }
.pagination .page-item.active .page-link { background-color: #3b82f6; border-color: #3b82f6; color: white; }
.pagination { margin-top: 10px; }
@media(max-width:768px){
    table.table { font-size: 0.9rem; }
    .form-label { font-size: 0.9rem; }
}
</style>
</head>
<body class="p-4">
<div class="container-fluid">
<h3>Pengurusan Dokumen Modul</h3>

<!-- Tambah Dokumen -->
<div class="card p-3 mb-3">
    <form id="formTambah" method="post" enctype="multipart/form-data" action="/dokumen/tambah">
        <input type="hidden" name="idservis" id="idservisTambah">
        <div class="row">
            <div class="col-md-4 mb-2">
                <label class="form-label">Pilih Servis</label>
                <select id="dropdownServis" class="form-select">
                    <option value="">-- Pilih Servis --</option>
                    <?php foreach($servis as $s): ?>
                        <option value="<?= $s['idservis'] ?>"><?= $s['namaservis'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <label class="form-label">Nama Dokumen</label>
                <input type="text" name="namafail" class="form-control" required>
            </div>
            <div class="col-md-4 mb-2">
                <label class="form-label">Catatan</label>
                <input type="text" name="descdoc" class="form-control">
            </div>
            <div class="col-md-4 mb-2">
                <label class="form-label">Pilih Fail</label>
                <input type="file" name="file" class="form-control" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Tambah</button>
    </form>
</div>

<!-- Search -->
<div class="mb-3 col-md-4">
    <input type="text" id="searchDokumen" class="form-control" placeholder="Cari dokumen / catatan...">
</div>

<!-- Table -->
<div id="dokumenArea" class="table-responsive"></div>
<nav>
    <ul class="pagination justify-content-center" id="dokumenPagination"></ul>
</nav>
</div>

<!-- Modal Edit Dokumen -->
<div class="modal fade" id="modalEdit" tabindex="-1">
  <div class="modal-dialog">
    <form id="formEdit" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Kemaskini Dokumen</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="iddoc" id="editIdDoc">
          <div class="mb-2">
            <label>Nama Fail</label>
            <input type="text" name="namafail" id="editNamaFail" class="form-control" required>
          </div>
          <div class="mb-2">
            <label>Catatan</label>
            <textarea name="descdoc" id="editDescdoc" class="form-control"></textarea>
          </div>
          <div class="mb-2">
            <label>Ganti Fail (optional)</label>
            <input type="file" name="file" id="editFile" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
let currentPage = 1;
let perPage = 20;
let sortField = 'created_at';
let sortOrder = 'desc';

// Format datetime Malaysia
function formatMalaysia(dt){
    if(!dt) return '-';
    let d = new Date(dt);
    let offset = 8*60;
    let local = new Date(d.getTime() + offset*60*1000);
    return local.toISOString().slice(0,19).replace('T',' ');
}

// Refresh table
function refreshTable(idservis, page=1){
    if(!idservis){ $('#dokumenArea').html(''); $('#dokumenPagination').html(''); return; }
    currentPage = page;
    let search = $('#searchDokumen').val();
    $.get(`/dokumen/getDokumen/${idservis}?page=${page}&perPage=${perPage}&search=${search}&sortField=${sortField}&sortOrder=${sortOrder}`, function(res){
        let data = (typeof res==='string')? JSON.parse(res):res;
        let dok = data.items;
        let total = data.total;
        if(!dok || dok.length===0){
            $('#dokumenArea').html('<div class="alert alert-warning">Tiada rekod</div>');
            $('#dokumenPagination').html('');
            return;
        }

        let table = '<table class="table table-bordered table-striped table-hover">';
        table += '<thead><tr>';
        table += `<th onclick="changeSort('nama')">Nama Fail <i class="bi bi-arrow-down-up"></i></th>`;
        table += `<th>Jenis</th>`;
        table += `<th onclick="changeSort('descdoc')">Catatan <i class="bi bi-arrow-down-up"></i></th>`;
        table += `<th onclick="changeSort('created_at')">Dicipta Pada <i class="bi bi-arrow-down-up"></i></th>`;
        table += `<th onclick="changeSort('tkhkemas')">Dikemaskini Pada <i class="bi bi-arrow-down-up"></i></th>`;
        table += '<th>Aksi</th></tr></thead><tbody>';

        dok.forEach(d=>{
            table += `<tr>
                <td>${d.nama}</td>
                <td>${d.mime}</td>
                <td>${d.descdoc}</td>
                <td>${formatMalaysia(d.created_at)}</td>
                <td>${formatMalaysia(d.tkhkemas)}</td>
                <td>
                    <button class="btn btn-sm btn-warning me-1 mb-1 btn-edit" data-iddoc="${d.iddoc}">Edit</button>
                    <button class="btn btn-sm btn-danger mb-1 btn-remove" data-iddoc="${d.iddoc}">Remove</button>
                </td>
            </tr>`;
        });

        table += '</tbody></table>';
        $('#dokumenArea').html(table);

        // Pagination
        let totalPages = Math.ceil(total/perPage);
        let html='';
        for(let i=1;i<=totalPages;i++){
            html += `<li class="page-item ${i===page?'active':''}">
                        <a class="page-link" href="#" onclick="refreshTable(${idservis},${i}); return false;">${i}</a>
                     </li>`;
        }
        $('#dokumenPagination').html(html);
    });
}

// Sorting
function changeSort(field){
    if(sortField===field) sortOrder = sortOrder==='asc'?'desc':'asc';
    else{ sortField=field; sortOrder='asc'; }
    refreshTable($('#dropdownServis').val(),1);
}

// Dropdown change
$('#dropdownServis').change(function(){
    let id = $(this).val();
    $('#idservisTambah').val(id);
    refreshTable(id,1);
});

// Search
$('#searchDokumen').on('input',function(){ refreshTable($('#dropdownServis').val(),1); });

// Event delegation for Edit
$(document).on('click','.btn-edit',function(){
    let iddoc = $(this).data('iddoc');
    $.get('/dokumen/getDokumenById/'+iddoc,function(res){
        let data = (typeof res==='string')? JSON.parse(res): res;
        if(data.error){ alert(data.error); return; }
        $('#editIdDoc').val(data.iddoc);
        $('#editNamaFail').val(data.namafail);
        $('#editDescdoc').val(data.descdoc);
        $('#editFile').val('');
        let modalEl = document.getElementById('modalEdit');
        let modal = new bootstrap.Modal(modalEl);
        modal.show();
    });
});

// Event delegation for Remove
$(document).on('click','.btn-remove',function(){
    let iddoc = $(this).data('iddoc');
    if(confirm('Adakah anda pasti mahu padam?')){
        $.get('/dokumen/remove/'+iddoc,function(res){
            let r = (typeof res==='string')? JSON.parse(res):res;
            alert(r.status?'Berjaya dipadam':'Gagal dipadam');
            refreshTable($('#dropdownServis').val(), currentPage);
        });
    }
});

// Tambah dokumen AJAX
$('#formTambah').submit(function(e){
    e.preventDefault();
    let formData = new FormData(this);
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: formData,
        processData:false,
        contentType:false,
        success: function(res){
            let data = (typeof res==='string')? JSON.parse(res): res;
            alert(data.msg || 'Dokumen berjaya ditambah');
            $('#formTambah')[0].reset();
            refreshTable($('#dropdownServis').val(), currentPage);
        },
        error: function(){ alert('Gagal tambah dokumen'); }
    });
});

// Edit dokumen AJAX
$('#formEdit').submit(function(e){
    e.preventDefault();
    let iddoc = $('#editIdDoc').val();
    let formData = new FormData(this);
    $.ajax({
        url: '/dokumen/kemaskini/' + iddoc,
        type: 'POST',
        data: formData,
        processData:false,
        contentType:false,
        success: function(res){
            let data = (typeof res==='string')? JSON.parse(res): res;
            alert(data.msg || (data.status ? 'Berjaya dikemaskini':'Gagal dikemaskini'));
            if(data.status){
                let modalEl = document.getElementById('modalEdit');
                let modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();
                refreshTable($('#dropdownServis').val(), currentPage);
            }
        },
        error: function(){ alert('Gagal kemaskini dokumen'); }
    });
});
</script>
</body>
</html>
