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
 * @module      Headerfooter_model
 * @filesource  Headerfooter_model.models
 */
 
class Headerfooter_model extends R_Model {
	
	public function __construct()
    {
        parent::__construct();
        $this->modtable='headerfooters';
        $this->modtableid='id_'.$this->modtable;
    }
    
    function _getdata(){
        $req['database']='default';
        $req['table']=$this->modtable;
        $req['column_order']=array('depot_name','active',NULL);
        $req['column_search']=array('depot_name');
        $req['order']=array('id'=>'asc');
        $isedit=$this->rauth->has_accesslevel($this->module_name,'update');
        $req['query'][]=$this->rdb->qb('join','depots','depots.id_depots=headerfooters.depots_id');
        $result=$this->rdatatable->gen($req);
        $data = array();
        $no = $_POST['start'];
        foreach ($result['data'] as $rowinfo) {
            $no++;
            $row = array();
            $row[] = $rowinfo['depot_name'];
            $row[] = $rowinfo['h1'];
            $row[] = $rowinfo['h2'];
            $row[] = $rowinfo['f'];
            if($isedit)
                $row[] = '<a href="'.$this->rview->url($this->ctrl_name.'/edit/'.$rowinfo[$this->modtableid]).'" class="simple-ajax-modal btn btn-primary" title="Edit"><i class="fa fa-edit"></i></a>';
            else
            $row[]='';
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
        $data=array(
          'depots_id' => $this->rinput->post('depot-name'),
          'h1' => $this->rinput->post('headerline1'),
          'h2' => $this->rinput->post('headerline2'),
          'f' => $this->rinput->post('footer')
        );
        $this->rdb->create('default',$this->modtable, $data);
        $return['status']=TRUE;
        $return['message']="<h4>Header-Footer Successfully Created !!!</h4>";
	        $query[]=$this->rdb->qb('where','depots_id',$data['depots_id']);
	        if(count($this->rdb->fetch('default','deviceadmins',$query))==1){
	        	unset($query);
	        	$ddata['config_status'] = 1;
	    		$condition[]=$this->rdb->qb('where','id_depots',$data['depots_id']);
	        	$this->rdb->update('default','depots', $ddata,$condition);
	        }
        return $return;
    }
	
	function get_depots()
	{
		$data[]=$this->rdb->qb('join',$this->modtable,"depots.id_depots=$this->modtable.depots_id",'left');
		$data[]=$this->rdb->qb('where','h1',NULL);
        return $this->rdb->fetch('default','depots',$data);
	}
	
	function edit($id=''){
        $data=array(
          'h1' => $this->rinput->post('headerline1'),
          'h2' => $this->rinput->post('headerline2'),
          'f' => $this->rinput->post('footer')
        );
        $condition[]=$this->rdb->qb('where',$this->modtableid,$id);
        $this->rdb->update('default',$this->modtable, $data,$condition);
        unset($data);
        unset($condition);
        $return['status']=TRUE;
        $return['message']="Updated Sucessfully";
        return $return;
    }
    
    function getinfo($id='')
    {
		$data[]=$this->rdb->qb('join','depots',"$this->modtable.depots_id=depots.id_depots");
        $data[]=$this->rdb->qb('where',$this->modtableid,$id);
        return $this->rdb->fetch('default',$this->modtable,$data,FALSE,TRUE);
    }
    
}
?>