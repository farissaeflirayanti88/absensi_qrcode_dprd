from locust import HttpUser, task, between
from bs4 import BeautifulSoup

EVENT_ID = 18

class AdminUser(HttpUser):
    wait_time = between(1, 2)

    def on_start(self):
        # Ambil halaman login untuk mendapatkan CSRF token
        response = self.client.get("/login", name="Buka Login")

        soup = BeautifulSoup(response.text, "html.parser")
        token_input = soup.find("input", {"name": "_token"})
        csrf_token = token_input["value"] if token_input else ""

        # Login admin
        login_response = self.client.post(
            "/login",
            data={
                "_token": csrf_token,
                "username": "Admin1",
                "password": "12345678"
            },
            name="Login Admin",
            allow_redirects=True
        )

        if login_response.status_code >= 400:
            print("Login gagal:", login_response.status_code)

    @task(4)
    def dashboard(self):
        self.client.get("/dashboard", name="Dashboard")

    @task(3)
    def daftar_acara(self):
        self.client.get("/events", name="Daftar Acara")

    @task(3)
    def rekap_kehadiran(self):
        self.client.get("/attendances", name="Rekap Kehadiran")

    @task(2)
    def detail_acara(self):
        self.client.get(f"/events/{EVENT_ID}/detail", name="Detail Acara")

    @task(2)
    def halaman_qr(self):
        self.client.get(f"/events/{EVENT_ID}/qr", name="Halaman QR")

    @task(1)
    def form_absensi_peserta(self):
        self.client.get(f"/attendance/form/{EVENT_ID}", name="Form Absensi Peserta")