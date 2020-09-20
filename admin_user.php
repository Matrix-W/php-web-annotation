<?php 
// 判断是否登录
if ($_COOKIE['role'] != 'admin') {
    header("Location: adminlogin.php");
    exit;
}
require 'mysql_fun.php';
$dbh = mysql_conn();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Theme Made By www.w3schools.com - No Copyright -->
    <meta charset="utf-8">
    <title>管理员-用户管理</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src='assets/js/jquery.dataTables.min.js'></script>
    <link rel="stylesheet" href='assets/css/jquery.dataTables.min.css'>
    <link rel="stylesheet" href='assets/css/dataTables.jqueryui.css'>
    <link rel="stylesheet" href='assets/css/jquery-ui.css'>
    <script src="assets/js/bootstrap.js"></script>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
    <script type="text/javascript" language="javascript" class="init">
    $(document).ready(function() {
        var dataTable = $('#table_id').DataTable({
            "searching": true,
            "pagingType": "full_numbers",
            "pageLength": 100, //默认显示100条
            "aLengthMenu": [
                [10, 50, 100, 300, 500],
                [10, 50, 100, 300, 500]
            ],
            "aaSorting": [],
            columnDefs: [{
                targets: 0,
                className: 'dt-body-right'
            }],
            // 点击列头排序时，设置先降序后升序
            aoColumnDefs: [{
                orderSequence: ["desc", "asc"],
                aTargets: ['_all']
            }],
        });
        
        // 点击添加用户，弹窗
        $(document).on('click', '.add_student_data', function() {
            $('#add_student_data_Modal').modal('show');
        });
        
        // 添加用户，将数据插入数据库
        $('#insert_student_form').on("submit", function(event) {
            event.preventDefault();
            if ($('#add_user_name').val() == '') {
                alert("user name is required");
            } else {
                var btn_val = $('#insert_student_submit').val();
                $.ajax({
                    url: "ajax/insert_user_data.php",
                    method: "POST",
                    data: $('#insert_student_form').serialize(),
                    beforeSend: function() {
                        $('#insert_student_submit').val("添加中...");
                    },
                    success: function(data) {
                        if (data == 'error') {
                            alert('数据库操作出错，用户名称已存在');
                            $('#insert_student_form').val(btn_val);
                            $('#insert_student_submit').val("添加");
                        } else {
                            $('#insert_student_submit').val("添加成功！");
                            $('#insert_student_form')[0].reset();
                            $('#add_student_data_Modal').modal('hide');
                            location.reload(); // 刷新页面
                        }
                    }
                });
                
            }
        });
       
        // 用户信息数据打开编辑 
        $(document).on('click', '.edit_data', function() {
            var user_id = $(this).attr("id");
            $.ajax({
                url: "ajax/fetch_user.php",
                method: "POST",
                data: {
                    user_id: user_id
                },
                dataType: "json",
                success: function(data) {
                    $('#user_name').val(data.user_name);
                    $('#password').val(data.password); 
                    $('#reg_date').val(data.reg_date); 
                    $('#user_id').val(data.user_id); 
                    $('#insert').val("Update");
                    $('#add_data_Modal').modal('show');
                }
            });
        });
        // 用户信息数据插入和更新
        $('#insert_form').on("submit", function(event) {
            event.preventDefault();
            if ($('#user_name').val() == '') {
                alert("user_name is required");
            } else {
                var btn_val = $('#insert').val();
                $.ajax({
                    url: "ajax/update_user.php",
                    method: "POST",
                    data: $('#insert_form').serialize(),
                    beforeSend: function() {
                        $('#insert').val("Updating...");
                    },
                    success: function(data) {
                        if (data == 'error') {
                            alert('数据库操作出错，用户名称重复');
                            $('#insert').val(btn_val);
                            $('#insert').val("Update");
                        } else {
                            $('#insert_form')[0].reset();
                            $('#add_data_Modal').modal('hide');
                            //  $('#employee_table').html(data);  
                            location.reload(); // 刷新页面
                        }
                    }
                });
            }
        });

        // 数据删除
        $(document).on('click', '.delete_data', function() {
            var user_id = $(this).attr("id");
            if (confirm("你确定要删除吗?")) {
                $.ajax({
                    url: "ajax/delete_user.php",
                    method: "POST",
                    data: {
                        user_id: user_id
                    },
                    success: function(data) {
                        alert(data);
                        location.reload(); // 刷新页面
                    }
                });
            } else {
                return false;
            }
        });
    });
    </script>
