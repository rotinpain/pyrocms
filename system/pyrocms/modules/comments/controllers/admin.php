<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @author 		Phil Sturgeon - PyroCMS Dev Team
 * @package 	PyroCMS
 * @subpackage 	Comments
 * @category 	Module
 **/
class Admin extends Admin_Controller
{
	/**
	 * Array that contains the validation rules
	 * @access private
	 * @var array
	 */
	private $validation_rules = array();
	
	/**
	 * Constructor method
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		// Call the parent constructor
		parent::Admin_Controller();
		
		// Load the required libraries, models, etc
		$this->load->library('form_validation');
		$this->load->model('comments_m');
		$this->lang->load('comments');
		
		// Set the validation rules
		$this->validation_rules = array(
			array(
				'field' => 'name',
				'label'	=> lang('comments.name_label'),
				'rules'	=> 'trim'
			),
			array(
				'field'	=> 'email',
				'label' => lang('comments.email_label'),
				'rules'	=> 'trim|valid_email'
			),
			array(
				'field'	=> 'website',
				'label' => lang('comments.website_label'),
				'rules'	=> 'trim'
			),
			array(
				'field'	=> 'comment',
				'label' => lang('comments.send_label'),
				'rules'	=> 'trim|required'
			),
		);
		
	    $this->template->set_partial('shortcuts', 'admin/partials/shortcuts');
	
		// Set the validation rules
		$this->form_validation->set_rules($this->validation_rules);
	}
	
	/**
	 * Index
	 * @access public
	 * @return void
	 */
	public function index()
	{
		// If we are moderating comments, show unmoderated comments
		$this->settings->moderate_comments
			? $this->unapproved()
			: $this->approved();
	}

	public function unapproved()
	{
		// Load the text helper
		$this->load->helper('text');
		
		// Create pagination links
		$total_rows 			= $this->comments_m->count_by('is_active', 0);
		$this->data->pagination = create_pagination('admin/comments/unapproved', $total_rows);
		
		// get all comments
		$comments = $this->comments_m
			->limit($this->data->pagination['limit'])
			->order_by('comments.created_on', 'desc')
			->get_many_by('comments.is_active', 0);
		
		$this->data->comments = process_comment_items($comments);
		$this->template->build('admin/index', $this->data);			
	}

	/**
	 * Displays active comments
	 * @access public
	 * @return void
	 */
	public function approved()
	{
		$this->load->helper('text');

		// Create pagination links
		$total_rows 			= $this->comments_m->count_by('is_active', 1);
		$this->data->pagination = create_pagination('admin/comments/approved', $total_rows);

		// get all comments
		$comments = $this->comments_m
			->limit($this->data->pagination['limit'])
			->order_by('comments.created_on', 'desc')
			->get_many_by('comments.is_active', 1);

		$this->data->comments 	= process_comment_items($comments);

		$this->template->build('admin/index', $this->data);
	}
	
	/**
	 * Action method, called whenever the user submits the form
	 * @access public
	 * @return void
	 */
	public function action()
	{
		if( $this->input->post('btnAction') )
		{
			// Get the action
			$id_array = $this->input->post('action_to');
			
			// Switch statement
			switch( strtolower( $this->input->post('btnAction') ) )
			{
				// Approve the comment
				case 'approve':
					// Loop through each ID
					foreach($id_array as $key => $value)
					{
						// Multiple ones ? 
						if(count($id_array) > 1)
						{
							$this->approve($value,FALSE,TRUE);
						}
						else
						{
							$this->approve($value,FALSE);
						}
					}
				break;
				// Unapprove the comment
				case 'unapprove':
					// Loop through each ID
					foreach($id_array as $key => $value)
					{
						// Multiple ones ? 
						if(count($id_array) > 1)
						{
							$this->unapprove($value,FALSE,TRUE);
						}
						else
						{
							$this->unapprove($value,FALSE);
						}
					}
				break;
				// Delete the comment
				case 'delete':
					$this->delete();
				break;
			}
			
			// Redirect
			redirect('admin/comments');
		}
		
	}
		
