<?php
defined( '_JEXEC' ) or die();
jimport('joomla.application.component.modellist');


class RoombookingModelRooms extends JModelList
{
	protected	$option 		= 'com_roombooking';
 
 
 	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'id',
				'room_name', 'room_name',
				'room_description', 'room_description',
				'status', 'status'
			);
		}

		parent::__construct($config);
	}
  
	
	protected function populateState($ordering=null , $direction=null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

    	$state = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_status', '', 'string');
		$this->setState('filter.state', $state);
 
        
  
		// Load the parameters.
		$params = JComponentHelper::getParams('com_roombooking');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('id', 'asc');
	}
    
    protected function getStoreId($id = '')
	{
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.state');
		
		return parent::getStoreId($id);
	}
    protected function getListQuery()
	{
	 
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		 
		$query->select(
			 "* "
		);
		$query->from('`#__kirk_rooms`');

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0) {
				$query->where('id = '.(int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
				$query->where('( room_name LIKE '.$search.')');
			}
		}


       	// Filter by published state.
		$published = $this->getState('filter.state');
         
        
		if (is_numeric($published)) {
			$query->where('status = '.(int) $published);
		}
		else if ($published === '') {
			$query->where('(status IN (0, 1))');
		}
        
        
        
       
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
        
       
		return $query;
	}
}
?>