</head>
<!-- Navbar -->
<nav class="navbar navbar-default">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="admin_index.php">主页</a>
        </div>
        <div class="collapse navbar-collapse" id="myNavbar">
            <ul class="nav navbar-nav navbar-right">
                <!-- <li><a href="upload.php">上传数据</a></li> -->
                <li><a href="admin_user.php">用户管理</a></li>
                <li><a href="admin_tagging_time.php">用户标注时间</a></li>
                <li><a href="admin_index.php">查看数据</a></li>
                <li><a><?php echo "welcome, <span style='color:red'>".$_COOKIE['user_name']; ?></span></a></li>
                <li><a href="logout.php">退出</a></li>
            </ul>
        </div>
    </div>
</nav>
<div id="global">
    <style>
        .demo-icons .col-lg-2 {
            padding: 10px;
            border: 1px solid transparent;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            border-radius: 2px;
        }

        .col-lg-2:hover {
            background: #f5f5f5;
            border-color: #eee;
        }
    </style>
    <div class="container-fluid">
        <div class="panel panel-default demo-icons">
            <div class="panel-heading">用户管理<span class="badge pull-right"></span></div>
            <div class="panel-body">
                <div id="message"></div>
                <!-- 用户管理表格start -->
                <div style="width:90%;align=center;">
                    <br>
                    <input type="button" name="add_student" value="添加用户" style="font-size:13px" class="btn btn-success btn-xs add_student_data" /><br>
                    <div class="table-responsive">
                        <br />
                        <div id="employee_table">
                            <table id="table_id" class="table table-condensed table-striped table-hover stripe hover condensed cell-border">
                                <thead align="left">
                                    <tr>
                                        <th>用户名称</th>
                                        <th>密码</th>
                                        <th>添加时间</th>
                                        <th>修改</th>
                                        <th>删除</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php  
                                            $q = "select * from `user` order by id desc";
                                            $res = $dbh->query($q);
                                            foreach ($res as $row) {
                                        ?>
                                        <tr>
                                            <td><?php echo $row['FullName']; ?></td>
                                            <td><?php echo $row['Password']; ?></td>
                                            <td><?php echo $row['RegDate']; ?></td>
                                            <td><input type="button" name="edit" value="编辑" style="font-size:13px"
                                                    id="<?php echo $row['id']; ?>"
                                                    class="btn btn-info btn-xs edit_data" /></td>
                                            <td><input type="button" name="delete" value="删除" style="font-size:13px"
                                                    id="<?php echo $row['id']; ?>"
                                                    class="btn btn-danger btn-xs delete_data" /></td>
                                        </tr>
                                    <?php  }  ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- 添加用户 -->
                <div id="add_student_data_Modal" class="modal fade">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">添加用户</h4>
                            </div>
                            <div class="modal-body">
                                <form method="post" id="insert_student_form">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td width="30%"><label>用户名称</label></td>
                                            <td width="70%"><input type="text" name="add_user_name"
                                                    id="add_user_name" class="form-control" required autocomplete="off" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="30%"><label>密码</label></td>
                                            <td width="70%"><input type="text" name="add_user_pwd"
                                                    id="add_user_pwd" class="form-control" required autocomplete="off" />
                                            </td>
                                        </tr>
                                    </table>
                                    <input type="submit" name="insert_student_submit" id="insert_student_submit" value="添加"
                                        class="btn btn-success" />
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 添加用户 end -->
                <!-- 修改用户信息 -->
                <div id="add_data_Modal" class="modal fade">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">修改</h4>
                            </div>
                            <div class="modal-body">
                                <form method="post" id="insert_form">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td width="30%"><label>用户名称</label></td>
                                            <td width="70%"><input type="text" name="user_name"
                                                    id="user_name" class="form-control" required autocomplete="off" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="30%"><label>密码</label></td>
                                            <td width="70%"><input type="text" name="password"
                                                    id="password" class="form-control" required autocomplete="off" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="30%"><label>添加时间</label></td>
                                            <td width="70%"><input type="text" name="reg_date"
                                                    id="reg_date" class="form-control" disabled required autocomplete="off" />
                                            </td>
                                        </tr>
                                    </table>
                                    <input type="hidden" name="user_id" id="user_id" />
                                    <input type="submit" name="insert" id="insert" value="Insert"
                                        class="btn btn-success" />
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 修改用户信息 end -->
                <!-- 用户管理表格end -->
            </div>
        </div>
    </div>