	/**
	 * Edit an existing comment
	 * @access public
	 * @return void
	 */
	public function edit($id = 0)
	{
		// Redirect if no ID has been specified
		if (!$id)
		{
			redirect('admin/comments');
		}

		// Get the comment based on the ID
		$comment = $this->comments_m->get($id);
		
		// Validate the results
		if ($this->form_validation->run())
		{		
			if($comment->user_id > 0)
			{
				$commenter['user_id'] 	= $this->input->post('user_id');
			}
			else
			{
				$commenter['name'] 		= $this->input->post('name');
				$commenter['email'] 	= $this->input->post('email');
			}
			
			$comment = array_merge($commenter, array(
				'comment'    	=> $this->input->post('comment'),
				'website'    	=> $this->input->post('website'),
				'module'   		=> $this->input->post('module'),
				'module_id' 	=> $this->input->post('module_id')
			));
			
			// Update the comment
			if($this->comments_m->update($id, $comment))
			{
				$this->session->set_flashdata('success', lang('comments.edit_success'));
			}
			else
			{
				$this->session->set_flashdata('error', lang('comments.edit_error'));
			}
			
			// Redirect the user
			redirect('admin/comments');
		}

		// Loop through each rule
		foreach($this->validation_rules as $rule)
		{
			if($this->input->post($rule['field']) !== FALSE)
			{
				$comment->{$rule['field']} = $this->input->post($rule['field']);
			}
		}

		$this->data->comment =& $comment;
		
		// Load WYSIWYG editor
		$this->template->append_metadata( $this->load->view('fragments/wysiwyg', $this->data, TRUE) );		
		$this->template->build('admin/form', $this->data);
	}	
		
	// Admin: Delete a comment
	public function delete($id = 0)
	{
		// Delete one
		$ids = ($id) ? array($id) : $this->input->post('action_to');
		
		// Go through the array of ids to delete
		$comments = array();
		foreach ($ids as $id)
		{
			// Get the current comment so we can grab the id too
			if($comment = $this->comments_m->get($id))
			{
				$this->comments_m->delete($id);
				
				// Wipe cache for this model, the content has changed
				$this->cache->delete('comment_m');				
				$comments[] = $comment->id;
			}
		}
		
		// Some comments have been deleted
		if(!empty($comments))
		{
			// Only deleting one comment
			if(count( $comments ) == 1)
			{
				$this->session->set_flashdata( 'success', sprintf(lang('comments.delete_single_success'), $comments[0]) );
			}			
			// Deleting multiple comments
			else
			{
				$this->session->set_flashdata( 'success', sprintf( lang('comments.delete_multi_success'), implode( ', #', $comments ) ) );
			}
		}
		
		// For some reason, none of them were deleted
		else
		{
			$this->session->set_flashdata( 'error', lang('comments.delete_error') );
		}
			
		redirect('admin/comments');
	}
	
	// Admin: activate a comment
	public function approve($id = 0, $redirect = TRUE, $multiple = FALSE)
	{
		if (!$id)
		{
			redirect('admin/comments');
		}
					
		if($this->comments_m->approve($id))
		{
			// Unapprove multiple comments ? 
			if($multiple == TRUE)
			{
				$this->session->set_flashdata( array('success'=> lang('comments.approve_success_multiple')));
			}
			else
			{
				$this->session->set_flashdata( array('success'=> lang('comments.approve_success')));
			}
		}
		
		else
		{
			// Error for multiple comments ? 
			if($multiple == TRUE)
			{
				$this->session->set_flashdata( array('error'=> lang('comments.approve_error_multiple')) );
			}
			else
			{
				$this->session->set_flashdata( array('error'=> lang('comments.approve_error')) );
			}
		}
		
		if($redirect == TRUE)
		{
			redirect('admin/comments');	
		}		
	}
	
	// Admin: deativate a comment
	public function unapprove($id = 0,$redirect = TRUE,$multiple = FALSE)
	{
		if (!$id)
		{
			redirect('admin/comments');
		}
					
		if($this->comments_m->unapprove($id))
		{
			// Unapprove multiple comments ? 
			if($multiple == TRUE)
			{
				$this->session->set_flashdata( array('success'=> lang('comments.unapprove_success_multiple')) );
			}
			
			else
			{
				$this->session->set_flashdata( array('success'=> lang('comments.unapprove_success')) );	
			}			
		}
		
		else
		{
			// Error for multiple comments ? 
			if($multiple == TRUE)
			{
				$this->session->set_flashdata( array('error'=> lang('comments.unapprove_error_multiple')) );
			}
			
			else
			{
				$this->session->set_flashdata( array('error'=> lang('comments.unapprove_error')) );
			}
		}
		
		if($redirect == TRUE)
		{
			redirect('admin/comments');	
		}
	}
	
	public function preview($id = 0)
	{		
		$this->data->comment = $this->comments_m->get($id);
		$this->template->set_layout(FALSE);
		$this->template->build('admin/preview', $this->data);
	}
}

?>