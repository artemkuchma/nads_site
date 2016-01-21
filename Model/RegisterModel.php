<?php


class RegisterModel {
    private $email;
    private $username;
    private $password;
    private $passwordConfirm;

    public function __construct(Request $request){
        $this->email = $request->post('email');
        $this->username = $request->post('username');
        $this->password = $request->post('password');
        $this->passwordConfirm = $request->post('passwordConfirm');

    }
    public function passwordMath()
    {
        return $this->password == $this->passwordConfirm;
    }
    public function isValid()
    {
        return !empty($this->email)&&!empty($this->username)&&!empty($this->password)&&!empty($this->passwordConfirm)&& $this->passwordMath();
    }

    public  function passwordStrong()
    {
       return preg_match("/(?=^.{8,}$)(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$/", trim($this->password) );
    }

    public function usernameValid()
    {
        return preg_match("/^[a-zA-Z][a-zA-Z0-9-_\.]{1,20}$/", trim($this->username) );

    }
    public function isUserExist()
    {
        $dbc = Connect::getConnection();
        $sql = 'SELECT * FROM users WHERE username = :username';
        //$date = $dbc->getPDO()->query($sql);
        //$d = $date->fetch(PDO::FETCH_ASSOC);
        $placeholders = array(
            'username'=> $this->username);
        $date = $dbc->getDate($sql, $placeholders);

        return empty($date)? true : false;
    }
    public function insertIntoDB()
    {
        $placeholders = array(
            'username'=>$this->username,
            'email'=>$this->email,
            'password'=>new Password($this->password)
        );
        $sql = 'INSERT INTO  users (username, email, password) VALUES (:username,:email, :password)';

        $dbc = Connect::getConnection();
        $sth = $dbc->getPDO()->prepare($sql);
        $sth ->execute($placeholders);
    }



    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPasswordConfirm($passwordConfirm)
    {
        $this->passwordConfirm = $passwordConfirm;
    }

    public function getPasswordConfirm()
    {
        return $this->passwordConfirm;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }




}