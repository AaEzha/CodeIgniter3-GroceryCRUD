<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Perpustakaan extends CI_Controller {
	function __construct() {
        parent::__construct();

 
        /* Standard Codeigniter Libraries */
        $this->load->database();
        $this->load->helper('url'); 
 
        $this->load->library('grocery_CRUD');    
    }
 
    private function _example_output($output = null) {
        $this->load->view('example.php',$output);    
    }
 
    public function buku() {
        $crud = new grocery_CRUD();
        $crud->set_table('buku');
     
        $output = $crud->render();
 
        $this->_example_output($output);

    }
}
