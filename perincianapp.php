<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Pengurusan Perincian Modul</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="<?= base_url('ckeditor5-build-classic/build/ckeditor.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="min-h-screen flex flex-col items-center py-10 bg-gray-50">

<div class="bg-white shadow-lg rounded-xl p-8 w-full max-w-4xl border border-gray-200">
    <h1 class="text-2xl font-bold text-blue-700 mb-6 text-center">Pengurusan Perincian Modul</h1>

    <!-- Dropdown Servis -->
    <div class="mb-6 relative">
        <label class="block font-semibold mb-2 text-gray-700">Pilih Servis:</label>
        <button id="dropdownBtn" class="border border-gray-300 w-full p-3 rounded-md text-left bg-white hover:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400">
            -- Sila Pilih Servis --
        </button>
        <ul id="dropdownList" class="absolute w-full border border-gray-300 bg-white rounded-md shadow-lg mt-1 max-h-60 overflow-y-auto hidden z-50">
            <?php foreach($servisList as $s): ?>
                <li class="p-3 hover:bg-blue-100 cursor-pointer" 
                    data-id="<?= $s['idservis'] ?>" 
                    data-name="<?= $s['namaservis'] ?>" 
                    data-infourl="<?= $s['infourl'] ?>" 
                    data-mohonurl="<?= $s['mohonurl'] ?>">
                    <?= $s['namaservis'] ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Status Summary -->
    <div id="dokumenStatus" class="mb-6 hidden border border-gray-300 rounded p-4 bg-gray-50">
        <h2 class="font-semibold text-gray-700 mb-2">Status Dokumen</h2>
        <div class="flex gap-4">
            <div><span class="font-semibold">Pending:</span> <span id="statusPending">0</span></div>
            <div><span class="font-semibold">Approved:</span> <span id="statusApproved">0</span></div>
            <div><span class="font-semibold">Rejected:</span> <span id="statusRejected">0</span></div>
            <div><span class="font-semibold">Total:</span> <span id="statusTotal">0</span></div>
        </div>
        <button id="viewDocuments" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">Lihat Dokumen</button>
    </div>

    <!-- Form -->
    <form id="servisForm" method="post" action="<?= base_url('perincianmodul/save') ?>" class="hidden space-y-5">
        <?= csrf_field() ?>
        <input type="hidden" name="idservis" id="idservis">

        <!-- Nama Servis -->
        <div>
            <label class="font-medium text-gray-700">Nama Servis:</label>
            <input type="text" id="namaservis" name="namaservis" class="border border-gray-300 rounded-md p-3 w-full" maxlength="145" required>
        </div>

        <!-- Info URL -->
        <div>
            <label class="font-medium text-gray-700">Info URL:</label>
            <input type="url" id="infourl" name="infourl" class="border border-gray-300 rounded-md p-3 w-full" placeholder="https://example.com/info">
        </div>

        <!-- Mohon URL -->
        <div>
            <label class="font-medium text-gray-700">Mohon URL:</label>
            <input type="url" id="mohonurl" name="mohonurl" class="border border-gray-300 rounded-md p-3 w-full" placeholder="https://example.com/mohon">
        </div>

        <!-- Description -->
        <div>
            <label class="font-medium text-gray-700">Description:</label>
            <textarea id="description" name="description" rows="8" class="border border-gray-300 rounded-md p-3 w-full"></textarea>
        </div>

        <div class="flex justify-end gap-3">
            <button type="reset" class="bg-gray-400 text-white px-5 py-2 rounded-md hover:bg-gray-500 transition">Reset</button>
            <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-md hover:bg-blue-700 transition">Simpan</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const dropdownBtn = document.getElementById('dropdownBtn');
    const dropdownList = document.getElementById('dropdownList');
    const form = document.getElementById('servisForm');
    const idInput = document.getElementById('idservis');
    const namaInput = document.getElementById('namaservis');
    const infourlInput = document.getElementById('infourl');
    const mohonurlInput = document.getElementById('mohonurl');
    const descField = document.getElementById('description');
    const dokumenStatus = document.getElementById('dokumenStatus');
    const statusPending = document.getElementById('statusPending');
    const statusApproved = document.getElementById('statusApproved');
    const statusRejected = document.getElementById('statusRejected');
    const statusTotal = document.getElementById('statusTotal');
    const viewDocuments = document.getElementById('viewDocuments');
    let editorInstance;

    ClassicEditor.create(descField).then(editor => { editorInstance = editor; }).catch(err => console.error(err));

    // Dropdown toggle
    dropdownBtn.addEventListener('click', () => dropdownList.classList.toggle('hidden'));

    // Dropdown selection
    dropdownList.querySelectorAll('li').forEach(item => {
        item.addEventListener('click', async () => {
            const id = item.dataset.id;
            const name = item.dataset.name;
            const info = item.dataset.infourl;
            const mohon = item.dataset.mohonurl;

            dropdownBtn.textContent = name;
            dropdownList.classList.add('hidden');
            form.classList.remove('hidden');

            idInput.value = id;
            namaInput.value = name;
            infourlInput.value = info || '';
            mohonurlInput.value = mohon || '';
            editorInstance.setData('');

            try {
                const res = await fetch(`<?= base_url('perincianmodul/getServis') ?>/${id}`);
                const data = await res.json();
                if (data.desc && data.desc.description) editorInstance.setData(data.desc.description);

                // Show dokumen status
                if (data.dokumen_status) {
                    statusPending.textContent = data.dokumen_status.pending;
                    statusApproved.textContent = data.dokumen_status.approved;
                    statusRejected.textContent = data.dokumen_status.rejected;
                    statusTotal.textContent = data.dokumen_status.total;
                    dokumenStatus.classList.remove('hidden');
                }
            } catch (err) {
                console.error('AJAX Error:', err);
            }
        });
    });

    // View documents button
    viewDocuments.addEventListener('click', () => {
        if (!idInput.value) return;
        window.location.href = `<?= base_url('approvaldokumen') ?>?idservis=${idInput.value}`;
    });

    // Client-side validation with SweetAlert3
    form.addEventListener('submit', (e) => {
        const nama = namaInput.value.trim();
        const info = infourlInput.value.trim();
        const mohon = mohonurlInput.value.trim();
        const desc = editorInstance.getData().trim();

        const keyboardRegex = /^[\x20-\x7E]{1,145}$/;
        const urlRegex = /^(https?|ftp):\/\/[^\s/$.?#].[^\s]*$/i;

        let errors = [];
        if (!nama || !keyboardRegex.test(nama)) errors.push("Nama servis hanya aksara keyboard standard dan max 145 aksara.");
        if (info && !urlRegex.test(info)) errors.push("Info URL tidak sah. Sertakan protocol http, https atau ftp.");
        if (mohon && !urlRegex.test(mohon)) errors.push("Mohon URL tidak sah. Sertakan protocol http, https atau ftp.");
        if (!desc) errors.push("Description diperlukan.");

        if (errors.length > 0) {
            e.preventDefault();
            Swal.fire({ icon:'error', title:'Ralat', html: errors.join('<br>') });
        }
    });

    // SweetAlert3 flash messages
    <?php if(session()->getFlashdata('success')): ?>
        Swal.fire({ icon: 'success', title: 'Berjaya', html: '<?= session()->getFlashdata("success") ?>' });
    <?php endif; ?>
    <?php if(session()->getFlashdata('error')): ?>
        Swal.fire({ icon: 'error', title: 'Ralat', html: '<?= session()->getFlashdata("error") ?>' });
    <?php endif; ?>
});
</script>

</body>
</html>
