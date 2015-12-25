<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Example extends CI_Controller {

	public function index(){
		$this->load->view('layout');
	}

	public function template_list(){
		$this->load->view('list');
	}

	public function template_detail(){
		$this->load->view('detail');
	}

}
