<?php


namespace App\Container;

abstract class Container{
    private $errors = [
        '101' => 'query parameter error',
        '102' => 'failed to issue token',
        '103' => 'failed to get_profile',
        '104' => 'user duplicated in register',
        '105' => 'does not exist user-information',
        '106' => 'you maybe did not resister',
        '107' => 'failed to update token',
        '108' => 'user_id error',

        '201' => 'plans pagenation error',
        '202' => 'did not find plan(s)',
        '203' => 'plans are too much',
        '204' => 'there are some problems in params',
        '205' => 'failed to delete plan',

        '301' => 'did not find calendar',
        '302' => 'this date is out of calendar',
        '303' => 'event was duplicated on selected date',
        '304' => 'event doesnt exist on selected date',
    ];


    private $error_code;
    private $error = ['code' => 0, 'discription' => 'no error'];
    private $status = false;

    protected $result = null;
    protected $user_id = null;



    public function setting($user_id){
        if(!$user_id){
            $this->set_error(108);
            return;
        }
        $this->user_id = $user_id;
        $this->status_ok();
    }



    protected function set_error($code){
        if(isset($this->errors[$code])){
            $this->error['code'] = $code;
            $this->error['discription'] = $this->errors[$code];
        }
        else{
            abort(400);
        }
    }
    public function get_error(){
        return $this->error;
    }



    protected function status_ok(){
        $this->status = true;
    }
    protected function status_bad(){
        $this->status = false;
    }


    public function is_status_ok(){
        return $this->status;
    }
    public function is_status_bad(){
        return !$this->status;
    }



    public function get_result(){
        return $this->result;
    }
}
