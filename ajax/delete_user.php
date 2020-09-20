<?php
    require '../mysql_fun.php';
    try {
        if (isset($_POST["user_id"])) {
            $sql = "DELETE FROM `user` WHERE id=:user_id";
            $dbc = mysql_conn();
            $stmt = $dbc->prepare($sql);
            $stmt->execute(['user_id' => $_POST["user_id"]]);
            if ($stmt->rowCount() == 1) {
                echo '删除成功！';
            }
            else {
                echo '删除失败!';
            }
        }
        else {
            echo '删除失败!';
        }
    }
    catch (PDOException $e) {
        echo "PDOException: Database Error!";
    }  