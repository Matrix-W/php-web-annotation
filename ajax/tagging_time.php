<?php
require '../mysql_fun.php';
$tableName = 'user_tagging_time';
$file = "notic_" . date("Ymd") . ".log";
$ct = date("Y-m-d H:i:s", time());
error_log("[" . $ct . "] ". print_r($_POST, true) ." \r\n", 3, $file);
if (!empty($_POST)) {
    $data = [
        [
            'user_name' => $_POST['user_name'],
            'start_time' => $_POST['start_time'],
            'end_time' => $_POST['end_time'],
            'tagging_id' => $_POST['tagging_id']
        ]
    ];
    $row_count = pdoMultiInsert($tableName, $data);
    if ($row_count > 0) {
        echo "succeed";
    }
    else {
        echo "error";
    }
}
else {
    echo "error";
}


