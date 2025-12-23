<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Craftly Style</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<style>
:root {
    --accent: #f4b860;
    --bg: #f9f9f9;
    --text: #1f2937;
    --muted: #6b7280;
}

body{background:var(--bg);font-family:Inter,sans-serif;color:var(--text);}
.auth-wrapper{min-height:100vh;display:flex;justify-content:center;align-items:center;}
.auth-card{background:#fff;padding:40px;border-radius:18px;box-shadow:0 10px 40px rgba(0,0,0,.06);width:100%;max-width:420px;}
.auth-title{font-size:28px;font-weight:700;margin-bottom:10px;}
.auth-sub{color:var(--muted);font-size:14px;margin-bottom:30px;}
.form-control{border-radius:12px;padding:12px;border:1px solid #e5e7eb;}
.form-control:focus{border-color:var(--accent);box-shadow:none;}
.btn-primary{background:var(--accent);border:none;border-radius:12px;padding:12px;font-weight:600;}
.btn-primary:hover{background:#e6a94d;}
.auth-link{color:var(--accent);text-decoration:none;font-weight:500;}
.auth-link:hover{text-decoration:underline;}
.divider{margin:24px 0;text-align:center;color:#d1d5db;font-size:13px;}
</style>
</head>
<body>

<div class="auth-wrapper">
<div class="auth-card">
<h1 class="auth-title text-center">Welcome Back</h1>
<p class="auth-sub text-center">Log in to continue</p>

<div id="alert"></div>

<form id="loginForm">
  <div class="mb-3">
    <label>Email</label>
    <input type="email" name="email" class="form-control" placeholder="you@email.com" required>
  </div>
  <div class="mb-4">
    <label>Password</label>
    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
  </div>
  <button class="btn btn-primary w-100">Sign in</button>
</form>

<div class="text-center mt-3">
  <button class="btn btn-link" data-bs-toggle="modal" data-bs-target="#changeModal">
    Change Password
  </button>
</div>

<div class="divider">or</div>
<p class="text-center text-muted mb-0">Don’t have an account? 
  <a href="<?= base_url('auth/register') ?>" class="auth-link">Create one</a>
</p>
</div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changeModal" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title">Change Password</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<div id="alertChange"></div>
<form id="changeForm">
  <input class="form-control mb-2" name="oldPassword" type="password" placeholder="Old Password" required>
  <input class="form-control mb-2" name="newPassword" type="password" placeholder="New Password" required>
  <input class="form-control mb-2" name="confirmPassword" type="password" placeholder="Confirm Password" required>
  <button class="btn btn-primary w-100 mt-2">Save</button>
</form>
</div>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const baseUrl = "<?= base_url() ?>";

// LOGIN
document.getElementById('loginForm').addEventListener('submit', async e=>{
  e.preventDefault();
  alert.innerHTML='';
  try{
    const res = await axios.post(baseUrl+'auth/processLogin',{
      email:e.target.email.value.trim(),
      password:e.target.password.value
    });
    alert.innerHTML=`<div class="alert alert-${res.data.status?'success':'danger'}">${res.data.message}</div>`;
    if(res.data.status) setTimeout(()=>location.href=res.data.redirect,900);
  }catch{
    alert.innerHTML=`<div class="alert alert-danger">Server error</div>`;
  }
});

// CHANGE PASSWORD
document.getElementById('changeForm').addEventListener('submit', async e=>{
  e.preventDefault();
  alertChange.innerHTML='';
  const {oldPassword,newPassword,confirmPassword} = e.target;

  if(newPassword.value!==confirmPassword.value){
    alertChange.innerHTML='<div class="alert alert-danger">Password not matched</div>';
    return;
  }

  try{
    const res = await axios.post(baseUrl+'auth/changePassword',{
      oldPassword: oldPassword.value,
      newPassword: newPassword.value,
      confirmPassword: confirmPassword.value
    });
    alertChange.innerHTML=`<div class="alert alert-${res.data.status?'success':'danger'}">${res.data.message}</div>`;
    if(res.data.status) e.target.reset();
  }catch{
    alertChange.innerHTML='<div class="alert alert-danger">Server error</div>';
  }
});
</script>
</body>
</html>
