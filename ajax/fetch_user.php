<?php  
    require '../mysql_fun.php';
    try {
        if (isset($_POST["user_id"])) {  
            $q = "select * from `user` WHERE id=:user_id limit 1";
            $dbc = mysql_conn();
            $res = $dbc->prepare($q);
            $res->execute(['user_id' => $_POST["user_id"]]); 
            foreach ($res as $row) {
                $row['user_id'] = stripslashes($row['id']);
                $row['user_name'] = stripslashes($row['FullName']);
                $row['password'] = stripslashes($row['Password']);
                $row['reg_date'] = stripslashes($row['RegDate']);
                echo json_encode($row);
            }
        }
    }
    catch (PDOException $e) {
        echo "PDOException: Database Error!";
    }  