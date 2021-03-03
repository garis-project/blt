<script>
    $(document).ready(function() {
        // $('select').select2();
        tmpTable = $('#tableTmp').DataTable({
            "processing": true,
            "serverSide": true,
            "retrieve": true,
            "bFilter": false,
            "lengthChange": false,
            "ajax": {
                "url": "<?= base_url('data/list') ?>",
                "type": "POST",
                "data": function(d) {
                    d.report = 1
                }
            },
        });
        reportTable = $('#tableReport').DataTable({
            "processing": true,
            "serverSide": true,
            "retrieve": true,
            "bFilter": false,
            "lengthChange": false,
            "ajax": {
                "url": "<?= base_url('data/list') ?>",
                "type": "POST",
                "data": function(d) {
                    d.filter = $('#filter').val()
                    d.key = $('#customValue').val()
                    d.report = 1
                }
            },
        });
        bltTable = $('#tableBlt').DataTable({
            "processing": true,
            "serverSide": true,
            "retrieve": true,
            "bFilter": false,
            "lengthChange": false,
            "ajax": {
                "url": "<?= base_url('data/list') ?>",
                "type": "POST",
                "data": function(d) {
                    d.filter = $('#search').val()
                    d.key = $('#key').val()
                    d.report = 0
                }
            },
        });
        $('#create').submit(function(e) {
            e.preventDefault();
            let data = new FormData(this);
            $.ajax({
                url: "<?= base_url("data/create") ?>",
                type: "post",
                data: data,
                dataType: 'json',
                processData: false,
                contentType: false,
                cache: false,
                async: false,
                success: function(data) {
                    if (data.status == "success") {
                        Swal.fire({
                            icon: 'success',
                            title: "Data Berhasil Disimpan"
                        })
                        refresh();
                        reset();
                        disabled();
                        $('#create input[name=number_id]').val(null);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: "Data Gagal Disimpan"
                        })
                    }
                },
                error: function(e) {
                    Swal.fire({
                        icon: 'error',
                        title: "Silahkan Hubungi Developer"
                    })
                    console.log(e);
                }
            });
        });
        $('#update').submit(function(e) {
            e.preventDefault();
            let data = new FormData(this);
            $.ajax({
                url: "<?= base_url("data/update") ?>",
                type: "post",
                data: data,
                dataType: 'json',
                processData: false,
                contentType: false,
                cache: false,
                async: false,
                success: function(data) {
                    if (data.status == "success") {
                        Swal.fire({
                            icon: 'success',
                            title: "Data Berhasil Disimpan"
                        })
                        refresh();
                        $('#dataBlt').modal('hide');
                        reset('update');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: "Data Gagal Disimpan"
                        })
                    }
                },
                error: function(e) {
                    Swal.fire({
                        icon: 'error',
                        title: "Silahkan Hubungi Developer"
                    })
                    console.log(e);
                }
            });
        });
        // $('#create input[name=family_card_number]').on("cut copy paste", function(e) {
        //     e.preventDefault();
        // });
        // $('#create input[name=number_id]').on("cut copy paste", function(e) {
        //     e.preventDefault();
        // });
        $('#importData').submit(function(e) {
            e.preventDefault();
            var data = new FormData(this);
            $.ajax({
                url: '<?= base_url('data/import') ?>',
                type: "post",
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                async: false,
                success: function(data) {
                    if (data) {
                        Swal.fire({
                            title: "Berhasil",
                            text: "Data Berhasil Diupload",
                            icon: "success"
                        });
                    } else {
                        Swal.fire({
                            title: "Gagal",
                            text: "Data Gagal Diupload",
                            icon: "error"
                        });
                    }
                }
            });
        });
    });



    function get(id) {
        $.ajax({
            url: "<?= base_url('data/get') ?>",
            method: "post",
            dataType: "json",
            data: {
                id
            },
            success: function(data) {
                setData(data);
            },
            erorr: function(e) {
                console.log(e);
            }
        })
    }
    <?php if ($this->session->userdata('username') == "admin" || $this->session->userdata('username') == "garis") { ?>

        function remove(id) {
            Swal.fire({
                title: 'Hapus Data Ini?',
                text: "Data yang terhapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Batal',
                confirmButtonText: 'Ya, Tetap Hapus!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        type: "POST",
                        url: "<?= base_url('data/delete'); ?>",
                        data: {
                            id: id
                        },
                        success: function() {
                            Swal.fire(
                                'Terhapus!',
                                'Data telah terhapus',
                                'success'
                            ).then(function() {
                                bltTable.ajax.reload();
                            })
                        }
                    });
                }
            });
        }

        function report() {
            let filter = $('#filter').val();
            let val = $('#customValue').val();
            let url = '<?= base_url('data/export') ?>'
            location.href = url + "/" + filter + "/" + val;
        }

        function getAdmin() {
            return $.ajax({
                url: "<?= base_url('data/admin') ?>",
                method: "post",
                dataType: 'json',
            });
        }

        function listAdmin() {
            getAdmin().done(function(data) {
                let html = icon = title = "";
                let i = 1;
                if (data.length <= 1) {
                    title = "Admin Kosong";
                    html = "Tidak ada admin yang terdaftar";
                    icon = "question";
                } else {
                    title = "Admin BLT Desa Sindangsari";
                    $.each(data, function(index, item) {
                        if (item.username != "admin") {
                            html += `
                                <div class="row mt-3">
                                    <div class="col-9 text-left"><strong>` + i + `. ` +
                                item.username + `</strong>
                                    </div>
                                    <div class="col-3">
                                        <button class="btn btn-danger btn-sm" onclick="deleteAdmin('` + item.admin_id + `')">Hapus</button>
                                    </div>
                                </div>`;
                            i++;
                        }
                    });
                }

                Swal.fire({
                    title: title,
                    html: html,
                    icon: icon,
                    focusConfirm: false,
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK',
                }).then(function(result) {});
            });
        }

        function createAdmin() {
            Swal.fire({
                title: 'Tambah Admin',
                html: '<label>Username</label>' +
                    '<input id="username" type="text" class="swal2-input" />' +
                    '<label>Password</label>' +
                    '<input id="password" type="password" class="swal2-input" maxlength="8" />',
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                cancelButtonColor: '#d33',
            }).then(function(result) {
                if (result.value) {
                    let username = $('#username').val();
                    let password = $('#password').val();
                    $.ajax({
                        url: "<?= base_url('data/createAdmin') ?>",
                        method: "post",
                        dataType: "json",
                        data: {
                            username,
                            password
                        },
                        success: function(data) {
                            Swal.fire(
                                'Berhasil!',
                                'Admin telah ditambahkan!',
                                'success'
                            )
                        }
                    })
                }
            });
        }

        function deleteAdmin(id) {
            Swal.fire({
                title: 'Hapus Admin ?',
                text: "Data yang terhapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Batal',
                confirmButtonText: 'Ya, Tetap Hapus!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        type: "POST",
                        url: "<?= base_url('data/deleteAdmin'); ?>",
                        data: {
                            id: id
                        },
                        success: function() {
                            Swal.fire(
                                'Terhapus!',
                                'Data telah terhapus',
                                'success'
                            ).then(function() {

                            })
                        }
                    });
                }
            });
        }
    <?php } ?>

    function setData(data) {
        $('#update input[name=id]').val(data.id);
        $('#update input[name=family_card_number]').val(data.family_card_number);
        $('#update input[name=number_id]').val(data.number_id);
        $('#update input[name=fullname]').val(data.fullname);
        $('#update input[name=village]').val(data.village);
        $('#update input[name=batch]').val(data.batch);
        $('#update input[name=neighborhood_association]').val(data.neighborhood_association);
        $('#update input[name=citizens_association]').val(data.citizens_association);
        (data.blt_desa == 1) ? data.kind_of_social_assistance = "BLT DESA": data.kind_of_social_assistance = "-";
        $('#update select[name=kind_of_social_assistance]').val(data.kind_of_social_assistance);
        $('#update select[name=kind_of_social_assistance]').trigger('change');
    }

    function search(val, type = "filter") {
        type == "report" ? reportTable.ajax.reload() : bltTable.ajax.reload();
    }

    function reset(type = "create", fc = null) {
        $('#' + type + ' input[name=id]').val(null);
        $('#' + type + ' input[name=family_card_number]').val(fc);
        if (type == "update") $('#' + type + ' input[name=number_id]').val(null);
        $('#' + type + ' input[name=fullname]').val(" ");
        $('#' + type + ' input[name=village]').val(null);
        $('#' + type + ' input[name=batch]').val(null);
        $('#' + type + ' input[name=neighborhood_association]').val(null);
        $('#' + type + ' input[name=citizens_association]').val(null);
        let social = "";
        (type == "update") ? social = "BLT DESA": social = "-";
        $('#' + type + ' select[name=kind_of_social_assistance]').val("social");
        $('#' + type + ' select[name=kind_of_social_assistance]').trigger('change');
    }

    function isExist(val) {
        // if (e.which == 17) return false;
        if (val.length != 16) {
            disabled(val);
            return false;
        }
        $.ajax({
            url: "<?= base_url('data/getKK') ?>",
            method: "post",
            dataType: "json",
            data: {
                val
            },
            success: function(data) {
                if (data) {
                    Swal.fire({
                        title: "KK Tidak Termasuk",
                        icon: "warning",
                        text: "Keluarga Telah Menerima Bantuan"
                    });
                    disabled(val);
                } else {
                    enabled();
                }
            },
            erorr: function(e) {
                console.log(e);
            }
        })
    }

    function isExistNik(val) {
        // if (e.which == 17) return false;

        if (val.length != 16) {
            // disabled(val, false);
            return false;
        }

        $.ajax({
            url: "<?= base_url('data/getNik') ?>",
            method: "post",
            dataType: "json",
            data: {
                val
            },
            success: function(data) {
                if (data) {
                    Swal.fire({
                        title: "NIK Ganda",
                        icon: "warning",
                        text: "Keluarga Telah Menerima Bantuan"
                    });
                    disabled(val, false);
                } else {
                    enabled();
                }
            },
            erorr: function(e) {
                console.log(e);
            }
        })
    }

    function getAddress(val, type = "create") {
        if (val.length < 2) {
            $('#' + type + ' input[name=citizens_association]').val(null);
            $('#' + type + ' input[name=village]').val(null);
            return false;
        }
        $.ajax({
            url: "<?= base_url('data/getAddress') ?>",
            method: "post",
            dataType: "json",
            data: {
                val
            },
            success: function(data) {
                $('#' + type + ' input[name=citizens_association]').val(data.citizens_association);
                $('#' + type + ' input[name=village]').val(data.village);
            },
            erorr: function(e) {
                console.log(e);
            }
        })
    }

    function disabled(val = null, kk = true) {
        if (kk === true) {
            $('#create input[name=family_card_number]').val(val);
            $('#create input[name=number_id]').prop('disabled', true);
        } else {
            $('#create input[name=number_id]').prop('disabled', false);
        }
        $('#create input[name=fullname]').prop('disabled', true);
        $('#create input[name=village]').prop('disabled', true);
        $('#create input[name=batch]').prop('disabled', true);
        $('#create input[name=neighborhood_association]').prop('disabled', true);
        $('#create input[name=citizens_association]').prop('disabled', true);
        $('#create select[name=kind_of_social_assistance]').prop('disabled', true);
        $('#create select[name=kind_of_social_assistance]').prop('disabled', true);
    }

    function enabled() {
        $('#create input[name=number_id]').prop('disabled', false);
        $('#create input[name=fullname]').prop('disabled', false);
        $('#create input[name=village]').prop('disabled', false);
        $('#create input[name=batch]').prop('disabled', false);
        $('#create input[name=neighborhood_association]').prop('disabled', false);
        $('#create input[name=citizens_association]').prop('disabled', false);
        $('#create select[name=kind_of_social_assistance]').prop('disabled', false);
        $('#create select[name=kind_of_social_assistance]').prop('disabled', false);

    }

    function refresh(type) {
        reportTable.ajax.reload();
        tmpTable.ajax.reload();
        bltTable.ajax.reload();
    }
</script>