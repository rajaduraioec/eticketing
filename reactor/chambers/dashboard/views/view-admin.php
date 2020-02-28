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
 * @module      Dashboard
 * @filesource  dashboard.views.view-admin
 */

?>
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title"><?=$ictdata['idioms']['dashboard_module_title'];?></h4>
        </div>
    </div>
</div>
<div class="row">
<div class="col-md-6">
    <div class="panel panel-border panel-primary">
        <div class="panel-heading">
            <div class="btn-group pull-right" id="lvrefreshtime">
            </div>
            <h3 class="panel-title"><?=$ictdata['idioms']['dashboard_live_title'];?></h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <script type="text/javascript">
                    var rdtinit=true;
                </script>
                <div class="col-md-12" id="livedata">
                    <table class="table table-striped table-bordered table-hover table-full-width" id="datatable">
                        <thead>
                            <tr>
                                <th><?=$ictdata['idioms']['dashboard_admin_c1'];?></th>
                                <th><?=$ictdata['idioms']['dashboard_admin_c2'];?></th>
                                <th><?=$ictdata['idioms']['dashboard_admin_c3'];?></th>
                                <th><?=$ictdata['idioms']['dashboard_admin_c4'];?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-6">
    <div class="panel panel-border panel-success">
        <div class="panel-heading">
            <div class="btn-group pull-right" id="larefreshtime">
            </div>
            <h3 class="panel-title"><?=$ictdata['idioms']['dashboard_last_title'];?></h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <script type="text/javascript">
                    var rdtinit=true;
                </script>
                <div class="col-md-12" id="lastdata">
                    <table class="table table-striped table-bordered table-hover table-full-width" id="datatable">
                        <thead>
                            <tr>
                                <th><?=$ictdata['idioms']['dashboard_admin_c1'];?></th>
                                <th><?=$ictdata['idioms']['dashboard_admin_c2'];?></th>
                                <th><?=$ictdata['idioms']['dashboard_admin_c3'];?></th>
                                <th><?=$ictdata['idioms']['dashboard_admin_c4'];?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script type="text/javascript">
    var baseurl='<?=$this->rview->url('dashboard');?>';
</script>
<script src="<?=$this->rview->init('jquery.ict.dashboard.init');?>"></script>

