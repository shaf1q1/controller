<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-name" content="<?= csrf_token() ?>">
    <meta name="csrf-hash" content="<?= csrf_hash() ?>">

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>

<!-- ======= CSS ======= -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body { display:flex; font-family: 'Segoe UI', sans-serif; background:#f5f6fa; min-height:100vh; margin:0; transition:0.3s; }
#sidebar { width:250px; background:#212529; color:#fff; position:fixed; top:0; bottom:0; left:0; padding:20px 10px; flex-direction:column; overflow-y:auto; transition:width 0.3s; }
#sidebar.collapsed { width:70px; }
#sidebar .nav-link { color:#cfd3d7; display:flex; align-items:center; justify-content:space-between; padding:8px 15px; border-radius:.5rem; margin:2px 0; transition:0.2s; }
#sidebar .nav-link:hover { background:#343a40; color:#fff; transform:scale(1.05); }
#sidebar .nav-link.active { background:#0d6efd; color:#fff; }
#sidebar .link-text { transition:0.2s; }
#sidebar.collapsed .link-text { display:none; }
.submenu { max-height:0; overflow:hidden; flex-direction:column; transition:max-height 0.3s; }
.submenu.show { max-height:500px; }
#sidebarToggle { width:100%; margin-bottom:15px; }
#main { margin-left:250px; padding:30px; width:100%; transition:margin-left 0.3s; }
#sidebar.collapsed ~ #main { margin-left:70px; }
.card { border:none; border-radius:1rem; box-shadow:0 4px 14px rgba(0,0,0,.07); transition:0.25s; }
.card:hover { transform:translateY(-5px); box-shadow:0 8px 20px rgba(0,0,0,0.15); }
body.dark { background:#1b1d1f; color:#e4e6eb; }
body.dark #sidebar { background:#111; }
body.dark .card { background:#2a2d31; color:#fff; }
body.dark table, body.dark th, body.dark td { color:#fff; }
</style>
</head>
<body>
<meta name="csrf-name" content="<?= csrf_token() ?>">
<meta name="csrf-hash" content="<?= csrf_hash() ?>">

<!-- ======= Sidebar ======= -->
<div id="sidebar">
    <button id="sidebarToggle" class="btn btn-light mb-3 w-100"><i class="bi bi-list"></i></button>
    <div class="d-flex justify-content-between align-items-center mb-3 px-2">
        <span class="fs-4 fw-bold text-white">DASHBOARD</span>
        <i class="bi bi-brightness-high-fill" id="themeToggle" style="cursor:pointer;font-size:22px;color:#ffc107;"></i>
    </div>
    <hr class="border-secondary">
    <?php $uri = service('uri')->getSegment(1); ?>
    <ul class="nav nav-pills flex-column mb-auto" id="sidebarMenu">
        <li><a href="/" class="nav-link <?= $uri == '' ? 'active':'' ?>"><i class="bi bi-speedometer2 me-2 fs-5"></i><span class="link-text">Dashboard</span></a></li>

        <li>
            <a href="#" class="nav-link has-submenu">
                <i class="bi bi-list-task me-2 fs-5"></i><span class="link-text">Perincian Modul</span>
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            
            <ul class="submenu nav flex-column ps-3">
                <li><a href="/perincianmodul" class="nav-link">All Modules</a></li>
                <li><a href="/tambahanperincian/add" class="nav-link">Add Module</a></li>
            </ul>
        </li>
        <li><a href="/dokumen" class="nav-link <?= $uri == 'dokumen' ? 'active':'' ?>"><i class="bi bi-folder2-open me-2 fs-5"></i><span class="link-text">Dokumen</span></a></li>
        <li><a href="/approvaldokumen" class="nav-link <?= $uri == 'approvaldokumen' ? 'active':'' ?>"><i class="bi bi-check2-circle me-2 fs-5"></i><span class="link-text">Approval Dokumen</span></a></li>
        <li><a href="/serviskelulusan" class="nav-link <?= $uri == 'serviskelulusan' ? 'active':'' ?>"><i class="bi bi-wrench me-2 fs-5"></i><span class="link-text">Servis Kelulusan</span></a></li>
    </ul>
    <hr class="border-secondary mt-auto">
    <div class="dropdown px-2 pb-3">
        <a href="#" class="text-white text-decoration-none dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle fs-4 me-2"></i>
            <strong><?= session()->get('username') ?: 'Admin' ?></strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark">
            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Change Password</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="/auth/logout">Logout</a></li>
        </ul>
    </div>
</div>

<!-- ======= Main Content ======= -->
<div id="main">
    <h1 class="dashboard-title mb-3">WELCOME, <?= session()->get('username') ?: '' ?><span class="badge bg-primary ms-2">Admin</span></h1>

    <!-- ======= Summary Cards ======= -->
    <div class="row g-4 mb-4">
        <div class="col-md-3"><div class="card p-3 text-white" style="background: linear-gradient(45deg,#007bff,#00c6ff);"><div class="d-flex justify-content-between align-items-center"><div><h5>Total Users</h5><p class="fs-2 fw-bold" id="totalUsers"><?= $totalUsers ?? 0 ?></p></div><i class="bi bi-people fs-1 opacity-75"></i></div></div></div>
        <div class="col-md-3"><div class="card p-3 text-white" style="background: linear-gradient(45deg,#28a745,#85e085);"><div class="d-flex justify-content-between align-items-center"><div><h5>Total Servis</h5><p class="fs-2 fw-bold" id="totalServis"><?= $totalServis ?? 0 ?></p></div><i class="bi bi-wrench fs-1 opacity-75"></i></div></div></div>
        <div class="col-md-3"><div class="card p-3 text-dark" style="background: linear-gradient(45deg,#ffc107,#ffe085);"><div class="d-flex justify-content-between align-items-center"><div><h5>Servis Pending</h5><p class="fs-2 fw-bold" id="totalPending"><?= $totalServisPending ?? 0 ?></p></div><i class="bi bi-hourglass-split fs-1 opacity-75"></i></div></div></div>
        <div class="col-md-3"><div class="card p-3 text-white" style="background: linear-gradient(45deg,#17a2b8,#85e0f0);"><div class="d-flex justify-content-between align-items-center"><div><h5>Total Dokumen</h5><p class="fs-2 fw-bold" id="totalDokumen"><?= $totalDokumen ?? 0 ?></p></div><i class="bi bi-folder2-open fs-1 opacity-75"></i></div></div></div>
    </div>

    <!-- ======= Charts ======= -->
    <div class="row mb-4">
        <div class="col-md-6"><div class="card p-3"><h5>Servis per Month</h5><canvas id="monthlyChart"></canvas></div></div>
        <div class="col-md-6"><div class="card p-3"><h5>Servis Status</h5><canvas id="servisChart"></canvas></div></div>
    </div>

    <!-- ======= Users Table ======= -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-bold">Users Management</span>
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#userModal" id="addUserBtn"><i class="bi bi-plus-circle"></i> Add User</button>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle" id="userTable">
                <thead class="table-light">
                    <tr><th>#</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- ======= Modals ======= -->
<!-- Change Password -->
<div class="modal fade" id="changePasswordModal" tabindex="-1"><div class="modal-dialog"><form id="changePasswordForm" class="modal-content"><div class="modal-header"><h5 class="modal-title">Change Password</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><div class="mb-3"><label class="form-label">Old Password</label><input type="password" name="oldPassword" class="form-control" required></div><div class="mb-3"><label class="form-label">New Password</label><input type="password" name="newPassword" class="form-control" required></div><div class="mb-3"><label class="form-label">Confirm Password</label><input type="password" name="confirmPassword" class="form-control" required></div></div><div class="modal-footer"><button class="btn btn-primary">Update Password</button><button class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div></form></div></div>

<!-- Add/Edit User -->
<div class="modal fade" id="userModal" tabindex="-1"><div class="modal-dialog"><form id="userForm" class="modal-content"><div class="modal-header"><h5 class="modal-title" id="userModalTitle">Add User</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><input type="hidden" name="userId"><div class="mb-3"><label class="form-label">Username</label><input type="text" name="username" class="form-control" required></div><div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div><div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control"></div><div class="mb-3"><label class="form-label">Role</label><select name="role" class="form-select"><option value="admin">Admin</option><option value="uploader">Uploader</option></select></div><div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select></div></div><div class="modal-footer"><button class="btn btn-primary" type="submit" id="userSaveBtn">Save</button><button class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div></form></div></div>

<!-- ======= JS ======= -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function(){

    // Sidebar toggle & dark mode
    $('#sidebarToggle').click(()=>$('#sidebar').toggleClass('collapsed'));
    const darkMode = localStorage.getItem('darkMode')==='true';
    if(darkMode) $('body').addClass('dark');
    $('#themeToggle').click(function(){
        $('body').toggleClass('dark');
        localStorage.setItem('darkMode',$('body').hasClass('dark'));
        $(this).toggleClass('bi-brightness-high-fill bi-moon-stars-fill');
        updateChartColors();
    });

    $('.has-submenu').click(function(e){ 
        e.preventDefault(); 
        const submenu=$(this).next('.submenu'); 
        $('.submenu').not(submenu).removeClass('show'); 
        submenu.toggleClass('show'); 
    });

    // CSRF
    const csrfName = $('meta[name="csrf-name"]').attr('content');
    let csrfHash = $('meta[name="csrf-hash"]').attr('content');

    // Users DataTable
    const userTable = $('#userTable').DataTable({
        ajax: '/users/getAll',
        columns: [
            { data:null },
            { data:'username' },
            { data:'email' },
            { data:'role', render:r=>`<span class="badge bg-info">${r}</span>` },
            { data:'status', render:s=>`<span class="badge ${s==='active'?'bg-success':'bg-danger'}">${s}</span>` },
            { data:null, render:(d,t,r)=>`
                <button class="btn btn-sm btn-primary editBtn" data-id="${r.id}"><i class="bi bi-pencil"></i></button>
                <button class="btn btn-sm btn-danger deleteBtn" data-id="${r.id}"><i class="bi bi-trash"></i></button>` }
        ],
        columnDefs: [{ targets:0, render:(data,type,row,meta)=>meta.row+1 }],
        pageLength:5,
        lengthMenu:[5,10,25],
        responsive:true
    });

    const userModalEl = document.getElementById('userModal'),
          userModal   = new bootstrap.Modal(userModalEl);

    // Add User Button
    $('#addUserBtn').click(function(){
        $('#userModalTitle').text('Add User');
        $('#userForm')[0].reset();
        $('input[name=userId]').val('');
    });

    // Edit User
    $('#userTable').on('click','.editBtn', function(){
        const id = $(this).data('id');
        $.getJSON(`/users/${id}`, function(data){
            $('#userModalTitle').text('Edit User');
            $('input[name=userId]').val(data.id);
            $('input[name=username]').val(data.username);
            $('input[name=email]').val(data.email);
            $('select[name=role]').val(data.role);
            $('select[name=status]').val(data.status);
            $('input[name=password]').val('');
            userModal.show();
        }).fail(()=>Swal.fire('Error','Failed to load user data','error'));
    });

    // Delete User
    $('#userTable').on('click','.deleteBtn', function(){
        const id = $(this).data('id');
        Swal.fire({
            title:'Are you sure?',
            text:"This action cannot be undone!",
            icon:'warning',
            showCancelButton:true,
            confirmButtonText:'Yes, delete it!',
            cancelButtonText:'Cancel'
        }).then((result)=>{
            if(result.isConfirmed){
                let postData = {};
                postData[csrfName] = csrfHash;
                $.post(`/users/delete/${id}`, postData, function(res){
                    csrfHash = res.csrfHash; // update CSRF
                    Swal.fire('Deleted!','User has been deleted.','success');
                    userTable.ajax.reload(null,false);
                }, 'json').fail(()=>Swal.fire('Error','Failed to delete user','error'));
            }
        });
    });

    // Add / Update User
    $('#userForm').submit(function(e){
        e.preventDefault();
        const id = $('input[name=userId]').val();
        const url = id ? `/users/update/${id}` : '/users/add';

        let formData = $(this).serializeArray();
        formData.push({ name: csrfName, value: csrfHash });

        $.ajax({
            url: url,
            type: 'POST',
            data: $.param(formData),
            dataType: 'json',
            success: function(res){
                csrfHash = res.csrfHash; // update CSRF
                if(res.status){
                    Swal.fire('Success', res.message, 'success');
                    userModal.hide();
                    $('#userForm')[0].reset();
                    userTable.ajax.reload(null,false);
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error: function(xhr){
                Swal.fire('Error','Server error: '+xhr.responseText,'error');
            }
        });
    });

    // Charts (remain unchanged)
    const monthlyChart = new Chart(document.getElementById('monthlyChart').getContext('2d'), { 
        type:'line', 
        data:{labels:[], datasets:[{label:'Servis', data:[], borderColor:'#0d6efd', backgroundColor:'rgba(13,110,253,0.2)'}]}, 
        options:{responsive:true, scales:{y:{beginAtZero:true}}} 
    });
    const servisChart = new Chart(document.getElementById('servisChart').getContext('2d'), { 
        type:'bar', 
        data:{labels:[], datasets:[{label:'Servis Status', data:[], backgroundColor:['#ffc107','#28a745','#dc3545','#17a2b8','#6c757d']}]}, 
        options:{responsive:true, scales:{y:{beginAtZero:true, stepSize:1}}} 
    });

    function updateChartColors(){
        if($('body').hasClass('dark')){
            monthlyChart.data.datasets[0].borderColor='#ffc107';
            monthlyChart.data.datasets[0].backgroundColor='rgba(255,193,7,0.3)';
        } else {
            monthlyChart.data.datasets[0].borderColor='#0d6efd';
            monthlyChart.data.datasets[0].backgroundColor='rgba(13,110,253,0.2)';
        }
        monthlyChart.update();
    }

    function loadDashboardCharts(){
        $.getJSON('/dashboard/getData', function(res){
            $('#totalUsers').text(res.summary.totalUsers);
            $('#totalServis').text(res.summary.totalServis);
            $('#totalPending').text(res.summary.totalServisPending);
            $('#totalDokumen').text(res.summary.totalDokumen);

            monthlyChart.data.labels=res.charts.monthly.labels;
            monthlyChart.data.datasets[0].data=res.charts.monthly.data;
            monthlyChart.update();

            servisChart.data.labels=res.charts.status.labels;
            servisChart.data.datasets[0].data=res.charts.status.data;
            servisChart.update();
        });
    }

    loadDashboardCharts();
    setInterval(loadDashboardCharts,60000);

});
</script>
</body>
</html>
