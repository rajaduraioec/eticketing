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

class Rdb
{

    public function __construct()
    {
    }
 
    public function __get($var)
    {
        return get_instance()->$var;
    }
   
    public function fetch($db='',$table='',$data=array(),$isarray=TRUE,$isrow=FALSE){
        $dbinst=$this->load($db, TRUE);
        $dbinst->from($table);
        foreach($data as $row):
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
        if($isarray){
            return $dbinst->get()->result_array();
        }elseif($isrow){
            return $dbinst->get()->row();
        }else{
            return $dbinst->get();
            
        }
    }   
    public function qb($type='',$param1='blah',$param2='blah',$param3='blah',$param4='blah'){
        $response['type']=$type;
        if($param1!='blah'){
           $response['param1']=$param1;
           $response['params']=1;
        }
        if($param2!='blah'){
           $response['param2']=$param2;
           $response['params']=2;
        }
        if($param3!='blah'){
           $response['param3']=$param3;
           $response['params']=3;
        }
        if($param4!='blah'){
           $response['param4']=$param4;
           $response['params']=4;
        }
        return $response;
    }   
    public function create($db='default',$table='',$data=array()){
        $dbinst=$this->load($db, TRUE);
        $dbinst->insert($table,$data);
        return $dbinst->insert_id();
    }   
    public function batchcreate($db='default',$table='',$data=array()){
        $dbinst=$this->load($db, TRUE);
        return $dbinst->insert_batch($table,$data);
    }   
//    public function replace($table='',$data=array()){
//        return $this->db->insert_batch($table,$data);
//    }   
    public function update($db='default',$table='',$data=array(),$condition=array()){
        $dbinst=$this->load($db, TRUE);
        foreach($condition as $row):
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
        return $dbinst->update($table,$data);
    }   
    public function batchupdate($table='',$data=array(),$field=''){
        return $this->db->update($table,$data,$field)->result_array();
    }   
    public function trash($db='default',$table='',$data=array()){
        $dbinst=$this->load($db, TRUE);
        foreach($data as $row):
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
        return $dbinst->delete($table);
    }   
    public function load($db='default'){
        return $this->load->database($db, TRUE);
    }   
}