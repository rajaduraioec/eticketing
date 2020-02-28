<?php

/* 
 * 
 * @package	MMT Transport E-Ticketing
 * @Framework	Reactor Framework
 * @author         Increatech Dev Team
 * @copyright	Copyright (c) Since 2017, Increatech Business Solution Pvt Ltd, India (http://increatech.com/)
 * @link           https://increatech.com
 * @since          Version 1.0.0
 * @module      Reportstagewise
 * @filesource
 */



defined('RAPPVERSION') or exit('No direct script access allowed');

/* 
 * Copyright 2017 Increatech Business Solution Pvt Ltd, India.
 * All Rights Reserved
 * www.increatech.com
 * info@increatech.com
 */

class Preports
{
    
    public function __construct()
    {
    }
 
    public function __get($var)
    {
        return get_instance()->$var;
    }
    
    public function genpassesreport($date,$depot){
        $from=date('Y-m-d 00:00:00',  strtotime($date[0]));
        $to=date('Y-m-d 23:59:59',  strtotime($date[1]));
        $reports=array();
        $devices=$this->_getdevices($depot);
        $count=0;
        $routeid='';
        $configid='';
        $query[]=$this->rdb->qb('order_by',"id_passes",'asc');
        $passes=$this->rdb->fetch('default','passes',$query);
        unset($query);
        $passcount=count($passes);
        foreach($devices as $device):
            $query[]=$this->rdb->qb('where',"(wbopentimestamp BETWEEN '$from' AND '$to' OR wbclosedrectimestamp IS NULL)",NULL,FALSE);
            $query[]=$this->rdb->qb('where',"wb_devices_id",$device['id_devices']);
            $waybills=$this->rdb->fetch('data','waybilldetails',$query);
            unset($query);
            if(count($waybills)!=0):
                foreach($waybills as $wb):
                    $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                    $trips=$this->rdb->fetch('data','tripdetails',$query);
                    unset($query);
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $query[]=$this->rdb->qb('select',"buses_id");
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_buses",$configinfo->buses_id);
                        $query[]=$this->rdb->qb('select',"bus_no");
                        $busno=$this->rdb->fetch('default','buses',$query,FALSE, TRUE)->bus_no;
                        unset($query);
                    endif;
                    $passcheck=0;
                    $passdata=array();
                    for($i=0;$i<=$passcount;$i++)
                        $passdata[$i]=0;
                    foreach($trips as $trip):
                        if($routeid!=$trip['deviceroutes_id']):
                            $query[]=$this->rdb->qb('where',"id_deviceroutes",$trip['deviceroutes_id']);
                            $query[]=$this->rdb->qb('select',"route_name,stages");
                            $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                            unset($query);
                            $routeid=$trip['deviceroutes_id'];
                            $stages=explode('#~#',$routeinfo->stages);
                        endif;
                        $query[]=$this->rdb->qb('order_by',"ticket_no",'asc');
                        $query[]=$this->rdb->qb('where','ticket_type','2');
                        $query[]=$this->rdb->qb('where',"tripdetails_id",$trip['id_tripdetails']);
                        $tktdetails=$this->rdb->fetch('data','ticketdetails',$query);
                        unset($query);
                        foreach($tktdetails as $tktdetail):
                            if($tktdetail['ticket_type']==2):
                                $passcheck=1;
                                $passdata[$tktdetail['pass_type']]+=$tktdetail['qty'];
                            endif;
                        endforeach;
                    endforeach;
                    
                    if($passcheck==1):
                        $reports[$count]['uid']=$device['uid'];
                        $reports[$count]['depot']=$device['depot_name'];
                        $reports[$count]['date']=$wb['wbopentimestamp'];
                        $reports[$count]['pass']=$passdata;
                        $reports[$count]['busno']=$busno;
                        $reports[$count]['wbn']=$wb['way_bill_no'];
                        $reports[$count]['routeno']=$routeinfo->route_name;
                        $count+=1;
                    endif;
                endforeach;
            endif;
            
        endforeach;
        return $reports;
    }
    
    
    public function geninspectionreport($date,$depot,$inspector,$bus,$route){
        $from=date('Y-m-d 00:00:00',  strtotime($date[0]));
        $to=date('Y-m-d 23:59:59',  strtotime($date[1]));
        $reports=array();
        $devices=$this->_getdevices($depot);
        $count=0;
        $routeid='';
        $configid='';
        foreach($devices as $device):
            $query[]=$this->rdb->qb('where',"(wbopentimestamp BETWEEN '$from' AND '$to' OR wbclosedrectimestamp IS NULL)",NULL,FALSE);
            $query[]=$this->rdb->qb('where',"wb_devices_id",$device['id_devices']);
            $waybills=$this->rdb->fetch('data','waybilldetails',$query);
            unset($query);
            if(count($waybills)!=0):
                foreach($waybills as $wb):
                    $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                    $trips=$this->rdb->fetch('data','tripdetails',$query);
                    unset($query);
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_buses",$configinfo->buses_id);
                        $query[]=$this->rdb->qb('select',"bus_no");
                        $busno=$this->rdb->fetch('default','buses',$query,FALSE, TRUE)->bus_no;
                        unset($query);
                    endif;
                    if($bus==NULL || $bus==$configinfo->buses_id):
                        foreach($trips as $trip):
                            if($routeid!=$trip['deviceroutes_id']):
                                $query[]=$this->rdb->qb('where',"id_deviceroutes",$trip['deviceroutes_id']);
                                $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                                unset($query);
                                $routeid=$trip['deviceroutes_id'];
                            endif;
                            if($route==NULL || $route==$routeinfo->routes_id):
                                $query[]=$this->rdb->qb('where',"i_tripdetails_id",$trip['id_tripdetails']);
                                $inspections=$this->rdb->fetch('data','inspectiondetails',$query);
                                unset($query);
                                if(count($inspections)!=0):
                                    foreach($inspections as $inspection):
                                        if($inspector==NULL || $inspector==$inspection['inspectors_id']):
                                            $reports[$count]['busno']=$busno;
                                            $reports[$count]['depot']=$device['depot_name'];
                                            $reports[$count]['routeno']=$routeinfo->route_name;
                                            $reports[$count]['wbn']=$wb['way_bill_no'];
                                            $reports[$count]['uid']=$device['uid'];
                                            $reports[$count]['tripno']=$trip['tripno'];
                                            $reports[$count]['date']=$inspection['i_timestamp'];
                                            $reports[$count]['insid']=$inspection['ins_id_no'];
                                            $reports[$count]['remarks']=$inspection['remarks'];
                                            $count+=1;
                                        endif;
                                    endforeach;
                                endif;
                            endif;
                        endforeach;
                    endif;
                endforeach;
            endif;
        endforeach;
        return $reports;
    }
    public function gendriverwisereport($date,$depot,$driver){
        $from=date('Y-m-d 00:00:00',  strtotime($date[0]));
        $to=date('Y-m-d 23:59:59',  strtotime($date[1]));
        $reports=array();
        $darray=array();
        $devices=$this->_getdevices($depot);
        $count=0;
        $routeid='';
        $configid='';
        $query[]=$this->rdb->qb('order_by','driver_name','asc');
        $dresults= $this->rdb->fetch('default','drivers',$query);
        unset($query);
        foreach($dresults as $drow):
            $darray[$drow['id_drivers']]['name']=$drow['driver_name'];
            $darray[$drow['id_drivers']]['driver_id']=$drow['driver_id'];
            $darray[$drow['id_drivers']]['km']=0;
            $darray[$drow['id_drivers']]['accident']=0;
            $darray[$drow['id_drivers']]['breakdown']=0;
            $darray[$drow['id_drivers']]['check']=0;
        endforeach;
        $initodometer=NULL;
        $endodometer=NULL;
        foreach($devices as $device):
            $query[]=$this->rdb->qb('where',"(wbopentimestamp BETWEEN '$from' AND '$to' OR wbclosedrectimestamp IS NULL)",NULL,FALSE);
            $query[]=$this->rdb->qb('where',"wb_devices_id",$device['id_devices']);
            $query[]=$this->rdb->qb('order_by',"wb_deviceconfig_id",'ASC');
            $waybills=$this->rdb->fetch('data','waybilldetails',$query);
            unset($query);
            if(count($waybills)!=0):
                foreach($waybills as $wb):
                    if($configid!=$wb['wb_deviceconfig_id']):
                        if($configid!=''):
                            if($initodometer!=NULL||$initodometer!=0||$initodometer!=0.00):
                                $darray[$did]['km']+=$initodometer-$endodometer;
                            endif;
                        endif;
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $initodometer=$configinfo->odometer;
                        $query[]=$this->rdb->qb('where',"id_devicedrivers",$configinfo->drivers_id);
                        $driverinfo=$this->rdb->fetch('config','devicedrivers',$query,FALSE, TRUE);
                        unset($query);
                    endif;
                    if($driver==NULL || $driver==$driverinfo->drivers_id):
                        $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                        $query[]=$this->rdb->qb('order_by',"tripno",'ASC');
                        $trips=$this->rdb->fetch('data','tripdetails',$query);
                        unset($query);
                        $did=$driverinfo->drivers_id;
                        $darray[$did]['check']=1;
                        foreach($trips as $trip):
                            $endodometer=$trip['odometer'];
                            $darray[$did]['accident']+=$trip['is_accident'];
                            $darray[$did]['breakdown']+=$trip['is_breakdown'];
                        endforeach;
                    endif;
                endforeach;
            endif;
            
        endforeach;
        
        foreach($darray as $drowid=>$drow):
            if($drow['check']==1):
                $reports[$count]['id_drivers']=$drowid;
                $reports[$count]['name']=$drow['name'];
                $reports[$count]['driver_id']=$drow['driver_id'];
                $reports[$count]['km']=$drow['km'];
                $reports[$count]['accident']=$drow['accident'];
                $reports[$count]['breakdown']=$drow['breakdown'];
                $count+=1;
            endif;
        endforeach;
        return $reports;
    }
    public function genconductorwisereport($date,$depot,$conductor){//Depot null to fetch all device details
        $from=date('Y-m-d 00:00:00',  strtotime($date[0]));
        $to=date('Y-m-d 23:59:59',  strtotime($date[1]));
        $reports=array();
        $carray=array();
        $devices=$this->_getdevices($depot);
        $count=0;
        $routeid='';
        $configid='';
        $query[]=$this->rdb->qb('order_by','conductor_name','asc');
        $cresults= $this->rdb->fetch('default','conductors',$query);
        unset($query);
        foreach($cresults as $crow):
            $carray[$crow['id_conductors']]['name']=$crow['conductor_name'];
            $carray[$crow['id_conductors']]['conductor_id']=$crow['conductor_id'];
            $carray[$crow['id_conductors']]['passenger']=0;
            $carray[$crow['id_conductors']]['luggages']=0;
            $carray[$crow['id_conductors']]['lugamt']=0;
            $carray[$crow['id_conductors']]['pass']=0;
            $carray[$crow['id_conductors']]['amount']=0;
            $carray[$crow['id_conductors']]['check']=0;
        endforeach;
        foreach($devices as $device):
            $query[]=$this->rdb->qb('where',"(wbopentimestamp BETWEEN '$from' AND '$to' OR wbclosedrectimestamp IS NULL)",NULL,FALSE);
            $query[]=$this->rdb->qb('where',"wb_devices_id",$device['id_devices']);
            $query[]=$this->rdb->qb('order_by',"wb_deviceconfig_id",'ASC');
            $waybills=$this->rdb->fetch('data','waybilldetails',$query);
            unset($query);
            if(count($waybills)!=0):
                foreach($waybills as $wb):
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_deviceconductors",$configinfo->conductors_id);
                        $conductorinfo=$this->rdb->fetch('config','deviceconductors',$query,FALSE, TRUE);
                        unset($query);
                    endif;
                    if($conductor==NULL || $conductor==$conductorinfo->conductors_id):
                        $cid=$conductorinfo->conductors_id;
                        $carray[$cid]['check']=1;
                        if($wb['is_wb_closed']==1):
                            $carray[$cid]['passenger']+=$wb['wb_total_tickets'];
                            $carray[$cid]['pass']+=$wb['wb_total_passes'];
                            $carray[$cid]['luggages']+=$wb['wb_total_luggages'];
                            $carray[$cid]['lugamt']+=$wb['wb_total_luggages'];
                            $carray[$cid]['amount']+=$wb['wb_tickets_amount']+$wb['wb_passes_amount'];
                        else:
                            $tdetails=$this->_getwbinfobyid($wb['id_waybilldetails']);
                            $carray[$cid]['passenger']+=$tdetails['tickets'];
                            $carray[$cid]['pass']+=$tdetails['passes'];
                            $carray[$cid]['luggages']+=$tdetails['luggages'];
                            $carray[$cid]['lugamt']+=$tdetails['lugamt'];
                            $carray[$cid]['amount']+=$tdetails['ticketsamt']+$tdetails['passesamt'];
                        endif;
                    endif;
                endforeach;
            endif;
        endforeach;
        foreach($carray as $crowid=>$crow):
            if($crow['check']==1):
                $reports[$count]['id_conductors']=$crowid;
                $reports[$count]['name']=$crow['name'];
                $reports[$count]['conductor_id']=$crow['conductor_id'];
                $reports[$count]['passenger']=$crow['passenger'];
                $reports[$count]['pass']=$crow['pass'];
                $reports[$count]['luggages']=$crow['luggages'];
                $reports[$count]['lugamt']=$crow['lugamt'];
                $reports[$count]['amount']=$crow['amount'];
                $count+=1;
            endif;
        endforeach;
        return $reports;
    }
    /*
     * Old
     */
    public function gendaywisereport($date,$depot){//Depot null to fetch all device details
        $from=date('Y-m-d 00:00:00',  strtotime($date[0]));
        $to=date('Y-m-d 23:59:59',  strtotime($date[1]));
        $reports=array();
        $devices=$this->_getdevices($depot);
        $count=0;
        $routeid='';
        $configid='';
        foreach($devices as $device):
            $query[]=$this->rdb->qb('where',"(wbopentimestamp BETWEEN '$from' AND '$to' OR wbclosedrectimestamp IS NULL)",NULL,FALSE);
            $query[]=$this->rdb->qb('where',"wb_devices_id",$device['id_devices']);
            $waybills=$this->rdb->fetch('data','waybilldetails',$query);
            unset($query);
            if(count($waybills)!=0):
                foreach($waybills as $wb):
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $query[]=$this->rdb->qb('select',"buses_id");
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_buses",$configinfo->buses_id);
                        $query[]=$this->rdb->qb('select',"bus_no");
                        $busno=$this->rdb->fetch('default','buses',$query,FALSE, TRUE)->bus_no;
                        unset($query);
                    endif;
                    $query[]=$this->rdb->qb('where',"tripno",1);
                    $query[]=$this->rdb->qb('select',"deviceroutes_id");
                    $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                    $tripinfo=$this->rdb->fetch('data','tripdetails',$query,FALSE, TRUE);
                    unset($query);
                    if($routeid!=$tripinfo->deviceroutes_id):
                        $query[]=$this->rdb->qb('where',"id_deviceroutes",$tripinfo->deviceroutes_id);
                        $query[]=$this->rdb->qb('select',"route_name");
                        $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                        unset($query);
                    endif;
                if($wb['is_wb_closed']==1):
                    $reports[$count]['busno']=$busno;
                    $reports[$count]['depot']=$device['depot_name'];
                    $reports[$count]['routeno']=$routeinfo->route_name;
                    $reports[$count]['wbopening']=$wb['wbopentimestamp'];
                    $reports[$count]['wbn']=$wb['way_bill_no'];
                    $reports[$count]['wbclosed']=1;
                    $reports[$count]['uid']=$device['uid'];
                    $reports[$count]['trips']=$wb['total_trips'];
                    $reports[$count]['tickets']=$wb['wb_total_tickets'];
                    $reports[$count]['passes']=$wb['wb_total_passes'];
                    $reports[$count]['ticketamt']=$wb['wb_tickets_amount'];
                    $reports[$count]['passamt']=$wb['wb_passes_amount'];
                    $reports[$count]['lugamt']=$wb['wb_luggages_amount'];
                    $reports[$count]['expensesamt']=$wb['wb_expenses_amount'];
                    $reports[$count]['cardamt']=$wb['wb_card_amount'];
                    $reports[$count]['cashamt']=$wb['wb_cash_amount'];
                else:
                    $tdetails=$this->_getwbinfobyid($wb['id_waybilldetails']);
                    $reports[$count]['busno']=$busno;
                    $reports[$count]['depot']=$device['depot_name'];
                    $reports[$count]['routeno']=$routeinfo->route_name;
                    $reports[$count]['wbopening']=$wb['wbopentimestamp'];
                    $reports[$count]['wbn']=$wb['way_bill_no'];
                    $reports[$count]['wbclosed']=0;
                    $reports[$count]['uid']=$device['uid'];
                    $reports[$count]['trips']=$tdetails['trips'];
                    $reports[$count]['tickets']=$tdetails['tickets'];
                    $reports[$count]['passes']=$tdetails['passes'];
                    $reports[$count]['ticketamt']=$tdetails['ticketsamt'];
                    $reports[$count]['passamt']=$tdetails['passesamt'];
                    $reports[$count]['lugamt']=$wb['wb_luggages_amount'];
                    $reports[$count]['expensesamt']=$tdetails['expenses'];
                    $reports[$count]['cardamt']=$tdetails['card'];
                    $reports[$count]['cashamt']=$tdetails['cash'];
                endif;
                $count+=1;
                endforeach;
            endif;
        endforeach;
        return $reports;
    }
    public function gentripwisereport($date,$depot){
        $from=date('Y-m-d 00:00:00',  strtotime($date[0]));
        $to=date('Y-m-d 23:59:59',  strtotime($date[1]));
        $reports=array();
        $devices=$this->_getdevices($depot);
        $count=0;
        $routeid='';
        $configid='';
        foreach($devices as $device):
            $query[]=$this->rdb->qb('where',"(wbopentimestamp BETWEEN '$from' AND '$to' OR wbclosedrectimestamp IS NULL)",NULL,FALSE);
            $query[]=$this->rdb->qb('where',"wb_devices_id",$device['id_devices']);
            $waybills=$this->rdb->fetch('data','waybilldetails',$query);
            unset($query);
            if(count($waybills)!=0):
                foreach($waybills as $wb):
                    $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                    $trips=$this->rdb->fetch('data','tripdetails',$query);
                    unset($query);
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $query[]=$this->rdb->qb('select',"buses_id");
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_buses",$configinfo->buses_id);
                        $query[]=$this->rdb->qb('select',"bus_no");
                        $busno=$this->rdb->fetch('default','buses',$query,FALSE, TRUE)->bus_no;
                        unset($query);
                    endif;
                    foreach($trips as $trip):
                        if($routeid!=$trip['deviceroutes_id']):
                            $query[]=$this->rdb->qb('where',"id_deviceroutes",$trip['deviceroutes_id']);
                            $query[]=$this->rdb->qb('select',"route_name");
                            $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                            unset($query);
                            $routeid=$trip['deviceroutes_id'];
                        endif;
                        if($trip['is_trip_closed']==1):
                            $reports[$count]['busno']=$busno;
                            $reports[$count]['depot']=$device['depot_name'];
                            $reports[$count]['routeno']=$routeinfo->route_name;
                            $reports[$count]['tripopening']=$trip['opentimestamp'];
                            $reports[$count]['wbn']=$wb['way_bill_no'];
                            $reports[$count]['tripclosed']=$trip['is_trip_closed'];
                            $reports[$count]['uid']=$device['uid'];
                            $reports[$count]['tripno']=$trip['tripno'];
                            $reports[$count]['tickets']=$trip['total_tickets'];
                            $reports[$count]['passes']=$trip['total_passes'];
                            $reports[$count]['ticketamt']=$trip['tickets_amount'];
                            $reports[$count]['passamt']=$trip['passes_amount'];
                            $reports[$count]['card']=$trip['card_amount'];
                            $reports[$count]['cash']=$trip['cash_amount'];
                        else:
                            $tdetails=$this->_gettripticketinfobyid($trip['id_tripdetails']);
                            $reports[$count]['busno']=$busno;
                            $reports[$count]['depot']=$device['depot_name'];
                            $reports[$count]['routeno']=$routeinfo->route_name;
                            $reports[$count]['tripopening']=$trip['opentimestamp'];
                            $reports[$count]['wbn']=$wb['way_bill_no'];
                            $reports[$count]['tripclosed']=$trip['is_trip_closed'];
                            $reports[$count]['uid']=$device['uid'];
                            $reports[$count]['tripno']=$trip['tripno'];
                            $reports[$count]['tickets']=$tdetails['tickets'];
                            $reports[$count]['passes']=$tdetails['passes'];
                            $reports[$count]['ticketamt']=$tdetails['ticketsamt'];
                            $reports[$count]['passamt']=$tdetails['passesamt'];
                            $reports[$count]['cashamt']=$tdetails['cash'];
                            $reports[$count]['cardamt']=$tdetails['card'];
                        endif;
                        $count+=1;
                    endforeach;
                endforeach;
            endif;
            
        endforeach;
        return $reports;
    }
    public function genexpensewisereport($date,$depot){
        $from=date('Y-m-d 00:00:00',  strtotime($date[0]));
        $to=date('Y-m-d 23:59:59',  strtotime($date[1]));
        $reports=array();
        $devices=$this->_getdevices($depot);
        $count=0;
        $routeid='';
        $configid='';
        $query[]=$this->rdb->qb('order_by',"id_expenses",'asc');
        $exp=$this->rdb->fetch('default','expenses',$query);
        unset($query);
        foreach($exp as $edetails):
            $expmaster[$edetails['id_expenses']]=$edetails['expense_name'];
        endforeach;
        foreach($devices as $device):
            $query[]=$this->rdb->qb('where',"(wbopentimestamp BETWEEN '$from' AND '$to' OR wbclosedrectimestamp IS NULL)",NULL,FALSE);
            $query[]=$this->rdb->qb('where',"wb_devices_id",$device['id_devices']);
            $waybills=$this->rdb->fetch('data','waybilldetails',$query);
            unset($query);
            if(count($waybills)!=0):
                foreach($waybills as $wb):
                    $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                    $trips=$this->rdb->fetch('data','tripdetails',$query);
                    unset($query);
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $query[]=$this->rdb->qb('select',"buses_id");
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_buses",$configinfo->buses_id);
                        $query[]=$this->rdb->qb('select',"bus_no");
                        $busno=$this->rdb->fetch('default','buses',$query,FALSE, TRUE)->bus_no;
                        unset($query);
                    endif;
                    foreach($trips as $trip):
                        if($trip['is_trip_closed']==1):
                            $query[]=$this->rdb->qb('where',"e_tripdetails_id",$trip['id_tripdetails']);
                            $expenses=$this->rdb->fetch('data','expensedetails',$query);
                            unset($query);
                            if(count($expenses)>0):
                                if($routeid!=$trip['deviceroutes_id']):
                                    $query[]=$this->rdb->qb('where',"id_deviceroutes",$trip['deviceroutes_id']);
                                    $query[]=$this->rdb->qb('select',"route_name");
                                    $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                                    unset($query);
                                    $routeid=$trip['deviceroutes_id'];
                                endif;
                                foreach($expenses as $expense):
                                    $reports[$count]['busno']=$busno;
                                    $reports[$count]['depot']=$device['depot_name'];
                                    $reports[$count]['routeno']=$routeinfo->route_name;
                                    $reports[$count]['wbn']=$wb['way_bill_no'];
                                    $reports[$count]['tripno']=$trip['tripno'];
                                    $reports[$count]['uid']=$device['uid'];
                                    $reports[$count]['date']=$expense['e_timestamp'];
                                    $reports[$count]['expense']=$expmaster[$expense['expenses_id']];
                                    $reports[$count]['amount']=$expense['amount'];
                                $count+=1;
                                
                                endforeach;
                            endif;
                        endif;
                    endforeach;
                endforeach;
            endif;
        endforeach;
        return $reports;
    }
    public function genstagewisereport($date,$depot){
        $from=date('Y-m-d 00:00:00',  strtotime($date[0]));
        $to=date('Y-m-d 23:59:59',  strtotime($date[1]));
        $reports=array();
        $devices=$this->_getdevices($depot);
        $count=0;
        $routeid='';
        $configid='';
        foreach($devices as $device):
            $query[]=$this->rdb->qb('where',"(wbopentimestamp BETWEEN '$from' AND '$to' OR wbclosedrectimestamp IS NULL)",NULL,FALSE);
            $query[]=$this->rdb->qb('where',"wb_devices_id",$device['id_devices']);
            $waybills=$this->rdb->fetch('data','waybilldetails',$query);
            unset($query);
            if(count($waybills)!=0):
                foreach($waybills as $wb):
                    $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                    $trips=$this->rdb->fetch('data','tripdetails',$query);
                    unset($query);
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $query[]=$this->rdb->qb('select',"buses_id");
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_buses",$configinfo->buses_id);
                        $query[]=$this->rdb->qb('select',"bus_no");
                        $busno=$this->rdb->fetch('default','buses',$query,FALSE, TRUE)->bus_no;
                        unset($query);
                    endif;
                    foreach($trips as $trip):
                        if($routeid!=$trip['deviceroutes_id']):
                            $query[]=$this->rdb->qb('where',"id_deviceroutes",$trip['deviceroutes_id']);
                            $query[]=$this->rdb->qb('select',"route_name,stages");
                            $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                            unset($query);
                            $routeid=$trip['deviceroutes_id'];
                            $oroutestages=explode('#~#',$routeinfo->stages);
                        endif;
                        if($trip['stage_type']==1)//Up
                            $stages=$oroutestages;
                        else//downstage
                            $stages=  array_reverse ($stages);
                        $query[]=$this->rdb->qb('order_by',"source_stage_no",'asc');
                        $query[]=$this->rdb->qb('order_by',"destination_stage_no",'asc');
                        $query[]=$this->rdb->qb('group_by',"source_stage_no");
                        $query[]=$this->rdb->qb('group_by',"destination_stage_no");
                        $query[]=$this->rdb->qb('where',"'ticket_type'!='3'");
                        $query[]=$this->rdb->qb('select',"sum(amount) as collection,source_stage_no,destination_stage_no");
                        $query[]=$this->rdb->qb('where',"tripdetails_id",$trip['id_tripdetails']);
                        $sdetails=$this->rdb->fetch('data','ticketdetails',$query);
                        unset($query);
                        $tdate=date('Y-m-d',  strtotime($trip['opentimestamp']));
                        foreach($sdetails as $sdetail):
                            $query[]=$this->rdb->qb('where',"tripdetails_id",$trip['id_tripdetails']);
                            $query[]=$this->rdb->qb('group_by',"ticket_type");
                            $query[]=$this->rdb->qb('where',"'ticket_type'!='3'");
                            $query[]=$this->rdb->qb('where','source_stage_no',$sdetail['source_stage_no']);
                            $query[]=$this->rdb->qb('where','destination_stage_no',$sdetail['destination_stage_no']);
                            $query[]=$this->rdb->qb('select',"count(id_ticketdetails) as tickets,ticket_type");
                            $recorddetails=$this->rdb->fetch('data','ticketdetails',$query);
                            unset($query);
                            $sno=intval($sdetail['source_stage_no'])-1;
                            $dno=intval($sdetail['destination_stage_no'])-1;
                            $reports[$count]['uid']=$device['uid'];
                            $reports[$count]['busno']=$busno;
                            $reports[$count]['depot']=$device['depot_name'];
                            $reports[$count]['date']=$tdate;
                            $reports[$count]['wbn']=$wb['way_bill_no'];
                            $reports[$count]['routeno']=$routeinfo->route_name;
                            $reports[$count]['tripno']=$trip['tripno'];
                            $reports[$count]['up']=$stages[$sno];
                            $reports[$count]['down']=$stages[$dno];
                            $reports[$count]['tickets']=0;
                            $reports[$count]['pass']=0;
                            $reports[$count]['collection']=$sdetail['collection'];
                            foreach($recorddetails as $recorddetail):
                                if($recorddetail['ticket_type']==1)//Passenger
                                    $reports[$count]['tickets']=$recorddetail['tickets'];
                                else if($recorddetail['ticket_type']==2)//Pass
                                    $reports[$count]['pass']=$recorddetail['tickets'];
                            endforeach;
                            $count+=1;
                        endforeach;
                    endforeach;
                endforeach;
            endif;
            
        endforeach;
        return $reports;
    }
    public function genticketwisereport($date,$depot){
        $from=date('Y-m-d 00:00:00',  strtotime($date[0]));
        $to=date('Y-m-d 23:59:59',  strtotime($date[1]));
        $reports=array();
        $devices=$this->_getdevices($depot);
        $count=0;
        $routeid='';
        $configid='';
        foreach($devices as $device):
            $query[]=$this->rdb->qb('where',"(wbopentimestamp BETWEEN '$from' AND '$to' OR wbclosedrectimestamp IS NULL)",NULL,FALSE);
            $query[]=$this->rdb->qb('where',"wb_devices_id",$device['id_devices']);
            $waybills=$this->rdb->fetch('data','waybilldetails',$query);
            unset($query);
            if(count($waybills)!=0):
                foreach($waybills as $wb):
                    $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                    $trips=$this->rdb->fetch('data','tripdetails',$query);
                    unset($query);
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $query[]=$this->rdb->qb('select',"buses_id");
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_buses",$configinfo->buses_id);
                        $query[]=$this->rdb->qb('select',"bus_no");
                        $busno=$this->rdb->fetch('default','buses',$query,FALSE, TRUE)->bus_no;
                        unset($query);
                    endif;
                    foreach($trips as $trip):
                        if($routeid!=$trip['deviceroutes_id']):
                            $query[]=$this->rdb->qb('where',"id_deviceroutes",$trip['deviceroutes_id']);
                            $query[]=$this->rdb->qb('select',"route_name,stages");
                            $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                            unset($query);
                            $routeid=$trip['deviceroutes_id'];
                            $stages=explode('#~#',$routeinfo->stages);
                        endif;
                        $query[]=$this->rdb->qb('order_by',"ticket_no",'asc');
                        $query[]=$this->rdb->qb('where',"'ticket_type'!='3'");
                        $query[]=$this->rdb->qb('where',"tripdetails_id",$trip['id_tripdetails']);
                        $tktdetails=$this->rdb->fetch('data','ticketdetails',$query);
                        unset($query);
                        foreach($tktdetails as $tktdetail):
                            if($tktdetail['ticket_type']!=3):
                                //order->date, bus no, wbn, route, trip no, tkt no, upstage, down stage, payment mode, ticket, pass, amt
                                $sno=intval($tktdetail['source_stage_no'])-1;
                                $dno=intval($tktdetail['destination_stage_no'])-1;
                                $reports[$count]['uid']=$device['uid'];
                                $reports[$count]['depot']=$device['depot_name'];
                                $reports[$count]['date']=$tktdetail['ticket_timestamp'];
                                $reports[$count]['tktno']=$tktdetail['ticket_no'];
                                $reports[$count]['busno']=$busno;
                                $reports[$count]['wbn']=$wb['way_bill_no'];
                                $reports[$count]['routeno']=$routeinfo->route_name;
                                $reports[$count]['tripno']=$trip['tripno'];
                                $reports[$count]['up']=$stages[$sno];
                                $reports[$count]['down']=$stages[$dno];
                                $reports[$count]['paytype']=($tktdetail['paytype']==0?'Cash':'Card');
                                if($tktdetail['ticket_type']==1)://Passenger Ticket
                                    $reports[$count]['tickets']=$tktdetail['qty'];
                                    $reports[$count]['pass']=0;
                                else:
                                    $reports[$count]['tickets']=0;
                                    $reports[$count]['pass']=$tktdetail['qty'];
                                endif;
                                $reports[$count]['amt']=$tktdetail['amount'];
                                $count+=1;
                            endif;
                        endforeach;
                    endforeach;
                endforeach;
            endif;
            
        endforeach;
        return $reports;
    }
    public function gencardbasedreport($date,$depot){
        $from=date('Y-m-d 00:00:00',  strtotime($date[0]));
        $to=date('Y-m-d 23:59:59',  strtotime($date[1]));
        $reports=array();
        $devices=$this->_getdevices($depot);
        $count=0;
        $routeid='';
        $configid='';
        foreach($devices as $device):
            $query[]=$this->rdb->qb('where',"(wbopentimestamp BETWEEN '$from' AND '$to' OR wbclosedrectimestamp IS NULL)",NULL,FALSE);
            $query[]=$this->rdb->qb('where',"wb_devices_id",$device['id_devices']);
            $waybills=$this->rdb->fetch('data','waybilldetails',$query);
            unset($query);
            if(count($waybills)!=0):
                foreach($waybills as $wb):
                    $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                    $trips=$this->rdb->fetch('data','tripdetails',$query);
                    unset($query);
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $query[]=$this->rdb->qb('select',"buses_id");
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_buses",$configinfo->buses_id);
                        $query[]=$this->rdb->qb('select',"bus_no");
                        $busno=$this->rdb->fetch('default','buses',$query,FALSE, TRUE)->bus_no;
                        unset($query);
                    endif;
                    foreach($trips as $trip):
                        if($routeid!=$trip['deviceroutes_id']):
                            $query[]=$this->rdb->qb('where',"id_deviceroutes",$trip['deviceroutes_id']);
                            $query[]=$this->rdb->qb('select',"route_name,stages");
                            $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                            unset($query);
                            $routeid=$trip['deviceroutes_id'];
                            $stages=explode('#~#',$routeinfo->stages);
                        endif;
                        $query[]=$this->rdb->qb('order_by',"ticket_no",'asc');
                        $query[]=$this->rdb->qb('where',"'ticket_type'!='3'");
                        $query[]=$this->rdb->qb('where',"paytype",'1');
                        $query[]=$this->rdb->qb('where',"tripdetails_id",$trip['id_tripdetails']);
                        $tktdetails=$this->rdb->fetch('data','ticketdetails',$query);
                        unset($query);
                        foreach($tktdetails as $tktdetail):
                            if($tktdetail['ticket_type']!=3):
                                //order->date, bus no, wbn, route, trip no, tkt no, upstage, down stage, payment mode, ticket, pass, amt
                                $sno=intval($tktdetail['source_stage_no'])-1;
                                $dno=intval($tktdetail['destination_stage_no'])-1;
                                $reports[$count]['uid']=$device['uid'];
                                $reports[$count]['depot']=$device['depot_name'];
                                $reports[$count]['date']=$tktdetail['ticket_timestamp'];
                                $reports[$count]['tktno']=$tktdetail['ticket_no'];
                                $reports[$count]['busno']=$busno;
                                $reports[$count]['wbn']=$wb['way_bill_no'];
                                $reports[$count]['routeno']=$routeinfo->route_name;
                                $reports[$count]['tripno']=$trip['tripno'];
                                $reports[$count]['up']=$stages[$sno];
                                $reports[$count]['down']=$stages[$dno];
                                $reports[$count]['card']=$tktdetail['carduid'];
                                $reports[$count]['amt']=$tktdetail['amount'];
                                $count+=1;
                            endif;
                        endforeach;
                    endforeach;
                endforeach;
            endif;
            
        endforeach;
        return $reports;
    }
    /*
     * Internal Functions
     */
    function _getwbinfobyrid($wbid=''){
        if($wbid!=''):
            $response=array();
            $query[]=$this->rdb->qb('where','waybilldetails_id',$wbid);
            $tripdetails=$this->rdb->fetch('data','tripdetails',$query);
            unset($query);
            $response['trips']=count($tripdetails);
            $response['tickets']=0;
            $response['routeno']='';
            $response['passes']=0;
            $response['luggages']=0;
            $response['ticketsamt']=0;
            $response['passesamt']=0;
            $response['lugamt']=0;
            $response['expenses']=0;
            $response['card']=0;
            $response['cash']=0;
            $routecheck=0;
            foreach($tripdetails as $trip):
                if($routecheck==1):
                    $query[]=$this->rdb->qb('where',"id_deviceroutes",$trip['deviceroutes_id']);
                    $query[]=$this->rdb->qb('select',"route_name");
                    $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                    unset($query);
                    $response['routeno']=$routeinfo->route_name;
                    $routecheck=1;
                endif;
                if($trip['is_trip_closed']==1):
                    $response['tickets']+=$trip['total_tickets'];
                    $response['passes']+=$trip['total_passes'];
                    $response['luggages']+=$trip['total_luggages'];
                    $response['ticketsamt']+=$trip['tickets_amount'];
                    $response['passesamt']+=$trip['passes_amount'];
                    $response['lugamt']+=$trip['luggages_amount'];
                    $response['expenses']+=$trip['expenses_amount'];
                    $response['card']+=$trip['card_amount'];
                    $response['cash']+=$trip['cash_amount'];
                else:
                    $tdetails=$this->_gettripticketinfobyid($trip['id_tripdetails']);
                    $response['tickets']+=$tdetails['tickets'];
                    $response['passes']+=$tdetails['passes'];
                    $response['luggages']+=$tdetails['luggages'];
                    $response['ticketsamt']+=$tdetails['ticketsamt'];
                    $response['passesamt']+=$tdetails['passesamt'];
                    $response['lugamt']+=$tdetails['lugamt'];
                    $response['card']+=$tdetails['card'];
                    $response['cash']+=$tdetails['cash'];
                endif;
            endforeach;
            return $response;
        endif;
        return;
    }
    function _getwbinfobyid($wbid=''){
        if($wbid!=''):
            $response=array();
            $query[]=$this->rdb->qb('where','waybilldetails_id',$wbid);
            $tripdetails=$this->rdb->fetch('data','tripdetails',$query);
            unset($query);
            $response['trips']=count($tripdetails);
            $response['tickets']=0;
            $response['routeno']='';
            $response['passes']=0;
            $response['luggages']=0;
            $response['ticketsamt']=0;
            $response['passesamt']=0;
            $response['lugamt']=0;
            $response['expenses']=0;
            $response['card']=0;
            $response['cash']=0;
            $routecheck=0;
            foreach($tripdetails as $trip):
                if($routecheck==1):
                    $query[]=$this->rdb->qb('where',"id_deviceroutes",$trip['deviceroutes_id']);
                    $query[]=$this->rdb->qb('select',"route_name");
                    $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                    unset($query);
                    $response['routeno']=$routeinfo->route_name;
                    $routecheck=1;
                endif;
                if($trip['is_trip_closed']==1):
                    $response['tickets']+=$trip['total_tickets'];
                    $response['passes']+=$trip['total_passes'];
                    $response['luggages']+=$trip['total_luggages'];
                    $response['ticketsamt']+=$trip['tickets_amount'];
                    $response['passesamt']+=$trip['passes_amount'];
                    $response['lugamt']+=$trip['luggages_amount'];
                    $response['expenses']+=$trip['expenses_amount'];
                    $response['card']+=$trip['card_amount'];
                    $response['cash']+=$trip['cash_amount'];
                else:
                    $tdetails=$this->_gettripticketinfobyid($trip['id_tripdetails']);
                    $response['tickets']+=$tdetails['tickets'];
                    $response['passes']+=$tdetails['passes'];
                    $response['luggages']+=$tdetails['luggages'];
                    $response['ticketsamt']+=$tdetails['ticketsamt'];
                    $response['passesamt']+=$tdetails['passesamt'];
                    $response['lugamt']+=$tdetails['lugamt'];
                    $response['card']+=$tdetails['card'];
                    $response['cash']+=$tdetails['cash'];
                endif;
            endforeach;
            return $response;
        endif;
        return;
    }
    function _getwbdbinfobyrid($wbid=''){
        if($wbid!=''):
            $response=array();
            $query[]=$this->rdb->qb('where','waybilldetails_id',$wbid);
            $tripdetails=$this->rdb->fetch('data','tripdetails',$query);
            unset($query);
            $response['trips']=count($tripdetails);
            $response['tickets']=0;
            $response['passes']=0;
            $response['ticketsamt']=0;
            $response['passesamt']=0;
            $response['expenses']=0;
            $response['card']=0;
            $response['cash']=0;
            $response['lugamt']=0;
            foreach($tripdetails as $trip):
                if($trip['is_trip_closed']==1):
                    $response['tickets']+=$trip['total_tickets'];
                    $response['passes']+=$trip['total_passes'];
                    $response['ticketsamt']+=$trip['tickets_amount'];
                    $response['passesamt']+=$trip['passes_amount'];
                    $response['expenses']+=$trip['expenses_amount'];
                    $response['card']+=$trip['card_amount'];
                    $response['cash']+=$trip['cash_amount'];
                    $response['lugamt']+=$trip['luggages_amount'];
                else:
                    $tdetails=$this->_gettripticketinfobyid($trip['id_tripdetails']);
                    $response['tickets']+=$tdetails['tickets'];
                    $response['passes']+=$tdetails['passes'];
                    $response['ticketsamt']+=$tdetails['ticketsamt'];
                    $response['passesamt']+=$tdetails['passesamt'];
                    $response['card']+=$tdetails['card'];
                    $response['cash']+=$tdetails['cash'];
                    $response['lugamt']+=$tdetails['lugamt'];
                endif;
            endforeach;
            return $response;
        endif;
        return;
    }
    function _getwbdbinfobyid($wbid=''){
        if($wbid!=''):
            $response=array();
            $query[]=$this->rdb->qb('where','waybilldetails_id',$wbid);
            $tripdetails=$this->rdb->fetch('data','tripdetails',$query);
            unset($query);
            $response['trips']=count($tripdetails);
            $response['tickets']=0;
            $response['passes']=0;
            $response['ticketsamt']=0;
            $response['passesamt']=0;
            $response['expenses']=0;
            $response['card']=0;
            $response['cash']=0;
            foreach($tripdetails as $trip):
                if($trip['is_trip_closed']==1):
                    $response['tickets']+=$trip['total_tickets'];
                    $response['passes']+=$trip['total_passes'];
                    $response['ticketsamt']+=$trip['tickets_amount'];
                    $response['passesamt']+=$trip['passes_amount'];
                    $response['expenses']+=$trip['expenses_amount'];
                    $response['card']+=$trip['card_amount'];
                    $response['cash']+=$trip['cash_amount'];
                else:
                    $tdetails=$this->_gettripticketinfobyid($trip['id_tripdetails']);
                    $response['tickets']+=$tdetails['tickets'];
                    $response['passes']+=$tdetails['passes'];
                    $response['ticketsamt']+=$tdetails['ticketsamt'];
                    $response['passesamt']+=$tdetails['passesamt'];
                    $response['card']+=$tdetails['card'];
                    $response['cash']+=$tdetails['cash'];
                endif;
            endforeach;
            return $response;
        endif;
        return;
    }
    function _gettripticketinfobyid($tid=''){
        if($tid!=''):
            $query[]=$this->rdb->qb('select','sum(amount) as amount, count(ticket_no) as tickets');
            $query[]=$this->rdb->qb('where','tripdetails_id',$tid);
            $query[]=$this->rdb->qb('where','ticket_type',1);
            $tickets=$this->rdb->fetch('data','ticketdetails',$query,FALSE,TRUE);
            unset($query);
            $query[]=$this->rdb->qb('select','sum(amount) as amount, count(ticket_no) as tickets');
            $query[]=$this->rdb->qb('where','tripdetails_id',$tid);
            $query[]=$this->rdb->qb('where','ticket_type',2);
            $pass=$this->rdb->fetch('data','ticketdetails',$query,FALSE,TRUE);
            unset($query);
            $query[]=$this->rdb->qb('select','sum(amount) as amount, count(ticket_no) as tickets');
            $query[]=$this->rdb->qb('where','tripdetails_id',$tid);
            $query[]=$this->rdb->qb('where','ticket_type',3);
            $lug=$this->rdb->fetch('data','ticketdetails',$query,FALSE,TRUE);
            unset($query);
            $query[]=$this->rdb->qb('select','sum(amount) as amount, count(ticket_no) as tickets');
            $query[]=$this->rdb->qb('where','tripdetails_id',$tid);
            $query[]=$this->rdb->qb('where','ticket_type',1);
            $query[]=$this->rdb->qb('where','paytype',1);
            $card=$this->rdb->fetch('data','ticketdetails',$query,FALSE,TRUE)->amount;
            unset($query);
            $query[]=$this->rdb->qb('select','sum(amount) as amount, count(ticket_no) as tickets');
            $query[]=$this->rdb->qb('where','tripdetails_id',$tid);
            $query[]=$this->rdb->qb('where','ticket_type',1);
            $query[]=$this->rdb->qb('where','paytype',0);
            $cash=$this->rdb->fetch('data','ticketdetails',$query,FALSE,TRUE)->amount;
            unset($query);
            $response['tickets']=$tickets->tickets;
            $response['ticketsamt']=$tickets->amount;
            $response['passes']=$pass->tickets;
            $response['passesamt']=$pass->amount;
            $response['luggages']=$lug->tickets;
            $response['lugamt']=$lug->amount;
            $response['card']=$card;
            $response['cash']=$cash;
            return $response;
        endif;
        return;
    }
    function _getstagewiseinfobytripid($tid=''){
        if($tid!=''):
            $query[]=$this->rdb->qb('select','sum(amount) as amount, count(ticket_no) as tickets');
            $query[]=$this->rdb->qb('where','tripdetails_id',$tid);
            $query[]=$this->rdb->qb('where','ticket_type',1);
            $tickets=$this->rdb->fetch('data','ticketdetails',$query,FALSE,TRUE);
            unset($query);
            $query[]=$this->rdb->qb('select','sum(amount) as amount, count(ticket_no) as tickets');
            $query[]=$this->rdb->qb('where','tripdetails_id',$tid);
            $query[]=$this->rdb->qb('where','ticket_type',2);
            $pass=$this->rdb->fetch('data','ticketdetails',$query,FALSE,TRUE);
            unset($query);
            $response['tickets']=$tickets->tickets;
            $response['ticketsamt']=$tickets->amount;
            $response['passes']=$pass->tickets;
            $response['passesamt']=$pass->amount;
            return $response;
        endif;
        return;
    }
    public function _getdevices($depot=NULL){
        if($depot!=NULL)
            $query[]=$this->rdb->qb('where','depots_id',$depot);
        $query[]=$this->rdb->qb('select','id_devices,uid,depot_name');
        $query[]=$this->rdb->qb('join','depots','depots.id_depots=devices.depots_id');
        $return=$this->rdb->fetch('default','devices',$query);
        unset($query);
        return $return;
    }
}