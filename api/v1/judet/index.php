<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

include_once dirname(__DIR__, 3) . '/api/config/DatabaseCorona.php';
include_once dirname(__DIR__, 3) . '/api/controllers/RomaniaInfoController.php';

$database = new DatabaseCorona();

$db = $database->getConnection();

$items = new RomaniaInfoController($db);

$judet = $_GET['judet'];

if (empty($_GET['avg']) || ! isset($_GET['avg'])) :
    // a default value
    $avg = 14;
else :
    // check if is a number !
    $avg = $_GET['avg'];
endif;

$stmt = $items->read($judet, $avg);
$itemCount = $stmt->rowCount();

if ($itemCount > 0) :
    http_response_code(200);

    $arr = array();
    $arr['response'] = array();
    $arr['count'] = $itemCount;
    $arr['avg'] = $avg;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) :
        $elem = $row;
        array_push($arr['response'], $elem);
    endwhile
    ;
    echo json_encode($arr);

else :
    http_response_code(404);

    echo json_encode(array(
        "type" => "danger",
        "title" => "failed",
        "message" => "No records found"
    ));
endif;

?>