<?php session_start() ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="main.css">
    <title>Document</title>
</head>
<body>
    <div class="form">
        <h1>Виберіть файл у форматі xls</h1>
        <form method="post" action="../core/upload.php" enctype="multipart/form-data">
            <input class="file-button file-put" type="file" name="xls">
            <input class="file-button" type="submit" value="Нажміть для відправки файла">
            <p class="message">
                <?php
                    if($_SESSION['message']) {
                        echo $_SESSION['message'];
                        unset($_SESSION['message']);
                    }
                ?>
            </p>
        </form>
    </div>
</body>
</html>