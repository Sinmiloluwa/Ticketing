<?php

class User
{
    private $conn;

     // User properties
     public $id;
     public $name;
     public $pwd;
     public $email;

     // constructor
     public function __construct($db)
     {
         $this->conn = $db;
     }

    public function create()
    {
        $data = json_decode(file_get_contents("php://input"));

        $this->name = $data->name;
        $this->password = $data->password;
        $this->email = $data->email;

        $sql = "INSERT INTO users (name, password, email) VALUES (:name, :password, :email)";
        $stmt = $this->conn->prepare($sql);
        $hashedPwd = password_hash($this->password, PASSWORD_DEFAULT);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':password', $hashedPwd);
        $stmt->bindParam(':email', $this->email);

        if ($stmt->execute()) {
           echo json_encode(["message"=>"User created successfully"]);
        } else{
            echo json_encode(["message"=>"User could not be created"]);
        }  
    }
    protected function checkUser($name, $email)
    {
        $sql = "SELECT name FROM users WHERE name = ? OR email =?;";
        $stmt = $this->conn->prepare($sql);
        if(!$stmt->execute([$name, $email])) 
        {
            $stmt = null;
            exit();
        }

        $resultCheck = null;
        if ($stmt->rowCount() > 0) {
            $resultCheck = false;
        }
        else {
            $resultCheck = true;
        }

        return $resultCheck;
    }

    public function getUser()
    {
        $data = json_decode(file_get_contents("php://input"));

        // $this->name = $data->name;
        $this->email = $data->email;
        $this->pwd = $data->password;

        $sql = "SELECT password FROM users WHERE email = :email OR name = :name";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':name', $this->name);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $pwdHashed = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $checkedPwd = password_verify($this->pwd, $pwdHashed[0]["password"]);
            if ($checkedPwd == false) {
                echo json_encode("Incorrect Pssword");
            }
            elseif ($checkedPwd == true) {
                $sql = "SELECT * FROM users WHERE email = :email AND password = :password";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':email', $this->email);
                $stmt->bindParam(':password',  $pwdHashed[0]["password"]);
                $stmt->execute();
                $num = $stmt->rowCount();

                if ($num == 0) {
                    echo json_encode("User not found");
                    exit();
                }

                $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

                session_start();
                $_SESSION["user_id"] = $user[0]["id"];
                $_SESSION["username"] = $user[0]["name"];

                $user_data = array();
                $user_data['id'] = $user[0]['id'];
                $user_data['name'] = $user[0]['name'];
                $user_data['email'] = $user[0]['email'];

                echo json_encode(["user" => $user_data]);
            }
        }
    }
}