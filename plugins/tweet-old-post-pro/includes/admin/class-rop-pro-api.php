<?php
/**
 * The PRO API calls for the plugin
 *
 * @link       https://themeisle.com/
 * @since      2.0.0
 *
 * @package    Rop_Pro
 * @subpackage Rop_Pro/includes/admin
 */

/**
 * Rop_Pro_Api Class
 *
 * @package    Rop
 * @subpackage Rop_Pro/includes/admin
 * @author     ThemeIsle <friends@themeisle.com>
 */
class Rop_Pro_Api {

	/**
	 * Stores the default response for the API.
	 *
	 * @since   8.0.0rc
	 * @access  private
	 * @var     array $response The default response.
	 */
	private $response;

	/**
	 * Rop_Rest_Api constructor.
	 *
	 * @since   8.0.0rc
	 * @access  public
	 */
	public function __construct() {
		$this->response = new Rop_Api_Response();
	}


	/**
	 * Method called by ROP API to skip a specific event.
	 *
	 * @since   8.0.0
	 * @access  public
	 *
	 * @param   array $data Data passed by the API call.
	 *
	 * @return mixed
	 */
	public function skip_queue_event( $data ) {
		$queue = new Rop_Queue_Model();
		$this->response->set_code( '500' );
		if ( $queue->skip_post( $data['post_id'], $data['account_id'] ) ) {
			$this->response->set_code( '201' )
						   ->set_data( $queue->get_ordered_queue() );
		}

		return $this->response->is_not_silent()->to_array();
	}

	/**
	 * Method called by ROP API to block a specific event.
	 *
	 * @since   8.0.0
	 * @access  public
	 *
	 * @param   array $data Data passed by the API call.
	 *
	 * @return mixed
	 */
	public function block_queue_event( $data ) {
		$queue = new Rop_Queue_Model();
		$this->response->set_code( '500' );
		if ( $queue->ban_post( $data['post_id'], $data['account_id'] ) ) {
			$this->response->set_code( '201' )
						   ->set_data( $queue->get_ordered_queue() );
		}

		return $this->response->is_not_silent()->to_array();
	}

	/**
	 * Method called by ROP API to update a specific event.
	 *
	 * @since   8.0.0
	 * @access  public
	 *
	 * @param   array $data Data passed by the API call.
	 *
	 * @return mixed
	 */
	public function update_queue_event( $data ) {
		$queue = new Rop_Queue_Model();
		$this->response->set_code( '500' );
		if ( $queue->update_queue_object( $data['account_id'], $data['post_id'], $data['custom_data'] ) ) {
			$this->response->set_code( '201' )
						   ->set_data( $queue->get_ordered_queue() );
		}

		return $this->response->is_not_silent()->to_array();
	}

	/**
	 * Method called by ROP API to save a schedule.
	 *
	 * @since   8.0.0
	 * @access  public
	 *
	 * @param   array $data Data passed by the API call.
	 *
	 * @return mixed
	 */
	public function save_schedule( $data ) {
		$schedules = new Rop_Scheduler_Model();
		$schedules->add_update_schedule( $data['account_id'], $data['data'] );

		$this->response->set_code( '201' )
					   ->set_data( $schedules->get_schedule() );

		return $this->response->is_not_silent()->to_array();
	}

}
