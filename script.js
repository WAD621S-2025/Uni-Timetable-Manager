const container = document.queryselector('.container');
const registerBtn = document.queryselector('.register-btn');
const loginBtn = document.queryselector('.login-btn');

registerBtn.addEventListener('click',()=>{
  container.classList.add('active');
})
loginBtn.addEventListener('click',()=>{
  container.classList.add('active');
})
