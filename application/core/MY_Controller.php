<?php

class My_Controller extends CI_Controller
{
	var $user = FALSE;
	var $client = FALSE;
	var $core_settings = FALSE;
	// Theme functionality
	protected $theme_view = 'application';
	protected $content_view = '';
	protected $view_data = array();
	
	function __construct()
	{
		
		parent::__construct();

		$this->view_data['core_settings'] = Setting::first();
		$this->view_data['datetime'] = date('Y-m-d H:i', time());
		if($this->input->cookie('fc2language') != ""){ $language = $this->input->cookie('fc2language');}else{ if(isset($this->view_data['language'])){$language = $this->view_data['language'];}else{
			if(!empty($this->view_data['core_settings']->language)){$language = $this->view_data['core_settings']->language; }else{ $language = "english"; }
		}}
		$this->lang->load('application', $language);
		$this->lang->load('messages', $language);
		$this->lang->load('event', $language);
		$this->user = $this->session->userdata('user_id') ? User::find_by_id($this->session->userdata('user_id')) : FALSE;
		$this->client = $this->session->userdata('client_id') ? Client::find_by_id($this->session->userdata('client_id')) : FALSE;


		if($this->user || $this->client){

			//check if user or client
			if($this->user){
				$access = $this->user->access;
				$access = explode(",", $access);
				$update = User::find($this->user->id); 
				$email = 'u'.$this->user->id;
				$this->view_data['menu'] = Module::find('all', array('order' => 'sort asc', 'conditions' => array('id in (?) AND type = ?', $access, 'main')));
				$this->view_data['widgets'] = Module::find('all', array('conditions' => array('id in (?) AND type = ?', $access, 'widget')));
				$this->view_data['user_online'] = User::all(array('conditions' => array('last_active+(30 * 60) > ? AND status = ?', time(), "active")));
				$this->view_data['client_online'] = Client::all(array('conditions' => array('last_active+(30 * 60) > ? AND inactive = ?', time(), "0")));
				$this->view_data['quotations_new'] = Quote::find_by_sql("select count(id) as amount from quotations where status='New'");
				$this->view_data['sticky'] = Project::find_by_sql("select distinct(projects.name), projects.id, projects.progress from projects, project_has_workers where projects.sticky = 1 AND projects.id = project_has_workers.project_id AND project_has_workers.user_id=".$this->user->id);
				$this->view_data['tickets_new'] = Ticket::find_by_sql("select count(id) as amount from tickets where `status`='New'");
			

			}else{
				$this->theme_view = 'application_client';
				$access = $this->client->access;
				$access = explode(",", $access);
				$email = 'c'.$this->client->id;
				$this->view_data['menu'] = Module::find('all', array('order' => 'sort asc', 'conditions' => array('id in (?) AND type = ?', $access, 'client')));
				$update = Client::find($this->client->id);
				$this->view_data['estimates_new'] = Invoice::find_by_sql("select count(id) as amount from invoices where (`estimate_status`='Sent' or `estimate_status`='Revised') and company_id =".$this->client->company_id);

				
			}

			//Update user last active
			$update->last_active = time();
			$update->save();

			$this->view_data['messages_new'] = Privatemessage::find_by_sql("select count(id) as amount from privatemessages where `status`='New' AND recipient = '".$email."'");
				
		}

		/*$this->load->database();
		$sql = "select * FROM templates WHERE type='notes'";
		$query = $this->db->query($sql); */
		$this->view_data["note_templates"] = "";//$query->result();
		
	}
	
	public function _output($output)
	{
		// set the default content view
		if($this->content_view !== FALSE && empty($this->content_view)) $this->content_view = $this->router->class . '/' . $this->router->method;
		//render the content view
		$yield = file_exists(APPPATH . 'views/' . $this->view_data['core_settings']->template . '/' . $this->content_view . EXT) ? $this->load->view($this->view_data['core_settings']->template . '/' . $this->content_view, $this->view_data, TRUE) : FALSE;

		//render the theme
		if($this->theme_view)
			echo $this->load->view($this->view_data['core_settings']->template . '/' .'theme/' . $this->theme_view, array('yield' => $yield), TRUE);
		else 
			echo $yield;
		
		echo $output;

		
	}
}
