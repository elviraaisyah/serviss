<?php
$conn = mysqli_connect("localhost", "root", "", "servis");
if (!$conn) die("Koneksi gagal: " . mysqli_connect_error());

// Tambah data
if (isset($_POST['tambah'])) {
  $plat = $_POST['plat'];
  $tipe = $_POST['tipe'];
  $perusahaan = $_POST['perusahaan'];
  $jenis_servis = $_POST['jenis_servis'];
  $tanggal = $_POST['tanggal'];

  // Cek apakah kendaraan sudah ada
  $cek = mysqli_query($conn, "SELECT id FROM kendaraan WHERE plat = '$plat'");
  if (mysqli_num_rows($cek) > 0) {
    $kendaraan = mysqli_fetch_assoc($cek);
    $id_kendaraan = $kendaraan['id'];
  } else {
    mysqli_query($conn, "INSERT INTO kendaraan (jenis_merk, tahun_pembuatan, pemilik, plat) 
                         VALUES ('$tipe', 2020, '$perusahaan', '$plat')");
    $id_kendaraan = mysqli_insert_id($conn);
  }

  mysqli_query($conn, "INSERT INTO jadwal_servis (id_kendaraan, jenis_servis, tanggal_servis, status)
                       VALUES ($id_kendaraan, '$jenis_servis', '$tanggal', 'belum')");

  header("Location: servis.php?pesan=sukses_tambah");
  exit;
}

// Hapus data
if (isset($_GET['hapus'])) {
  $id = $_GET['hapus'];
  mysqli_query($conn, "DELETE FROM jadwal_servis WHERE id = $id");
  header("Location: servis.php?pesan=sukses_hapus");
  exit;
}

// Edit data
if (isset($_POST['update'])) {
  $id = $_POST['id'];
  $jenis_servis = $_POST['jenis_servis'];
  $tanggal = $_POST['tanggal'];

  mysqli_query($conn, "UPDATE jadwal_servis 
                       SET jenis_servis = '$jenis_servis', tanggal_servis = '$tanggal'
                       WHERE id = $id");

  header("Location: servis.php?pesan=sukses_update");
  exit;
}

// Ambil data perusahaan
$perusahaanList = mysqli_query($conn, "SELECT nama FROM perusahaan");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manajemen Servis Kendaraan</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(to right, #e0f2fe, #bae6fd);
      padding: 20px;
    }

    h2 {
      text-align: center;
      color: #1e3a8a;
      background: linear-gradient(to right, #3b82f6, #1d4ed8);
      color: white;
      padding: 15px;
      border-radius: 10px;
      max-width: 600px;
      margin: 0 auto 20px auto;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    form {
      background: #ffffff;
      padding: 20px;
      border-radius: 10px;
      max-width: 600px;
      margin: auto;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    input, select {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    button {
      width: 100%;
      padding: 10px;
      background: linear-gradient(to right, #3b82f6, #2563eb);
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
      transition: background 0.3s ease;
    }

    button:hover {
      background: linear-gradient(to right, #1d4ed8, #1e40af);
    }

    table {
      width: 100%;
      margin-top: 30px;
      border-collapse: collapse;
      background: #ffffff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    th, td {
      padding: 12px;
      border: 1px solid #ccc;
      text-align: center;
    }

    th {
      background: linear-gradient(to right, #3b82f6, #1d4ed8);
      color: white;
    }

    .btn {
      padding: 6px 10px;
      text-decoration: none;
      color: white;
      border-radius: 4px;
      font-size: 13px;
    }

    .edit {
      background: linear-gradient(to right, #34d399, #10b981);
    }

    .hapus {
      background: linear-gradient(to right, #f87171, #ef4444);
    }

    .toast {
      text-align: center;
      background: #16a34a;
      color: white;
      padding: 10px;
      border-radius: 8px;
      margin: 15px auto;
      max-width: 500px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>

  <?php if (isset($_GET['pesan'])): ?>
    <div class="toast" id="toast">
      <?php
        if ($_GET['pesan'] == "sukses_tambah") echo "✅ Data berhasil ditambahkan!";
        if ($_GET['pesan'] == "sukses_update") echo "✅ Data berhasil diperbarui!";
        if ($_GET['pesan'] == "sukses_hapus") echo "🗑️ Data berhasil dihapus!";
      ?>
    </div>
    <script>
      setTimeout(() => {
        const toast = document.getElementById('toast');
        if (toast) toast.style.display = 'none';
      }, 3000);
    </script>
  <?php endif; ?>

  <h2>Manajemen Servis Kendaraan</h2>

  <?php
  if (isset($_GET['edit'])):
    $edit_id = $_GET['edit'];
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM jadwal_servis WHERE id = $edit_id"));
  ?>
    <form method="POST">
      <input type="hidden" name="id" value="<?= $edit['id'] ?>">
      <label>Jenis Servis:</label>
      <select name="jenis_servis" required>
        <option value="rutin" <?= $edit['jenis_servis'] == 'rutin' ? 'selected' : '' ?>>Rutin</option>
        <option value="pajak_tahunan" <?= $edit['jenis_servis'] == 'pajak_tahunan' ? 'selected' : '' ?>>Pajak Tahunan</option>
        <option value="ganti_plat" <?= $edit['jenis_servis'] == 'ganti_plat' ? 'selected' : '' ?>>Ganti Plat</option>
      </select>

      <label>Tanggal Servis:</label>
      <input type="date" name="tanggal" value="<?= $edit['tanggal_servis'] ?>" required>

      <button type="submit" name="update">Simpan Perubahan</button>
    </form>

  <?php else: ?>
    <form method="POST">
      <input type="text" name="plat" placeholder="Plat Nomor" required>
      <input type="text" name="tipe" placeholder="Tipe Kendaraan" required>

      <select name="perusahaan" required>
        <option value="">-- Pilih Perusahaan --</option>
        <?php while($row = mysqli_fetch_assoc($perusahaanList)): ?>
          <option value="<?= $row['nama'] ?>"><?= $row['nama'] ?></option>
        <?php endwhile; ?>
      </select>

      <select name="jenis_servis" required>
        <option value="">Jenis Servis</option>
        <option value="rutin">Rutin</option>
        <option value="pajak_tahunan">Pajak Tahunan</option>
        <option value="ganti_plat">Ganti Plat</option>
      </select>

      <input type="date" name="tanggal" required>

      <button type="submit" name="tambah">+ Tambah Data</button>
    </form>
  <?php endif; ?>

  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Plat</th>
        <th>Tipe</th>
        <th>Perusahaan</th>
        <th>Servis</th>
        <th>Tanggal</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $no = 1;
      $data = mysqli_query($conn, "SELECT js.id, k.plat, k.jenis_merk, k.pemilik, js.jenis_servis, js.tanggal_servis 
                                   FROM jadwal_servis js 
                                   JOIN kendaraan k ON js.id_kendaraan = k.id 
                                   ORDER BY js.tanggal_servis DESC");

      while ($row = mysqli_fetch_assoc($data)):
      ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= $row['plat'] ?></td>
          <td><?= $row['jenis_merk'] ?></td>
          <td><?= $row['pemilik'] ?></td>
          <td><?= ucwords(str_replace('_', ' ', $row['jenis_servis'])) ?></td>
          <td><?= $row['tanggal_servis'] ?></td>
          <td>
            <a href="?edit=<?= $row['id'] ?>" class="btn edit">Edit</a>
            <a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus?')" class="btn hapus">Hapus</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

</body>
</html>
