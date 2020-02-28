<!DOCTYPE html>
<html>
    <head>
    <?php
        $assets=array(
            array('css','bootstrap.min.css'),
            array('css','core.css'),
            array('css','components.css'),
            array('css','icons.css'),
            array('css','pages.css'),
            array('css','menu.css'),
            array('css','responsive.css'),
            array('css','custom.css'),
            array('js','modernizr.min.js'),
            array('js','jquery.min.js'),
        );
        $plugin=array(
            array('css','datatables','jquery.dataTables.min.css'),
            array('css','datatables','buttons.bootstrap.min.css'),
            array('css','datatables','responsive.bootstrap.min.css'),
            array('css','datatables','dataTables.bootstrap.min.css'),
            array('css','jquery-toastr','jquery.toast.min.css'),
            array('css','pace','pace-theme-center-atom.css'),
            array('css','magnific-popup','magnific-popup.css'),
            array('css','select2','select2.min.css'),
            array('css','switchery','switchery.min.css'),
            array('css','bootstrap-daterangepicker','daterangepicker.css'),
        );
        $this->rview->populateheader($assets,$plugin);?>
    </head>
    <body>
        <header id="topnav">
            <div class="topbar-main">
                <div class="container">
                    <div class="logo">
                        <a href="#" class="logo">
                            <img src="<?=$this->rview->assets('logo.png','images');?>" class='hidden-lg' alt="">
                            <img src="<?=$this->rview->assets('techmaaxx-logo.png','images');?>" class='visible-lg' alt="">
                        </a>
                    </div>
                    <div class="menu-extras">
                        <?php $this->rview->buildusermenu();?>
                        <div class="menu-item">
                            <a class="navbar-toggle">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="navbar-custom">
                <div class="container">
                    <div id="navigation">
                        <?php $this->rview->mainmenu();?>
                    </div>
                </div>
            </div>
        </header>
        <div class="wrapper">
            <div class="container">
                <?=$this->rview->populatecontent($subview);?>
            </div>
        </div>
        <?php
                
        $assets=array(
            array('js','bootstrap.min.js'),
            array('js','jquery.slimscroll.js'),
            array('js','jquery.scrollTo.min.js'),
            array('js','jquery.core.js'),
            array('js','jquery.app.js'),
        );
        $plugin=array(
            array('js','datatables','jquery.dataTables.min.js'),
            array('js','datatables','dataTables.bootstrap.js'),
            array('js','datatables','dataTables.buttons.min.js'),
            array('js','datatables','buttons.bootstrap.min.js'),
            array('js','datatables','jszip.min.js'),
            array('js','datatables','pdfmake.min.js'),
            array('js','datatables','vfs_fonts.js'),
            array('js','datatables','buttons.html5.min.js'),
            array('js','datatables','buttons.print.min.js'),
            array('js','datatables','dataTables.responsive.min.js'),
            array('js','datatables','responsive.bootstrap.min.js'),
            array('js','jquery-validation','jquery.validate.min.js'),
            array('js','jquery-validation','pattern.js'),
            array('js','magnific-popup','jquery.magnific-popup.min.js'),
            array('js','select2','select2.min.js'),
            array('js','switchery','switchery.min.js'),
            array('js','jquery-toastr','jquery.toast.min.js'),
            array('js','pace','pace.min.js'),
            array('js','moment','moment.js'),
            array('js','bootstrap-daterangepicker','daterangepicker.js'),
        );
        $init=array('jquery.ict.ajax.init','jquery.ict-modals.ajax.init','jquery.select.init');
        $this->rview->populatefooterscripts($assets,$plugin,$init);?>
    </body>
</html>