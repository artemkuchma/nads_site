<?php


class ContactModel {
    public $name;
    public $email;
    public $message;
    public $date;

    public function __construct(Request $request)
    {
        $this->name = $request->post('name');
        $this->email = $request->post('email');
        $this->message = $request->post('message');
        $this->date = $request->post('date');
    }

    public function isValid()
    {
        return $this->name !== '' && $this->email !== ''&& $this->message !== '';

    }
    public function saveToDb()
    {
        $placeholders = array(
            'name'=> $this->name,
            'email'=> $this->email,
            'date'=> $this->date,
            'message'=> $this->message);
        $sql = 'INSERT INTO  message (user_name, user_email, message_date, message) VALUES (:name,:email, :date, :message)';

        $dbc = Connect::getConnection();
        $sth = $dbc->getPDO()->prepare($sql);
        $sth ->execute($placeholders);
    }

}