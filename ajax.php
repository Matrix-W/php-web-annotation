<?php
// $file = "notic_" . date("Ymd") . ".log";
// $ct = date("Y-m-d H:i:s", time());
// error_log("[" . $ct . "] ". print_r($_POST, true) ." \r\n", 3, $file);
require 'mysql_fun.php';
$tableName = 'tagging_sentence_words';
if ($_POST['sentence_id'] == '-1') {
    $data = [
        [
            'user_name' => $_POST['user_name'],
            'tagging_id' => $_POST['tagging_id'],
            'create_time' => date('Y-m-d H:i:s')
        ]
    ];
}
else {
    $data = [
        [
            'user_name' => $_POST['user_name'],
            'tagging_id' => $_POST['tagging_id'],
            'sentence_id' => $_POST['sentence_id'],
            'tagging_words' => $_POST['tagging_words'],
            'verbs_classification_select' => $_POST['verbs_classification_select'],
            'speech_attr_select' => $_POST['speech_attr_select'],
            'one_select' => $_POST['one_select'],
            'two_select' => $_POST['two_select'],
            'create_time' => date('Y-m-d H:i:s')
        ]
    ];
}

$row_count = pdoMultiInsert($tableName, $data);
if ($row_count > 0) {
    echo "succeed";
}
else {
    echo "error";
}
