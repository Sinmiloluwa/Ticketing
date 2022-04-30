<?php

class User extends Db
{
    protected function create($name, $pwd, $email)
    {
        $sql = "INSERT INTO users (name, password, email) VALUES (?, ?, ?)";
        $stmt = $this->connect()->prepare($sql);
        $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);

        if (!$stmt->execute([$name, $hashedPwd, $email])) {
           $stmt = null;
           exit();
        }

        return true;
    }
    protected function checkUser($name, $email)
    {
        $sql = "SELECT name FROM users WHERE name = ? OR email =?;";
        $stmt = $this->connect()->prepare($sql);
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

    protected function getUser($email, $pwd)
    {
        $sql = "SELECT password FROM users WHERE email = ? OR name = ?";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$email, $pwd]);
        if ($stmt->rowCount() > 0) {
            $pwdHashed = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $checkedPwd = password_verify($pwd, $pwdHashed[0]["password"]);
            if ($checkedPwd == false) {
                echo json_encode("Incorrect Pssword");
            }
            elseif ($checkedPwd == true) {
                $sql = "SELECT * FROM users WHERE email = ? OR name = ? AND password = ?";
                $stmt = $this->connect()->prepare($sql);
                if (!$stmt->execute([$email, $email, $pwd]) ) {
                    echo json_encode("Could not fetch user");
                    exit();
                }

                if ($stmt->rowCount() == 0) {
                    echo json_encode("User not found");
                    exit();
                }

                $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

                session_start();
                $_SESSION["user_id"] = $user[0]["id"];
                $_SESSION["username"] = $user[0]["name"];
            }
        }
    }
}