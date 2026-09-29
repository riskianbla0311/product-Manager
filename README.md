# Kedai Rasa : Product Manager (Praktikum 3 - Pemrograman Web)

Aplikasi manajemen menu **Minuman & Makanan** berbasis **PHP (PDO) + MySQL + CSS (Box Model & Flexbox)**.

> Nama: `ISI_NAMA` &nbsp;|&nbsp; NIM: `ISI_NIM`

## Fitur

| Fitur | Keterangan |
|---|---|
| Create | Nama, kategori (Minuman/Makanan), harga, stok + validasi server + pola PRG |
| Read | Card responsif (Flexbox), ringkasan stok & nilai persediaan |
| Update | Edit berdasarkan ID, form terisi otomatis |
| Delete | Method POST + token CSRF + konfirmasi |
| Validasi | Nama >= 3 karakter & unik, kategori hanya Minuman/Makanan, harga > 0, stok >= 0 |
| Keamanan | Prepared statement (PDO), `htmlspecialchars` untuk semua output, CSRF pada aksi ubah data |
| Bonus | Search (GET), filter kategori, pagination |

## Struktur

```
product-manager/
├── config/
│   ├── db.php           # koneksi PDO
│   └── helpers.php      # escape, CSRF, validasi
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── product_form.php # form dipakai create & edit
├── public/
│   ├── index.php        # READ + search/filter/pagination
│   ├── create.php       # CREATE + PRG
│   ├── edit.php         # READ one + UPDATE
│   ├── delete.php       # DELETE + CSRF
│   └── assets/style.css
├── database/store_db.sql
└── README.md
```


## Cara menjalankan (XAMPP)

1. Jalankan **Apache** dan **MySQL** di XAMPP Control Panel.
2. Salin folder `product-manager` ke `C:\xampp\htdocs\`.
3. Buka `http://localhost/phpmyadmin` -> tab **Import** -> pilih `database/store_db.sql` -> **Go**.
   (Atau lewat CLI: `mysql -u root < database/store_db.sql`). Import ulang akan mengosongkan tabel `products` lalu mengisi 12 menu contoh.
4. Cek `config/db.php`. Bawaan XAMPP: user `root`, password kosong.
5. Buka **http://localhost/product-manager/public/**

Alternatif tanpa Apache: masuk ke folder `public` lalu jalankan `php -S localhost:8000`, buka `http://localhost:8000`.

## Checklist pengujian

- [ ] Tambah produk valid -> muncul di daftar
- [ ] Nama < 3 karakter -> ditolak, tidak tersimpan
- [ ] Kategori selain Minuman/Makanan (ubah lewat DevTools) -> ditolak
- [ ] Harga <= 0 / stok negatif -> ditolak dengan pesan jelas
- [ ] Nama produk sama (tanpa peduli huruf besar/kecil) -> ditolak
- [ ] Refresh setelah create -> tidak ada data ganda (PRG)
- [ ] Nama `<b>Promo</b>` -> tampil sebagai teks, bukan tebal
- [ ] Edit dan hapus berjalan; hapus tanpa token -> 403
- [ ] Perkecil jendela browser -> card membungkus rapi

Catatan menguji validasi server: atribut `required`/`min` di HTML dapat menghentikan submit
di browser. Untuk melihat pesan dari server, hapus atributnya lewat DevTools (Inspect) lalu submit,
atau kirim request dengan `curl`.



## Upload ke GitHub

```bash
cd product-manager
git init
git add .
git commit -m "Praktikum 3: Product Manager PHP MySQL"
git branch -M main
git remote add origin https://github.com/USERNAME/product-manager.git
git push -u origin main
```

Buat dulu repository kosong bernama `product-manager` di github.com (tanpa README/gitignore
agar tidak bentrok), lalu ganti `USERNAME` dengan username GitHub kamu.
