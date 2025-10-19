// Add click events for login/register if the buttons exist (on index page)
if (loginBtn) {
    loginBtn.addEventListener('click', () => {
        window.location.href = 'login/login.php';
    });
}

if (registerBtn) {
    registerBtn.addEventListener('click', () => {
        window.location.href = 'login/login.php';
    });
}