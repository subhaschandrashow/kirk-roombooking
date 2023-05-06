<?php
defined( '_JEXEC' ) or die( '=;)' );

jimport( 'joomla.application.component.view');

class  RoombookingViewEvent extends JViewLegacy
{
	protected $form;
    function display($tpl = null)
    {
		$this->form			= $this->get('Form');
		$this->item			= $this->get('Item');
	   	JToolBarHelper::title(   JText::_( 'Event' ), 'generic.png' ); 
       	JToolBarHelper::preferences('com_roombooking', '400');   
	   	JToolbarHelper::apply('event.apply'); 
	   	JToolbarHelper::save('event.save');
	   	JToolbarHelper::cancel('event.cancel');          
       	parent::display($tpl);  
    }      
	
}
