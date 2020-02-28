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
 * @module      auth
 * @filesource  auth.views.login
 */

?>

            <div class="container">
                <div class="row">
                    <div class="col-sm-12">

                        <div class="wrapper-page">

                            <div class="account-pages">
                                <div class="account-box">
                                    <div class="account-logo-box">
                                        <h2 class="text-uppercase text-center">
                                            <a href="#l" class="text-success">
                                                <span><img src="<?=$this->rview->assets('logo.png','images');?>" alt=""></span>
                                            </a>
                                        </h2>
                                        <h5 class="text-uppercase font-bold m-b-5 m-t-50">Sign In</h5>
                                    </div>
                                    
                                    <div class="account-content">
                                        <?php if(isset($error)): ?>
                                        <div class="alert alert-icon alert-danger alert-dismissible fade in" role="alert">
                                            <button type="button" class="close" data-dismiss="alert"
                                                    aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                            <i class="mdi mdi-block-helper"></i>
                                            <strong>Opps!</strong> Invalid Login Credentials.
                                        </div>
                                        <?php endif; 
                                        if($mode!='logme'):
                                            $str=$mode;
                                        else:
                                            $str='';
                                        endif;
                                        ?>
                                        <?php echo form_open($this->rview->url("auth/login/$str") , array('class' => 'form-horizontal'));?>

                                            <div class="form-group m-b-20">
                                                <div class="col-xs-12">
                                                    <label for="emailaddress">User Name</label>
                                                    <input class="form-control" type="text" id="username" name="username" required="" placeholder="john">
                                                </div>
                                            </div>

                                            <div class="form-group m-b-20">
                                                <div class="col-xs-12">
                                                    <!--<a href="<?=$this->rview->url('auth/forgot');?>" class="text-muted pull-right"><small>Forgot your password?</small></a>-->
                                                    <label for="password">Password</label>
                                                    <input class="form-control" type="password" required="" id="password" name="password" placeholder="Enter your password">
                                                </div>
                                            </div>
                                            <div class="form-group text-center m-t-10">
                                                <div class="col-xs-12">
                                                    <button class="btn btn-md btn-block btn-primary waves-effect waves-light" type="submit">Sign In</button>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- end card-box-->


                        </div>
                        <div class="row m-t-50">
                            <div class="col-sm-12 text-center" style="color:white;">
                                <?=getfootertext()?>
                            </div>
                        </div>
                        <!-- end wrapper -->

                    </div>
                </div>
            </div>