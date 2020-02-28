<?php

/* 
 * 
 * @package	MMT Transport E-Ticketing
 * @Framework	Reactor Framework
 * @author         Increatech Dev Team
 * @copyright	Copyright (c) Since 2017, Increatech Business Solution Pvt Ltd, India (http://increatech.com/)
 * @link           https://increatech.com
 * @since          Version 1.0.0
 * @filesource
 */

?>
<ul class="navigation-menu">
    <li class="has-submenu">
        <a href="<?=$this->rview->url('dashboard');?>"><i class="fi-air-play"></i>Dashboard</a>
    </li>
    <?php
    $rdata[]=$this->rdb->qb('where','parent_id',NULL);
    $rdata[]=$this->rdb->qb('where','active',1);
    $rdata[]=$this->rdb->qb('order_by','ordering','asc');
    $menus=$this->rdb->fetch('default','ict_menu',$rdata);
    unset($rdata);
    foreach($menus as $mitem):
        echo '<li class="has-submenu">';
        if($mitem['controller_name']!=NULL)
            if($mitem['action_name']!=NULL)
                $url=$this->rview->url($mitem['controller_name'].'/'.$mitem['action_name']);
            else
                $url=$this->rview->url($mitem['controller_name']);
        else
            $url='javascript:void(0)';
        if($mitem['icon']!=NULL)
            $iname='<i class="'.$mitem['icon'].'"></i>'.' '.$mitem['menu_name'];
        else
            $iname=$mitem['menu_name'];
        echo '<a href="'.$url.'">'.$iname.'</a>';
            $rdata[]=$this->rdb->qb('where','parent_id',$mitem['id_ict_menu']);
            $rdata[]=$this->rdb->qb('where','active',1);
            $rdata[]=$this->rdb->qb('order_by','ordering','asc');
            $submenus=$this->rdb->fetch('default','ict_menu',$rdata);
            unset($rdata);
            if($submenus)
        echo '</li>';
    endforeach;
//    print_r($menus);
    ?>
</ul>