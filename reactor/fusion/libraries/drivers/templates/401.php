<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>404</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <meta content="Application for MMT Transport developed by Increatech Business Solution Pvt Ltd" name="description" />
        <meta content="Increatech" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <!-- App favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.ico">

        <!-- App css -->
        <link href="<?=$this->rview->assets('bootstrap.min.css','css');?>" rel="stylesheet" type="text/css" />
        <link href="<?=$this->rview->assets('core.css','css');?>" rel="stylesheet" type="text/css" />
        <link href="<?=$this->rview->assets('components.css','css');?>" rel="stylesheet" type="text/css" />
        <link href="<?=$this->rview->assets('icons.css','css');?>" rel="stylesheet" type="text/css" />
        <link href="<?=$this->rview->assets('pages.css','css');?>" rel="stylesheet" type="text/css" />
        <link href="<?=$this->rview->assets('menu.css','css');?>" rel="stylesheet" type="text/css" />
        <link href="<?=$this->rview->assets('responsive.css','css');?>" rel="stylesheet" type="text/css" />
        <link href="<?=$this->rview->assets('custom.css','css');?>" rel="stylesheet" type="text/css" />

        <script src="<?=$this->rview->assets('modernizr.min.js','js');?>"></script>

    </head>


    <body class="bg-accpunt-pages">

        <!-- HOME -->
        <section>
            <div class="container">
                <div class="row">
                    <div class="col-sm-12 text-center">

                        <div class="wrapper-page">
                            <div class="account-pages">
                                <div class="account-box">

                                    <div class="account-logo-box">
                                        <h2 class="text-uppercase text-center">
                                            <a href="#" class="text-success">
                                                <span><img src="<?=$this->rview->assets('logo.png','images');?>" alt=""></span>
                                            </a>
                                        </h2>
                                    </div>

                                    <div class="account-content">
                                        <h1 class="text-error">401</h1>
                                        <h2 class="text-uppercase text-danger m-t-30">Unauthorized</h2>
                                        <p class="text-muted m-t-30">You seems unauthorized to access this..</p>

                                        <a class="btn btn-md btn-block btn-primary waves-effect waves-light m-t-20" href="<?=$this->rview->url();?>"> Return</a>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
          </section>
          <!-- END HOME -->


        <!-- jQuery  -->
        <script src="<?=$this->rview->assets('jquery.min.js','js');?>"></script>
        <script src="<?=$this->rview->assets('bootstrap.min.js','js');?>"></script>
        <!--<script src="<?=$this->rview->assets('waves.js','js');?>"></script>-->
        <script src="<?=$this->rview->assets('jquery.slimscroll.js','js');?>"></script>
        <script src="<?=$this->rview->assets('jquery.scrollTo.min.js','js');?>"></script>

        <!-- App js -->
        <script src="<?=$this->rview->assets('jquery.core.js','js');?>"></script>
        <script src="<?=$this->rview->assets('jquery.app.js','js');?>"></script>

    </body>
</html>