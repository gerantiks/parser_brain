<?php
require_once('../private/information_api.php');
require_once('../vendor/autoload.php');
require_once('excel_processing.php');

global $login, $password, $dbname, $host, $userDb, $passwordDb, $database, $arrayCode, $arrayPrice;

//Отримуємо SID api Brain
$authentication= new \app\CurlRequest('http://api.brain.com.ua/auth');
$params = [
    'login' => $login,
    'password' => $password
];

try {
    $authentication = $authentication->post($params);
    $responseDecode = json_decode($authentication, true);
    $sid = $responseDecode['result'];
} catch (Exception $e) {
    file_put_contents('../private/logs.txt', date("H:i:s - Y-m-d ").$e->getMessage() . "\n" , FILE_APPEND);
    die;
}

//Ядро обробки
$error = '';
$i = 0;
$positionId = 0;

$getPosition = new \app\Database($host, $database, $userDb, $passwordDb);
$sql = "SELECT MAX(positionId) AS last_element FROM products";
$getPosition->setSql($sql);
$result = $getPosition->fetch();
$positionId = $result[0]['last_element'];

$db = new \app\Database($host, $database, $userDb, $passwordDb);

while($arrayCode[$i]) {
    $request = new \app\CurlRequest("http://api.brain.com.ua/product/product_code/$arrayCode[$i]/$sid?lang=ua");
    $response = $request->get([]);
    $decodeResponse = json_decode($response, true);

    $name = $decodeResponse['result']['name'];
    $avaliable = $decodeResponse['result']['self_delivery'];
    $article = $decodeResponse['result']['articul'];
    $briefDescription = $decodeResponse['result']['brief_description'];
    $links = $decodeResponse['result']['medium_image'];
    isset($decodeResponse['error_message']) ? $error = $decodeResponse['error_message']: $error ='';

    $description = $decodeResponse['result']['description'];
    empty($description) ? $description = $decodeResponse['result']['brief_description'] : $description;
    $delTags = strip_tags($description);

    $dataProduct = [
        'positionId' => ++$positionId,
        'name' => $name,
        'avaliable' => $avaliable,
        'article' => $article,
        'briefDescription' => $briefDescription,
        'description' => $delTags,
        'links' => $links
    ];

// Запис інформації в БД
    $sql = "INSERT INTO products(`positionId`, `name`, `available`, `article`, `briefDescription`, `description`, `links`)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $db->setSql($sql);
    $db->setData($dataProduct);
    $db->query();

// Строка з параметрами які будуть записуватись в файл формату YML.
    $xmlInfo = <<<EOT
        <offer id=$positionId available="$avaliable">
            <price>$arrayPrice[$i]</price>
            <currencyId>UAH</currencyId>
            <categoryId>1</categoryId>
            <picture>$links</picture>
            <delivery>true</delivery>
            <name_ua>$name</name_ua>
            <vendorCode>$article</vendorCode>
            <param name="Загальні параметри">$briefDescription</param>
            <keywords>$article</keywords>
            <description>$delTags</description>
        </offer>                
EOT;

    file_put_contents('test.yml', $xmlInfo, FILE_APPEND);
    $i++;
}
echo "Дані успішно додані в базу";
//Ліквідація сесії SID
$logout = new \app\CurlRequest("http://api.brain.com.ua/logout/$sid");
$logout->post();
die;





