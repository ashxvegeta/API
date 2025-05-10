<?php
include 'db.php';
header('Content-Type: application/json; charset=utf-8');
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

$action = isset($_GET['action']) ? $_GET['action'] : null;

if ($action === 'toggleStatus' && isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $result = $connection->query("SELECT status FROM users WHERE id = $id");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $newStatus = $row['status'] == 1 ? 0 : 1;

        $update = $connection->query("UPDATE users SET status = $newStatus WHERE id = $id");

        if ($update) {
            http_response_code(200);
            echo json_encode([
                "message" => "Status updated successfully",
                "new_status" => $newStatus
            ]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to update status"]);
        }
    } else {
        http_response_code(404);
        echo json_encode(["message" => "User not found"]);
    }

    exit;
}


if ($action === 'likeunlike' && isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $result = $connection->query("SELECT likes FROM users WHERE id = $id");

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $status = $row['likes'] == 1 ? 0 : 1;

        $update = $connection->query("UPDATE users SET likes = $status WHERE id = $id");

        if ($update) {
            http_response_code(200);
            echo json_encode(["message" => "Status updated", "status" => $status]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to update status"]);
        }
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Invalid ID"]);
    }
    exit;
}




switch ($method) {
    case 'GET':
        
        if(isset($_GET['id'])){
           $id =  $_GET['id'];
           $result = $connection->query("SELECT * FROM users WHERE id = $id"); 
           $data = $result->fetch_assoc();
           if($data){
            http_response_code(200); // OK
              echo json_encode($data);
           }else {
            # code...
            http_response_code(404);
                echo json_encode(["message"=>"User not found"]);
           }
        //    // Sample JSON data to be sent in the body of the request
        //    // http://localhost/PHP_API/api.php?id=5

        }else{

            // Fetch all users
            //http://localhost/PHP_API/api.php
            $result = $connection->query("SELECT * FROM users");
            $users = array();
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            echo json_encode($users);
        }

        break;


    case 'POST':
        $name = trim($input['name']);
        $email = trim($input['email']);
        $age = $input['age'];

        $errors = [];
        if(empty($name)){
            $errors[]= "Name is required";
        }
        if(empty($email)){
            $errors[]= "Email is required";
        }
        if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
            $errors[]= "Email is not valid";
        }
        if(empty($age)){
            $errors[]= "Age is required";
        }
        if(!is_numeric($age)){
            $errors[]= "Age must be a number";
        }
        if(!empty($errors)){
            echo json_encode(["message"=>"Validation error","errors"=>$errors]);
            exit;
        }

        $stmt = $connection->prepare("INSERT INTO users(name,email,age) VALUES(?,?,?)");
        $stmt->bind_param("ssi",$name,$email,$age);
        $stmt->execute();
        if($stmt->affected_rows> 0){
           http_response_code(201);
           echo json_encode(["message"=>"User created successfully"]);
        }else{
            http_response_code(400); 
            echo json_encode(["message"=>"User not created"]);
        }

        $stmt->close();
        break;
        # code...
        // Sample JSON data to be sent in the body of the request
        // {
        //     "name": "GeeksforGeeks2",
        //     "email": "geek@geeksforgeeks.com",
        //     "age": 27
        // }

    case 'PUT':
        $id  =  $_GET['id'];
        $name = $input['name'];
        $email = $input['email'];
        $age = $input['age'];

        $errors = [];
        if(empty($id)){
          $errors[] = "ID is required";
        }
        if (empty($name)) {
            $errors[] = "Name is required";
        }

        if(empty($email)){
            $errors[] = "Email is required";

        }
        if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
           $errors[] = "Email is not valid";
        }
        if(empty($age)){
          $errors[] = "Age is required";
        }
        if(!is_numeric($age)){
           $errors[] = "Age must be a number";
        }

        if(!empty($errors)){
            echo json_encode(["message"=>"Validation error","errors"=>$errors]);
            exit;
        }

        if(!empty($errors)){
           echo json_encode(["message"=>"Validation erros","errors"=>$errors]);
            exit;
        }
        

        $stmt = $connection->prepare("UPDATE users set name=?,email=?,age=? WHERE id = ?");
        $stmt->bind_param("ssii",$name,$email,$age,$id);
        $stmt->execute();
        if($stmt->affected_rows>0){
            http_response_code(200);
          echo json_encode(["message"=>"User updated successfully"]);
        }else{
            http_response_code(400);
           echo json_encode(["message"=>"User not updated"]);
        }
        $stmt->close();
        break;

        // Sample JSON data to be sent in the body of the request
        // http://localhost/PHP_API/api.php?id=5

        // {
        //     "name": "Write for GeeksforGeeks",
        //     "email": "geeksforgeeks@geeksforgeeks.com",
        //     "age": 26
        // }


    case 'DELETE':
        $id  =  $_GET['id'];
        $stmt = $connection->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
        if($stmt->affected_rows >0){
            http_response_code(200);
           echo json_encode(["message"=>"user deleted successfully"]);
        }else{
            http_response_code(404);
              echo json_encode(["message"=>"user not deleted"]);
        }
        $stmt->close();
        break;

        // Sample JSON data to be sent in the body of the request
        // http://localhost/PHP_API/api.php?id=5

    
    default:
       echo json_encode(["message"=>"Method not allowed"]);
        break;
}

?>