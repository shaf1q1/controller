<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pengurusan Perincian Modul</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<script src="<?= base_url('ckeditor5-build-classic/build/ckeditor.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    /* Scrollbar for list */
    #servisList::-webkit-scrollbar { width: 8px; }
    #servisList::-webkit-scrollbar-thumb { background: #a78bfa; border-radius: 10px; }
    #servisList::-webkit-scrollbar-track { background: #f3f4f6; border-radius: 10px; }

    /* Card hover effect */
    .hover-card { transition: all 0.3s ease; }
    .hover-card:hover { transform: translateY(-5px); box-shadow: 0 12px 25px rgba(0,0,0,0.15); }

    /* Gradient buttons */
    .btn-gradient {
        background: linear-gradient(90deg, #8b5cf6, #3b82f6);
        color: #fff;
        transition: all 0.3s ease;
    }
    .btn-gradient:hover { transform: scale(1.05); }
</style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center py-10">

<div class="w-full max-w-7xl px-4">

    <!-- Header -->
    <div class="text-center mb-10">
        <h1 class="text-3xl md:text-4xl font-bold text-purple-600">Pengurusan Servis & Perincian Modul</h1>
        <p class="text-gray-600 mt-2">Tambah, kemas kini, dan padam servis dengan mudah</p>
    </div>

    <div class="flex flex-col md:flex-row gap-8">

        <!-- Left Panel: Servis List -->
        <div class="md:w-1/3 bg-white rounded-2xl shadow-lg p-5 border border-gray-200 max-h-[500px] overflow-y-auto hover-card">
            <h2 class="text-xl font-semibold text-purple-600 mb-5">Senarai Servis</h2>
            <ul id="servisList" class="space-y-2">
                <?php foreach($servisList as $s): ?>
                    <li data-id="<?= $s['idservis'] ?>" 
                        class="p-3 cursor-pointer hover:bg-purple-50 rounded-xl border border-gray-200 transition hover-card">
                        <?= $s['namaservis'] ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Right Panel: Add/Update & Description -->
        <div class="md:w-2/3 flex flex-col gap-6">

            <!-- Add / Update Servis Card -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 hover-card flex flex-col md:flex-row gap-4 items-center">
                <input type="hidden" id="idservisInput">
                <input type="text" id="namaservisInput" placeholder="Nama Servis"
                       class="flex-1 border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-purple-400 focus:outline-none">
                <button id="saveServisBtn" class="btn-gradient px-6 py-3 rounded-lg">Simpan / Update</button>
                <button id="deleteServisBtn" class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition">Padam</button>
            </div>

            <!-- Description Card -->
            <div id="descCard" class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 hidden hover-card flex flex-col gap-4">
                <input type="hidden" id="idservisDesc">
                <label class="text-gray-700 font-medium">Description:</label>
                <textarea id="description" name="description" rows="8" 
                          class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-300 focus:outline-none"></textarea>
                <div class="flex justify-end gap-3">
                    <button type="reset" id="resetDesc" class="bg-gray-400 text-white px-6 py-2 rounded-lg hover:bg-gray-500 transition">Reset</button>
                    <button type="submit" id="saveDesc" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 hover:scale-105 transition">Simpan Description</button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let editorInstance;

    // Initialize CKEditor
    ClassicEditor.create(document.getElementById('description'))
        .then(editor => editorInstance = editor)
        .catch(err => console.error(err));

    const idInput = document.getElementById('idservisInput');
    const nameInput = document.getElementById('namaservisInput');
    const servisListEl = document.getElementById('servisList');
    const descCard = document.getElementById('descCard');
    const idDescInput = document.getElementById('idservisDesc');

    // Load selected servis
    function loadServis(servisEl) {
        const id = servisEl.dataset.id;
        idInput.value = id;
        nameInput.value = servisEl.textContent;
        idDescInput.value = id;
        descCard.classList.remove('hidden');
        editorInstance.setData('');

        fetch(`<?= base_url('tambahanperincian/getServis') ?>/${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.status && data.desc) {
                    editorInstance.setData(data.desc.description || '');
                }
            })
            .catch(err => console.error(err));
    }

    // Click on servis list item
    servisListEl.querySelectorAll('li').forEach(item => {
        item.addEventListener('click', () => loadServis(item));
    });

    // Save / Update Servis
    document.getElementById('saveServisBtn').addEventListener('click', async () => {
        const id = idInput.value;
        const name = nameInput.value.trim();
        if (!name) { Swal.fire('Ralat','Nama servis diperlukan','error'); return; }

        try {
            const formData = new FormData();
            formData.append('idservis', id);
            formData.append('namaservis', name);
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

            const res = await fetch('<?= base_url("tambahanperincian/saveServis") ?>', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.status) { Swal.fire('Berjaya', data.message, 'success'); location.reload(); }
            else Swal.fire('Ralat', data.message, 'error');
        } catch (err) {
            console.error(err);
            Swal.fire('Ralat', 'Gagal simpan servis', 'error');
        }
    });

    // Delete Servis
    document.getElementById('deleteServisBtn').addEventListener('click', async () => {
        const id = idInput.value;
        if (!id) { Swal.fire('Ralat','Sila pilih servis','error'); return; }

        const confirm = await Swal.fire({
            title: 'Padam Servis?',
            text: 'Tindakan ini tidak boleh dikembalikan',
            icon: 'warning',
            showCancelButton: true
        });

        if (!confirm.isConfirmed) return;

        try {
            const formData = new FormData();
            formData.append('idservis', id);
            formData.append('<?= csrf_token() ?>','<?= csrf_hash() ?>');

            const res = await fetch('<?= base_url("tambahanperincian/deleteServis") ?>', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.status) { Swal.fire('Berjaya', data.message, 'success'); location.reload(); }
            else Swal.fire('Ralat', data.message, 'error');
        } catch (err) {
            console.error(err);
            Swal.fire('Ralat','Gagal padam servis','error');
        }
    });

    // Save Description
    document.getElementById('saveDesc').addEventListener('click', async () => {
        const id = idDescInput.value;
        const desc = editorInstance.getData().trim();
        if (!id || !desc) { Swal.fire('Ralat','Sila lengkapkan servis & description','error'); return; }

        try {
            const formData = new FormData();
            formData.append('idservis', id);
            formData.append('description', desc);
            formData.append('<?= csrf_token() ?>','<?= csrf_hash() ?>');

            const res = await fetch('<?= base_url("tambahanperincian/save") ?>', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.status) Swal.fire('Berjaya', data.message, 'success');
            else Swal.fire('Ralat', data.message, 'error');
        } catch (err) {
            console.error(err);
            Swal.fire('Ralat','Gagal simpan description','error');
        }
    });

    // Reset description
    document.getElementById('resetDesc').addEventListener('click', () => editorInstance.setData(''));
});
</script>

</body>
</html>
