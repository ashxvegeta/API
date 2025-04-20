<?php
include 'db.php';
header('Content-Type: application/json; charset=utf-8');
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);



switch ($method) {
    case 'GET':
        
        if(isset($_GET['id'])){
           $id =  $_GET['id'];
           $result = $connection->query("SELECT * FROM users WHERE id = $id"); 
           $data = $result->fetch_assoc();
           if($data){
              echo json_encode($data);
           }else {
            # code...
                echo json_encode(["message"=>"User not found"]);
           }

        }else{
            $result = $connection->query("SELECT * FROM users");
            $users = array();
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            echo json_encode($users);
        }

        break;


    case 'POST':
        $name = $input['name'];
        $email = $input['email'];
        $age = $input['age'];
        $connection->query("INSERT INTO users (name, email, age) VALUES ('$name', '$email', '$age')");
        if($connection->affected_rows > 0){
        echo json_encode(["message"=>"User created successfully"]);
        }else{
            echo json_encode(["message"=>"User not created"]);
        }
            # code...
    break;
    
    
    case 'PUT':
        $id  =  $_GET['id'];
        $name = $input['name'];
        $email = $input['email'];
        $age = $input['age'];
        $connection->query("UPDATE users SET name = '$name', email = '$email', age = '$age' WHERE id = $id");
        if($connection->affected_rows > 0){
            echo json_encode(["message"=>"User updated successfully"]);
        }else{
            echo json_encode(["message"=>"User not updated"]);
        }
        break;


    case 'DELETE':
        $id  =  $_GET['id'];
        $connection->query("DELETE FROM users WHERE id = $id");
        if($connection->affected_rows > 0){
            echo json_encode(["message"=>"User deleted successfully"]);
        }else{
            echo json_encode(["message"=>"User not deleted"]);
        }
        break;

    
    default:
       echo json_encode(["message"=>"Method not allowed"]);
        break;
}

?>