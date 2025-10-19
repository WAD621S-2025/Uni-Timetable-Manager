document.addEventListener("DOMContentLoaded", () => {
    const container = document.querySelector(".container");
    const registerBtn = document.querySelector(".register-btn");
    const loginBtn = document.querySelector(".login-btn");

    registerBtn.addEventListener("click", () => container.classList.add("active"));
    loginBtn.addEventListener("click", () => container.classList.remove("active"));

    const loginForm = document.querySelector(".form-box.login form");
    const registerForm = document.querySelector(".form-box.register form");

    loginForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        const formData = new FormData(loginForm);
        formData.append("action", "login");

        try {
            const response = await fetch("login_registration.php", {
                method: "POST",
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            console.log("Login response:", result);

            if (result.success) {
                window.location.href = "../dashboard/dashboard.php";
            } else {
                alert(result.message || "Login failed!");
            }
        } catch (error) {
            console.error("Error during login:", error);
            alert("Error connecting to server. Check console for details.");
        }
    });

    registerForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        const formData = new FormData(registerForm);
        formData.append("action", "register");

        try {
            const response = await fetch("login_registration.php", {
                method: "POST",
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            console.log("Register response:", result);

            if (result.success) {
                alert("Registration successful! Please login.");
                container.classList.remove("active");
            } else {
                alert(result.message || "Registration failed.");
            }
        } catch (error) {
            console.error("Error during registration:", error);
            alert("Error connecting to server. Check console for details.");
        }
    });
});
