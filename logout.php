<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Logging Out</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
:root {
    --accent: #f4b860;
    --bg: #f9f9f9;
    --text: #1f2937;
    --muted: #6b7280;
}

body {
    background: var(--bg);
    font-family: Inter, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
}

.logout-card {
    background: #fff;
    padding: 40px;
    border-radius: 18px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.08);
    text-align: center;
    max-width: 400px;
    width: 90%;
    opacity: 0;
    transform: translateY(-20px);
    animation: fadeIn 0.8s forwards;
}

@keyframes fadeIn { to { opacity:1; transform: translateY(0); } }

.spinner {
    margin: 25px auto;
    width: 50px;
    height: 50px;
    border: 6px solid #f3f3f3;
    border-top: 6px solid var(--accent);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

.logout-text {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 12px;
    color: var(--text);
}

.sub-text {
    color: var(--muted);
    font-size: 14px;
    margin-bottom: 0;
}

.fade-out { animation: fadeOut 0.8s forwards; }
@keyframes fadeOut { to { opacity:0; transform: translateY(-20px); } }
</style>
</head>
<body>

<div class="logout-card" id="logoutCard">
    <div class="logout-text">Logging out...</div>
    <div class="spinner"></div>
    <p class="sub-text">Redirecting to login page shortly</p>
</div>

<script>
// 1️⃣ Grab card element
const logoutCard = document.getElementById('logoutCard');

// 2️⃣ Wait 1s → fade out → redirect
setTimeout(() => {
    logoutCard.classList.add('fade-out');
    setTimeout(() => {
        window.location.href = "<?= base_url('auth/login') ?>";
    }, 800); // match fade-out duration
}, 1000);
</script>

</body>
</html>
