<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    # header('Content-Type: application/json; charset=utf-8');
    $mysql = mysqli_connect("localhost", "passwords", "pass", "qpassword_manager") or die(mysqli_connect_error());
    $action = $_POST['action'];


    if ($action != 'new_user'){
        $user = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];

        $sql = "SELECT ID from Users where (User = \"" . $user . "\" and MasterKey = \"" . $password . "\")";
        $result = mysqli_query($mysql, $sql);

        $UserID = '';
        if ($id = mysqli_fetch_array($result)['ID']){
            $UserID = $id;
        }
    }

    if ($action == 'get_id'){
        echo $UserID;
    }

    else if ($action == 'new_user'){
        $user = $_POST['user'];
        $master_key = $_POST['master_key'];
        $sql = 'insert into Users (User, MasterKey) values ("' . $user . '", "' . $master_key . '")';

        if(!(mysqli_query($mysql, $sql))){
            echo mysqli_error($mysql);
        }
    }

    else if($action == 'delete'){
        $id = $_POST['id'];
        $sql = 'update Passwords set Deleted = 1 where (ID = ' . $id . ' and UserID = ' . $UserID . ')';
        if(mysqli_query($mysql, $sql)){
            echo "Successfully updated";
        }
        else {
            echo "Error: " . mysqli_error($mysql);
        }
    }

    else if ($action == 'add'){
        $password = $_POST['password'];
        $username = $_POST['username'];
        $website = $_POST['website'];
        $sql = 'insert into Passwords (UserID, Website, Username, Password) values (' . $UserID . ', "' . $website . '", "' . $username . '", "' . $password . '")';
        if(mysqli_query($mysql, $sql)){
            echo "Successfully updated, $sql";
        }
        else {
            echo "Error: " . mysqli_error($mysql);
        }
    }

    else if ($action == 'get_pass_ids'){
        $sql = 'select ID from Passwords where (UserID = ' . $UserID . ' and Deleted = 0)';
        $result = mysqli_query($mysql, $sql);
        $data = array();
        while($row = mysqli_fetch_array($result)){
            $data[] = $row['ID'];
        }
        echo json_encode($data);
    }

    else if ($action == 'get_row'){
        $sql = 'SELECT Website, Username, Password from Passwords where (ID = ' . $_POST['id'] . ' and Deleted = 0)';
        $result = mysqli_query($mysql, $sql);
        echo json_encode(mysqli_fetch_array($result));
    }

    else if ($action == 'create_table'){
        $sql = 'SELECT Website, Username, Password from Passwords where (UserID = ' . $UserID . ' and Deleted = 0)';
        $result = mysqli_query($mysql, $sql);
        $data = array();
        while($row = mysqli_fetch_array($result)){
            $data[] = $row;
        }
        echo json_encode($data);
    }
    else{
        echo 'invalid action';
    }
    mysqli_close($mysql);
    exit;
}

else{
    if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
       $uri = 'https://';
    } else {
       $uri = 'http://';
    }
    $uri .= $_SERVER['HTTP_HOST'];

    header('Location:'  . $uri . '/home/');
    exit;

    # $mysql = mysqli_connect("localhost", "passwords", "pass", "qpassword_manager") or die(mysqli_connect_error());
    # $sql = "SELECT User, Passwords.ID, Passwords.Username, Website, Password, Deleted FROM Passwords inner join Users on (Users.ID = UserID);";
    # $result = mysqli_query($mysql, $sql);
    # while ($row = mysqli_fetch_array($result)){
    #     echo "<p>" . json_encode($row) . "</p>";
    # }
    # $mysqli_close($mysql);
}
?>
