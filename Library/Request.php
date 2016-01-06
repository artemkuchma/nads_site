<?php


class Request
{
    private $get = array();
    private $post = array();
    private $server = array();

    private function request_method($method, $key)
    {
        if (isset($method[$key])) {
            return $method[$key];
        }
        return null;
    }

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->server = $_SERVER;
    }


    public function get($key)
    {
        return $this->request_method($this->get, $key);
    }

    public function post($key)
    {
        return $this->request_method($this->post, $key);

    }

    public function server($key)
    {
        return $this->request_method($this->server, $key);
    }

    public function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }


}