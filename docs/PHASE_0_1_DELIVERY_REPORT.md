# Phase 0 & Phase 1 Delivery Report — Niche Lab
**YouTube Auto Content Studio (CollabPals Niche Score–Driven)**
**Tanggal:** 30 Oktober 2026  
**Status:** Phase 0 & Phase 1 Completed (Siap untuk Gate 1 Approval)

---

## 1. Hasil Audit Infrastruktur (Phase 0)
- **PHP:** 8.4.21 (CLI Visual C++ 2022 x64, Laragon)
- **Composer:** 2.9.4
- **Database:** MariaDB 10.11 / MySQL 8.4 pada port `3308`
- **Database Name:** `sleepless`
- **Node.js:** v24.12.0
- **Python:** 3.13.0
- **Browser Automation:** Playwright 1.60.0 dengan Chromium / Edge headless runner
- **App Stack:** Laravel 12 + Tailwind CSS + Eloquent ORM + Playwright Worker

---

## 2. Hasil Data Aktual CollabPals (Live Verified)

Data berikut **100% diambil langsung** dari `https://www.collabpals.com/tools/youtube-niche-finder` menggunakan Playwright worker (tanpa mengarang skor):

| Parameter | Electric Vehicles (EV) | Sleep Content |
|---|---|---|
| **CollabPals Niche Score** | **78** (0–100) | **72** (0–100) |
| **Kategori Sumber** | Automotive | ASMR / Ambience |
| **CPM Range** | **$6–$18** | **$2–$5** |
| **Views per 1K Subs** | 38 | 35 |
| **Growth Trend** | Rising | Rising |
| **Equipment Level** | Basic | Basic |
| **Faceless-Friendly** | No (Tampil basic) | **Yes (Faceless)** |
| **Status Verifikasi** | **Verified** | **Verified** |
| **Confidence** | 1.0 (100%) | 1.0 (100%) |
| **Bukti Screenshot** | `public/storage/evidence/collabpals_electric_vehicles_*.png` | `public/storage/evidence/collabpals_sleep_content_*.png` |
| **Contoh Channel** | Out of Spec, Electric Viking, Cleanerwatt | Jason Stephenson, Michael Sealey, SleepTube |
| **Format Konten Populer**| Reviews, Range tests, Charging guides, Comparisons | Sleep stories, Rain sounds, White noise, Guided sleep |

---

## 3. Hasil Perhitungan Decision Engine (PRD Section 3.4)

### Formula Lapisan B:
$$\text{internal\_score} = 0.40 \times \text{niche\_score} + 0.20 \times \text{feasibility} + 0.20 \times \text{audience} + 0.10 \times \text{economics} + 0.10 \times \text{diversification}$$

1. **Electric Vehicles (EV):**
   - Niche Score (40%): $0.40 \times 78 = 31.20$
   - Feasibility (20%): $0.20 \times 85 = 17.00$
   - Audience Validation (20%): $0.20 \times 85 = 17.00$
   - Production Economics (10%): $0.10 \times 75 = 7.50$
   - Monetization Diversification (10%): $0.10 \times 85 = 8.50$
   - **Internal Pilot Score:** **81.20 / 100** (`recommended`)

2. **Sleep Content:**
   - Niche Score (40%): $0.40 \times 72 = 28.80$
   - Feasibility (20%): $0.20 \times 80 = 16.00$
   - Audience Validation (20%): $0.20 \times 80 = 16.00$
   - Production Economics (10%): $0.10 \times 70 = 7.00$
   - Monetization Diversification (10%): $0.10 \times 65 = 6.50$
   - **Internal Pilot Score:** **74.30 / 100** (`pending`)

### Evaluasi Selisih Skor:
- **Selisih Skor CollabPals:** $78 - 72 = 6$ poin ($\ge 5$ poin).
- **Rekomendasi Algoritma:** Sesuai PRD Section 3.4, karena Electric Vehicles memiliki keunggulan $\ge 5$ poin dan lolos kriteria *content feasibility*, **Electric Vehicles direkomendasikan sebagai kandidat utama pilot channel.**

---

## 4. Laporan Pengujian Otomatis (Test Report)

Menjalankan `php artisan test tests/Feature/NicheLabTest.php`:
```text
PASS  Tests\Feature\NicheLabTest
✓ niche lab dashboard renders successfully
✓ decision engine calculates correct internal score
✓ decision engine blocks unverified snapshot
✓ stale snapshot detection
✓ manual score entry creates needs review status
✓ reviewer can verify snapshot
✓ owner cannot select unverified niche
✓ owner can select verified niche
✓ audit log is recorded

Tests:    9 passed (25 assertions)
Duration: 1.45s
```

---

## 5. Cara Menjalankan & Mengakses Niche Lab

1. Server lokal aktif di: **`http://127.0.0.1:8000`**
2. Command CLI untuk live capture:
   ```bash
   # Capture semua kandidat
   php artisan niche:capture

   # Capture spesifik kandidat
   php artisan niche:capture electric-vehicles
   php artisan niche:capture sleep-content
   ```
3. Seluruh screenshot bukti tersimpan di `public/storage/evidence/`.

