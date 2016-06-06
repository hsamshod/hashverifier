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
                <h1><?= $l['header'] ?></h1>
                <p class="flash bg-warning text-warning" v-show="<?= getFlash('error'); ?>"><?= $l['cert_err'] ?></p>
                <p class="flash bg-danger text-danger" v-show="<?= getFlash('not_verified'); ?>"><?= $l['cert_not_verified'] ?></p>
                <p class="flash bg-danger text-danger" v-show="<?= getFlash('sign_not_found'); ?>"><?= $l['cert_not_found'] ?></p>
                <p class="flash bg-danger text-danger" v-show="<?= getFlash('captcha_err'); ?>"><?= $l['captcha_err'] ?></p>

                <div v-show="<?= getFlash('verified'); ?>">
                    <p class="flash bg-success text-success"><?= $l['cert_verified'] ?></p>

                    <div id="verifier-info" class="flash bg-info text-info">
                        <b><?=$l['cert_info_title']?></b>
                        <div>
                            <ul class="list-group">
                                <?php foreach ($data as $k => $v) : ?>
                                    <li><b><?= $l['cert_info_'.$k] . '</b> '.$v; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="chooseFile"><?= $l['choose_file'] ?></label>
                        <input type="file" name="file" id="chooseFile" class="form-control" v-model="file">
                    </div>
                    <div class="form-group">
                        <label for="chooseFile"><?= $l['choose_sign'] ?></label>
                        <input type="file" name="sign" id="chooseSign" class="form-control" v-model="sign">
                    </div>
                    <div class="form-group">
                        <label for="chooseFile"><?= $l['enter_captcha'] ?></label>
                        <div class="row">
                            <div class="col-sm-1">
                                <img id="captcha-img" src="app/captcha.php">
                            </div>
                            <div class="col-sm-11">
                                <input type="text" name="captcha" id="captcha" class="form-control" placeholder="Введите код с картинки"
                                       v-model="captchaInput"
                                       @change="disabled = captchaInput.length < 5">
                            </div>
                        </div>


                    </div>
                    <div class="form-group" >
                        <input type="submit"
                               name="check"
                               :class="{ disabled: disabled }"
                               @click="handleClick($event)"
                               class="btn btn-primary"
                               value="<?= $l['submit_button_text'] ?>"
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>