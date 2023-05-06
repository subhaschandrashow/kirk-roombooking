<?php
defined( '_JEXEC' ) or die();
jimport('joomla.application.component.modellist');


class RoombookingModelBooking_enquiries extends JModelList
{
	protected	$option 		= 'com_roombooking';
 
 
 	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'id',
				'room_id', 'room_id',
				'booking_date', 'booking_date',
				'checkin_time', 'checkin_time',
				'checkout_time', 'checkout_time',
				'customer_name', 'customer_name',
				'customer_phone', 'customer_phone',
				'customer_email', 'customer_email',
				'booking_reason', 'booking_reason',
				'admin_note', 'admin_note'
			);
		}

		parent::__construct($config);
	}
  
	
	protected function populateState($ordering=null , $direction=null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.

    	$roomid = $app->getUserStateFromRequest($this->context.'.filter.roomid', 'filter_roomid', '', 'string');
		$this->setState('filter.roomid', $roomid);
 
        
  
		// Load the parameters.
		$params = JComponentHelper::getParams('com_roombooking');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('id', 'asc');
	}
    
    protected function getStoreId($id = '')
	{
		$id	.= ':'.$this->getState('filter.roomid');
		
		return parent::getStoreId($id);
	}
    protected function getListQuery()
	{
	 
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		 
		$query->select(
			 "* "
		);
		$query->from('`#__kirk_booking_enquiries`');

		// Filter by search in title
		$roomid = $this->getState('filter.roomid');
		if (!empty($roomid))
		{
			if (stripos($roomid, 'id:') === 0) {
				$query->where('room_id = '.(int) substr($roomid, 3));
			}
			else
			{
				//$roomid = $db->Quote('%'.$db->getEscaped($search, true).'%');
				$query->where('( room_id LIKE '.$roomid.')');
			}
		}

       
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
        
       
		return $query;
	}
}
?>