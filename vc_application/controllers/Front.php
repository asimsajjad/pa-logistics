<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Front extends CI_Controller {
    public function __construct()
 {
  parent::__construct();
  
	 $this->load->library('session');
   $this->load->helper('url');
$this->load->model('Login_model'); 
$this->load->library('form_validation');
$this->load->library('cart');
$this->load->helper('headerdata');
  $this->load->database();

 
   
 }
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	 
	 
	public function index()
	{
	    	 
      redirect(base_url('AdminLogin'));
	

		$data['page_slug'] = 'home';
		$data['page_title'] = 'big manual';
		$data['page_keywords'] = '';
		$data['page_description'] = '';
		


			$data['main_content'] = 'front';
			$this->load->view('includes/front/template', $data);
	}
	
}
