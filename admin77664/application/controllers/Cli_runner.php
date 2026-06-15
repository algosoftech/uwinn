<?php
class Cli_runner extends CI_Controller {

    public function __construct() {
        parent::__construct();

        // CLI friendly server vars
        if(php_sapi_name() == 'cli'){
            $_SERVER['SERVER_NAME'] = '2021upos.com';
            $_SERVER['HTTP_HOST']   = '2021upos.com';
        }

        $this->load->model('notification_model');
    }

    public function notifications() {
        $this->notification_model->backgroundInsert();
    }
}
