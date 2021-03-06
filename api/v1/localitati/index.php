<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

include_once dirname(__DIR__, 3) . '/api/config/DatabaseMisc.php';
include_once dirname(__DIR__, 3) . '/api/controllers/LocalitatiController.php';
include_once dirname(__DIR__, 3) . '/api/controllers/JudeteController.php';

$database = new DatabaseMisc();

$db = $database->getConnection();

$items = new LocalitatiController($db);

$judet = $_GET['judet'];

$stmt = $items->read($judet);
$itemCount = $stmt->rowCount();

if ($itemCount > 0) :

    http_response_code(200);

    $arr = array();
    $arr['localitati'] = array();
    $arr['count'] = $itemCount;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) :
        $elem = $row;
        array_push($arr['localitati'], $elem['localitate']);
    endwhile
    ;

    $judetinfo = new JudeteController($db);
    $stmtjudet = $judetinfo->read($judet);
    $judetrow = $stmtjudet->fetch(PDO::FETCH_ASSOC);
    $arr['judet'] = $judetrow;

    echo json_encode($arr);

else :
    http_response_code(404);

    if (! empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
        $uri = 'https://';
    } else {
        $uri = 'http://';
    }
    $uri .= $_SERVER['HTTP_HOST'];
    $uri .= "/api/v1/judete";

    echo json_encode(array(
        "judet_invalid" => "vezi aici o lista a judetelor valide: " . $uri,
        "type" => "danger",
        "title" => "failed",
        "message" => "No records found"
    ));
endif;

?>