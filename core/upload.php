<?php
//Модуль завантаження файлу у форматі xlsx з валідацією
session_start();

$fileXls = $_FILES;
$fileTmp = $fileXls['xls']['tmp_name'];
$fileName = $fileXls['xls']['name'];
$fileDestination = '../uploads/'. $fileName;
$validation = preg_match('/\.(xlsx|xls)$/i', $fileName);
if ($validation) {
    var_dump(move_uploaded_file($fileTmp, $fileDestination));
} else {
    $_SESSION['message'] = "Невірний формат файлу. Завантажте його у форматі xlsx або xls.";
    header('Location: index.php');
    exit;
}





