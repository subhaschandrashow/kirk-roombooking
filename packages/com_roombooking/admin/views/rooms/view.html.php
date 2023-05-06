<?php
defined( '_JEXEC' ) or die( '=;)' );

jimport( 'joomla.application.component.view');



class  RoombookingViewRooms extends JViewLegacy
{
	protected $items;
    function display($tpl = null)
    {
	 	$this->items    = $this->get('Items'); 
	   	JToolBarHelper::title(   JText::_( 'ROOMS' ), 'generic.png' ); 
       	JToolBarHelper::preferences('com_roombooking', '400');   
	   	JToolbarHelper::addNew('room.add');
	   	JToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'room.delete', 'JTOOLBAR_DELETE');    
       	parent::display($tpl);  
    }      
	
}
