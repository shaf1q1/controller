<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Pengurusan Perincian Modul</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="<?= base_url('ckeditor5-build-classic/build/ckeditor.js') ?>"></script>
</head>
<body class="min-h-screen flex items-center justify-center py-10">

<div class="bg-white shadow-lg rounded-xl p-8 w-full max-w-3xl border border-gray-200">
    <h1 class="text-2xl font-bold text-blue-700 mb-6 text-center">Pengurusan Perincian Modul</h1>

    <!-- Flash Messages -->
    <?php if(session()->getFlashdata('success')): ?>
        <div class="bg-green-100 text-green-800 p-3 mb-4 rounded-md border border-green-300">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    <?php if(session()->getFlashdata('error')): ?>
        <div class="bg-red-100 text-red-800 p-3 mb-4 rounded-md border border-red-300">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- Dropdown Servis -->
    <div class="mb-6 relative">
        <label class="block font-semibold mb-2 text-gray-700">Pilih Servis:</label>
        <button id="dropdownBtn" class="border border-gray-300 w-full p-3 rounded-md text-left bg-white hover:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400">
            -- Sila Pilih Servis --
        </button>
        <ul id="dropdownList" class="absolute w-full border border-gray-300 bg-white rounded-md shadow-lg mt-1 max-h-60 overflow-y-auto hidden z-50">
            <?php foreach($servisList as $s): ?>
                <li class="p-3 hover:bg-blue-100 cursor-pointer" data-id="<?= $s['idservis'] ?>" data-name="<?= $s['namaservis'] ?>">
                    <?= $s['namaservis'] ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Form -->
    <form id="servisForm" method="post" action="<?= base_url('perincian/save') ?>" class="hidden space-y-5">
        <?= csrf_field() ?>
        <input type="hidden" name="idservis" id="idservis">

        <div>
            <label class="font-medium text-gray-700">Nama Servis:</label>
            <input type="text" id="namaservis" readonly class="border border-gray-300 rounded-md p-3 w-full bg-gray-100">
        </div>

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
    const descField = document.getElementById('description');
    let editorInstance;

    ClassicEditor.create(descField).then(editor => { editorInstance = editor; }).catch(err => console.error(err));

    dropdownBtn.addEventListener('click', () => dropdownList.classList.toggle('hidden'));

    dropdownList.querySelectorAll('li').forEach(item => {
        item.addEventListener('click', async () => {
            const id = item.dataset.id;
            const name = item.dataset.name;
            dropdownBtn.textContent = name;
            dropdownList.classList.add('hidden');

            idInput.value = id;
            namaInput.value = name;
            form.classList.remove('hidden');

            // Reset description first
            editorInstance.setData('');

            try {
                const res = await fetch(`<?= base_url('perincian/getServis') ?>/${id}`);
                const data = await res.json();
                if (data.desc && data.desc.description) {
                    const plainText = data.desc.description.replace(/<\/?[^>]+(>|$)/g, "");
                    editorInstance.setData(plainText);
                }
            } catch (err) {
                console.error('AJAX Error:', err);
            }
        });
    });

    // Client-side validation
    form.addEventListener('submit', (e) => {
        const namaServis = namaInput.value.trim();
        const description = editorInstance.getData().trim();

        if (namaServis === '' || description === '') {
            e.preventDefault();
            alert('Sila pilih servis dan masukkan description.');
            return false;
        }

        const textValue = description.replace(/<\/?[^>]+(>|$)/g, "");
        editorInstance.setData(textValue);
    });
});
</script>

</body>
</html>
