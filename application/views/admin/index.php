<div class="row container mt-5">
    <button class="btn btn-outline-success ml-auto" type="button" onclick="refresh()">
        <i class="fas fa-sync"></i> Refresh Data
    </button>
    <?php if ($this->session->userdata('username') == "admin" || $this->session->userdata('username') == "garis") { ?>
        <button class="btn btn-outline-info ml-3" data-toggle="collapse" data-target="#optionAdmin" aria-expanded="false" aria-controls="optionAdmin">
            <i class="fas fa-user-tie"></i> Opsi Admin
        </button>
        <div class="collapse" id="optionAdmin">
            <div class="row ml-3">
                <button class="btn btn-outline-info" onclick="createAdmin()">
                <i class="fas fa-user-plus"></i>
                Tambahkan Admin</button>
                <button class="btn btn-outline-info mx-3" onclick="listAdmin()">
                <i class="fas fa-list"></i>
                List Admin</button>
            </div>
        </div>
    <?php } ?>
    <button class="btn btn-outline-danger ml-3" onclick="location.href='<?= base_url('auth/logout') ?>'">
        <i class="fas fa-power-off"></i> Logout
    </button>
</div>
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true"><i class="fas fa-home"></i> Home</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="searched-tab" data-toggle="tab" href="#searched" role="tab" aria-controls="searched" aria-selected="false"><i class="fas fa-search"></i> Pencarian</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="report-tab" data-toggle="tab" href="#report" role="tab" aria-controls="report" aria-selected="false"><i class="fas fa-file-download"></i> Laporan</a>
    </li>
    <?php if ($this->session->userdata('username') == "admin" || $this->session->userdata('username') == "garis") { ?>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="import-tab" data-toggle="tab" href="#import" role="tab" aria-controls="import" aria-selected="false"><i class="fas fa-file-upload"></i> Import</a>
        </li>
    <?php } ?>
