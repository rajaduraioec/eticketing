<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/* 
 * 
 * @package	MMT Transport E-Ticketing
 * @Framework	Reactor Framework
 * @author      Increatech Dev Team
 * @copyright	Copyright (c) Since 2017, Increatech Business Solution Pvt Ltd, India (http://increatech.com/)
 * @link        https://increatech.com
 * @since       Version 1.0.0
 * @module      Users
 * @filesource  users.views.permission
 */

?>
<div class="modal-block modal-block-md modal-ajaxcontent-block">
    <div class="panel panel-color panel-info">
        <div class="panel-heading">
            <button type="button" class="close panel-action modal-dismiss pull-right" aria-hidden="true"><i class="fa fa-close"></i></button>
            <h3 class="panel-title"><?=$ictdata['idioms']['users_permission_title1'].' '.$info->username.' '.$ictdata['idioms']['users_permission_title2'];?> </h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <form id="modal-form" class="form-horizontal  mb-lg">
                        <div class="form-group">
                            
                            <table class="table table-space m-0">

                                <thead>
                                <tr>
                                    <th><?=$ictdata['idioms']['users_permission_c1'];?></th>
                                    <th><?=$ictdata['idioms']['users_permission_c2'];?></th>
                                    <th><?=$ictdata['idioms']['users_permission_c3'];?></th>
                                    <th><?=$ictdata['idioms']['users_permission_c4'];?></th>
                                    <th><?=$ictdata['idioms']['users_permission_c5'];?></th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach($permitsinfo as $permit):
                                        $pid=$permit['id_ict_permission'];
                                        echo '<tr><td>'.$permit['name'].'</td><td>';
                                        if($this->rauth->permit_accesslevel($permit['permit_level'], 'create'))
                                            echo '<input type="checkbox" name="permit['.$pid.'][c]" checked data-plugin="switchery" data-color="#039cfd"  data-size="small"/>';
                                        else
                                            echo '<input type="checkbox" name="permit['.$pid.'][c]" data-plugin="switchery" data-color="#039cfd"  data-size="small"/>';
                                        echo '</td><td>';
                                        if($this->rauth->permit_accesslevel($permit['permit_level'], 'read'))
                                            echo '<input type="checkbox" name="permit['.$pid.'][r]" checked data-plugin="switchery" data-color="#039cfd"  data-size="small"/>';
                                        else
                                            echo '<input type="checkbox" name="permit['.$pid.'][r]" data-plugin="switchery" data-color="#039cfd"  data-size="small"/>';
                                        echo '</td><td>';
                                        if($this->rauth->permit_accesslevel($permit['permit_level'], 'update'))
                                            echo '<input type="checkbox" name="permit['.$pid.'][u]" checked data-plugin="switchery" data-color="#039cfd"  data-size="small"/>';
                                        else
                                            echo '<input type="checkbox" name="permit['.$pid.'][u]" data-plugin="switchery" data-color="#039cfd"  data-size="small"/>';
                                        echo '</td><td>';
                                        if($this->rauth->permit_accesslevel($permit['permit_level'], 'delete'))
                                            echo '<input type="checkbox" name="permit['.$pid.'][d]" checked data-plugin="switchery" data-color="#039cfd"  data-size="small"/>';
                                        else
                                            echo '<input type="checkbox" name="permit['.$pid.'][d]" data-plugin="switchery" data-color="#039cfd"  data-size="small"/>';
                                        echo '</td><tr>';
                                    endforeach;
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group mt-lg" align="center">
                            <button id='modal-submit' onclick="validateme('<?=$this->rview->url($ctrl_name.'/permissionsave/'.$id);?>','modal-form')" class="btn btn-primary"><?=$ictdata['idioms']['submitbtn'];?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="row">
                <div class="col-md-12 text-right">
                    <button class="btn btn-default modal-dismiss"><?=$ictdata['idioms']['x'];?></button>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $(document).ready(function($) {
                $('[data-plugin="switchery"]').each(function (idx, obj) {
                    new Switchery($(this)[0], $(this).data());
                });
            });
        </script>
    </div>
</div>