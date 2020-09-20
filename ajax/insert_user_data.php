<?php
require '../mysql_fun.php';
$dbc = mysql_conn();

if (!empty($_POST)) {
    $output = '';
    $message = '';
    $add_user_name = clean_xss($_POST["add_user_name"]);
    $add_user_pwd = clean_xss($_POST["add_user_pwd"]);
    $q = "select * from `user` where FullName = '$add_user_name'";
    $user_res = $dbc->prepare($q);
    $user_res->execute();
    if ($user_res->rowCount() == 1) {
        $output = 'error';
    }
    else {
        $user_info_q = "insert into `user` (FullName, Password) values('$add_user_name', '$add_user_pwd')";
        $user_info_res = $dbc->prepare($user_info_q);
        $user_info_res->execute();
        if ($user_info_res->rowCount() == 1) {
            $message = '添加成功!';  
        } 
        else {
            $output = 'error';
        }
    }

    echo $output;  
}
