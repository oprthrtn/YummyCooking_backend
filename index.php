<?php
mysqli_report(MYSQLI_REPORT_ALL);
include_once 'helpers/headers.php';
global $Link;
function getData($method)
{
    $data = new stdClass();

    if ($method != "GET") {
        $data->body = json_decode(file_get_contents('php://input'));
    }

    $data->parameters = [];
    $dataGet = $_GET;

    foreach ($dataGet as $key => $value) {
        if ($key != "q") {
            $data->parameters[$key] = $value;
        }
    }

    return $data;
}

function getMethod()
{
    return $_SERVER['REQUEST_METHOD'];
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    return;
}

$Link = mysqli_connect("127.0.0.1", "root", "", "yummycooking");

if (!$Link) {
    echo "Ошибка: Невозможно установить соединение с MySQL." . PHP_EOL;
    echo "Код ошибки errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Текст ошибки error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}

$url = isset($_GET['q']) ? $_GET['q'] : '';
$url = rtrim($url, '/');
$urlList = explode('/', $url);

$router = $urlList[0];
$urlList = array_slice($urlList, 1);
$requestData = getData(getMethod());
if (file_exists(realpath(dirname(__FILE__)) . '/routers/' . $router . '.php')) {
    include_once 'routers/' . $router . '.php';
    route(getMethod(), $urlList, $requestData);
} else {
    setHTTPStatus("404", "Page not found");
}

mysqli_close($Link);
