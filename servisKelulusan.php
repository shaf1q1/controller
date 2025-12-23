<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<title>Sistem Kelulusan Servis</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.10.1/lottie.min.js"></script>
<style>
body { font-family: 'Inter', sans-serif; background-color: #f1f5f9; }
.status-available { background-color: #d0f0fd; color: #0369a1; }
.status-unavailable { background-color: #fee2e2; color: #b91c1c; }
.card { transition: transform 0.2s, box-shadow 0.2s; display:flex; flex-direction: column; justify-content: space-between; cursor:pointer; }
.card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.pagination button { min-width: 36px; transition: all 0.2s; }
.lottieCard { width: 100%; height: 120px; margin-bottom: 8px; }
</style>
</head>
<body class="min-h-screen p-8">

<div class="max-w-7xl mx-auto bg-white shadow-lg rounded-lg p-6 relative">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Sistem Kelulusan Servis</h1>

    <!-- Filter & Search -->
    <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-3">
        <div class="flex gap-2">
            <select id="filterStatus" class="border p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="all">All Status</option>
                <option value="available">Available</option>
                <option value="unavailable">Unavailable</option>
            </select>
        </div>
        <input type="text" id="searchServis" placeholder="Search Servis..." class="border p-2 rounded w-full md:w-1/3 focus:outline-none focus:ring-2 focus:ring-blue-400">
    </div>

    <!-- Card Grid -->
    <div id="servisGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"></div>

    <!-- Numeric Pagination -->
    <div class="mt-6 flex justify-center items-center space-x-1 pagination"></div>
</div>

<!-- Modal View Servis -->
<div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg w-3/4 max-w-2xl p-6 shadow-lg relative">
        <h2 class="text-xl font-bold mb-4">Maklumat Servis</h2>
        <div id="servisDetails" class="mb-4 space-y-2"></div>
        <div class="flex justify-end gap-3">
            <button id="closeViewModal" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 transition">Tutup</button>
        </div>
    </div>
</div>

<script>
// ====================== CDN Animation Map ======================
const serviceAnimations = {
    "elaun": "https://assets9.lottiefiles.com/packages/lf20_vnikrcia.json",
    "internet": "https://assets2.lottiefiles.com/packages/lf20_tfb3estd.json",
    "telesidang": "https://assets8.lottiefiles.com/packages/lf20_mjlh3hcy.json",
    "perisian": "https://assets6.lottiefiles.com/packages/lf20_jtbfg2nb.json",
    "id smu": "https://assets1.lottiefiles.com/packages/lf20_6c3ozd.json",
    "pinjaman": "https://assets3.lottiefiles.com/packages/lf20_3vbOcw.json",
    "multimedia": "https://assets7.lottiefiles.com/packages/lf20_ydo1amjm.json",
    "laluan": "https://assets2.lottiefiles.com/packages/lf20_xz7n7j.json",
    "pangkalan": "https://assets10.lottiefiles.com/packages/lf20_w51pcehl.json",
    "email": "https://assets1.lottiefiles.com/packages/lf20_2ks3pjua.json",
    "server": "https://assets5.lottiefiles.com/packages/lf20_5cmu6w.json",
    "dns": "https://assets4.lottiefiles.com/packages/lf20_h4th9ofg.json",
    "vpn": "https://assets9.lottiefiles.com/packages/lf20_jcikwtux.json",
    "uat": "https://assets4.lottiefiles.com/packages/lf20_ezjmv1.json",
    "teknikal": "https://assets2.lottiefiles.com/packages/lf20_wk0ov6.json",
    "peka": "https://assets10.lottiefiles.com/packages/lf20_t8cmg8wk.json",
    "web": "https://assets3.lottiefiles.com/packages/lf20_fjts2ec.json",
    "e-mel": "https://assets3.lottiefiles.com/packages/lf20_bozgf3.json",
    "pemasangan": "https://assets3.lottiefiles.com/packages/lf20_jcikwtux.json",
    "permohonan": "https://assets3.lottiefiles.com/packages/lf20_xldzo7dx.json",
    "default": "https://assets3.lottiefiles.com/packages/lf20_jcikwtux.json"
};

function getAnimationForService(name){
    if(!name) return serviceAnimations.default;
    name = name.toLowerCase();
    for(const key in serviceAnimations){
        if(name.includes(key)) return serviceAnimations[key];
    }
    return serviceAnimations.default;
}

// ====================== MAIN ======================
document.addEventListener('DOMContentLoaded', () => {
    const servisGrid = document.getElementById('servisGrid');
    const searchInput = document.getElementById('searchServis');
    const filterStatus = document.getElementById('filterStatus');
    const viewModal = document.getElementById('viewModal');
    const servisDetails = document.getElementById('servisDetails');
    const closeViewModal = document.getElementById('closeViewModal');
    const paginationContainer = document.querySelector('.pagination');

    let currentPage = 1;
    let totalPages = 1;

    // ======================= LOAD DATA =======================
    async function loadData(page = 1) {
        try {
            const res = await fetch(`<?= base_url("serviskelulusan/getAll") ?>?status=${filterStatus.value}&page=${page}`);
            const result = await res.json();
            renderGrid(result.data);
            currentPage = result.page;
            totalPages = Math.ceil(result.total / result.limit);
            renderPagination();
        } catch (e) { console.error('Load data failed:', e); }
    }

    // ======================= RENDER GRID =======================
    function renderGrid(data) {
        servisGrid.innerHTML = '';

        data.forEach(d => {
            const statusText = d.status == 1 ? 'Available' : 'Unavailable';
            const statusClass = d.status == 1 ? 'status-available' : 'status-unavailable';

            const card = document.createElement('div');
            card.className = 'card bg-white p-4 rounded-lg shadow';

            card.innerHTML = `
                <div class="lottieCard"></div>
                <h3 class="text-lg font-semibold mb-2">${d.nama}</h3>
                <span class="px-2 py-1 rounded-full text-sm font-semibold ${statusClass}">${statusText}</span>
                <div class="flex gap-2 mt-4">
                    <button class="viewBtn flex-1 bg-blue-500 text-white px-3 py-1 rounded" data-id="${d.idservis}">View</button>
                    <button class="toggleStatusBtn flex-1 ${d.status==1?'bg-red-500':'bg-green-500'} text-white px-3 py-1 rounded" data-id="${d.idservis}">
                        ${d.status==1?'Set Unavailable':'Set Available'}
                    </button>
                </div>
            `;
            servisGrid.appendChild(card);

            // ===================== LOTTIE ANIMATION =====================
            const animDiv = card.querySelector('.lottieCard');
            try {
                const animation = lottie.loadAnimation({
                    container: animDiv,
                    renderer: 'svg',
                    loop: true,
                    autoplay: true,
                    path: getAnimationForService(d.nama)
                });
                // hover speed effect
                card.addEventListener('mouseenter', ()=> animation.setSpeed(1.8));
                card.addEventListener('mouseleave', ()=> animation.setSpeed(1));
            } catch { animDiv.innerHTML = 'No Animation'; }
        });
    }

    // ======================= RENDER PAGINATION =======================
    function renderPagination() {
        paginationContainer.innerHTML = '';
        for(let i=1;i<=totalPages;i++){
            const btn = document.createElement('button');
            btn.textContent = i;
            btn.className = `px-3 py-1 rounded ${i===currentPage?'bg-blue-500 text-white':'bg-gray-200'}`;
            btn.addEventListener('click', ()=> loadData(i));
            paginationContainer.appendChild(btn);
        }
    }

    // ======================= SEARCH =======================
    searchInput.addEventListener('input', ()=>{
        const term = searchInput.value.toLowerCase();
        [...servisGrid.children].forEach(c=>{
            c.style.display = c.textContent.toLowerCase().includes(term)?'':'none';
        });
    });

    // ======================= FILTER =======================
    filterStatus.addEventListener('change', ()=> loadData(1));

    // ======================= VIEW MODAL & TOGGLE STATUS =======================
    servisGrid.addEventListener('click', async e=>{
        const id = e.target.dataset.id;
        if(!id) return;
        if(e.target.classList.contains('viewBtn')){
            try{
                const res = await fetch(`<?= base_url('serviskelulusan/getServis') ?>/${id}`);
                const data = await res.json();
                servisDetails.innerHTML = `
                    <p><strong>Nama:</strong> ${data.nama}</p>
                    <p><strong>Info URL:</strong> <a href="${data.infourl}" target="_blank">${data.infourl}</a></p>
                    <p><strong>Mohon URL:</strong> <a href="${data.mohonurl}" target="_blank">${data.mohonurl}</a></p>
                    <p><strong>Description:</strong> ${data.infoperincian ?? '-'}</p>
                    <p><strong>Status:</strong> ${data.status==1?'Available':'Unavailable'}</p>
                `;
                viewModal.classList.remove('hidden');
            }catch(err){ console.error(err); }
        } else if(e.target.classList.contains('toggleStatusBtn')){
            const newStatus = e.target.textContent.includes('Unavailable') ? 'unavailable' : 'available';
            Swal.fire({
                title: `Confirm ${newStatus}`,
                text: `Anda pasti mahu set servis ini ke ${newStatus}?`,
                icon: 'question',
                showCancelButton:true,
                confirmButtonText: 'Yes'
            }).then(async (result)=>{
                if(!result.isConfirmed) return;
                try{
                    const res = await fetch(`<?= base_url('serviskelulusan/changeStatus') ?>/${id}/${newStatus}`, {method:'POST'});
                    const data = await res.json();
                    if(data.status){
                        Swal.fire('Berjaya!', data.message, 'success');
                        loadData(currentPage);
                    } else Swal.fire('Error', data.message,'error');
                }catch(err){ console.error(err); }
            });
        }
    });

    closeViewModal.addEventListener('click', ()=> viewModal.classList.add('hidden'));

    loadData();
});
</script>

</body>
</html>
