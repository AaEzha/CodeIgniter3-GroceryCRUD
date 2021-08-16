<?php
defined('BASEPATH') or exit('No direct script access allowed');

include(APPPATH . 'libraries/GroceryCrudEnterprise/autoload.php');

use GroceryCrud\Core\GroceryCrud;

class User extends CI_Controller
{
	private function _getDbData()
	{
		$db = [];
		include(APPPATH . 'config/database.php');
		return [
			'adapter' => [
				'driver' => 'Pdo_Mysql',
				'host'     => $db['default']['hostname'],
				'database' => $db['default']['database'],
				'username' => $db['default']['username'],
				'password' => $db['default']['password'],
				'charset' => 'utf8'
			]
		];
	}

	private function _getGroceryCrudEnterprise($bootstrap = true, $jquery = true)
	{
		$db = $this->_getDbData();
		$config = include(APPPATH . 'config/gcrud-enterprise.php');
		$groceryCrud = new GroceryCrud($config, $db);
		return $groceryCrud;
	}

	function _example_output($output = null)
	{
		if (isset($output->isJSONResponse) && $output->isJSONResponse) {
			header('Content-Type: application/json; charset=utf-8');
			echo $output->output;
			exit;
		}

		$this->load->view('example.php', $output);
	}

	public function index()
	{
		$crud = $this->_getGroceryCrudEnterprise();
		$crud->setTable('user');
		$crud->setSubject('User', 'Users');
		// $crud->columns(['customerName', 'country', 'state', 'addressLine1']);

		$crud->setRelation('role_id','role','role');
		$crud->setRelation('provinsi_id','provinsi','provinsi');
		$crud->setRelation('kabupaten_id','kabupaten','kabupaten');
		$crud->setRelation('kecamatan_id','kecamatan','kecamatan');
		$crud->setRelation('kelurahan_id','kelurahan','kelurahan');

		$crud->setDependentRelation('kabupaten_id', 'provinsi_id', 'provinsi_id');
		$crud->setDependentRelation('kecamatan_id', 'kabupaten_id', 'kabupaten_id');
		$crud->setDependentRelation('kelurahan_id', 'kecamatan_id', 'kecamatan_id');

		$crud->displayAs([
			'provinsi_id' => 'Provinsi',
			'kabupaten_id' => 'Kabupaten',
			'kecamatan_id' => 'Kecamatan',
			'kelurahan_id' => 'Kelurahan',
			'role_id' => 'Role'
		]);

		$crud->unsetColumns(['password', 'created_at', 'updated_at']);
		$crud->unsetFields(['created_at', 'updated_at']);

		$crud->fieldType('password', 'password');
		$crud->callbackEditField('password', function ($fieldValue, $primaryKeyValue, $rowData) {
			return '<input name="password" class="form-control" type="password" value=""  />';
		});

		$crud->callbackBeforeUpdate(function ($s) {
			if (!empty($s->data['password'])) {
				$s->data['password'] = md5($s->data['password']);
			} else {
				unset($s->data['password']);
			}
		
			return $s;
		});

		$output = $crud->render();
		$this->_example_output($output);
	}
}
