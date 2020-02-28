<?php

/* 
 * Reactor Framework
 * 
 * Copyright (c) 2014 - 2017, Increatech Business Solution Pvt Ltd, India
 * 
 * New BSD License
 * 
 * Redistribution and use in source and binary forms, with or without 
 * modification, are permitted provided that the following conditions are met:
 * 
 * Redistributions of source code must retain the above copyright notice, this 
 * list of conditions and the following disclaimer. Redistributions in binary 
 * form must reproduce the above copyright notice, this list of conditions and 
 * the following disclaimer in the documentation and/or other materials provided 
 * with the distribution. Neither the name of Reactor or INCREATECH BUSINESS 
 * SOLUTION PVT LTD, nor the names of its contributors may be used to endorse 
 * or promote products derived from this software without specific prior written 
 * permission. 
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
 * AND WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED WARRANTIES, INCLUDING, 
 * BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS 
 * FOR A PARTICULAR PURPOSE ARE NONINFRINGEMENT. IN NO EVENT SHALL THE COPYRIGHT 
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, 
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, 
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; 
 * OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, 
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR 
 * OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF 
 * ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 * @package	Reactor Framework
 * @author	Increatech Dev Team
 * @copyright	Copyright (c) 2013 - 2017, Increatech Business Solution Pvt Ltd, India (http://increatech.com/)
 * @link	https://increatech.com
 * @since	Version 1.0.0
 * @filesource
 */

defined('BASEPATH') or exit('No direct script access allowed');

/* 
 * Copyright 2017 Increatech Business Solution Pvt Ltd, India.
 * All Rights Reserved
 * www.increatech.com
 * info@increatech.com
 */

class Rview
{

    public function __construct()
    {
        $this->assetpath='assets/';
        $this->pluginpath='plugins/';
        $this->initpath='assets/init/';
    }
 
    public function __get($var)
    {
        return get_instance()->$var;
    }
    public function mainheader($params){
        return $this->load->view('mainheader',$params);
    }   
    public function mainfooter($params){
        return $this->load->view('mainheader',$params);
    }   
    /**
     * Generate template from rods folder
     * @param type $view
     * @param type $vars
     * @param type $return
     * @return type
     */
    public function gen($view, $vars = array(), $return = FALSE){
        return $this->load->view($view,$vars,$return);
    }   
    public function genajax($view, $vars = array()){
        return $this->load->view($view,$vars,TRUE);
    }   
    public function genview($vars = array(), $return = FALSE){
        $this->load->template_view('_layout_main',$vars,$return);
        return;
    }   
    public function authview($vars = array(), $return = FALSE){
        $this->load->template_view('_layout_auth',$vars,$return);
        return;
    }   
    /**
     * Generate URL for assets
     * @param type $file
     * @param type $filetype
     * @return type
     */
    public function assets($file, $filetype){
        return base_url($this->assetpath.$filetype.'/'.$file);
    }
    public function plugin($file, $folder){
        return base_url($this->pluginpath.$folder.'/'.$file);
    }
    public function init($file){
        return base_url($this->initpath.$file.'.js');
    }
    public function url($url=''){
        return base_url($url);
    }
    public function redirect($uri=''){
        redirect(base_url($uri),'auto');
    }
    public function mainmenu(){
        $data=$this->config->item('permit');
        foreach($data as $key=>$row)
            $permit[]=$key;
        $query[]=$this->rdb->qb('where','status','1');
        $query[]=$this->rdb->qb('order_by','sort','asc');
        $pmenuitems=$this->rdb->fetch('default','ict_menu',$query);unset($query);
        $menu = array(
            'items' => array(),
            'parents' => array()
        );
        $permit[]=NULL;
        foreach ($pmenuitems as $v_menu) {
            if(in_array($v_menu['code'],$permit)){
                $menu['items'][$v_menu['id_ict_menu']] = $v_menu;
                $menu['parents'][$v_menu['parent']][] = $v_menu['id_ict_menu'];
            }
        }
        foreach($menu['items'] as $menuitem){
            if($menuitem['link']=='#'){
                if (!isset($menu['parents'][$menuitem['id_ict_menu']])){
                    unset($menu['items'][$menuitem['id_ict_menu']]);
                    foreach($menu['parents'][$menuitem['parent']] as $key=>$citem){
                        if($citem==$menuitem['id_ict_menu'])
                            array_splice($menu['parents'][$menuitem['parent']],$key,1);
                    }
                }

            }
        }
        return print_r( $this->buildMenu(0, $menu,NULL,NULL));
    }

