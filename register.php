<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create account</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<style>
body{
    background:#fafafa;
    font-family:Inter,system-ui,sans-serif;
}

.auth-wrapper{
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
}

.auth-card{
    width:100%;
    max-width:440px;
    background:#fff;
    padding:40px;
    border-radius:18px;
    box-shadow:0 10px 40px rgba(0,0,0,.06);
}

h1{font-size:28px;font-weight:700}
p{color:#6b7280;font-size:14px}

.form-control{
    border-radius:12px;
    padding:12px;
}

.btn-primary{
    background:#f4b860;
    border:none;
    border-radius:12px;
    padding:12px;
    font-weight:600;
}

.auth-link{color:#f4b860;text-decoration:none;font-weight:500}
</style>
</head>

<body>
<div class="auth-wrapper">
<div class="auth-card">

<h1>Create your account</h1>
<p class="mb-4">Start building with us today</p>

<div id="alert"></div>

<form id="registerForm">
<input class="form-control mb-3" name="username" placeholder="Username" required>
<input class="form-control mb-3" name="email" type="email" placeholder="Email address" required>
<input class="form-control mb-4" name="password" type="password" placeholder="Password (min 6 chars)" required>

<button class="btn btn-primary w-100">Create account</button>
</form>

<p class="text-center text-muted mt-4">
Already have an account?
<a href="<?= base_url('auth/login') ?>" class="auth-link">Sign in</a>
</p>

</div>
</div>

<script>
const baseUrl = "<?= base_url() ?>";

document.getElementById('registerForm').addEventListener('submit', async e=>{
e.preventDefault();
alert.innerHTML='';

try{
const res = await axios.post(baseUrl+'auth/processRegister',{
username:e.target.username.value.trim(),
email:e.target.email.value.trim(),
password:e.target.password.value
});

alert.innerHTML=`
<div class="alert alert-${res.data.status?'success':'danger'}">
${res.data.message}
</div>`;

if(res.data.status){
setTimeout(()=>location.href=baseUrl+'auth/login',1000);
}
}catch{
alert.innerHTML='<div class="alert alert-danger">Server error</div>';
}
});
</script>
</body>
</html>
