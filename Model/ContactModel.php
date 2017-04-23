<?php


class ContactModel {
    public $name;
    public $email;
    public $message_subject;
    public $message;
    public $date;
    public $captcha;
    private $id_reg_user;

    public function __construct(Request $request)
    {
        $this->name = Session::has('user')? Session::get('user')['user'] : $request->post('name');
        $this->email = Session::has('user')? $this->regUserData()['email'] : $request->post('email');
        $this->message_subject = $request->get('message_subject');
        $this->message = $request->post('message');
        $this->date = $request->post('date');
        $this->captcha = $request->post('g-recaptcha-response');
        $this->id_reg_user = Session::has('user')? Session::get('user')['id'] : null;
    }

    private function regUserData()
    {
        $dbc = Connect::getConnection();
        $placeholders = array(
            'username' => Session::get('user')['user']
        );
        $sql = "SELECT `email` FROM `users` WHERE `username`= :username";
        $data = $dbc->getDate($sql, $placeholders);

        return $data[0];
    }

    public function isValid()
    {
        return $this->captcha !==''&& $this->name !== '' && $this->email !== ''&& $this->message !== ''&& $this->message_subject !== '';

    }


    private  function SiteVerify($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 15);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36");
        $curlData = curl_exec($curl);
        curl_close($curl);
        return $curlData;
    }
    public function captchaValid()
    {
        $google_url = "https://www.google.com/recaptcha/api/siteverify";
        $secret = '6LdOvx0UAAAAANDBP9Cjf1uc2WpFfwbDCxKm3z6I';
        $ip = $_SERVER['REMOTE_ADDR'];
        $url = $google_url."?secret=".$secret."&response=".$this->captcha."&remoteip=".$ip;
        $res = $this->SiteVerify($url);
        $res= json_decode($res, true);
        return $res['success'];
    }



    public function saveToDb()
    {
        $placeholders = array(
            'name'=> $this->name,
            'email'=> $this->email,
            'date'=> $this->date,
            'message'=> $this->message,
            'message_subject' => $this->message_subject,
            'id_reg_user' => $this->id_reg_user
        );

        $sql = 'INSERT INTO  message (user_name, user_email, message_date, message_subject, message, id_reg_user) VALUES (:name,:email, :date, :message_subject, :message, :id_reg_user)';

        $dbc = Connect::getConnection();
        $sth = $dbc->getPDO()->prepare($sql);
        $sth ->execute($placeholders);
    }

    public function getMessagesList()
    {
        $dbc = Connect::getConnection();
        $placeholders = array();
        $sql = "SELECT * FROM `message`";
        $data = $dbc->getDate($sql, $placeholders);

        return $data;

    }

    public function deleteMessage($id)
    {
        $placeholders = array(
            'id' => $id
        );

        $dbc = Connect::getConnection();
        $sql = "DELETE FROM `message` WHERE id = :id";
        $sth = $dbc->getPDO()->prepare($sql);
        $sth->execute($placeholders);
    }

}