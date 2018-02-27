<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Projects extends MY_Controller {
               
	function __construct()
	{
		parent::__construct();
		$access = FALSE;
		if($this->client){	
			if($this->input->cookie('fc2_link') != ""){
					$link = $this->input->cookie('fc2_link');
					$link = str_replace("/tickets/", "/ctickets/", $link);
					redirect($link);
			}else{
				redirect('cprojects');
			}
			
		}elseif($this->user){
			$this->view_data['invoice_access'] = FALSE;
			foreach ($this->view_data['menu'] as $key => $value) { 
				if($value->link == "invoices"){ $this->view_data['invoice_access'] = TRUE;}
				if($value->link == "projects"){ $access = TRUE;}
			}
			if(!$access){redirect('login');}
		}else{
			redirect('login');
		}
		$this->view_data['submenu'] = array(
				 		$this->lang->line('application_all') => 'projects',
				 		$this->lang->line('application_open') => 'projects/filter/open',
				 		$this->lang->line('application_closed') => 'projects/filter/closed'
				 		);	
		$this->load->database();
		
	}	
	function index()
	{
		$this->view_data['project'] = Project::all();
		$this->content_view = 'projects/all';
		$this->view_data['projects_assigned_to_me'] = ProjectHasWorker::find_by_sql('select count(distinct(projects.id)) AS "amount" FROM projects, project_has_workers WHERE projects.progress != "100" AND (projects.id = project_has_workers.project_id AND project_has_workers.user_id = "'.$this->user->id.'") ');
		$this->view_data['tasks_assigned_to_me'] = ProjectHasTask::count(array('conditions' => 'user_id = '.$this->user->id.' and status = "open"'));

		$now = time();
		$beginning_of_week = strtotime('last Monday', $now); // BEGINNING of the week
		$end_of_week = strtotime('next Sunday', $now) + 86400; // END of the last day of the week
		$this->view_data['projects_opened_this_week'] = Project::find_by_sql('select count(id) AS "amount", DATE_FORMAT(FROM_UNIXTIME(`datetime`), "%w") AS "date_day", DATE_FORMAT(FROM_UNIXTIME(`datetime`), "%Y-%m-%d") AS "date_formatted" from projects where datetime >= "'.$beginning_of_week.'" AND datetime <= "'.$end_of_week.'" ');

	}
	function filter($condition)
	{
		switch ($condition) {
			case 'open':
				$options = array('conditions' => 'progress < 100');
				break;
			case 'closed':
				$options = array('conditions' => 'progress = 100');
				break;
		}
		
		$this->view_data['project'] = Project::all($options);
		$this->content_view = 'projects/all';

		$this->view_data['projects_assigned_to_me'] = ProjectHasWorker::find_by_sql('select count(distinct(projects.id)) AS "amount" FROM projects, project_has_workers WHERE projects.progress != "100" AND (projects.id = project_has_workers.project_id AND project_has_workers.user_id = "'.$this->user->id.'") ');
		$this->view_data['tasks_assigned_to_me'] = ProjectHasTask::count(array('conditions' => 'user_id = '.$this->user->id.' and status = "open"'));
		
		$now = time();
		$beginning_of_week = strtotime('last Monday', $now); // BEGINNING of the week
		$end_of_week = strtotime('next Sunday', $now) + 86400; // END of the last day of the week
		$this->view_data['projects_opened_this_week'] = Project::find_by_sql('select count(id) AS "amount", DATE_FORMAT(FROM_UNIXTIME(`datetime`), "%w") AS "date_day", DATE_FORMAT(FROM_UNIXTIME(`datetime`), "%Y-%m-%d") AS "date_formatted" from projects where datetime >= "'.$beginning_of_week.'" AND datetime <= "'.$end_of_week.'" ');

	}
	function create()
	{	
		if($_POST){
			unset($_POST['send']);
			$_POST['datetime'] = time();
			$_POST = array_map('htmlspecialchars', $_POST);
			unset($_POST['files']);

			$project = Project::create($_POST);
			$new_project_reference = $_POST['reference']+1;
			$project_reference = Setting::first();
			$project_reference->update_attributes(array('project_reference' => $new_project_reference));
       		if(!$project){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_create_project_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_create_project_success'));
       			$project_last = Project::last();
       			$sql = "INSERT INTO `project_has_workers` (`project_id`, `user_id`) VALUES (".$project_last->id.", ".$this->user->id.")";
				$query = $this->db->query($sql);
       			}
			redirect('projects');
		}else
		{
			$this->view_data['companies'] = Company::find('all',array('conditions' => array('inactive=?','0')));
			$this->view_data['next_reference'] = Project::last();
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_create_project');
			$this->view_data['form_action'] = 'projects/create';
			$this->content_view = 'projects/_project';
		}	
	}	
	function update($id = FALSE)
	{	
		if($_POST){
			unset($_POST['send']);
			$id = $_POST['id'];
			unset($_POST['files']);
			$_POST = array_map('htmlspecialchars', $_POST);
			if (!isset($_POST["progress_calc"])) {
				$_POST["progress_calc"] = 0;
			}
			$project = Project::find($id);
			$project->update_attributes($_POST);
       		if(!$project){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_project_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_project_success'));}
			redirect('projects/view/'.$id);
		}else
		{
			$this->view_data['companies'] = Company::find('all',array('conditions' => array('inactive=?','0')));
			$this->view_data['project'] = Project::find($id);
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_edit_project');
			$this->view_data['form_action'] = 'projects/update';
			$this->content_view = 'projects/_project';
		}	
	}	
	function assign($id = FALSE)
	{	
		$this->load->helper('notification');
		if($_POST){
			unset($_POST['send']);
			$id = addslashes($_POST['id']);
			$project = Project::find_by_id($id);
			$sql = "SELECT user_id FROM project_has_workers WHERE project_id=".$id;
			$query = $this->db->query($sql);
			$query = $query->result_array();
			foreach($query as $k => $a) {
    			if (is_array($a)) { $query[$k] = $a['user_id']; }
			}

			$added = array_diff($_POST["user_id"], $query);
			$removed = array_diff($query, $_POST["user_id"]);

			foreach ($added as $value){
			$value = htmlspecialchars(addslashes($value));
			$sql = "INSERT INTO `project_has_workers` (`project_id`, `user_id`) VALUES (".$id.", ".$value.")";
			$query = $this->db->query($sql);
			$receiver = User::find_by_id($value);
			send_notification($receiver->email, $this->lang->line('application_notification_project_assign_subject'), $this->lang->line('application_notification_project_assign').'<br><strong>'.$project->name.'</strong>');
			}

			foreach ($removed as $value){
			$sql = "DELETE FROM `project_has_workers` WHERE user_id = ".$value." AND project_id=".$id;
			$query = $this->db->query($sql);
			//$receiver = User::find_by_id($value);
			}

       		if(!$query){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_project_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_project_success'));}
			redirect('projects/view/'.$id);
		}else
		{
			$this->view_data['users'] = User::find('all',array('conditions' => array('status=?','active')));
			$this->view_data['project'] = Project::find($id);
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_assign_to_agents');
			$this->view_data['form_action'] = 'projects/assign';
			$this->content_view = 'projects/_assign';
		}	
	}	
	function delete($id = FALSE)
	{	
		$project = Project::find($id);
		$project->delete();
		$sql = 'DELETE FROM project_has_tasks WHERE project_id = "'.$id.'"';
		$this->db->query($sql);
		$this->content_view = 'projects/all';
		if(!$project){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_delete_project_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_delete_project_success'));}
			if(isset($view)){redirect('projects/view/'.$id);}else{redirect('projects');}
	}
	function timer_reset($id = FALSE){
		$project = Project::find($id);
		$attr = array('time_spent' => '0');
		$project->update_attributes($attr);
		$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_timer_reset'));
		redirect('projects/view/'.$id);
	}
	function timer_set($id = FALSE){
		if($_POST){
		$project = Project::find_by_id($_POST['id']);
		$hours = $_POST['hours'];
		$minutes = $_POST['minutes'];
		$timespent = ($hours*60*60)+($minutes*60);
		$attr = array('time_spent' => $timespent);
		$project->update_attributes($attr);
		$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_timer_set'));
		redirect('projects/view/'.$_POST['id']);
		}else{
			$this->view_data['project'] = Project::find($id);
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_timer_set');
			$this->view_data['form_action'] = 'projects/timer_set';
			$this->content_view = 'projects/_timer';
		}
	}
	function view($id = FALSE)
	{ 
		$this->view_data['submenu'] = array();
		$this->view_data['project'] = Project::find($id);
		$this->view_data['project_has_invoices'] = Invoice::all(array('conditions' => array('project_id = ? AND estimate != ?', $id, 1)));
		if(!isset($this->view_data['project_has_invoices'])){$this->view_data['project_has_invoices'] = array();}
		$tasks = ProjectHasTask::count(array('conditions' => 'project_id = '.$id));
		$this->view_data['alltasks'] = $tasks;
		$this->view_data['opentasks'] = ProjectHasTask::count(array('conditions' => array('status != ? AND project_id = ?', 'done', $id)));
		$this->view_data['usercountall'] = User::count(array('conditions' => array('status = ?', 'active')));
		$this->view_data['usersassigned'] = ProjectHasWorker::count(array('conditions' => array('project_id = ?', $id)));

		$this->view_data['assigneduserspercent'] = round($this->view_data['usersassigned']/$this->view_data['usercountall']*100);
		


		$this->view_data['time_days'] = round((human_to_unix($this->view_data['project']->end.' 00:00') - human_to_unix($this->view_data['project']->start.' 00:00')) / 3600 / 24);
		$this->view_data['time_left'] = $this->view_data['time_days'];
		$this->view_data['timeleftpercent'] = 100;

		if(human_to_unix($this->view_data['project']->start.' 00:00') < time() && human_to_unix($this->view_data['project']->end.' 00:00') > time()){
			$this->view_data['time_left'] = round((human_to_unix($this->view_data['project']->end.' 00:00') - time()) / 3600 / 24);
			$this->view_data['timeleftpercent'] = $this->view_data['time_left']/$this->view_data['time_days']*100;
		}
		if(human_to_unix($this->view_data['project']->end.' 00:00') < time()){
			$this->view_data['time_left'] = 0;
			$this->view_data['timeleftpercent'] = 0;
		}
		$this->view_data['mytasks'] = ProjectHasTask::count(array('conditions' => array('status != ? AND project_id = ? AND user_id = ?', 'done', $id, $this->user->id)));
		$tasks_done = ProjectHasTask::count(array('conditions' => array('status = ? AND project_id = ?', 'done', $id)));
		$this->view_data['progress'] = $this->view_data['project']->progress;
		if($this->view_data['project']->progress_calc == 1){
			if ($tasks) {@$this->view_data['progress'] = round($tasks_done/$tasks*100);}
			$attr = array('progress' => $this->view_data['progress']);
			$this->view_data['project']->update_attributes($attr);
		}
		@$this->view_data['opentaskspercent'] = ($tasks == 0 ? 0 : $tasks_done/$tasks*100);
		$projecthasworker = ProjectHasWorker::all(array('conditions' => array('user_id = ? AND project_id = ?', $this->user->id, $id)));
		if(!$projecthasworker && $this->user->admin != 1){ 
				$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_no_access_error'));
				redirect('projects'); 
		}
		$tracking = $this->view_data['project']->time_spent;
		if(!empty($this->view_data['project']->tracking)){ $tracking=(time()-$this->view_data['project']->tracking)+$this->view_data['project']->time_spent; }
		$this->view_data['timertime'] = $tracking;
		$this->view_data['time_spent_from_today'] = time() - $this->view_data['project']->time_spent;	
		$tracking = floor($tracking/60);
		$tracking_hours = floor($tracking/60);
		$tracking_minutes = $tracking-($tracking_hours*60);

		

		$this->view_data['time_spent'] = $tracking_hours." ".$this->lang->line('application_hours')." ".$tracking_minutes." ".$this->lang->line('application_minutes');
		$this->view_data['time_spent_counter'] = sprintf("%02s", $tracking_hours).":".sprintf("%02s", $tracking_minutes);

		$this->content_view = 'projects/view';

	}
	function tasks($id = FALSE, $condition = FALSE, $task_id = FALSE)
	{
		$this->view_data['submenu'] = array(
								$this->lang->line('application_back') => 'projects',
								$this->lang->line('application_overview') => 'projects/view/'.$id,
						 		);
		switch ($condition) {
			case 'add':
				$this->content_view = 'projects/_tasks';
				if($_POST){
					unset($_POST['send']);
					unset($_POST['files']);
					$description = $_POST['description'];
					$_POST = array_map('htmlspecialchars', $_POST);
					$_POST['description'] = $description;
					$_POST['project_id'] = $id;
					$task = ProjectHasTask::create($_POST);
		       		if(!$task){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_task_error'));}
		       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_task_success'));}
					redirect('projects/view/'.$id);
				}else
				{
					$this->theme_view = 'modal';
					$this->view_data['project'] = Project::find($id);
					$this->view_data['title'] = $this->lang->line('application_add_task');
					$this->view_data['form_action'] = 'projects/tasks/'.$id.'/add';
					$this->content_view = 'projects/_tasks';
				}	
				break;
			case 'update':
				$this->content_view = 'projects/_tasks';
				$this->view_data['task'] = ProjectHasTask::find($task_id);
				if($_POST){
					unset($_POST['send']);
					unset($_POST['files']);
					if(!isset($_POST['public'])){$_POST['public'] = 0;}
					$description = $_POST['description'];
					$_POST = array_map('htmlspecialchars', $_POST);
					$_POST['description'] = $description;
					$task_id = $_POST['id'];
					$task = ProjectHasTask::find($task_id);
					$task->update_attributes($_POST);
		       		if(!$task){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_task_error'));}
		       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_task_success'));}
					redirect('projects/view/'.$id);
				}else
				{
					$this->theme_view = 'modal';
					$this->view_data['project'] = Project::find($id);
					$this->view_data['title'] = $this->lang->line('application_edit_task');
					$this->view_data['form_action'] = 'projects/tasks/'.$id.'/update/'.$task_id;
					$this->content_view = 'projects/_tasks';
				}	
				break;
			case 'check':
					$task = ProjectHasTask::find($task_id);
					if ($task->status == 'done'){$task->status = 'open';}else{$task->status = 'done';}
					$task->save();
					$project = Project::find($id);
					$tasks = ProjectHasTask::count(array('conditions' => 'project_id = '.$id));
					$tasks_done = ProjectHasTask::count(array('conditions' => array('status = ? AND project_id = ?', 'done', $id)));
					if($project->progress_calc == 1){
						if ($tasks) {$progress = round($tasks_done/$tasks*100);}
						$attr = array('progress' => $progress);
						$project->update_attributes($attr);
					}
		       		if(!$task){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_task_error'));}
		       		$this->theme_view = 'ajax';
		       		$this->content_view = 'projects';
				break;
			case 'delete':
					$task = ProjectHasTask::find($task_id);
					$task->delete();
		       		if(!$task){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_delete_task_error'));}
		       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_delete_task_success'));}
					redirect('projects/view/'.$id);
				break;
			default:
				$this->view_data['project'] = Project::find($id);
				$this->content_view = 'projects/tasks';
				break;
		}

	}
	function notes($id = FALSE)
	{	
		if($_POST){
			unset($_POST['send']);
			$_POST = array_map('htmlspecialchars', $_POST);
			$_POST['note'] = strip_tags($_POST['note']);
			$project = Project::find($id);
			$project->update_attributes($_POST);
		}
		$this->theme_view = 'ajax';
	}	
	function media($id = FALSE, $condition = FALSE, $media_id = FALSE)
	{
	    $this->load->helper('notification');
		$this->view_data['submenu'] = array(
								$this->lang->line('application_back') => 'projects',
								$this->lang->line('application_overview') => 'projects/view/'.$id,
						 		$this->lang->line('application_tasks') => 'projects/tasks/'.$id,
						 		$this->lang->line('application_media') => 'projects/media/'.$id,
						 		);
		switch ($condition) {
			case 'view':

				if($_POST){
					unset($_POST['send']);
					unset($_POST['_wysihtml5_mode']);
					unset($_POST['files']);
					//$_POST = array_map('htmlspecialchars', $_POST);
					$_POST['text'] = $_POST['message'];
					unset($_POST['message']);
					$_POST['project_id'] = $id;
					$_POST['media_id'] = $media_id;
					$_POST['from'] = $this->user->firstname.' '.$this->user->lastname;
					$this->view_data['project'] = Project::find_by_id($id);
					$this->view_data['media'] = ProjectHasFile::find($media_id);
					$message = Message::create($_POST);
       				if(!$message){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_message_error'));}
       				else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_message_success'));

       					foreach ($this->view_data['project']->project_has_workers as $workers){
            			    send_notification($workers->user->email, "[".$this->view_data['project']->name."] New comment", 'New comment on media file: '.$this->view_data['media']->name.'<br><strong>'.$this->view_data['project']->name.'</strong>');
            			}
            			if(isset($this->view_data['project']->company->client->email)){
            				$access = explode(',', $this->view_data['project']->company->client->access); 
            				if(in_array('12', $access)){
            					send_notification($this->view_data['project']->company->client->email, "[".$this->view_data['project']->name."] New comment", 'New comment on media file: '.$this->view_data['media']->name.'<br><strong>'.$this->view_data['project']->name.'</strong>');
            				}
            			}
       				}
       				redirect('projects/media/'.$id.'/view/'.$media_id);
				}
				$this->content_view = 'projects/view_media';
				$this->view_data['media'] = ProjectHasFile::find($media_id);
				$this->view_data['form_action'] = 'projects/media/'.$id.'/view/'.$media_id;
				$this->view_data['filetype'] = explode('.', $this->view_data['media']->filename);
				$this->view_data['filetype'] = $this->view_data['filetype'][1];
				$this->view_data['backlink'] = 'projects/view/'.$id;
				break;
			case 'add':
				$this->content_view = 'projects/_media';
				$this->view_data['project'] = Project::find($id);
				if($_POST){
					$config['upload_path'] = './files/media/';
					$config['encrypt_name'] = TRUE;
					$config['allowed_types'] = '*';

					$this->load->library('upload', $config);

					if ( ! $this->upload->do_upload())
						{
							$error = $this->upload->display_errors('', ' ');
							$this->session->set_flashdata('message', 'error:'.$error);
							redirect('projects/media/'.$id);
						}
						else
						{
							$data = array('upload_data' => $this->upload->data());

							$_POST['filename'] = $data['upload_data']['orig_name'];
							$_POST['savename'] = $data['upload_data']['file_name'];
							$_POST['type'] = $data['upload_data']['file_type'];
						}

					unset($_POST['send']);
					unset($_POST['userfile']);
					unset($_POST['file-name']);
					unset($_POST['files']);
					$_POST = array_map('htmlspecialchars', $_POST);
					$_POST['project_id'] = $id;
					$_POST['user_id'] = $this->user->id;
					$media = ProjectHasFile::create($_POST);
		       		if(!$media){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_media_error'));}
		       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_media_success'));
		       		
		       		    $attributes = array('subject' => $this->lang->line('application_new_media_subject'), 'message' => '<b>'.$this->user->firstname.' '.$this->user->lastname.'</b> '.$this->lang->line('application_uploaded'). ' '.$_POST['name'], 'datetime' => time(), 'project_id' => $id, 'type' => 'media', 'user_id' => $this->user->id);
					    $activity = ProjectHasActivity::create($attributes);
    		       		
    		       		foreach ($this->view_data['project']->project_has_workers as $workers){
            			    send_notification($workers->user->email, "[".$this->view_data['project']->name."] ".$this->lang->line('application_new_media_subject'), $this->lang->line('application_new_media_file_was_added').' <strong>'.$this->view_data['project']->name.'</strong>');
            			}
            			if(isset($this->view_data['project']->company->client->email)){
            				$access = explode(',', $this->view_data['project']->company->client->access); 
            				if(in_array('12', $access)){
            					send_notification($this->view_data['project']->company->client->email, "[".$this->view_data['project']->name."] ".$this->lang->line('application_new_media_subject'), $this->lang->line('application_new_media_file_was_added').' <strong>'.$this->view_data['project']->name.'</strong>');
            				}
            			}

		       		}
					redirect('projects/view/'.$id);
				}else
				{
					$this->theme_view = 'modal';
					$this->view_data['title'] = $this->lang->line('application_add_media');
					$this->view_data['form_action'] = 'projects/media/'.$id.'/add';
					$this->content_view = 'projects/_media';
				}	
				break;
			case 'update':
				$this->content_view = 'projects/_media';
				$this->view_data['media'] = ProjectHasFile::find($media_id);
				$this->view_data['project'] = Project::find($id);
				if($_POST){
					unset($_POST['send']);
					unset($_POST['_wysihtml5_mode']);
					unset($_POST['files']);
					$_POST = array_map('htmlspecialchars', $_POST);
					$media_id = $_POST['id'];
					$media = ProjectHasFile::find($media_id);
					$media->update_attributes($_POST);
		       		if(!$media){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_media_error'));}
		       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_media_success'));}
					redirect('projects/view/'.$id);
				}else
				{
					$this->theme_view = 'modal';
					$this->view_data['title'] = $this->lang->line('application_edit_media');
					$this->view_data['form_action'] = 'projects/media/'.$id.'/update/'.$media_id;
					$this->content_view = 'projects/_media';
				}	
				break;
			case 'delete':
					$media = ProjectHasFile::find($media_id);
					$media->delete();
					$this->load->database();
					$sql = "DELETE FROM messages WHERE media_id = $media_id";
					$this->db->query($sql);
		       		if(!$media){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_delete_media_error'));}
		       		else{	unlink('./files/media/'.$media->savename);
		       				$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_delete_media_success'));
		       			}
					redirect('projects/view/'.$id);
				break;
			default:
				$this->view_data['project'] = Project::find($id);
				$this->content_view = 'projects/view/'.$id;
				break;
		}

	}
	function deletemessage($project_id, $media_id, $id){
					$message = Message::find($id);
					if($message->from == $this->user->firstname." ".$this->user->lastname || $this->user->admin == "1"){
					$message->delete();
					}
		       		if(!$message){
		       			$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_delete_message_error'));
		       		}
		       		else{ 
		       			$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_delete_message_success'));
		       		}
					redirect('projects/media/'.$project_id.'/view/'.$media_id);
	}
	function tracking($id = FALSE)
	{
		$project = Project::find($id);
		if(empty($project->tracking)){
			$project->update_attributes(array('tracking' => time()));	
			
		}else{
		$timeDiff=time()-$project->tracking;
		$project->update_attributes(array('tracking' => '', 'time_spent' => $project->time_spent+$timeDiff));
		}
		redirect('projects/view/'.$id);

	}
	function sticky($id = FALSE)
	{
		$project = Project::find($id);
		if($project->sticky == 0){
			$project->update_attributes(array('sticky' => '1'));
       		$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_make_sticky_success'));	
			
		}else{
		$project->update_attributes(array('sticky' => '0'));
		$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_remove_sticky_success'));
		}
		redirect('projects/view/'.$id);

	}
	function download($media_id = FALSE){

        $this->load->helper('download');
		$media = ProjectHasFile::find($media_id);
		$media->download_counter = $media->download_counter+1;
		$media->save();
/*
		$data = file_get_contents('./files/media/'.$media->savename); 
		$name = $media->filename;
		force_download($name, $data);
*/
		$file = './files/media/'.$media->savename;

		if(file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            exit; 
        }
	}
	function activity($id = FALSE, $condition = FALSE, $activityID = FALSE)
	{
	    $this->load->helper('notification');
		$project = Project::find_by_id($id);
		//$activity = ProjectHasAktivity::find_by_id($activityID);
		switch ($condition) {
			case 'add':
				if($_POST){
					unset($_POST['send']);
					$_POST['subject'] = htmlspecialchars($_POST['subject']);
					$_POST['message'] = strip_tags($_POST['message'], '<br><br/><p></p><a></a><b></b><i></i><u></u><span></span>');
					$_POST['project_id'] = $id;
					$_POST['user_id'] = $this->user->id;
					$_POST['type'] = "comment";
					unset($_POST['files']);
					$_POST['datetime'] = time();
					$activity = ProjectHasActivity::create($_POST);
		       		if(!$activity){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_error'));}
		       		else{
		       		    $this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_success'));
		       		    foreach ($project->project_has_workers as $workers){
            			    send_notification($workers->user->email, "[".$project->name."] ".$_POST['subject'], $_POST['message'].'<br><strong>'.$project->name.'</strong>');
            			}
            			if(isset($project->company->client->email)){
            				$access = explode(',', $project->company->client->access); 
            				if(in_array('12', $access)){
            					send_notification($project->company->client->email, "[".$project->name."] ".$_POST['subject'], $_POST['message'].'<br><strong>'.$project->name.'</strong>');
            				}
            			}
		       		}
					//redirect('projects/view/'.$id);
					
				}
				break;
			case 'update':
				
				break;
			case 'delete':
				
				break;
		}

	}

}