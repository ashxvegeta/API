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
        $name = $input['name'];
        $email = $input['email'];
        $age = $input['age'];
        $stmt = $connection->prepare("INSERT INTO users(name,email,age) VALUES(?,?,?)");
        $stmt->bind_param("ssi",$name,$email,$age);
        $stmt->execute();
        if($stmt->affected_rows> 0){
           echo json_encode(["message"=>"User created successfully"]);
        }else{
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
        $stmt = $connection->prepare("UPDATE users set name=?,email=?,age=? WHERE id = ?");
        $stmt->bind_param("ssii",$name,$email,$age,$id);
        $stmt->execute();
        if($stmt->affected_rows>0){
          echo json_encode(["message"=>"User updated successfully"]);
        }else{
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
           echo json_encode(["message"=>"user deleted successfully"]);
        }else{
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