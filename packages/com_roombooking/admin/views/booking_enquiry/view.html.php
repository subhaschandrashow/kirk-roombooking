<?php
defined( '_JEXEC' ) or die( '=;)' );

jimport( 'joomla.application.component.view');

class  RoombookingViewBooking_enquiry extends JViewLegacy
{
	protected $form;
    function display($tpl = null)
    {
		$this->form			= $this->get('Form');
		$this->item			= $this->get('Item');
	   	JToolBarHelper::title(   JText::_( 'ROOM' ), 'generic.png' ); 
       	JToolBarHelper::preferences('com_roombooking', '400');   
	   	JToolbarHelper::apply('booking_enquiry.apply'); 
	   	JToolbarHelper::save('booking_enquiry.save');
	   	JToolbarHelper::cancel('booking_enquiry.cancel'); 
		JToolBarHelper::custom('booking_enquiry.book', 'book', '', 'Book', false);
       	parent::display($tpl);  
    }      
	
}
