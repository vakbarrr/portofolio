<div class="container-fluid">
    <!-- Content Row -->
    <div class="row">

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-12 col-md-12">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800"><?= $page_title ?></h1>
                <a href="<?= base_url(uri_string()) . '/add'; ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Add</a>
            </div>
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Category</th>
                                    <th>Image</th>
                                    <th>Date Added</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($list as $key => $val) : ?>
                                    <tr>
                                        <td><?= $key + 1 ?></td>
                                        <td><?= $val['category'] ?></td>
                                        <td><img src="<?= base_url($val['image']) ?>" alt="" class="w-25"></td>
                                        <td><?= $val['date_added'] ?></td>
                                        <td class="text-center">
                                            <a href="<?= base_url(uri_string()) . '/edit/' . $val['category_id']; ?>" class="btn btn-warning btn-circle btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="javascript:void(0)" onclick="soft_delete(this)" data-id="<?= $val['category_id'] ?>" class="btn btn-danger btn-circle btn-sm">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>


<script>
    function soft_delete(el) {
        var id = $(el).attr('data-id');
        Swal.fire({
            title: 'Are you sure?',
            // text: 'text',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes !'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= base_url(uri_string()) . '/delete/'; ?>" + id,
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        if (data.error === false) {
                            let timerInterval
                            Swal.fire({
                                icon: 'success',
                                title: data.title,
                                text: data.msg,
                                html: 'I will close in <b id="timerpopup"></b> milliseconds.',
                                timer: 2000,
                                timerProgressBar: true,
                                didOpen: () => {
                                    Swal.showLoading()
                                    timerInterval = setInterval(() => {
                                        const content = Swal.getHtmlContainer()
                                        if (content) {
                                            const b = content.querySelector('b#timerpopup')
                                            if (b) {
                                                b.textContent = Swal.getTimerLeft()
                                            }
                                        }
                                    }, 100)
                                },
                                willClose: () => {
                                    clearInterval(timerInterval)
                                }
                            }).then((result) => {
                                /* Read more about handling dismissals below */
                                if (result.dismiss === Swal.DismissReason.timer) {
                                    window.location.reload();
                                    // console.log('I was closed by the timer')
                                }
                            })
                        } else {
                            Swal.fire(
                                data.title,
                                data.msg,
                                'error'
                            )
                        }
                    },
                    error: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!',
                        })
                    }
                });
            }
        })
    }
</script>
