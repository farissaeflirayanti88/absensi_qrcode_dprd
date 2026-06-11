from locust import HttpUser, task, between
from bs4 import BeautifulSoup
import random
import time

EVENT_ID = 18

class PesertaReses(HttpUser):
    wait_time = between(1, 3)

    @task
    def absensi_via_qr(self):
        # 1. Buka form absensi
        with self.client.get(
            f"/attendance/form/{EVENT_ID}",
            name="Buka Form Absensi",
            catch_response=True
        ) as response:

            if response.status_code != 200:
                response.failure(f"Gagal buka form: {response.status_code}")
                return

            soup = BeautifulSoup(response.text, "html.parser")
            token_input = soup.find("input", {"name": "_token"})

            if not token_input:
                response.failure("CSRF token tidak ditemukan")
                return

            csrf_token = token_input["value"]
            response.success()

        # 2. Data peserta unik
        unique_number = int(time.time() * 1000) + random.randint(1000, 9999)

        form_data = {
            "_token": csrf_token,
            "nama": f"PESERTA TEST {unique_number}",
            "alamat": "Jl. Raya Batam Centre Kota Batam",
            "telepon": f"0812{random.randint(10000000, 99999999)}",
            "confirmation": "1"
        }

        # 3. Submit absensi
        with self.client.post(
            f"/attendance/store/{EVENT_ID}",
            data=form_data,
            name="Submit Absensi Peserta",
            catch_response=True,
            allow_redirects=True
        ) as submit:

            text = submit.text.lower()

            if submit.status_code not in [200, 302]:
                submit.failure(f"Gagal submit: {submit.status_code}")
                return

            if "sudah absen" in text or "sudah melakukan absensi" in text:
                submit.failure("Duplikasi absensi")
                return

            if "the nama field is required" in text:
                submit.failure("Field nama tidak terbaca")
                return

            if "the alamat field is required" in text:
                submit.failure("Field alamat tidak terbaca")
                return

            if "the telepon field is required" in text:
                submit.failure("Field telepon tidak terbaca")
                return

            if "error" in text or "exception" in text:
                submit.failure("Terjadi error pada halaman submit")
                return

            submit.success()