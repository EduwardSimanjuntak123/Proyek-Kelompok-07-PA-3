<?php $__env->startSection('title', 'List Kelompok'); ?>

<?php $__env->startSection('content'); ?>
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>List Kelompok</h4>
                            <a href="<?php echo e(route('kelompok.create')); ?>" class="btn btn-primary">
                                <i class="nav-icon fas fa-folder-plus"></i>&nbsp; Tambah Kelompok
                            </a>
                        </div>
                        <div class="card-body">
                            <?php echo $__env->make('partials.alert', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <div class="table-responsive">
                                <table class="table table-striped" id="table-2">
                                    <thead>
                                        <tr>
                                            <th>Nomor Kelompok</th>
                                            <th>Kategori Proyek</th>
                                            <th>Angkatan</th>
                                            <th>Tahun Ajaran</th>
                                            <th>Program Studi</th>
                                            <th>Jumlah Mahasiswa</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $kelompok; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($item->nomor_kelompok); ?></td>
                                                <td><?php echo e($item->kategoripa->kategori_pa ?? 'N/A'); ?></td>
                                                <td><?php echo e($item->tahunMasuk->Tahun_Masuk ?? 'N/A'); ?></td>
                                                <td>
                                                    <?php echo e($item->tahunAjaran->tahun_mulai ?? 'N/A'); ?>

                                                    /
                                                    <?php echo e($item->tahunAjaran->tahun_selesai ?? 'N/A'); ?>

                                                </td>
                                                <td><?php echo e($item->prodi->nama_prodi ?? 'N/A'); ?></td>
                                                <td><?php echo e($item->kelompok_mahasiswa_count); ?></td>
                                                <td><?php echo e($item->status); ?></td>
                                                <td>
                                                    <div class="d-flex" style="gap: 8px;"   >
                                                        <a href="<?php echo e(route('kelompokMahasiswa.index', $item->id)); ?>"
                                                            class="btn btn-primary btn-sm" data-toggle="tooltip"
                                                            data-placement="top" title="Kelola Anggota Kelompok">
                                                            <i class="fas fa-users"></i>
                                                        </a>
                                                        <a href="<?php echo e(route('kelompok.edit', Crypt::encrypt($item->id))); ?>"
                                                            class="btn btn-success btn-sm" data-toggle="tooltip"
                                                            data-placement="top" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form method="POST"
                                                            action="<?php echo e(route('kelompok.destroy', $item->id)); ?>">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('delete'); ?>
                                                            <button class="btn btn-danger btn-sm show_confirm"
                                                                data-toggle="tooltip" data-placement="top" title="Hapus">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('script'); ?>
    <script>
        $(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });

        document.getElementById('agentBtn').addEventListener('click', function() {

            const mahasiswa = <?php echo json_encode($mahasiswa, 15, 512) ?>;

            console.log("DATA MAHASISWA:", mahasiswa);

            fetch("<?php echo e(route('agent.generate')); ?>", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "<?php echo e(csrf_token()); ?>",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        mahasiswa: mahasiswa,
                        group_size: 6
                    })
                })
                .then(res => res.json()) // ✅ HANYA SEKALI
                .then(data => {

                    console.log("HASIL AI:", data);

                    const resultContainer = document.getElementById("aiResult");
                    resultContainer.innerHTML = "";

                    if (!data.groups) {
                        resultContainer.innerHTML =
                            "<div class='alert alert-danger'>Struktur data AI tidak valid</div>";
                        return;
                    }

                    const groups = data.groups;

                    groups.forEach((group, index) => {

                        let card = document.createElement("div");
                        card.className = "card mb-3";

                        let header = document.createElement("div");
                        header.className = "card-header";
                        header.innerHTML = "<strong>Kelompok " + (index + 1) + "</strong>";

                        let body = document.createElement("div");
                        body.className = "card-body";

                        let ul = document.createElement("ul");

                        group.forEach(name => {
                            let li = document.createElement("li");
                            li.textContent = name;
                            ul.appendChild(li);
                        });

                        body.appendChild(ul);
                        card.appendChild(header);
                        card.appendChild(body);

                        resultContainer.appendChild(card);
                    });

                })
                .catch(err => {
                    console.error("ERROR:", err);
                });

        });
    </script>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('script'); ?>
    <script type="text/javascript">
        $('.show_confirm').click(function(event) {
            var form = $(this).closest("form");
            var name = $(this).data("name");
            event.preventDefault();
            swal({
                    title: `Yakin ingin menghapus data ini?`,
                    text: "Data akan terhapus secara permanen!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        form.submit();
                    }
                });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Koordinator/kelompok/index.blade.php ENDPATH**/ ?>