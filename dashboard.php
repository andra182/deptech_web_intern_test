<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
$query_admin = mysqli_query($conn, "SELECT * FROM admins WHERE id='$admin_id'");
$admin_data = mysqli_fetch_assoc($query_admin);

$_SESSION['admin_nama_depan'] = $admin_data['nama_depan'];
$_SESSION['admin_nama_belakang'] = $admin_data['nama_belakang'];
$_SESSION['admin_nama'] = $admin_data['nama_depan'] . ' ' . $admin_data['nama_belakang'];
$_SESSION['admin_email'] = $admin_data['email'];
$_SESSION['admin_tanggal_lahir'] = $admin_data['tanggal_lahir'];
$_SESSION['admin_jenis_kelamin'] = $admin_data['jenis_kelamin'];

$query_students = "SELECT * FROM siswa ORDER BY nama_depan";
$result_students = mysqli_query($conn, $query_students);
$students = mysqli_fetch_all($result_students, MYSQLI_ASSOC);

$query_ekskul = "SELECT * FROM ekstrakurikuler ORDER BY nama";
$result_ekskul = mysqli_query($conn, $query_ekskul);
$ekskulList = mysqli_fetch_all($result_ekskul, MYSQLI_ASSOC);

$query_siswa_ekskul = "SELECT e.nama as ekskul_nama, 
                              CONCAT(s.nama_depan, ' ', s.nama_belakang) as nama_lengkap,
                              se.tahun_mulai,
                              s.jenis_kelamin,
                              se.id,
                              se.siswa_id,
                              se.ekstrakurikuler_id
                       FROM siswa_ekstrakurikuler se
                       JOIN siswa s ON se.siswa_id = s.id
                       JOIN ekstrakurikuler e ON se.ekstrakurikuler_id = e.id
                       ORDER BY e.nama, s.nama_depan";
$result_siswa_ekskul = mysqli_query($conn, $query_siswa_ekskul);