    public function buildMenu($parent, $menu, $sub = NULL,$meg=NULL)
    {
        $html = "";
        if (isset($menu['parents'][$parent])) {
            if (!empty($sub)) {
                $html .= '<ul class="submenu    '. $meg .'">';
            } else {
                $html .= '<ul class="navigation-menu">';
            }
            foreach ($menu['parents'][$parent] as $itemId) {
                if ( $menu['items'][$itemId]['icon']!=NULL) {
                    $icon = "<i class='" . $menu['items'][$itemId]['icon'] . "'></i>";
                } else {
                    $icon = '';
                }
                if ($menu['items'][$itemId]['label'] == 'Reports') {
                    $menutype = 'megamenu';
                } else {
                    $menutype = null;
                }
                if ($menu['items'][$itemId]['label'] == 'People'||$menu['items'][$itemId]['label'] == 'Operational') {
                    $html.= '</ul></li>';
                } 
                if (!isset($menu['parents'][$itemId])) { //if condition is false only view menu
                    if($menu['items'][$itemId]['link']=='##'){
                        $html .= "<li><ul><li ><span>" . $menu['items'][$itemId]['label'] . "</span></li> \n";
                    }else{
                        $html .= "<li ><a href='" . $this->url($menu['items'][$itemId]['link']). "'>" . $icon.$menu['items'][$itemId]['label'] . "</a></li> \n";
                    }
                }
                if (isset($menu['parents'][$itemId])) { //if condition is true show with submenu
                    $html .= "<li class='has-submenu'><a href='javascript:void(0)'> " . $icon. $menu['items'][$itemId]['label'] . "</span></a>\n";
                    $html .= self::buildMenu($itemId, $menu, $menu['items'][$itemId]['label'],$menutype);
                    $html .= ($menu['items'][$itemId]['label'] == 'Reports'?'</li></ul></li>':"</li> \n");
                }
            }
            $html .= "</ul> \n";
        }
        return $html;
    }

