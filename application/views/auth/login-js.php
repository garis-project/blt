<script>
    function login() {
        let username = $('#username').val();
        let password = $('#password').val();
        $.ajax({
            url: "<?= base_url('auth/login') ?>",
            method: "post",
            dataType: 'json',
            data: {
                username: username,
                password: password
            },
            success: function(data) {
                switch (data.status) {
                    case "success":
                        location.href = "<?= base_url('admin') ?>";
                        break;
                    case "failed":
                        Swal.fire({
                            icon: 'error',
                            title: "Gagal Login",
                            text: "Akun yang anda masukan salah",
                        })
                        break;
                }
            },
            error: function(e) {
                console.log(e);
            }
        })
    }
</script>