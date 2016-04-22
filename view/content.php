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
        <div class="row col-md-12">
            <h1><?= $l['header'] ?></h1>
            <p class="flash bg-warning text-warning" v-show="<?= getFlash('error'); ?>"><?= $l['cert_err'] ?></p>
            <p class="flash bg-danger text-danger" v-show="<?= getFlash('not_verified'); ?>"><?= $l['cert_not_verified'] ?></p>
            <p class="flash bg-danger text-danger" v-show="<?= getFlash('sign_not_found'); ?>"><?= $l['cert_not_found'] ?></p>
            
            <div v-show="<?= getFlash('verified'); ?>">
                <p class="flash bg-success text-success"><?= $l['cert_verified'] ?></p>
                <div class="flash bg-info text-info">
                    <table>
                        <thead>
                            <tr>
                                <td colspan="2">
                                    <b><?= $l['cert_info_title'] ?></b>
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td width="150px;"><?= $l['cert_info_date'] ?></td>
                                <td><?= $date ?></td>
                            </tr>
                            <tr>
                                <td><?= $l['cert_info_city'] ?></td>
                                <td><?= $city ?></td>
                            </tr>
                            <tr>
                                <td><?= $l['cert_info_organization'] ?></td>
                                <td><?= $school ?></td>
                            </tr>
                            <tr>
                                <td><?= $l['cert_info_position'] ?></td>
                                <td><?= $position ?></td>
                            </tr>
                            <tr>
                                <td><?= $l['cert_info_fio'] ?></td>
                                <td><?= $name ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <blockquote v-show="<?= hasFlash('work_time') ? 1 : 0; ?>">
                <p>Время работы: <?= number_format(getFlash('work_time'), 4); ?></p>
            </blockquote>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="chooseFile"><?= $l['choose_file'] ?></label>
                    <input type="file" name="file" id="chooseFile" class="form-control" placeholder="Выберите файл">
                    <input type="hidden" name="algo" value="new" v-model="algo">
                </div>
                <div class="form-group" >
                    <input type="submit" @click="algo='new'" name="check" value="<?= $l['submit_button_text'] ?>" class="btn btn-primary">
                    <!-- <input type="submit" @click="algo='old'" name="check" value="Проверить (старым алгоритмом)" class="btn btn-primary"> -->
                </div>
            </form>
        </div>
    </div>
</body>
</html>