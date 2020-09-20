<?php
session_start();
error_reporting(0);
require 'mysql_fun.php';
$dbh = mysql_conn();
// 回到登录页面后，自动退出登录
if (isset($_POST['login'])) {
  //code for captach verification
  if ($_POST["vercode"] != $_SESSION["vercode"] or $_SESSION["vercode"] == '') {
    echo "<script>alert('验证码输入错误');</script>";
  } 
  else {
    $full_name = $_POST['full_name'];
    $password = $_POST['password'];
    $sql = "SELECT * FROM `user` WHERE FullName=:full_name and Password=:password";
    $query = $dbh->prepare($sql);
    $query->bindParam(':full_name', $full_name, PDO::PARAM_STR);
    $query->bindParam(':password', $password, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
    if ($query->rowCount() > 0) {
      foreach ($results as $result) {
        setcookie("user_name", $_POST['full_name']); 
        setcookie("role", 'user');
        echo "<script type='text/javascript'> document.location ='index.php'; </script>";
      }
    } 
    else {
      echo "<script>alert('Invalid Details');</script>";
    }
  }
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title>用户登录</title>
  <!-- BOOTSTRAP CORE STYLE  -->
  <link href="assets/css/bootstrap.css" rel="stylesheet" />
  <!-- FONT AWESOME STYLE  -->
  <link href="assets/css/font-awesome.css" rel="stylesheet" />
  <!-- CUSTOM STYLE  -->
  <link href="assets/css/style.css" rel="stylesheet" />
  <!-- GOOGLE FONT -->
  <link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />

</head>

<body>
  <!------MENU SECTION START-->
  <section class="menu-section">
        <div class="container">
            <div class="row ">
                <div class="col-md-12">
                    <div class="navbar-collapse collapse ">
                        <ul id="menu-top" class="nav navbar-nav navbar-right">
                            <li><a href="adminlogin.php">管理员登录</a></li>
                            <li><a href="login.php">用户登录</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
  <!-- MENU SECTION END-->
  <div class="content-wrapper">
    <div class="container">
      <div class="row pad-botm">
        <div class="col-md-12">
          <h4 class="header-line">用户登录</h4>
        </div>
      </div>

      <!--LOGIN PANEL START-->
      <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
          <div class="panel panel-info">
            <div class="panel-heading">
              用户登录
            </div>
            <div class="panel-body">
              <form role="form" method="post">

                <div class="form-group">
                  <label>用户名</label>
                  <input class="form-control" type="text" name="full_name" required autocomplete="off" />
                </div>
                <div class="form-group">
                  <label>密码</label>
                  <input class="form-control" type="password" name="password" required autocomplete="off" />
                </div>

                <div class="form-group">
                  <label>验证码 : </label>
                  <input type="text" class="form-control1" name="vercode" maxlength="5" autocomplete="off" required style="height:25px;" />&nbsp;<img src="captcha.php">
                </div>
                <button type="submit" name="login" class="btn btn-info">登录 </button> 
              </form>
            </div>
          </div>
        </div>
      </div>
      <!---LOGIN PABNEL END-->


    </div>
  </div>
  <!-- FOOTER SECTION END-->
  <script src="assets/js/jquery-1.10.2.js"></script>
  <!-- BOOTSTRAP SCRIPTS  -->
  <script src="assets/js/bootstrap.js"></script>
  <!-- CUSTOM SCRIPTS  -->
  <script src="assets/js/custom.js"></script>

</body>

</html>