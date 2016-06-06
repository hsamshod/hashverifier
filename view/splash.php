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
                <div v-if="<?=isset($err)?>">
                    <p class="flash bg-danger text-danger"><?= $l['cert_not_verified'] ?></p>
                </div>
                <div v-if="<?=!isset($err)?>">
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
            </div>
        </div>
    </div>
</body>
</html>