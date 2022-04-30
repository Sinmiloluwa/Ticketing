<?php
header('Content-Type: application/json'); 

class AuthController extends User
{
    private $pwd;
    private $email;
    private $name;
    private $checkPwd;

    public function __construct($pwd, $email, $name, $checkPwd)
    {
        $this->pwd = $pwd;
        $this->email = $email;
        $this->name = $name;
        $this->checkPwd = $checkPwd;
    }

    public function createUser()
    {
            
        if ($this->emptyInput() == false) {
            echo 'Empty Input';
            exit();
        }

        if ($this->invalidEmail() == false) {
            echo 'Invalid Email';
            exit();
        }

        if ($this->invalidUid() == false) {
            echo 'Username is Invalid';
            exit();
        }
        
        if ($this->pwdCheck() == false) {
            echo 'Passwords do not match';
            exit();
        }
        if ($this->userCheck() == false) {
            echo 'User already exists';
            exit();
        }


        $this->create($this->name, $this->pwd, $this->email);
        $user = [];
        $user['name'] = $this->name;
        $user['email'] = $this->email;
        $data = json_encode($user);
        echo $data;
        exit();
    }

    private function emptyInput()
    {
        $result = null;
        if (empty($this->pwd) || empty($this->email) || empty($this->name)) 
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

    private function invalidEmail()
    {
        $result = null;
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $result = true;
        }
        else {
            $result = true;
        }
        return $result;
    }

    private function pwdCheck()
    {
        $result = null;
        if ($this->pwd !== $this->checkPwd) {
            $result = false;
        }
        else {
            $result = true;
        }

        return $result;
    }

    private function userCheck()
    {
        $result = null;
        if ($this->checkUser($this->name, $this->email)) {
            $result = true;
        }
        else {
            $result = false;
        }

        return $result;
    }
}