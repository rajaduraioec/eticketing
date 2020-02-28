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
 * @filesource  dashboard.views.response-depot
 */

 $rand=rand(0,1000);?>
<script type="text/javascript">
    jQuery(document).ready(function($)
    {
        $("#datatable<?=$rand;?>").dataTable({
            "bFilter": false,
            "bSort" : false,
            "bLengthChange": false,
            "pageLength": 5
        });
    });
</script>
<table class="table table-striped table-bordered table-hover table-full-width datatable table-colored <?=($colourtype=='1'? 'table-primary':'table-success');?>" id="datatable<?=$rand;?>">
    <thead>
        <tr>
            <th><?=$ictdata['idioms']['dashboard_depot_c1'];?></th>
            <th><?=$ictdata['idioms']['dashboard_depot_c2'];?></th>
            <th><?=$ictdata['idioms']['dashboard_depot_c3'];?></th>
            <th><?=$ictdata['idioms']['dashboard_depot_c4'];?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $pass=0;
        $collection=0;
        foreach($results as $row):
        ?>
        <tr>
            <td><?=$row['busno'];?></td>
            <td><?=$row['routeno'];?></td>
            <td style="text-align:right;"><?=$row['passengers'];?></td>
            <td style="text-align:right;"><?=number_format ( $row['collections'] , 2 , "." , "," );?></td>
        </tr>
        <?php
        $pass+=$row['passengers'];
        $collection+=$row['collections'];
        endforeach;
        ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2">Total</td>
            <td style="text-align:right;"><?=$pass;?></td>
            <td style="text-align:right;"><?=number_format ( $collection , 2 , "." , "," );?></td>
        </tr>
    </tfoot>
</table>