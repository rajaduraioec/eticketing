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
 * @module      Config
 * @filesource  Config.views.warning
 */
 
 ?>
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title"><?=$ictdata['idioms']['ct_module_title'];?></h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="panel panel-border panel-primary">
        <div class="panel-heading"></div><div class="panel-body">
            <div class="row">
                <div class="alert alert-icon alert-white alert-danger fade in" role="alert">
                   <i class="mdi mdi-block-helper"></i>
                    <strong>Oh snap!</strong> Depot Header Footer Details / Device Admin User is not Configured. Kindly configure and then visit back.
                </div>
    		</div>
		</div>
	</div>
</div>