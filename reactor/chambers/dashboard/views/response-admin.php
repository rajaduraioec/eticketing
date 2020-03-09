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
 * @filesource  dashboard.views.response-admin
 */

 $rand=rand(0,1000);?>
<script type="text/javascript">
    jQuery(document).ready(function($)
    {
        $("#datatable<?=$rand;?>").dataTable({
            "bFilter": false,
            "bSort" : false,
            "bLengthChange": false,
            "pageLength": 5,
            drawCallback: function(){
                $('.simple-ajax-modal').magnificPopup({
                   type: 'ajax',
                   modal: 'true'
                });
             }
        });
    });
</script>
<table class="table table-striped table-bordered table-hover table-full-width datatable table-colored <?=($colourtype=='1'? 'table-primary':'table-success');?>" id="datatable<?=$rand;?>">
    <thead>
        <tr>
            <th><?=$ictdata['idioms']['dashboard_admin_c1'];?></th>
            <th><?=$ictdata['idioms']['dashboard_admin_c5'];?></th>
            <th><?=$ictdata['idioms']['dashboard_admin_c2'];?></th>
            <th><?=$ictdata['idioms']['dashboard_admin_c3'];?></th>
            <th><?=$ictdata['idioms']['dashboard_admin_c4'];?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $amount=0;
        $pass=0;
        $wbcount=0;
        $dvcount=0;
        foreach($results as $row):
        ?>
        <tr>
            <td>
                <a href="<?=$this->rview->url("dashboard/ajdepotstatus/".$row['depotid']);?>" class="simple-ajax-modal">
            <?=$row['depot'];?></a>
            </td>
            <td style="text-align:right;"><?=$row['devicecount'];?></td>
            <td style="text-align:right;"><?=$row['waybills'];?></td>
            <td style="text-align:right;"><?=number_format ( $row['amount'] , 2 , "." , "," );?></td>
            <td style="text-align:right;"><?=$row['passengers'];?></td>
        </tr>
        <?php
        $dvcount+=$row['devicecount'];
        $wbcount+=$row['waybills'];
        $amount+=$row['amount'];
        $pass+=$row['passengers'];
        endforeach;
        ?>
    </tbody>
    <tfoot>
        <tr>
            <td>Total</td>
            <td style="text-align:right;"><?=$dvcount;?></td>
            <td style="text-align:right;"><?=$wbcount;?></td>
            <td style="text-align:right;"><?=number_format ( $amount , 2 , "." , "," );?></td>
            <td style="text-align:right;"><?=$pass;?></td>
        </tr>
    </tfoot>
</table>