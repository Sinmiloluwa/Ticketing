<?php
header('Content-Type: application/json'); 

class LoginController extends User
{
    private $pwd;
    private $email;

    public function __construct($email, $pwd)
    {
        $this->pwd = $pwd;
        $this->email = $email;
    }

    public function loginUser()
    {
        if ($this->emptyInput() == false) {
            echo json_encode('Fill in all fields');
            exit();
        }
        $this->getUser($this->email, $this->pwd);

        $user = array();
        $user['email'] = $this->email;
        $user['message'] = "Login Successful";
        echo json_encode($user);
    }

    private function emptyInput()
    {
        $result = null;
        if (empty($this->pwd) || empty($this->email)) 
        {
            $result = false;
        }
        else {
            $result = true;
        }
        return $result;
    }

    private function invalidUid()
    {
        $result = null;
        if (!preg_match("/^[a-zA-Z0-9]*$/", $this->name)) {
            $result = false;
        }
        else {
            $result = true;
        }
        return $result;
    }


}