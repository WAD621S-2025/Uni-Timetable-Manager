const container = document.querySelector('.container');
const registerBtn = document.querySelector('.register-btn');
const loginBtn = document.querySelector('.login-btn');

registerBtn.addEventListener('click',()=>{
  container.classList.add('active');
})
loginBtn.addEventListener('click',()=>{
  container.classList.add('active');
})

const loginForm = document.querySelector('.form-box.login form');

loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(loginForm);

    const response = await fetch('login.php', {
        method: 'POST',
        body: formData
    });

    const result = await response.json();

    if (result.success) {
        window.location.href = 'dashboard.php'; 
    } else {
        alert(result.message); 
    }
});
const registerForm = document.querySelector('.form-box.register form');

registerForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(registerForm);

    const response = await fetch('register.php', {
        method: 'POST',
        body: formData
    });

    const result = await response.json();

    if (result.success) {
        alert('Registration successful! Please login.');
        container.classList.remove('active'); // switch to login form
    } else {
        alert(result.message); // show error
    }
});

