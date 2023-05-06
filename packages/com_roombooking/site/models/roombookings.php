<?php
defined( '_JEXEC' ) or die();
jimport('joomla.application.component.modellist');


class VmapiModelOnlineorders extends JModelList
{
	protected	$option 		= 'com_vmapi';
 
 
 	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'a.virtuemart_order_id', 'virtuemart_order_id',
				'a.virtuemart_user_id', 'virtuemart_user_id',
				'a.order_number', 'order_number',
				'a.order_total', 'order_total',
				'a.virtuemart_shipmentmethod_id', 'virtuemart_shipmentmethod_id',
				'a.virtuemart_paymentmethod_id', 'virtuemart_paymentmethod_id',
				'b.first_name', 'first_name',
				'b.last_name', 'last_name',
				'b.middle_name', 'middle_name',
				'b.email', 'email',
				'c.shipment_name', 'shipment_name',
				'd.payment_name', 'payment_name'
				
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
		$params = JComponentHelper::getParams('com_vmapi');
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
	 	$params = JComponentHelper::getParams('com_vmapi');
		$virtuemart_lang 	= $params->get('virtuemart_lang');
		
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		 
		$query->select(
			 "a.* , b.* , c.* , d.* "
		);
		$query->from(' `#__virtuemart_orders` as a , `#__virtuemart_order_userinfos` as b , `#__virtuemart_shipmentmethods_'.$virtuemart_lang.'` as c , `#__virtuemart_paymentmethods_'.$virtuemart_lang.'` as d ');
		$query->where(' a.virtuemart_order_id = b.virtuemart_order_id AND a.virtuemart_shipmentmethod_id = c.virtuemart_shipmentmethod_id AND a.virtuemart_paymentmethod_id = d.virtuemart_paymentmethod_id AND a.virtuemart_order_id NOT IN ( SELECT `virtuemart_order_id` FROM `#__vmapi_order_managers` )');
		
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
				$query->where('( role_name LIKE '.$search.')');
			}
		}


       	// Filter by published state.
		$published = $this->getState('filter.state');
         
        
		//if (is_numeric($published)) {
//			$query->where('status = '.(int) $published);
//		}
//		else if ($published === '') {
//			$query->where('(status IN (0, 1))');
//		}
        
        
        
       
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
        
        //echo $query;
		return $query;
	}
}
?>