</ul>
<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
        <div class="card w-100 pb-5">
            <div class="card-header text-center">
                <h3>INPUT DATA</h3>
            </div>
            <div class="card-body">
                <form id="create">
                    <div class="row container">
                        <label class="col-md-2 col-4">No KK</label>
                        <input type="text" placeholder="Masukan No.KK" class="form-control col-md-4 col-8" name="family_card_number" onkeypress="return /^[0-9]*$/i.test(event.key)" maxlength="16" minlength="16" required onblur="isExist(this.value)" />
                    </div>
                    <div class="row container mt-3">
                        <label class="col-md-2 col-4">Nama</label>
                        <input type="text" placeholder="Masukan Nama Sesuai KTP" class="form-control col-md-4 col-8" name="fullname" onkeypress="return /^[A-Z ]*$/i.test(event.key)" required />
                    </div>
                    <div class="row container mt-3">
                        <label class="col-md-2 col-4">NIK</label>
                        <input type="text" placeholder="Masukan NIK" class="form-control col-md-4 col-8" name="number_id" onkeypress="return /^[0-9]*$/i.test(event.key)" maxlength="16" minlength="16" onblur="isExistNik(this.value)" required />
                        <label class="col-md-2 col-4 ml-auto mt-3 mt-md-0">Gelombang</label>
                        <input type="text" placeholder="Masukan Gelombang (1 s/d 3)" class="form-control col-md-3 col-8 mt-3 mt-md-0" name="batch" onkeypress="return /^[0-3]*$/i.test(event.key)" maxlength="1" minlength="1" required />
                    </div>
                    <div class="row container mt-3">
                        <label class="col-md-2 col-4 ml-0 mt-3 mt-md-0">RT</label>
                        <input name="neighborhood_association" placeholder="RT" oninput="getAddress(this.value)" class="form-control col-1" maxlength="2" minlength="2" />
                        <label class="col-md-1 col-4 mt-3 mt-md-0 ml-auto">RW</label>
                        <input type="text" placeholder="RW" class="form-control col-md-1 col-2 mt-3 mt-md-0" name="citizens_association" onkeypress="return /^[0-9]*$/i.test(event.key)" required maxlength="2" minlength="2" readonly />
                        <label class="col-md-2 col-3 ml-auto">Dusun</label>
                        <input type="text" name="village" placeholder="Nama Dusun" class="form-control col-md-3 col-8" required readonly />
                    </div>
                    <div class="row container mt-3">
                        <label class="col-md-2 col-4">Jenis Bansos</label>
                        <select name="kind_of_social_assistance" class="form-control col-md-4 col-8" required>
                            <option value="-" disabled>PILIH BANSOS</option>
                            <option value="BLT DESA" selected>BLT DESA</option>
                            <option value="PKH">PKH</option>
                            <option value="BPNT">BPNT</option>
                            <option value="BST">BST</option>
                        </select>
                    </div>
                    <div class="row container mt-3">
                        <div class="ml-auto">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-save"></i>
                                Simpan</button>
                            <button class="btn btn-warning" type="button" onclick="reset()">
                                <i class="fas fa-sync"></i> Reset
                            </button>
                        </div>
                    </div>
                </form>
                <div class="table-responsive mt-5">
                    <table class="table table-striped w-100" id="tableTmp">
                        <thead class="text-center thead-dark">
                            <th>No</th>
                            <th>Nama</th>
                            <th>NIK</th>
                            <th>No KK</th>
                            <th>RT</th>
                            <th>RW</th>
                            <th>Dusun</th>
                            <th>Jenis Bansos</th>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="searched" role="tabpanel" aria-labelledby="searched-tab">
        <div class="card w-100 pb-5">
            <div class="card-header text-center">
                <h3>PENCARIAN DATA</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <h5 class="col-md-2 col-6 ml-md-0 ml-auto ">Pencarian</h5>
                    <select id="search" class="form-control col-md-3 col-6" onchange="search($('#key').val())">
                        <option value="fullname">Nama</option>
                        <option value="number_id">NIK</option>
                        <option value="family_card_number">NO.KK</option>
                    </select>
                    <input type="text" class="form-control col-md-4 mt-md-0 mt-2" id="key" oninput="search(this.value)" />
                </div>

                <div class="table-responsive">
                    <table class="table table-striped w-100" id="tableBlt">
                        <thead class="text-center thead-dark">
                            <th>No</th>
                            <th>Nama</th>
                            <th>NIK</th>
                            <th>No KK</th>
                            <th>RT</th>
                            <th>RW</th>
                            <th>Dusun</th>
                            <th>Jenis Bansos</th>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="report" role="tabpanel" aria-labelledby="report-tab">
        <div class="card w-100 pb-5">
            <div class="card-header text-center">
                <h3>REKAPITULASI DATA</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <label for="filter" class="col-md-2 col-6 ml-md-0 ml-auto ">Filter Berdasarkan</label>
                    <select id="filter" class="form-control col-md-3 col-6" onchange="search($('#customValue').val(),'report')">
                        <option value="neighborhood_association">RT</option>
                        <option value="citizens_association ">RW</option>
                        <option value="village">DUSUN</option>
                        <option value="kind_of_social_assistance">JENIS BANSOS</option>
                        <option value="batch">GELOMBANG</option>
                    </select>
                    <input type="text" class="form-control col-md-4 mt-md-0 mt-2" id="customValue" oninput="search(this.value,'report')" />
                    <button class="btn btn-success mr-auto" onclick="report()">
                        <i class="fas fa-file-excel"></i> Export
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped w-100" id="tableReport">
                        <thead class="text-center thead-dark">
                            <th>No</th>
                            <th>Nama</th>
                            <th>NIK</th>
                            <th>No KK</th>
                            <th>RT</th>
                            <th>RW</th>
                            <th>Dusun</th>
                            <th>Jenis Bansos</th>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php if ($this->session->userdata('username') == "admin" || $this->session->userdata('username') == "garis") { ?>
        <div class="tab-pane fade" id="import" role="tabpanel" aria-labelledby="import-tab">
            <div class="card w-100 pb-5">
                <div class="card-header text-center">
                    <h3>IMPORT DATA</h3>
                </div>
                <div class="card-body">
                    <form id="importData" enctype="multipart/form-data">
                        <div class="row container mt-3">
                            <label class="col-md-2">Jenis Bansos</label>
                            <select name="kind_of_social_assistance" class="form-control col-md-4 col-8" required>
                                <option value="-" disabled selected>PILIH BANSOS</option>
                                <option value="BLT DESA">BLT DESA</option>
                                <option value="PKH">PKH</option>
                                <option value="BPNT">BPNT</option>
                                <option value="BST">BST</option>
                            </select>
                            <input type="file" name="file" class="form-control col-md-4 ml-auto" accept="application/vnd.ms-excel" />
                            <button class="btn btn-info" type="submit">
                                <i class="fas fa-file-upload"></i>
                                Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<div class="modal fade" id="dataBlt" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="dataBltLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dataBltLabel">DATA PENERIMA BANTUAN</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="update">
                <div class="modal-body">
                    <input type="hidden" name="id">
                    <div class="row container">
                        <label class="col-md-2 col-4">No KK</label>
                        <input type="text" placeholder="Masukan No.KK" class="form-control col-md-4 col-8" name="family_card_number" onkeypress="return /^[0-9]*$/i.test(event.key)" maxlength="16" minlength="16" required />
                    </div>
                    <div class="row container mt-3">
                        <label class="col-md-2 col-4">Nama</label>
                        <input type="text" placeholder="Masukan Nama Sesuai KTP" class="form-control col-md-4 col-8" name="fullname" onkeypress="return /^[A-Z ]*$/i.test(event.key)" required />
                    </div>
                    <div class="row container mt-3">
                        <label class="col-md-2 col-4">NIK</label>
                        <input type="text" placeholder="Masukan NIK" class="form-control col-md-4 col-8" name="number_id" onkeypress="return /^[0-9]*$/i.test(event.key)" maxlength="16" minlength="16" required />
                        <label class="col-md-2 col-4 ml-auto mt-3 mt-md-0">Gelombang</label>
                        <input type="text" placeholder="Masukan Gelombang (1 s/d 4)" class="form-control col-md-3 col-8 mt-3 mt-md-0" name="batch" onkeypress="return /^[0-4]*$/i.test(event.key)" maxlength="1" minlength="1" required />
                    </div>
                    <div class="row container mt-3">
                        <label class="col-md-2 col-4 ml-0 mt-3 mt-md-0">RT</label>
                        <input name="neighborhood_association" placeholder="RT" oninput="getAddress(this.value,'update')" class="form-control col-1" maxlength="2" minlength="2" />
                        <label class="col-md-1 col-4 mt-3 mt-md-0 ml-auto">RW</label>
                        <input type="text" placeholder="RW" class="form-control col-md-1 col-2 mt-3 mt-md-0" name="citizens_association" onkeypress="return /^[0-9]*$/i.test(event.key)" required maxlength="2" minlength="2" readonly />
                        <label class="col-md-2 col-3 ml-auto">Dusun</label>
                        <input type="text" name="village" placeholder="Nama Dusun" class="form-control col-md-3 col-8" required readonly />
                    </div>
                    <div class="row container mt-3">
                        <label class="col-md-2 col-4">Jenis Bansos</label>
                        <select name="kind_of_social_assistance" class="form-control col-md-4 col-8" required>
                            <option value="-" disabled>PILIH BANSOS</option>
                            <option value="BLT DESA" selected>BLT DESA</option>
                            <option value="PKH">PKH</option>
                            <option value="BPNT">BPNT</option>
                            <option value="BST">BST</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="reset('update')">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>