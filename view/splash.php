<!DOCTYPE html>
<html>
<head>
    <title><?= $l['title'] ?></title>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel="stylesheet" type="text/css" href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css'>
    <script src='https://code.jquery.com/jquery-2.2.3.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.21/vue.min.js'></script>
    <script src='http://underscorejs.org/underscore-min.js'></script>
    <script src='assets/app.js'></script>
    <link rel='stylesheet' href='assets/style.css'>
    <script src='https://www.google.com/recaptcha/api.js?hl=ru'></script>
</head>
<body>
<div class='logo'><table border=0 cellspacing=0 cellpadding=0 width='100%'><tr><td width='25%'><img src='assets/images/logo.png' width="180px" /></td><td valign="bottom" id="slogan"width="50%"><a href="<?= HOST; ?>" ><span style="text-decoration:none; color:#ff6600;paddingleft:0px;">Affix.</span><span>E-Publish.Ru</a></span><span style="color:#497285;padding-left:20px;">Цифровая подпись документов</span></td><td width="25%"></td></tr></table></div>
<script>
    var data = {
        VERIFY_PARAM_ERR      : -5,
        VERIFY_FILE_ERR       : -4,
        VERIFY_SIGN_ERR       : -3,
        VERIFY_KEY_ERR        : -2,
        VERIFY_ERR            : -1,
        VERIFY_OK             :  0,
        STATUS_BANNED         :  6
    };

    data['verify_captcha'] = <?= json_encode(getFlash('verify_captcha')) ?>;
    data['verify_result']  = <?= json_encode(getFlash('verify_result')) ?>;
</script>
<div class='container' id='app'>
    <div class='row'>
        <div class='col-md-12'>
            <div class='info-block' v-show='verify_captcha && verify_captcha.error && !showform'>
                <p class='flash text-red'><?= $l['captcha_err'] ?></p>
            </div>
            <div class='info-block' v-show='verify_result.length' v-for='result in verify_result'>
                <p class='flash text-red' v-show='result.code == VERIFY_SIGN_ERR'><?= $l['cert_not_found'] ?></p>
                <p class='flash text-red' v-show='result.code == VERIFY_FILE_ERR'><?= $l['cert_file_err'] ?></p>
                <div v-show='result.code != VERIFY_KEY_ERR || result.code != VERIFY_PARAM_ERR'>
                    <p class='flash text-red' v-show='result.status == STATUS_BANNED'><?= $l['cert_banned'] ?></p>
                    <p class='flash text-red' v-show='result.code == VERIFY_ERR'><?= $l['cert_not_verified'] ?></p>
                    <p class='flash text-orange' v-show='result.status != STATUS_BANNED && result.code == VERIFY_OK'><?= $l['cert_verified'] ?></p>
                    <div id='verifier-info' class='flash text-info'>
                        <b><?=$l['cert_info_title']?>:</b>
                        <div>
                            <ul class="list-group">
                                <?php foreach (CERT_FILE_SHOW_FIELDS as $field) : ?>
                                    <li><b><?= $l['cert_info_'.$field]; ?>:</b> {{ result.<?=$field?> }}
                                        <?= in_array($field, DATE_FIELDS) ? date('d.m.Y', strtotime($data[$field])) : $data[$field]; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class='clearfix'></div>
        </div>

    </div>
</div>

<div id='slider'>
    <iframe class='row' id='frame' src='assets/slider/index.html' style='border:1px solid #fff;margin-top:100px;' scrolling='no' border='0'></iframe>
</div>
<div id='footer'>
    <p> <span>&#x24B8; <?= VENDOR_TITLE ?>,</span><span> <a href="<?= VENDOR_FULL ?>" target="_blank"><?= VENDOR ?></a></span><span> <?= date('Y'); ?> г.</span></p></div>
</body>
</html>