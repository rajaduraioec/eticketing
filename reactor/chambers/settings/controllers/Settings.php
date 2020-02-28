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
 * @module      Settings
 * @filesource  Settings.controllers
 */
class Settings extends BE_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Settings_model');
        $this->ctrl_name="settings";
        $this->module_name="settings";
    }
    public function index() {   
            $this->rview->redirect('dashboard');
    }
    public function site() {
        $data['info']=$this->Settings_model->getsitesettings();
        $data['ctrl_name']=$this->ctrl_name;
        $data['ictdata']=$this->ictdata;
        $this->rview->gen('sitesettings',$data);
    }
    public function savesite() {
        $status=$this->Settings_model->edit();
        echo json_encode($this->rview->astatus($status,TRUE));
    }

}