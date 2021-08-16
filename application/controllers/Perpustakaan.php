<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Perpustakaan extends CI_Controller
{
	function __construct()
	{
		parent::__construct();


		/* Standard Codeigniter Libraries */
		$this->load->database();
		$this->load->helper('url');

		$this->load->library('grocery_CRUD');
	}

	private function _example_output($output = null)
	{
		$this->load->view('example.php', $output);
	}

	public function buku()
	{
		$crud = new grocery_CRUD();
		$crud->set_table('buku');

		$crud->set_field_upload('cover', 'assets/uploads/files');
		$crud->set_subject('Buku', 'Daftar Buku');
		$crud->set_theme('datatables');

		$output = $crud->render();

		$this->_example_output($output);
	}

	public function role()
	{
		$crud = new grocery_CRUD();
		$crud->set_table('role');

		$crud->set_subject('Role', 'Daftar Role');
		$crud->set_theme('datatables');

		$output = $crud->render();

		$this->_example_output($output);
	}

	public function user()
	{
		$crud = new grocery_CRUD();
		$crud->set_table('user');

		$crud->set_subject('User', 'Daftar User');
		$crud->set_theme('datatables');
		$crud->set_relation('role_id', 'role', 'role');
		$crud->field_type('password', 'password');
		$crud->display_as('role_id', 'Role');
		$crud->callback_before_insert(array($this, 'encrypt_password_callback'));
		$crud->unset_columns(['password', 'created_at', 'updated_at']);
		// $crud->unset_edit_fields(['password']);
		$crud->callback_edit_field('password', function ($value, $primary_key) {
			return '<input type="password" maxlength="50" value="" name="password">';
		});
		$crud->callback_before_update(array($this, 'update_password_callback'));
		$crud->unset_fields(['created_at', 'updated_at']);

		$output = $crud->render();

		$this->_example_output($output);
	}

	function encrypt_password_callback($post_array)
	{
		$post_array['password'] = md5($post_array['password']);
		return $post_array;
	}

	function update_password_callback($post_array, $primary_key)
	{
		//Encrypt password only if is not empty. Else don't change the password to an empty field
		if (!empty($post_array['password'])) {
			$post_array['password'] = md5($post_array['password']);
		} else {
			unset($post_array['password']);
		}

		return $post_array;
	}

	public function provinsi()
	{
		$crud = new grocery_CRUD();
		$crud->set_table('provinsi');

		$crud->set_subject('Provinsi', 'Daftar Provinsi');
		$crud->set_theme('datatables');
		$crud->callback_column('provinsi', array($this, '_callback_webpage_provinsi'));

		$output = $crud->render();

		$this->_example_output($output);
	}

	public function _callback_webpage_provinsi($value, $row)
	{
		return "<a href='" . site_url('perpustakaan/kabupaten/' . $row->id) . "'>$value</a>";
	}

	public function kabupaten($provinsi_id)
	{
		$crud = new grocery_CRUD();
		$crud->set_table('kabupaten');

		$crud->set_subject('Kabupaten', 'Daftar Kabupaten');
		$crud->set_theme('datatables');
		$crud->callback_column('kabupaten', function($value, $row){
			return "<a href='" . site_url('perpustakaan/kecamatan/' . $row->id) . "'>$value</a>";
		});
		$crud->where('provinsi_id', $provinsi_id);
		$crud->display_as('provinsi_id', 'Provinsi');
		$crud->fields('kabupaten', 'provinsi_id');
		$crud->field_type('provinsi_id', 'hidden', $provinsi_id);
		// $crud->set_relation('provinsi_id', 'provinsi', 'provinsi');
		$crud->callback_column('provinsi_id', function ($value, $row) {
			$res = $this->db->get_where('provinsi', ['id' => $value])->row();
			return $res->provinsi;
		});

		$output = $crud->render();

		$this->_example_output($output);
	}
}
