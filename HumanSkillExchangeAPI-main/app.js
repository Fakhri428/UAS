// Base URL Laravel API. Sesuaikan jika server berjalan di host/port lain.
const API_BASE = "http://127.0.0.1:8000/api";

const codeText = `POST /api/login
Content-Type: application/json

{
  "email": "fakhri@example.com",
  "password": "password123"
}

Response:
{
  "status": true,
  "message": "Login berhasil",
  "data": {
    "user": { "id": 1, "name": "Fakhri", "plan": "Gratis" },
    "token_type": "Bearer",
    "access_token": "fakhri-token-123"
  }
}`;

const typingTarget = document.getElementById("typing-code");
let index = 0;

function typeCode() {
    if (!typingTarget) {
        return;
    }

    if (index < codeText.length) {
        typingTarget.textContent += codeText.charAt(index);
        index++;
        setTimeout(typeCode, 15);
    }
}

typeCode();

const fadeElements = document.querySelectorAll(".fade-up");

const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.classList.add("show");
        }
    });
}, {
    threshold: 0.18
});

fadeElements.forEach((element) => observer.observe(element));

const btnLogin = document.getElementById("btnLogin");
const btnMatches = document.getElementById("btnMatches");
const btnPlans = document.getElementById("btnPlans");
const apiResult = document.getElementById("apiResult");

function showResult(data) {
    apiResult.textContent = JSON.stringify(data, null, 2);
}

function showError(message, error) {
    apiResult.textContent = `${message}\n\n${error}`;
}

if (btnLogin) {
    btnLogin.addEventListener("click", async () => {
        apiResult.textContent = "Mengirim request login...";

        try {
            const response = await fetch(`${API_BASE}/login`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    email: "fakhri@example.com",
                    password: "password123"
                })
            });

            showResult(await response.json());
        } catch (error) {
            showError("Gagal login. Pastikan server Laravel aktif (php artisan serve).", error);
        }
    });
}

if (btnMatches) {
    btnMatches.addEventListener("click", async () => {
        apiResult.textContent = "Mengambil daftar match...";

        try {
            const response = await fetch(`${API_BASE}/matches`, {
                method: "GET",
                headers: {
                    "Authorization": "Bearer fakhri-token-123",
                    "Accept": "application/json"
                }
            });

            showResult(await response.json());
        } catch (error) {
            showError("Gagal mengambil match. Pastikan database sudah di-seed.", error);
        }
    });
}

if (btnPlans) {
    btnPlans.addEventListener("click", async () => {
        apiResult.textContent = "Mengambil paket langganan...";

        try {
            const response = await fetch(`${API_BASE}/plans`, {
                method: "GET",
                headers: {
                    "Accept": "application/json"
                }
            });

            showResult(await response.json());
        } catch (error) {
            showError("Gagal mengambil paket.", error);
        }
    });
}
