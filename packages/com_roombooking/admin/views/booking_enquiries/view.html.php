<?php
defined( '_JEXEC' ) or die( '=;)' );

jimport( 'joomla.application.component.view');



class  RoombookingViewBooking_enquiries extends JViewLegacy
{
	protected $items;
    function display($tpl = null)
    {
	 	$this->items    = $this->get('Items'); 
		$this->state		= $this->get('State');
	   	JToolBarHelper::title(   JText::_( 'BOOKINGENQUIRIES' ), 'generic.png' ); 
       	JToolBarHelper::preferences('com_roombooking', '400');   
	   	//JToolbarHelper::addNew('booking_enquiry.add');
	   	JToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'booking_enquiry.delete', 'JTOOLBAR_DELETE');    
       	parent::display($tpl);  
    }      
	
}