$studentsByEx = [];
while ($row = mysqli_fetch_assoc($result_siswa_ekskul)) {
    if (!isset($studentsByEx[$row['ekskul_nama']])) {
        $studentsByEx[$row['ekskul_nama']] = [];
    }
    $studentsByEx[$row['ekskul_nama']][] = [
        'nama_lengkap' => $row['nama_lengkap'],
        'tahun_mulai' => $row['tahun_mulai'],
        'jenis_kelamin' => $row['jenis_kelamin'],
        'id' => $row['id']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Tabel Interaktif</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold text-gray-800">Aplikasi Manajemen Siswa</span>
                </div>
                <div class="flex items-center">                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 relative">
                            <img class="h-8 w-8 rounded-full bg-gray-300" 
                                src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['admin_nama_depan'] . ' ' . $_SESSION['admin_nama_belakang']); ?>" 
                                alt="Profile">
                            <span class="text-gray-700"><?php echo htmlspecialchars($_SESSION['admin_nama_depan'] . ' ' . $_SESSION['admin_nama_belakang']); ?></span>
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div @click.away="open = false" x-show="open" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1">
                            <a href="#" onclick="showModal('modalEditProfile')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Edit Profile</a>
                            <a href="#" onclick="showModal('modalChangePassword')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Ubah Password</a>
                            <hr class="my-1">
                            <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="p-6 bg-gray-100">
        <!-- Tabel Siswa -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold mb-4">Tabel Siswa</h2>
            <div class="flex justify-between items-center mb-4">
                <div></div>
                <button class="ml-2 p-2 bg-yellow-500 text-white rounded justify-end" onclick="showModal('modalSiswa')">Add New</button>
            </div>
            <div class="overflow-x-auto">
                <table id="table-siswa" class="min-w-full bg-white shadow-md rounded-lg">
                    <thead>
                        <tr class="bg-blue-200">
                            <th class="px-4 py-2">Foto</th>
                            <th class="px-4 py-2">Nama Depan</th>
                            <th class="px-4 py-2">Nama Belakang</th>
                            <th class="px-4 py-2">NIS</th>
                            <th class="px-4 py-2">Nomor HP</th>
                            <th class="px-4 py-2">Jenis Kelamin</th>
                            <th class="px-4 py-2">Alamat</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($students as $i => $s): ?>
                        <tr class="<?php echo $i % 2 == 0 ? 'bg-white' : 'bg-gray-50'; ?>">
                            <td class="border px-4 py-2"><img src="<?php echo $s['foto']; ?>" alt="Foto <?php echo $s['nama_depan']; ?>" class="w-10 h-10 rounded-full mx-auto"></td>
                            <td class="border px-4 py-2"><?php echo $s['nama_depan']; ?></td>
                            <td class="border px-4 py-2"><?php echo $s['nama_belakang']; ?></td>
                            <td class="border px-4 py-2"><?php echo $s['nis']; ?></td>
                            <td class="border px-4 py-2"><?php echo $s['nomor_hp']; ?></td>
                            <td class="border px-4 py-2"><?php echo $s['jenis_kelamin']; ?></td>
                            <td class="border px-4 py-2"><?php echo $s['alamat']; ?></td>
                            <td class="border px-4 py-2">
                                <div class="flex w-full space-x-2 justify-center">
                                    <button onclick="editStudent('<?php echo $s['nis']; ?>')" 
                                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-sm">
                                        Edit
                                    </button>
                                    <button onclick="deleteStudent('<?php echo $s['nis']; ?>')"
                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-sm">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabel Ekstrakurikuler -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold mb-4">Tabel Ekstrakurikuler</h2>
            <div class="flex justify-between items-center mb-4">
                <div></div>
                <button class="ml-2 p-2 bg-yellow-500 text-white rounded justify-end" onclick="showModal('modalEkskul')">Add New</button>
            </div> 
            <div class="overflow-x-auto">
                <table id="table-ekskul" class="min-w-full bg-white shadow-md rounded-lg">
                    <thead>
                        <tr class="bg-green-200">
                            <th class="px-4 py-2 ">Nama Ekstrakurikuler</th>
                            <th class="px-4 py-2 ">Penanggung Jawab</th>
                            <th class="px-4 py-2 ">Status</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($ekskulList as $i => $e): ?>
                        <tr class="<?php echo $i % 2 == 0 ? 'bg-white' : 'bg-gray-50'; ?>">
                            <td class="border px-4 py-2"><?php echo $e['nama']; ?></td>
                            <td class="border px-4 py-2"><?php echo $e['penanggung_jawab']; ?></td>
                            <td class="border px-4 py-2"><?php echo $e['status']; ?></td>
                            <td class="border px-4 py-2">
                                <div class="flex w-full space-x-2 justify-center">
                                    <button onclick="editEkskul('<?php echo $e['id']; ?>')" 
                                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-sm">
                                        Edit
                                    </button>
                                    <button onclick="deleteEkskul('<?php echo $e['id']; ?>')"
                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-sm">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabel Siswa dengan Ekstrakurikuler -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold mb-4">Tabel Siswa dengan Ekstrakurikuler</h2>
            <div class="flex justify-between items-center mb-4">
                <div></div>
                <button class="ml-2 p-2 bg-yellow-500 text-white rounded justify-end" onclick="showModal('modalSiswaEkskul')">Add New</button>
            </div>
                <div class="overflow-x-auto">
                <table id="table-siswa-ekskul" class="min-w-full bg-white shadow-md rounded-lg">
                    <thead>
                        <tr class="bg-purple-200">
                            <th class="px-4 py-2">Nama Lengkap</th>
                            <th class="px-4 py-2">Tahun Mulai</th>
                            <th class="px-4 py-2">Jenis Kelamin</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($studentsByEx as $ekskulName => $list): ?>
                        <tr class="bg-gray-200 font-bold">
                            <td colspan="3"><?php echo $ekskulName; ?></td>
                        </tr>
                            <?php foreach($list as $j => $std): ?>
                            <tr class="<?php echo $j % 2 == 0 ? 'bg-white' : 'bg-gray-50'; ?>">
                                <td class="border px-4 py-2"><?php echo $std['nama_lengkap']; ?></td>
                                <td class="border px-4 py-2"><?php echo $std['tahun_mulai']; ?></td>
                                <td class="border px-4 py-2"><?php echo $std['jenis_kelamin']; ?></td>
                                <td class="border px-4 py-2">
                                <div class="flex w-full space-x-2 justify-center">
                                    <button onclick="editStudentWithEkskul('<?php echo $std['id']; ?>')" 
                                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-sm">
                                        Edit
                                    </button>
                                    <button onclick="deleteStudentWithEkskul('<?php echo $std['id']; ?>')"
                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-sm">
                                        Delete
                                    </button>
                                </div>
                            </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>    <!-- Modal Edit Profile -->
    <div id="modalEditProfile" class="hidden fixed inset-0 z-50">
        <div class="fixed inset-0 bg-black opacity-50"></div>
        <div class="relative w-full max-w-lg mx-auto my-12">
            <div class="relative bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900">Edit Profile</h3>
                    <button onclick="closeModal('modalEditProfile')" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form id="formEditProfile" method="POST" action="update_profile.php">
                    <input type="hidden" name="admin_id" value="<?php echo $_SESSION['admin_id']; ?>">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Depan</label>
                            <input type="text" name="nama_depan" id="edit_nama_depan" 
                                value="<?php echo htmlspecialchars($_SESSION['admin_nama_depan']); ?>" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Belakang</label>
                            <input type="text" name="nama_belakang" id="edit_nama_belakang" 
                                value="<?php echo htmlspecialchars($_SESSION['admin_nama_belakang']); ?>" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="edit_email" 
                                value="<?php echo htmlspecialchars($_SESSION['admin_email']); ?>" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" id="edit_tanggal_lahir" 
                                value="<?php echo $_SESSION['admin_tanggal_lahir']; ?>" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="edit_jenis_kelamin" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki" <?php echo ($_SESSION['admin_jenis_kelamin'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                                <option value="Perempuan" <?php echo ($_SESSION['admin_jenis_kelamin'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('modalEditProfile')" 
                            class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="submit" 
                            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Change Password -->
    <div id="modalChangePassword" class="hidden fixed inset-0 z-50">
        <div class="fixed inset-0 bg-black opacity-50"></div>
        <div class="relative w-full max-w-lg mx-auto my-12">
            <div class="relative bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900">Ubah Password</h3>
                    <button onclick="closeModal('modalChangePassword')" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form id="formChangePassword" method="POST" action="update_password.php">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Password Lama</label>
                            <input type="password" name="old_password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Password Baru</label>
                            <input type="password" name="new_password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                            <input type="password" name="confirm_password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('modalChangePassword')" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
    function openAddModal(param) {
        document.getElementById(param).classList.remove("hidden");
    }

    function closeAddModal(param) {
        document.getElementById(param).classList.add("hidden");
    }

    function saveNewData(param) {
        closeAddModal(param);
    }

    function showModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    function editStudent(nis) {
        fetch(`get_siswa.php?nis=${nis}`)
            .then(response => response.json())
            .then(data => {
                if (!data.error) {
                    document.getElementById('siswaId').value = data.id;
                    document.getElementById('nama_depan').value = data.nama_depan;
                    document.getElementById('nama_belakang').value = data.nama_belakang;
                    document.getElementById('nis').value = data.nis;
                    document.getElementById('nomor_hp').value = data.nomor_hp;
                    document.getElementById('jenis_kelamin').value = data.jenis_kelamin;
                    document.getElementById('alamat').value = data.alamat;
                    document.getElementById('modalTitle').textContent = 'Edit Student';
                    document.getElementById('formSiswa').action = 'update_siswa.php';
                    showModal('modalSiswa');
                } else {
                    alert('Error: ' + data.error);
                }
            });
    }

    function deleteStudent(nis) {
        if (confirm('Apakah anda yakin ingin menghapus siswa ini?')) {
            fetch('delete_siswa.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `nis=${nis}`
            })
            .then(response => response.text())
            .then(result => {
                if (result === 'success') {
                    location.reload();
                } else {
                    alert('Gagal menghapus siswa: ' + result);
                }
            });
        }
    }

    function editEkskul(id) {
        fetch(`get_ekskul.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('ekskulId').value = data.id;
                document.getElementById('nama_ekskul').value = data.nama;
                document.getElementById('penanggung_jawab').value = data.penanggung_jawab;
                document.getElementById('status').value = data.status;
                document.getElementById('modalTitleEkskul').textContent = 'Edit Ekstrakurikuler';
                document.getElementById('formEkskul').action = 'update_ekskul.php';
                showModal('modalEkskul');
            });
    }

    function deleteEkskul(id) {
        if (confirm('Apakah anda yakin ingin menghapus ekstrakurikuler ini?')) {
            fetch('delete_ekskul.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${id}`
            })
            .then(response => response.text())
            .then(result => {
                if (result === 'success') {
                    location.reload();
                } else {
                    alert('Gagal menghapus ekstrakurikuler: ' + result);
                }
            });
        }
    }

    function editStudentWithEkskul(id) {
        fetch(`get_siswa_ekskul.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('siswaEkskulId').value = data.id;
                document.getElementById('siswa_id').value = data.siswa_id;
                document.getElementById('ekstrakurikuler_id').value = data.ekstrakurikuler_id;
                document.getElementById('tahun_mulai').value = data.tahun_mulai;
                document.getElementById('modalTitleSiswaEkskul').textContent = 'Edit Student Extracurricular';
                document.getElementById('formSiswaEkskul').action = 'update_siswa_ekskul.php';
                showModal('modalSiswaEkskul');
            });
    }

    function deleteStudentWithEkskul(id) {
        if (confirm('Apakah anda yakin ingin menghapus data ini?')) {
            fetch('delete_siswa_ekskul.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${id}`
            })
            .then(response => response.text())
            .then(result => {
                if (result === 'success') {
                    location.reload();
                } else {
                    alert('Gagal menghapus data: ' + result);
                }
            });
        }
    }

    document.querySelectorAll('[onclick^="openAddModal"]').forEach(button => {
        button.onclick = function() {
            const modalId = {
                'addNewModalSiswa': 'modalSiswa',
                'addNewModalEkskul': 'modalEkskul',
                'addNewModalSiswaWithEkskul': 'modalSiswaEkskul'
            }[this.getAttribute('onclick').match(/'([^']+)'/)[1]];
            showModal(modalId);
        }
    });

    window.onclick = function(event) {
        const modals = ['modalSiswa', 'modalEkskul', 'modalSiswaEkskul'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (event.target === modal) {
                closeModal(modalId);
            }
        });
    }
    </script>

    <!-- Modal Siswa -->
    <div id="modalSiswa" class="hidden fixed inset-0 z-50">
        <div class="fixed inset-0 bg-black opacity-50"></div>
        
        <div class="relative w-full max-w-lg mx-auto my-12">
            <div class="relative bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900" id="modalTitle">Add New Student</h3>
                    <button onclick="closeModal('modalSiswa')" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form id="formSiswa" method="POST" action="create_siswa.php" enctype="multipart/form-data">
                    <input type="hidden" id="siswaId" name="id">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Depan</label>
                            <input type="text" name="nama_depan" id="nama_depan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Belakang</label>
                            <input type="text" name="nama_belakang" id="nama_belakang" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">NIS</label>
                            <input type="text" name="nis" id="nis" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nomor HP</label>
                            <input type="text" name="nomor_hp" id="nomor_hp" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="jenis_kelamin" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Foto</label>
                            <input type="file" name="foto" id="foto" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Alamat</label>
                            <textarea name="alamat" id="alamat" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required></textarea>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('modalSiswa')" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Ekstrakurikuler -->
    <div id="modalEkskul" class="hidden fixed inset-0 z-50">
        <div class="fixed inset-0 bg-black opacity-50"></div>
        
        <div class="relative w-full max-w-lg mx-auto my-12">
            <div class="relative bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900" id="modalTitleEkskul">Add New Ekstrakurikuler</h3>
                    <button onclick="closeModal('modalEkskul')" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form id="formEkskul" method="POST" action="create_ekskul.php">
                    <input type="hidden" id="ekskulId" name="id">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Ekstrakurikuler</label>
                            <input type="text" name="nama" id="nama_ekskul" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Penanggung Jawab</label>
                            <input type="text" name="penanggung_jawab" id="penanggung_jawab" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                                <option value="">Pilih Status</option>
                                <option value="Aktif">Aktif</option>
                                <option value="Tidak Aktif">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('modalEkskul')" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Siswa Ekstrakurikuler -->
    <div id="modalSiswaEkskul" class="hidden fixed inset-0 z-50">
        <div class="fixed inset-0 bg-black opacity-50"></div>
        
        <div class="relative w-full max-w-lg mx-auto my-12">
            <div class="relative bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900" id="modalTitleSiswaEkskul">Add Student to Ekstrakurikuler</h3>
                    <button onclick="closeModal('modalSiswaEkskul')" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form id="formSiswaEkskul" method="POST" action="create_siswa_ekskul.php">
                    <input type="hidden" id="siswaEkskulId" name="id">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Siswa</label>
                            <select name="siswa_id" id="siswa_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                                <option value="">Pilih Siswa</option>
                                <?php
                                $query_siswa = "SELECT id, CONCAT(nama_depan, ' ', nama_belakang) as nama_lengkap FROM siswa";
                                $result_siswa = mysqli_query($conn, $query_siswa);
                                while($row = mysqli_fetch_assoc($result_siswa)) {
                                    echo "<option value='".$row['id']."'>".$row['nama_lengkap']."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Ekstrakurikuler</label>
                            <select name="ekstrakurikuler_id" id="ekstrakurikuler_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                                <option value="">Pilih Ekstrakurikuler</option>
                                <?php
                                $query_ekskul = "SELECT id, nama FROM ekstrakurikuler WHERE status = 'Aktif'";
                                $result_ekskul = mysqli_query($conn, $query_ekskul);
                                while($row = mysqli_fetch_assoc($result_ekskul)) {
                                    echo "<option value='".$row['id']."'>".$row['nama']."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tahun Mulai</label>
                            <input type="number" name="tahun_mulai" id="tahun_mulai" min="2000" max="2099" step="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('modalSiswaEkskul')" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
