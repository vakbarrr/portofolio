<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $page_title ?></h1>
        <a href="<?= base_url(array_slice(explode('/', uri_string()), 0, -1)); ?>" class="d-none d-sm-inline-block btn  btn-danger shadow-sm"><i class="fas fa-arrow-left fa-sm text-white-50"></i> Back</a>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-12 col-md-12">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <form class="user" id="myform" url="<?= base_url(array_slice(explode('/', uri_string()), 0, -1)) . '/insert'; ?>">
                        <div class="form-group">
                            <label for="">Category</label>
                            <input type="text" class="form-control" name="category" id="exampleFirstName" placeholder="Enter Category" required>
                        </div>

                        <div class="form-group">
                            <label for="">Image</label>
                            <div>
                                <input type="file" class="" name="image" id="exampleFirstName" required>
                            </div>
                        </div>
                        <button type="submit" id="submit" class="btn btn-primary btn-block">
                            Save
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

</div>


<script>
    $(document).ready(function() {
        $('#myform').on('submit', function(e) {
            e.preventDefault();
            var btn = $('#submit');
            var formData = new FormData(this);
            $.ajax({
                url: $(this).attr('url'),
                data: formData,
                type: 'POST',
                processData: false,
                dataType: 'json',
                cache: false,
                contentType: false,
                beforeSend: function() {
                    btn.attr('type', 'button');
                    btn.html('Loading...');
                },
                complete: function() {
                    btn.attr('type', 'submit');
                    btn.html('Save');
                },
                success: function(response) {
                    $('.form-group').removeClass('has-error');
                    $('.text-danger').remove();
                    if (response.error === true) {
                        if (response.validation) {
                            for (i in response.msg) {
                                $('input[name=\'' + i + '\']').closest('.form-group').addClass('has-error');
                                $('input[name=\'' + i + '\']').after('<small class="text-danger"><i>' + response.msg[i] + '</i></small>');
                            }
                        } else {
                            Swal.fire(
                                'Error',
                                response.msg,
                                'error'
                            )
                        }
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: response.msg,
                            text: response.msg,
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
                                window.location.href = response.redirect;
                                // console.log('I was closed by the timer')
                            }
                        })
                    }
                },
                error: function(response) {
                    Swal.fire(
                        'Error',
                        "Something went wrong :(",
                        'error'
                    )
                }
            });
        });
    });
</script>
