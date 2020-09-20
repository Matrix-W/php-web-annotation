<?php
require 'config.php';
function mysql_conn()
{
    try {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . '';
        $pdo = new PDO($dsn, DB_USER, DB_PASSWD);
        $pdo->exec('set names utf8');
        if (!$pdo) {
            echo 'error';
            exit;
        }
    } catch (PDOException $e) {
        echo 'error';
        // echo $e->getMessage();
        exit;
    }
    return $pdo;
}

function pdoMultiInsert($tableName, $data)
{
    $pdoObject = mysql_conn();
    //Will contain SQL snippets.
    $rowsSQL = array();
    //Will contain the values that we need to bind.
    $toBind = array();
    //Get a list of column names to use in the SQL statement.
    $columnNames = array_keys($data[0]);
    //Loop through our $data array.
    foreach ($data as $arrayIndex => $row) {
        $params = array();
        foreach ($row as $columnName => $columnValue) {
            $param = ":" . $columnName . $arrayIndex;
            $params[] = $param;
            $toBind[$param] = $columnValue;
        }
        $rowsSQL[] = "(" . implode(", ", $params) . ")";
    }
    $sql = "INSERT  INTO $tableName  (" . implode(", ", $columnNames) . ") VALUES " . implode(", ", $rowsSQL);
    //Prepare our PDO statement.
    $pdoStatement = $pdoObject->prepare($sql);
    //Bind our values.
    foreach ($toBind as $param => $val) {
        $pdoStatement->bindValue($param, $val);
    }
    $pdoStatement->execute();
    return $pdoStatement->rowCount();
}

function custom_query($sql)
{
    $pdoObject = mysql_conn();
    $sel = $pdoObject->prepare($sql);
    $sel->execute();
    $sel->setFetchMode(PDO::FETCH_OBJ);
    return $sel;
}

// 百度翻译
function baidu_translate($query) {
    $app_id = '20151113000005349';
    $key = 'osubCEzlGjzvw8qdQc41';  

    // $query = 'Are you ok';  //  要翻译的内容
    $url = 'https://api.fanyi.baidu.com/api/trans/vip/translate';  //  发送请求的URL
    $salt = mt_rand(11111111, 99999999);  //  随机数
    $sign = $app_id . $query . $salt . $key;  //  APPID+翻译内容+随机数+密钥
    $sign = md5($sign);  //  md5加密，生成签名

    //  下面是url拼接
    $url = $url . '?q=' . urlencode($query) . '&from=auto&to=zh&appid=' . $app_id . '&salt=' . $salt . '&sign=' . $sign;

    $data = file_get_contents($url);  //  发送get请求
    $data=json_decode($data); //化为字符串格式以便操作
    //echo $data->trans_result[0]->src; //src元素代表原文
    return $data->trans_result[0]->dst; //dst元素代表翻译结果
}

function clean_xss($string)
{
    $str = trim($string);
    $str = strip_tags($str);
    // $str = htmlspecialchars($str, ENT_QUOTES);
    return $str;
}