<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<title>Approval Dokumen</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
<style>
body { font-family: 'Inter', sans-serif; background-color: #f4f6f8; }
.status-pending, .status-approved, .status-rejected {
    display:flex; align-items:center; justify-content:center; gap:4px; padding:2px 6px; border-radius:12px; font-weight:600;
}
.status-pending { background-color: #fef3c7; color: #b45309; }
.status-approved { background-color: #d1fae5; color: #065f46; }
.status-rejected { background-color: #fee2e2; color: #b91c1c; }
th.sortable { cursor: pointer; }
th.sortable:hover { background-color: #e2e8f0; }
#viewModal { transition: all 0.3s ease-in-out; }
.pagination button { min-width: 36px; }
.lottie-small { width:20px; height:20px; display:none; }
.status-wrapper:hover .lottie-small { display:block; }
</style>
</head>
<body class="min-h-screen p-8">

<div class="max-w-7xl mx-auto bg-white shadow-lg rounded-lg p-6">
    <!-- Header with Lottie Animation -->
    <div class="flex flex-col items-center mb-6">
        <lottie-player 
            src="https://assets9.lottiefiles.com/packages/lf20_4kx2q32n.json"
            background="transparent" speed="1" style="width:200px; height:200px;" loop autoplay>
        </lottie-player>
        <h1 class="text-3xl font-bold text-gray-800 mt-4 text-center">Sistem Approval Dokumen</h1>
    </div>

    <!-- Filter & Search -->
    <div class="mb-4 flex justify-between items-center">
        <div>
            <select id="filterStatus" class="border p-2 rounded focus:outline-none focus:ring-2 focus:ring-purple-400">
                <option value="all">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
        <input type="text" id="searchDokumen" placeholder="Search Dokumen..." class="border p-2 rounded w-1/3 focus:outline-none focus:ring-2 focus:ring-purple-400">
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto border border-gray-200 rounded shadow-sm" id="dokumenTable">
            <thead class="bg-purple-100 text-gray-700">
                <tr>
                    <th class="border p-2 text-center">No</th>
                    <th class="border p-2 sortable text-left" data-column="nama">Nama Dokumen</th>
                    <th class="border p-2">MIME</th>
                    <th class="border p-2 sortable text-center" data-column="status">Status</th>
                    <th class="border p-2 sortable text-left" data-column="created_at">Tarikh Hantar</th>
                    <th class="border p-2 text-center">Tindakan</th>
                </tr>
            </thead>
            <tbody class="text-gray-600"></tbody>
        </table>
    </div>

    <!-- Numeric Pagination -->
    <div class="mt-4 flex justify-center items-center space-x-1 pagination"></div>
</div>

<!-- Modal View Dokumen -->
<div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg w-3/4 max-w-2xl p-6 shadow-lg relative">
        <h2 class="text-xl font-bold mb-4">Maklumat Dokumen</h2>
        <div id="dokumenDetails" class="mb-4 space-y-2"></div>
        <div class="flex justify-end gap-3">
            <button id="closeViewModal" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 transition">Tutup</button>
        </div>
    </div>
</div>

<!-- Lottie Success Animation (hidden) -->
<lottie-player id="successAnimation" 
    src="https://assets10.lottiefiles.com/packages/lf20_jbrw3hcz.json"
    background="transparent" speed="1" 
    style="width:150px; height:150px; position: fixed; top:50%; left:50%; transform:translate(-50%, -50%); z-index:1000; display:none;"
    autoplay></lottie-player>

<script>
d<script>
document.addEventListener('DOMContentLoaded', () => {
    const tbody = document.querySelector('#dokumenTable tbody');
    const searchInput = document.getElementById('searchDokumen');
    const filterStatus = document.getElementById('filterStatus');
    const paginationContainer = document.querySelector('.pagination');
    const viewModal = document.getElementById('viewModal');
    const dokumenDetails = document.getElementById('dokumenDetails');
    const closeViewModal = document.getElementById('closeViewModal');
    const successAnimation = document.getElementById('successAnimation');

    let currentPage = 1;
    let totalPages = 1;
    const limit = 10;
    let sortColumn = '';
    let sortOrder = 'asc';

    // CSRF token for POST requests
    const csrfName = document.querySelector('meta[name="csrf-name"]').getAttribute('content');
    const csrfHash = document.querySelector('meta[name="csrf-hash"]').getAttribute('content');

    async function loadData(page = 1) {
        const status = filterStatus.value;
        try {
            const res = await fetch(`<?= base_url("approvaldokumen/getAll") ?>?status=${status}&page=${page}`);
            const result = await res.json();
            console.log('AJAX Result:', result); // <--- debug

            if(!result || !result.data) { tbody.innerHTML = '<tr><td colspan="6" class="text-center p-4">Tiada rekod ditemui</td></tr>'; return; }

            let data = result.data;

            // Optional sorting
            if(sortColumn){
                data.sort((a,b)=>{
                    let valA = a[sortColumn] ?? '';
                    let valB = b[sortColumn] ?? '';
                    if(sortColumn === 'created_at'){ valA=new Date(valA); valB=new Date(valB);}
                    else { valA = valA.toString().toLowerCase(); valB = valB.toString().toLowerCase();}
                    return sortOrder==='asc'? (valA>valB?1:-1):(valA>valB?-1:1);
                });
            }

            populateTable(data);
            currentPage = result.page ?? 1;
            totalPages = Math.ceil((result.total ?? data.length)/ (result.limit ?? limit));
            renderPagination();
        } catch(err){ console.error('AJAX Error:', err); }
    }

    function populateTable(data){
        tbody.innerHTML='';
        if(data.length===0){
            tbody.innerHTML='<tr><td colspan="6" class="text-center p-4">Tiada rekod ditemui</td></tr>';
            return;
        }
        data.forEach((d, idx)=>{
            const tr=document.createElement('tr');
            tr.classList.add('border-b','hover:bg-purple-50');

            const statusClass = d.status==='approved'?'status-approved':d.status==='rejected'?'status-rejected':'status-pending';

            tr.innerHTML=`
                <td class="border p-2 text-center">${idx+1 + (currentPage-1)*limit}</td>
                <td class="border p-2">${d.nama ?? '-'}</td>
                <td class="border p-2">${d.mime ?? '-'}</td>
                <td class="border p-2 text-center">
                    <span class="${statusClass}">${d.status ?? 'pending'}</span>
                </td>
                <td class="border p-2">${d.created_at ?? '-'}</td>
                <td class="border p-2 text-center flex justify-center gap-2">
                    <button class="viewBtn bg-blue-500 text-white px-3 py-1 rounded shadow hover:bg-blue-600 transition" data-id="${d.iddoc}">View</button>
                    <button class="approveBtn bg-green-500 text-white px-3 py-1 rounded shadow hover:bg-green-600 transition" data-id="${d.iddoc}">Approve</button>
                    <button class="rejectBtn bg-red-500 text-white px-3 py-1 rounded shadow hover:bg-red-600 transition" data-id="${d.iddoc}">Reject</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function renderPagination(){
        paginationContainer.innerHTML='';
        for(let i=1;i<=totalPages;i++){
            const btn=document.createElement('button');
            btn.textContent=i;
            btn.className=`px-3 py-1 rounded ${i===currentPage?'bg-purple-500 text-white':'bg-gray-200 hover:bg-gray-300'}`;
            btn.addEventListener('click',()=>{ currentPage=i; loadData(i); });
            paginationContainer.appendChild(btn);
        }
    }

    tbody.addEventListener('click', async e=>{
        const id = e.target.dataset.id;
        if(!id) return;
        if(e.target.classList.contains('viewBtn')) showDokumenModal(id);
        else if(e.target.classList.contains('approveBtn')) changeStatus(id,'approved');
        else if(e.target.classList.contains('rejectBtn')) changeStatus(id,'rejected');
    });

    async function showDokumenModal(id){
        try{
            const res = await fetch(`<?= base_url('approvaldokumen/getDokumen') ?>/${id}`);
            const data = await res.json();
            if(!data || !data.status){ Swal.fire('Error','Dokumen tidak dijumpai','error'); return; }

            dokumenDetails.innerHTML = `
                <p><strong>Nama:</strong> ${data.nama ?? '-'}</p>
                <p><strong>Nama Fail:</strong> ${data.namafail ?? '-'}</p>
                <p><strong>MIME:</strong> ${data.mime ?? '-'}</p>
                <p><strong>Status:</strong> ${data.status ?? '-'}</p>
            `;
            viewModal.classList.remove('hidden');
        }catch(err){ console.error(err); }
    }

    async function changeStatus(id, status){
        const confirmText = status.charAt(0).toUpperCase()+status.slice(1);
        Swal.fire({
            title:`Confirm ${confirmText}`,
            text:`Anda pasti mahu ${status} dokumen ini?`,
            icon:'question',
            showCancelButton:true,
            confirmButtonText:confirmText
        }).then(async result=>{
            if(!result.isConfirmed) return;
            try{
                const formData=new FormData();
                formData.append(csrfName,csrfHash);

                const res = await fetch(`<?= base_url('approvaldokumen/changeStatus') ?>/${id}/${status}`,{
                    method:'POST',
                    body: formData
                });
                const data=await res.json();
                if(data.status){
                    successAnimation.style.display='block';
                    successAnimation.play();
                    setTimeout(()=> successAnimation.style.display='none',1500);
                    Swal.fire(confirmText+'!', data.message,'success');
                    loadData(currentPage);
                }else Swal.fire('Error',data.message,'error');
            }catch(err){ console.error(err); }
        });
    }

    closeViewModal.addEventListener('click',()=> viewModal.classList.add('hidden'));

    loadData(currentPage);
});
</script>
</body>
</html>
