<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Sistem</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    /* === COLOR THEME === */
    :root {
      --gray-dark: #302d2dff;
      --gray-medium: #2c2c2c;
      --gray-light: #3a3a3a;
      --accent: #d6c999;
      --white: #ffffff;
    }

    body {
      background-color: var(--gray-dark);
      color: var(--white);
      font-family: 'Poppins', sans-serif;
      transition: background 0.4s, color 0.4s;
    }

    /* === NAVBAR === */
    .navbar {
      background-color: var(--gray-medium);
      border-bottom: 3px solid var(--accent);
    }

    .navbar-brand, .navbar a {
      color: var(--white) !important;
      font-weight: bold;
    }

    .navbar a:hover {
      color: var(--accent) !important;
    }

    /* === TITLE === */
    h1 {
      color: var(--white);
      font-weight: 700;
    }

    /* === CARD DESIGN === */
    .card {
      border: none;
      border-radius: 15px;
      background-color: var(--gray-light);
      color: var(--white);
      transition: all 0.3s ease;
      box-shadow: 0 4px 10px rgba(0,0,0,0.4);
    }

    .card-hover:hover {
      transform: translateY(-6px);
      background-color: var(--accent);
      color: var(--gray-dark);
      box-shadow: 0 10px 25px rgba(0,0,0,0.6);
    }

    .card i {
      color: var(--accent);
    }

    .card-hover:hover i {
      color: var(--gray-dark);
    }

    .card h5 {
      color: var(--white);
    }

    .card p {
      color: #f0f0f0;
    }

    

    /* === TOGGLE BUTTON === */
  

   
    /* === LINK RESET === */
    a.text-decoration-none {
      color: inherit;
    }
  </style>
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg">
    <div class="container d-flex justify-content-between align-items-center">
      <a class="navbar-brand" href="#">📊 Dashboard Sistem</a>
      <button class="theme-toggle" id="themeToggle" title="Tukar Tema">
        <i class="bi bi-sun-fill" id="themeIcon"></i>
      </button>
    </div>
  </nav>

  <!-- Content -->
  <div class="container py-5">
    <h1 class="text-center mb-5">Selamat Datang ke Dashboard</h1>

    <div class="row g-4 justify-content-center">

      <!-- Perincian Modul -->
      <div class="col-md-4 col-sm-6">
        <a href="<?= base_url('perincianmodul') ?>" class="text-decoration-none">
          <div class="card card-hover text-center p-4">
            <i class="bi bi-file-text-fill display-4 mb-3"></i>
            <h5 class="fw-bold">Perincian Modul</h5>
            <p>Urus perincian servis dan modul</p>
          </div>
        </a>
      </div>

      <!-- Dokumen Modul -->
      <div class="col-md-4 col-sm-6">
        <a href="<?= base_url('dokumen') ?>" class="text-decoration-none">
          <div class="card card-hover text-center p-4">
            <i class="bi bi-folder-fill display-4 mb-3"></i>
            <h5 class="fw-bold">Dokumen Modul</h5>
            <p>Tambah, kemaskini & padam dokumen</p>
          </div>
        </a>
      </div>

      <!-- Future Module -->
      <div class="col-md-4 col-sm-6">
        <div class="card card-hover text-center p-4">
          <i class="bi bi-gear-fill display-4 mb-3"></i>
          <h5 class="fw-bold">Tetapan / Lain-lain</h5>
          <p>Fungsi tambahan akan datang</p>
        </div>
      </div>

    </div>
  </div>




</body>
</html>