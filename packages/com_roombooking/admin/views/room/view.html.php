<?php
defined( '_JEXEC' ) or die( '=;)' );

jimport( 'joomla.application.component.view');

class  RoombookingViewRoom extends JViewLegacy
{
	protected $form;
    function display($tpl = null)
    {
		$this->form			= $this->get('Form');
		$this->item			= $this->get('Item');
	   	JToolBarHelper::title(   JText::_( 'ROOM' ), 'generic.png' ); 
       	JToolBarHelper::preferences('com_roombooking', '400');   
	   	JToolbarHelper::apply('room.apply'); 
	   	JToolbarHelper::save('room.save');
	   	JToolbarHelper::cancel('room.cancel');          
       	parent::display($tpl);  
    }      
	
}
