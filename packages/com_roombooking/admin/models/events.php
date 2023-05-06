<?php
defined( '_JEXEC' ) or die();
jimport('joomla.application.component.modellist');


class RoombookingModelEvents extends JModelList
{
	protected	$option 		= 'com_roombooking';


 	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'a.id', 'id',
				'a.booking_id', 'booking_id',
				'a.event_title', 'event_title',
				'a.event_description', 'event_description',
				'a.regular', 'regular',
				'a.event_enddate', 'event_enddate',
				'a.promotion_starts', 'promotion_starts',
				'b.room_id', 'room_id',
				'b.booking_date', 'booking_date',
				'b.checkin_time', 'checkin_time',
				'b.checkout_time', 'checkout_time',
				'b.customer_name', 'customer_name',
				'b.customer_phone', 'customer_phone',
				'b.customer_email', 'customer_email',
				'b.customer_address', 'customer_address',
				'b.booking_reason', 'booking_reason',
				'b.admin_note', 'admin_note',
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
		parent::populateState('a.id', 'DESC');
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
			 " a.* , b.room_id , b.booking_date , b.checkin_time , b.checkout_time , b.customer_name , b.customer_phone , b.customer_email , b.customer_address , b.booking_reason , b.admin_note "
		);
		$query->from('`#__kirk_booking_events` as a , `#__kirk_bookings` as b');
		$query->where('a.booking_id = b.id AND b.booking_master_id = 0');
		// Filter by search in title
		$roomid = $this->getState('filter.roomid');
		if (!empty($roomid))
		{
			if (stripos($roomid, 'id:') === 0) {

			}

		}


		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');

		$query->order($orderCol . ' ' . $orderDirn);

		return $query;
	}
}
?>
