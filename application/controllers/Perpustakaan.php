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

		$crud->set_field_upload('cover','assets/uploads/files');
		$crud->set_subject('Buku', 'Daftar Buku');
		$crud->set_theme('datatables');
     
        $output = $crud->render();
 
        $this->_example_output($output);

    }

	public function role() {
        $crud = new grocery_CRUD();
        $crud->set_table('role');

		$crud->set_subject('Role', 'Daftar Role');
		$crud->set_theme('datatables');
     
        $output = $crud->render();
 
        $this->_example_output($output);

    }

	public function user() {
        $crud = new grocery_CRUD();
        $crud->set_table('user');

		$crud->set_subject('User', 'Daftar User');
		$crud->set_theme('datatables');
		$crud->set_relation('role_id','role','role');
		$crud->display_as('role_id','Role');
     
        $output = $crud->render();
 
        $this->_example_output($output);

    }
}
