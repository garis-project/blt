<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/fontawesome/css/all.min.css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/select2/css/select2.min.css">
    <style>
        .form-group>.select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: 40px;
            top: 50%;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }
    </style>
</head>

<body>
    <div class="container">