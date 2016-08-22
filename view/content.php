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
    <script src='assets/app.js'></script>
    <link rel='stylesheet' href='assets/style.css'>
    <script src='https://www.google.com/recaptcha/api.js?hl=ru'></script>
</head>
<body>
    <div class='logo'><table border=0 cellspacing=0 cellpadding=0 width='100%'><tr><td width='25%'><img src='assets/images/logo.png' width="180px" /></td><td valign="bottom" id="slogan"width="50%"><a href="<?= HOST; ?>" ><span style="text-decoration:none; color:#ff6600;paddingleft:0px;">Affix.</span><span>E-Publish.Ru</a></span><span style="color:#497285;padding-left:20px;">Цифровая подпись документов</span></td><td width="25%"></td></tr></table></div>

    <div class='container' id='app'>
        <div class="row">
            <div class="col-md-12">
                <div class="info-block" v-show="<?= hasFlash('showinfo'); ?> && !showform">
                    <p class="flash text-red" v-show="<?= getFlash('captcha_err'); ?>"><?= $l['captcha_err'] ?></p>
                    <p class="flash text-red" v-show="<?= getFlash('sign_not_found'); ?>"><?= $l['cert_not_found'] ?></p>
                    <p class="flash text-red" v-show="<?= getFlash('file_err'); ?>"><?= $l['cert_file_err'] ?></p>
                    <div v-show="<?= hasFlash('verified') || hasFlash('not_verified'); ?>">
                        <p class="flash text-orange" v-show="<?= $data['status'] != STATUS_BANNED && getFlash('verified'); ?>"><?= $l['cert_verified'] ?></p>
                        <p class='flash text-red' v-show="<?= $data['status'] == STATUS_BANNED && getFlash('verified'); ?>"><?= $l['cert_banned'] ?></p>
                        <p class="flash text-orange" v-show="<?= getFlash('verified'); ?>"><?= $l['cert_verified'] ?></p>
                        <p class="flash text-red" v-show="<?= getFlash('not_verified'); ?>"><?= $l['cert_not_verified'] ?></p>

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

                <div v-show="<?= hasFlash('showinfo') ?> &&  hidemore">
                    <input type="submit"
                           @click="toggleForm($event)"
                           class="btn btn-primary"
                           value="<?= $l['verify_another'] ?>">
                </div>

                <div class='verify-form' v-show='<?= getFlash("showinfo") ? 0 : 1 ?> || showform'>
                    <h1><?= $l['header'] ?></h1>
                    <form method='post' enctype='multipart/form-data'>
                        <div class='form-group'>
                            <label for='chooseFile'><?= $l['choose_file'] ?></label>
                            <input type="file" name='file' id='chooseFile' class='form-control' v-model='file'>
                            </div>
                        <div class='form-group'>
                            <label for='chooseFile'><?= $l['choose_sign'] ?></label>
                            <input type='file' name='sign' id='chooseSign' class='form-control' v-model='sign' accept='<?= SIGN_EXT ?>'>
                        </div>
                        <div class='form-group'>
                            <label for='chooseFile'><?= $l['enter_captcha'] ?></label>
                            <div class='row'>
                                <div class="col-sm-6">
                                    <div class='g-recaptcha' data-callback='verifyCaptcha' data-sitekey='6LeoxCETAAAAAJIBJ0E3iL3QLV20kQNaEG2SrOmN'></div>
                                </div>
                            </div>
                        </div>
                        <div class='form-group'>
                            <input type='submit'
                                   name='check'
                                   :class='{disabled:disabled}'
                                   @click='handleClick($event)'
                                   class='btn btn-primary'
                                   value='<?= $l['submit_button_text'] ?>'
                        </div>
                    </form>
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