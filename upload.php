<?php
require 'mysql_fun.php';
//首先导入PHPExcel
require_once './PHPExcel/Classes/PHPExcel.php';
if ($_COOKIE['role'] != 'user') {
    header('location: login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
    if ($_FILES['upload_file']['name'] != "") {
        $inputFileName = $_FILES['upload_file']['tmp_name'];
        // 读取excel文档
        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } 
        catch(Exception $e) {
            die('加载文档发生错误："'.pathinfo($inputFileName, PATHINFO_BASENAME).'": '.$e->getMessage());
        }
        try {
            // 确定要读取的sheet，什么是sheet，看excel的右下角
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            // echo "<br>" . $highestColumn . "<br>";
            // 获取头部信息
            $row_header_data = $sheet->rangeToArray('A' . 1 . ':' . $highestColumn . 1, NULL, TRUE, FALSE);
            // print_r($row_header_data[0]);
            
            $all_data_arr = array();
            $all_data_amp_arr = array();
            // 获取一行的数据
            $rowsToInsert = array();
            for ($row = 1; $row <= $highestRow; $row++) {
                $res = array();
                // Read a row of data into an array
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
                //这里得到的rowData都是一行的数据，得到数据后自行处理，我们这里只打出来看看效果
                // var_dump($rowData);
                // echo $rowData[0][1] . "<hr>";
                // 处理过滤每一行的数据
                $paragraph_id = trim($rowData[0][0]);
                $paragraph = trim($rowData[0][1]);
                if (empty($paragraph)) {
                    continue;
                }
                $paragraph = str_replace('U.S.', 'US', $paragraph);
                // $paragraph = preg_split("[^.?!]+ ?", $paragraph);
                // var_dump($paragraph);  // 每一句
                // 将每一段分句，存入数据库
                $paragraph_to_sentence_list = explode(". ", $paragraph); 
                for ($i = 0; $i < count($paragraph_to_sentence_list); $i++) {
                    $db_sentense = array();
                    $db_sentense['sentence'] = $paragraph_to_sentence_list[$i];
                    $db_sentense['paragraph_id'] = $paragraph_id;
                    $db_sentense['create_time'] = date("Y-m-d H:i:s",time());
                    $db_sentense['user_name'] = $_COOKIE['user_name'];
                    // echo ".</p><br>" . baidu_translate($list[$i]) . "<hr>";
                    $rowsToInsert[] = $db_sentense;
                }
            }
            // print_r($rowsToInsert);
            $exec_count = pdoMultiInsert('tagging', $rowsToInsert);
            if ($exec_count > 0) {
                echo "<script src=\"jquery-2.1.4.min.js\"></script><script>$(document).ready(function (){ $('.alert-success').show(); $('.alert-danger').hide(); });</script>";
            }
            else {
                echo "<script src=\"jquery-2.1.4.min.js\"></script><script>$(document).ready(function (){ $('.alert-danger').show(); $('.alert-success').hide(); });</script>";
            }
            echo $exec_count;
        }
        catch (Exception $e) {
            echo '<h2 style="color:red">文档格式错误！</h2>';
        }
    }
    else {
        echo "<h2>请选择需要处理的文件！</h2>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Theme Made By www.w3schools.com - No Copyright -->
    <meta charset="utf-8">
    <title>文件上传</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
    <script src="jquery-2.1.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <style>
        body {
            font: 20px Montserrat, sans-serif;
            line-height: 1.8;
        }

        p {
            font-size: 16px;
        }

        .margin {
            margin-bottom: 45px;
        }

        .bg-1 {
            background-color: #1abc9c;
            /* Green */
            color: #ffffff;
        }

        .bg-2 {
            background-color: #474e5d;
            /* Dark Blue */
            color: #ffffff;
        }

        .bg-3 {
            background-color: #ffffff;
            /* White */
            color: #555555;
        }

        .bg-4 {
            background-color: #2f2f2f;
            /* Black Gray */
            color: #fff;
        }

        .container-fluid {
            padding-top: 70px;
            padding-bottom: 70px;
        }

        .navbar {
            padding-top: 15px;
            padding-bottom: 15px;
            border: 0;
            border-radius: 0;
            margin-bottom: 0;
            font-size: 12px;
            letter-spacing: 5px;
        }

        .navbar-nav li a:hover {
            color: #1abc9c !important;
        }

        .container_main {
            padding-top: 70px;
            padding-bottom: 70px;
            width: 80%;
            min-height: 500px;
            color: black;

        }
        .alert-success {
            display: none;
        }
        .alert-danger {
            display: none;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-default">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">主页</a>
            </div>
            <div class="collapse navbar-collapse" id="myNavbar">
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="upload.php">上传数据</a></li>
                    <li><a href="index.php">查看数据</a></li>
                    <!-- <li><a href="login.php">登录</a></li> -->
                    <li><a><?php echo "welcome, <span style='color:red'>".$_COOKIE['user_name']; ?></span></a></li>
                    <li><a href="logout.php">退出</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- First Container -->
    <center>
        <div class="container_main">
            <div class="alert alert-success" role="alert">
                数据上传成功！
            </div>
            <div class="alert alert-danger" role="alert">
                数据上传失败！
            </div>
            <form action="" method="post" name="addForm" id="addForm" enctype="multipart/form-data">
                    <table>
                        <tr>
                            <td style="width: 200px">选择上传文件</td>
                            <td><input type="file" accept=".xlsx,.csv" id="file" name="upload_file"></td>
                        </tr>
                    </table>
                    <button style="margin-top:50px" type="submit" class="btn btn-primary">上传</button>
                    
            </form>
        </div>
    </center>

    <!-- Footer -->
    <!-- <footer class="container-fluid bg-4 text-center">
        <p>标注系统 Copyright 2019</p>
    </footer> -->

</body>

</html>