    public function buildusermenu(){
        $data=$this->config->item('permit');
        foreach($data as $key=>$row)
            $permit[]=$key;
        $query[]=$this->rdb->qb('where','status','1');
        $query[]=$this->rdb->qb('order_by','sort','asc');
        $pmenuitems=$this->rdb->fetch('default','ict_user_menu',$query);unset($query);
        $menu = array(
            'items' => array(),
            'parents' => array()
        );
        $permit[]=NULL;
        $html='';
        $html.='<ul class="nav navbar-nav navbar-right pull-right"><li class="navbar-c-items"></li>
        <li class="dropdown navbar-c-items">
            <a href="" class="dropdown-toggle waves-effect profile" data-toggle="dropdown" aria-expanded="true"><i class="fi-head"></i> </a>
            <ul class="dropdown-menu dropdown-menu-right arrow-dropdown-menu arrow-menu-right user-list notify-list">
                <li class="text-center"><h5>Hi, '.$this->session->userdata('name').'</h5></li>';
        $permit[]=NULL;
        foreach ($pmenuitems as $v_menu) {
            if(in_array($v_menu['code'],$permit)){
                
                if ($v_menu['label'] == 'Logout') {
                    $html.='<li><a href="'.$this->rview->url($v_menu['link']).'"><i class="'.$v_menu['icon'].'"></i>'.$v_menu['label'].'</a></li>';
                } else {
                    $html.='<li><a href="'.$this->rview->url($v_menu['link']).'" class="simple-ajax-modal"><i class="'.$v_menu['icon'].'"></i>'.$v_menu['label'].'</a></li>';
                }
            }
        }
        $html.='</ul></li></ul>';
        return print_r($html);
    }
    public function populateheader($assets=array(),$plugins=array()){
        $query[]=$this->rdb->qb('where','id_site_settings','1');
        $sitesettings=$this->rdb->fetch('default','site_settings',$query,FALSE,TRUE);
        unset($query);
        $sitename=$sitesettings->sitename;
        $html='';
        $html.='<meta charset="utf-8" />
            <title>'.$sitename.'</title>
            <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
            <meta content="Application for MMT Transport developed by Increatech Business Solution Pvt Ltd" name="description" />
            <meta content="Increatech" name="author" />
            <meta http-equiv="X-UA-Compatible" content="IE=edge" />';
        $html.='<link rel="shortcut icon" href="'.$this->assets('favicon.ico','images').'">';
        foreach($assets as $asset):
            if($asset[0]=='css')
                $html.='<link href="'.$this->assets($asset[1],$asset[0]).'" rel="stylesheet" type="text/css" />';
            elseif($asset[0]=='js')
                $html.='<script src="'.$this->assets($asset[1],$asset[0]).'"></script>';
        endforeach;
        foreach($plugins as $plugin):
            if($plugin[0]=='css')
                $html.='<link href="'.$this->plugin($plugin[2],$plugin[1]).'" rel="stylesheet" type="text/css"/>';
            elseif($plugin[0]=='js')
                $html.='<script src="'.$this->plugin($plugin[2],$plugin[1]).'"></script>';
        endforeach;
        print_r($html); return;
    }
    public function populatecontent($subview=''){
        $content='';
        $content.=$subview;
        $content.='<footer class="footer text-right">
                    <div class="container">
                        <div class="row">
                            <div class="col-xs-12 text-center">'.getfootertext().'
                            </div>
                        </div>
                    </div>
                </footer>';
        print_r($content);
        return;
    }
    public function populatefooterscripts($assets,$plugins,$inits){
        $html='';
        foreach($assets as $asset):
            if($asset[0]=='css')
                $html.='<link href="'.$this->assets($asset[1],$asset[0]).'" rel="stylesheet" type="text/css" />';
            elseif($asset[0]=='js')
                $html.='<script src="'.$this->assets($asset[1],$asset[0]).'"></script>';
        endforeach;
        foreach($plugins as $plugin):
            if($plugin[0]=='css')
                $html.='<link href="'.$this->plugin($plugin[2],$plugin[1]).'" rel="stylesheet" type="text/css"/>';
            elseif($plugin[0]=='js')
                $html.='<script src="'.$this->plugin($plugin[2],$plugin[1]).'"></script>';
        endforeach;
        foreach($inits as $init):
            $html.='<script src="'.$this->init($init).'"></script>';
        endforeach;
        print_r($html); return;
    }
    /**
     * Gen View file from fussion folders
     * @param type $folder
     * @param type $view
     * @param array $vars
     * @param boolean $return
     * @return type
     */
    public function genfile($folder, $view, $vars = array(), $return = FALSE){
        $this->load->file_view($folder, $view, $vars = array(), $return = FALSE);
        return;
    }   
    public function astatus($status, $tablereload='FALSE'){
        $response['status']=$status['status'];
        if($status['status']){
        $response['content']=$this->load->view('modal/success',array('msg'=>$status['message']),TRUE);
        if($tablereload)
            $response['tablereload']=TRUE;
        }else{
        $response['content']=$this->load->view('modal/failure',array('msg'=>$status['message']),TRUE);
        $response['tablereload']=FALSE;
        }
        return $response;
    }   
}