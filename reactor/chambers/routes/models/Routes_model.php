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
 * @module      Routes_model
 * @filesource  Routes_model.models
 */

class Routes_model extends R_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->modtable='routes';
        $this->modtableid='id_'.$this->modtable;
        $this->symbolsstages=array('~','`','!','@','#','$','%','^','&','*','(',')','{','}',':','"',';',"'",'<','>','?',',','/','=','+','_','\\');
    }
    /*
     * New Datatable
     */
    function _getdata(){
        $req['database']='default';
        $req['table']=$this->modtable;
        $req['column_order']=array('route_name','route_no',NULL);
        $req['column_search']=array('route_name');
        $req['order']=array('id'=>'asc');
        $result=$this->rdatatable->gen($req);
        $data = array();
        $no = $_POST['start'];
        foreach ($result['data'] as $rowinfo) {
            $no++;
            $row = array();
            $row[] = $rowinfo['route_name'];
            $row[] = $rowinfo['route_no'];
            $row[] = $rowinfo['service_type'];
            $row[] = $rowinfo['total_stages'];
            $row[] = ($rowinfo['active']=='1'?'Active':'Not Active');
            $row[] = '<a href="'.$this->rview->url($this->ctrl_name.'/edit/'.$rowinfo[$this->modtableid]).'" class="simple-ajax-modal btn btn-primary" title="Edit"><i class="fa fa-edit"></i></a>' 
                    .' <a href="'.$this->rview->url($this->ctrl_name.'/faretablemode/'.$rowinfo[$this->modtableid]).'" class="simple-ajax-modal btn btn-success" title="Fare Table"><i class="fa fa-money"></i></a>';
            $data[] = $row;
        }
        $output = array(
                    "draw" => $_POST['draw'],
                    "recordsTotal" => $result['totalcount'],
                    "recordsFiltered" => $result['filteredcount'],
                    "data" => $data,
                );
        return $output;
    }

    function create()
    {
        $stages=str_replace($this->symbolsstages,'',array_filter($this->rinput->post('stages'), 'trim'));
        $stagecount = count($stages);
        $check=$this->exists($this->rinput->post('routeid'));
        if($stagecount==$this->rinput->post('total_stages')&&$stagecount<=200&&count($check)==0){
            $stages = implode('#~#', $stages);
            $data['route_name'] = $this->rinput->post('route-name');
            $data['route_no'] = $this->rinput->post('routeid');
            $data['service_type'] = $this->rinput->post('service_type');
            $data['route_target'] = $this->rinput->post('route_target');
            $data['load_factor'] = $this->rinput->post('load_factor');
            $data['distance'] = $this->rinput->post('distance');
            $data['fare_table_type'] = $this->rinput->post('f_table_type');
            $data['total_stages'] = $this->rinput->post('total_stages');
            $data['stages'] = $stages;
            $data['active'] = 0;
            $data['modifiedon'] = date('Y-m-d H:i:s');
            $this->rdb->create('default',$this->modtable, $data);
            $return['status']=TRUE;
            $return['message']="<h4>Route Created Successfully !!!</h4>";
        }else{
            $error='';
            if(count($check)!=0)
                $error.='-Route ID entered already exist</br>';
            if($stagecount!=$this->rinput->post('total_stages'))
                $error.='-Total Stages not matching stages entered</br>';
            if($stagecount>200)
                $error.='-Total Stages should not be more than 200 Stages</br>';
            if($error=='')
                $error='Something Went Wrong!!!';
            $return['status']=FALSE;
            $return['message']="<h4>$error</h4>";
        }
        return $return;
    }
    function edit($id=''){
        $stages=str_replace($this->symbolsstages,'',array_filter($this->rinput->post('stages'), 'trim'));
        $stagecount = count($stages);
        if($stagecount==$this->rinput->post('total_stages')&&$stagecount<=200){
            $info=$this->getinfo($id);
            $stages = implode('#~#', $stages);
            $data['route_name'] = $this->rinput->post('route-name');
            $data['service_type'] = $this->rinput->post('service_type');
            $data['route_target'] = $this->rinput->post('route_target');
            $data['load_factor'] = $this->rinput->post('load_factor');
            $data['distance'] = $this->rinput->post('distance');
            $data['fare_table_type'] = $this->rinput->post('f_table_type');
            $data['total_stages'] = $this->rinput->post('total_stages');
            $data['stages'] = $stages;
            $data['modifiedon'] = date('Y-m-d H:i:s');
            if($info->total_stages!=$data['total_stages']||$info->fare_table_type!=$data['fare_table_type']||$info->stages!=''||$info->stages!=NULL){
                $data['active'] = 0;
                $data['fare_table'] = NULL;
            }
            $condition[]=$this->rdb->qb('where',$this->modtableid,$id);
            $this->rdb->update('default',$this->modtable, $data,$condition);
            $return['status']=TRUE;
            $return['message']="<h4>Route Updated Successfully !!!</h4>";
        }else{
            $error='';
            if($stagecount!=$this->rinput->post('total_stages'))
                $error.='-Total Stages not matching stages entered</br>';
            if($stagecount>200)
                $error.='-Total Stages should not be more than 200 Stages</br>';
            if($error=='')
                $error='Something Went Wrong!!!';
            $return['status']=FALSE;
            $return['message']="<h4>$error</h4>";
        }
        return $return;
    }
    function getinfo($id='')
    {
        $data[]=$this->rdb->qb('where',$this->modtableid,$id);
        return $this->rdb->fetch('default',$this->modtable,$data,FALSE,TRUE);
    }
    function exists($name='')
    {
        $data[]=$this->rdb->qb('where','route_no',$name);
        return $this->rdb->fetch('default',$this->modtable,$data,TRUE);
    }
   
    function faretable($id='')
    {
        $info=$this->getinfo($id);
        $stagecount=$info->total_stages;
        $tabletype=$info->fare_table_type;
        $rowcount=0;
        $fareinput=$this->rinput->post('fare');
        $fare='';
        if($tabletype==2){
         
        for($j=0;$j<$stagecount;$j++){
            for($i=0;$i<$stagecount;$i++){
                    if($rowcount<=$i){
                        $fare.=$fareinput[$i][$rowcount].'#~#';
                    }
                }
                $rowcount++;
            }   
        }else{
            for($i=0;$i<$stagecount;$i++){
                        $fare.=$fareinput[$i].'#~#';
                }
        }
        $data['fare_table'] = rtrim($fare, "#~#");
        $data['active'] =1;
            $data['modifiedon'] = date('Y-m-d H:i:s');
        $condition[]=$this->rdb->qb('where',$this->modtableid,$id);
        $this->rdb->update('default',$this->modtable, $data,$condition);
        $return['status']=TRUE;
        $return['message']="<h4>Route Updated Successfully !!!</h4>";
        return $return;
    }
    function faretablecsv($id=''){
        $csv_mimetypes = array(
                'text/csv',
                'text/plain',
                'application/csv',
                'text/comma-separated-values',
                'application/excel',
                'application/vnd.ms-excel',
                'application/vnd.msexcel',
                'text/anytext',
                'application/octet-stream',
                'application/txt',
            );

            if (in_array($_FILES['faretable']['type'], $csv_mimetypes)) {
                $info=$this->getinfo($id);
                $stagecount=$info->total_stages;
                $tabletype=$info->fare_table_type;
                $filecolumncheck='';
                $filerowcheck='';
                $fileisnumbercheck='';
                $filenumbergreatercheck='';
                $handle = fopen($_FILES['faretable']['tmp_name'], "r");
                $faredata=array();
                $i=0;
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if($tabletype==2){
                    if(count($data)!=$stagecount+1)
                        $filecolumncheck=1;
                    }else{
                    if(count($data)!=2)
                        $filecolumncheck=1;
                    }
                    $faredata[$i]=$data;
                    $i+=1;
                }
                if($i!=$stagecount+1)
                    $filerowcheck=1;
                fclose($handle);
                $fare='';
                $rowcount=1;
                $validate=1;
                if($tabletype==2){
                    for($j=1;$j<=$stagecount;$j++){
                        for($i=1;$i<=$stagecount;$i++){
                            if($rowcount<=$i){
                                $famt=$faredata[$i][$rowcount];
                                if(is_float($famt)){
                                    $fileisnumbercheck=1;
                                    if($famt>=100000)
                                        $filenumbergreatercheck=1;
                                }
                                $fare.=$faredata[$i][$rowcount].'#~#';
                            }
                        }
                        $rowcount++;
                    }   
                }else{
                    for($i=1;$i<=$stagecount;$i++){
                        $fare.=$faredata[1][$i].'#~#';
                    }
                }
                if($filecolumncheck==''&&$filenumbergreatercheck==''&&$filerowcheck==''&&$fileisnumbercheck==''){
                    
                    $data['fare_table'] =rtrim($fare, "#~#");
                    $data['active'] =1;
            $data['modifiedon'] = date('Y-m-d H:i:s');
                    $condition[]=$this->rdb->qb('where',$this->modtableid,$id);
                    $this->rdb->update('default',$this->modtable, $data,$condition);
                    $result['status']=TRUE;
                    $result['message']='Updated Successfully';
                }else{
                    $error='';
                    if($filecolumncheck!='')
                        $error.="-Columns Dosen't match please use prescribed format to upload</br>";
                    if($filerowcheck!='')
                        $error.="-Rows Dosen't match please use prescribed format to upload</br>";
                    if($fileisnumbercheck!='')
                        $error.="-Come column dosen't have valid number please check</br>";
                    if($filenumbergreatercheck!='')
                        $error.="-Allowed fare value is less than 100000</br>";
                    $result['status']=FALSE;
                    $result['message']=$error;
                    
                }
            }else{
                $result['status']=FALSE;
                $result['content']='Uploaded file is not CSV file';
            }
            return $result;
            
    }
    function faretablecsvdata($id=''){
        
        $info=$this->getinfo($id);
        $stages=explode("#~#", $info->stages);
        $stagecount=$info->total_stages;
        $response['routename']=$info->route_no;
        $tabletype=$info->fare_table_type;
        if($info->fare_table==''||$info->fare_table==NULL){
            $farecheck=0;
        }else{
            $farecheck=1;
            $fares=explode('#~#', $info->fare_table);
            if($tabletype==2){
                $farecount=0;
                $rowcount=0;
                for($j=0;$j<$stagecount;$j++){
                    for($i=0;$i<$stagecount;$i++){
                        if($rowcount<=$i){
                            $fareinput[$i][$rowcount]=$fares[$farecount];
                            $farecount++;
                        }
                    }
                    $rowcount++;
                }  
            }
        }
        $data[]=array();
        if($tabletype==2){
            $data[0][0]='Stage';
            $count=1;
            foreach ($stages as $stage){
                $data[0][$count]=$stage;
                $count+=1;
            }
            $rowcount=0;
            $columncount=0;
            $count=1;
            foreach($stages as $stage){
                $data[$count][]=$stage;
                for($i=0;$i<$stagecount;$i++){
                    if($i==$rowcount){
                        $data[$count][]=0;
                    }elseif($i>$rowcount){
                        $data[$count][]=0;
                    }else{
                        if($farecheck==1){
                            $fare=$fareinput[$rowcount][$i];
                        }else{
                            $fare='';
                        }
                        $data[$count][]=$fare;
                    }
                }
                $rowcount++;
                $count+=1;
            }

        }else{
            $data[0][]='Stages';
            $data[0][]='Rate';
            $rowcount=0;
            $farecount=0;
            $count=1;
            foreach($stages as $stage){
                $data[$count][]=$stage;
                if($rowcount==0)
                    $data[$count][]=0;
                else{
                    if($farecheck==1){
                        $data[$count][]=$fares[$farecount];
                    }else{
                        $data[$count][]='';
                    }
                }
                $farecount++;
                $rowcount++;
                $count++;
            }
        }
        $response['data']=$data;
        return $response;
    }
}