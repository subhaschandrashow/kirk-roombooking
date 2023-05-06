<?php
defined( '_JEXEC' ) or die( '=;)' );

jimport( 'joomla.application.component.view');

class  RoombookingViewBooking extends JViewLegacy
{
	protected $form;
    function display($tpl = null)
    {
		$this->form			= $this->get('Form');
		$this->item			= $this->get('Item');
	   	JToolBarHelper::title(   JText::_( 'ROOM' ), 'generic.png' ); 
       	JToolBarHelper::preferences('com_roombooking', '400');   
	   	JToolbarHelper::apply('booking.apply'); 
	   	JToolbarHelper::save('booking.save');
	   	JToolbarHelper::cancel('booking.cancel');
		JToolBarHelper::custom( 'booking.refund', 'ban-circle', 'icon-ban-circle', 'Refund',  false);
       	parent::display($tpl);  
    }      
	
}
