<?php
    require '../mysql_fun.php';
    $dbc = mysql_conn();
    if (!empty($_POST)) {  
        $output = '';  
        $message = '';  
        $sql_res = false;
        $user_name = clean_xss($_POST["user_name"]);  
        $password = clean_xss($_POST["password"]);  
        $user_id = clean_xss($_POST["user_id"]);  
       
        if ($user_id != '') {  
            $q = "
                UPDATE user   
                SET FullName=:FullName,   
                Password=:Password
                WHERE id=:user_id
            ";
            try {
                $res = $dbc->prepare($q);
                $res->execute(['FullName' => $user_name, 'Password' => $password, 'user_id' => $user_id ]); 
                if ($res->rowCount() > 0) {
                    $message = 'Data Updated';  
                    echo 'Data Updated';  
                }
                else {
                    echo 'error';  
                }
                
            }
            catch (PDOException $e){
                echo "error";
            }
        }  
        else {
            echo "error";  
        }
        
    }  