<?php


if (!defined('RAPPVERSION'))
    exit('No direct script access allowed');
/* 
 * 
 * @package	MMT Transport E-Ticketing
 * @Framework	Reactor Framework
 * @author      Increatech Dev Team
 * @copyright	Copyright (c) Since 2017, Increatech Business Solution Pvt Ltd, India (http://increatech.com/)
 * @link        https://increatech.com
 * @since       Version 1.0.0
 * @module      auth
 * @filesource  auth.controllers
 */


class Auth extends BE_Controller {

    function __construct() {
        parent::__construct();
        $this->ctrl_name="auth";
        $this->load->model('Auth_model');
        $this->module_name="auth";
    }
    public function index() {   
        if(!$this->rauth->is_login())
        {
            $this->rview->redirect('auth/login');
        }
        else {
            $this->rview->redirect('dashboard');
        }
    }
    public function login($mode='logme') {   
        if(!$this->rauth->is_login())
        {
            
            if($this->input->server('REQUEST_METHOD') == 'POST'){
                $validate=$this->rauth->login($this->input->post('username'),$this->input->post('password'),$mode);
                if($validate):
                        $this->rview->redirect('dashboard');
                    else:
                        $data['page_name']="Login";
                        $data['error']=TRUE;
                        $data['ctrl_name']=$this->ctrl_name;
                        $data['mode']=$mode;
                        $render['subview']=$this->rview->gen('login',$data,TRUE);
                        $render['ictdata']=$this->ictdata;
                        $this->rview->authview($render);
                endif;
            }else{
                $data['page_name']="Login";
                $data['ctrl_name']=$this->ctrl_name;
                $data['mode']=$mode;
                $render['subview']=$this->rview->gen('login',$data,TRUE);
                $render['ictdata']=$this->ictdata;
                $this->rview->authview($render);
            }
        }
        else {
            $this->rview->redirect('dashboard');
        }
    }
    
    public function profile() {
        $data['info']=$this->Auth_model->getprofile();
        $data['ictdata']=$this->ictdata;
        $data['ctrl_name']=$this->ctrl_name;
        $this->rview->gen('profile',$data);
    }
    public function saveprofile($id='') {
        $status=$this->Auth_model->edit($id);
        echo json_encode($this->rview->astatus($status,TRUE));
    }
    public function changepass(){
            $data['ctrl_name']=$this->ctrl_name;
            $data['ictdata']=$this->ictdata;
            $this->rview->gen('password',$data);
    }
    public function setpassword() {
        $status=$this->Auth_model->passwordupdate();
        echo json_encode($this->rview->astatus($status,TRUE));
    }
    function logout() {
        $this->session->sess_destroy();
        $render['subview']=$this->rview->gen('logout','',TRUE);
        $this->rview->authview($render);
    }

}
