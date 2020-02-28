<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* load the MX_Loader class */
require APPPATH."libraries/modular/Loader.php";

class ICT_Loader extends MX_Loader {
    
    function template_view($view, $vars = array(), $return = FALSE) {
    $this->_r_view_paths = array_merge($this->_r_view_paths, array(BASEPATH . '../templates/' => TRUE));
    return $this->_r_load(array(
                '_r_view' => $view,
                '_r_vars' => $this->_r_prepare_view_vars($vars),
                '_r_return' => $return
            ));
    }
      function file_view($folder, $view, $vars = array(), $return = FALSE) {
    $this->_r_view_paths = array_merge($this->_r_view_paths, array(BASEPATH .'../'.$folder. '/' => TRUE));
    return $this->_r_load(array(
                '_r_view' => $view,
                '_r_vars' => $this->_r_prepare_view_vars($vars),
                '_r_return' => $return
            ));
    }
}