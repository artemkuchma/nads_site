<?php


class LoginModel
{
    private $username;
    private $password;

    public function __construct(Request $request)
    {
        $this->username = $request->post('username');
        $this->password = $request->post('password');
    }

    public function isValid()
    {
        return !empty($this->username) && !empty($this->password);
    }

    public function getUser()
    {
        $dbc = Connect::getConnection();
        $sql = "SELECT * FROM users WHERE username = :username AND password = :password";

        $placeholders = array(
            'username'=> $this->username,
            'password'=>new Password($this->password)

        );
        $date = $dbc->getDate($sql, $placeholders);
        if(!$date){
            return false;
        }
       return $date; //!empty($date);


    }


    public function getPassword()
    {
        return $this->password;
    }


    public function getUsername()
    {
        return $this->username;
    }



}