<!DOCTYPE html>
<html>
<head>
    <title><?= $l['title'] ?></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-2.2.3.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.21/vue.min.js"></script>
    <script src="assets/app.js"></script>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container" id="app">
        <div class="row">
            <div class="col-md-12">
                <div class="info-block">
                    <p class="flash text-red" v-show="<?= $err == VERIFY_PARAM_ERR; ?>"><?= $l['cert_link_err'] ?></p>
                    <p class="flash text-red" v-show="<?= in_array($err, [VERIFY_KEY_ERR, VERIFY_SIGN_ERR]); ?>"><?= $l['cert_not_found'] ?></p>
                    <p class="flash text-red" v-show="<?= $err == VERIFY_FILE_ERR; ?>"><?= $l['cert_file_err'] ?></p>
                    <div v-if="<?=isset($code) ?>">
                        <p class="flash text-orange" v-show="<?= $code == VERIFY_OK; ?>"><?= $l['cert_verified'] ?></p>
                        <p class="flash text-red" v-show="<?= $code == VERIFY_ERR; ?>"><?= $l['cert_not_verified'] ?></p>

                        <div id="verifier-info" class="flash text-info">
                            <b><?=$l['cert_info_title']?>:</b>
                            <div>
                                <ul class="list-group">
                                    <?php foreach (CERT_FILE_SHOW_FIELDS as $field) : ?>
                                        <li><b><?= $l['cert_info_'.$field]; ?>:</b> <?= in_array($field, DATE_FIELDS) ? date('d.m.Y', strtotime($data[$field])) : $data[$field]; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!--div id="slider" >
            <iframe class="row" id="frame" src="assets/slider/index.html" style="border:1px solid #fff;margin-top:100px;" scrolling="no" border="0"></iframe>
        </div-->

        <div id="footer">
            <p> <span>&#x24B8; <?= VENDOR_TITLE ?>,</span><span> <a href="<?= VENDOR_FULL ?>" target="_blank"><?= VENDOR ?></a></span><span> <?= date('Y'); ?> г.</span></p></div>
    </div>
</body>
</html>