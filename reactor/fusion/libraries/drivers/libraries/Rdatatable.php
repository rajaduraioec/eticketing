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

class Rdatatable
{

    public function __construct()
    {
    }
 
    public function __get($var)
    {
        return get_instance()->$var;
    }
    function gen($req=array()){
        $dbinst=$this->rdb->load($req['database'], TRUE);
        $dbinst->from($req['table']);
        $i = 0;
        $column_search_l=count($req['column_search'])-1;// Column Search Last loop
        foreach ($req['column_search'] as $item) // loop column 
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {
                if($i===0) // first loop
                {
                    $dbinst->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $dbinst->like($item, $_POST['search']['value']);
                }
                else
                {
                    $dbinst->or_like($item, $_POST['search']['value']);
                }
                if($column_search_l == $i) //last loop
                    $dbinst->group_end(); //close bracket
            }
            $i++;
        }
        //http://mbahcoding.com/tutorial/php/codeigniter/codeigniter-simple-server-side-datatable-example.html
        if(isset($_POST['order'])) // here order processing
        {
            $dbinst->order_by($req['column_order'][$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } 
        else if(isset($req['column_order']))
        {
            $order = $req['column_order'];
            $dbinst->order_by(key($order), $order[key($order)]);
        }
        if($_POST['length'] != -1)
            $dbinst->limit($_POST['length'], $_POST['start']);
        if(isset($req['query']))
        foreach($req['query'] as $row):
                $type=$row['type'];
            switch ($row['params']):
            case '1':
                $dbinst->$type($row['param1']);
                break;
            case '2':
                $dbinst->$type($row['param1'],$row['param2']);
                break;
            case '3':
                $dbinst->$type($row['param1'],$row['param2'],$row['param3']);
                break;
            case '4':
                $dbinst->$type($row['param1'],$row['param2'],$row['param3'],$row['param4']);
                break;
            endswitch;
        endforeach;
        $response['data'] = $dbinst->get()->result_array();
        $response['totalcount'] = count($response['data']);
        $response['filteredcount'] = $dbinst->count_all_results($req['table']);
        return $response;
    }   
}