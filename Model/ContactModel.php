<?php


class ContactModel {
    public $name;
    public $email;
    public $message_subject;
    public $message;
    public $date;
    private $id_reg_user;

    public function __construct(Request $request)
    {
        $this->name = Session::has('user')? Session::get('user')['user'] : $request->post('name');
        $this->email = Session::has('user')? $this->regUserData()['email'] : $request->post('email');
        $this->message_subject = $request->get('message_subject');
        $this->message = $request->post('message');
        $this->date = $request->post('date');
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
        return $this->name !== '' && $this->email !== ''&& $this->message !== ''&& $this->message_subject !== '';

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