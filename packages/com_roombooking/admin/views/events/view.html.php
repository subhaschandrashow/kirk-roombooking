<?php
defined( '_JEXEC' ) or die( '=;)' );

jimport( 'joomla.application.component.view');



class  RoombookingViewEvents extends JViewLegacy
{
	protected $items;
	protected $pagination;
	
    function display($tpl = null)
    {
	 	$this->items    = $this->get('Items'); 
		$this->state		= $this->get('State');
		$this->pagination    = $this->get('Pagination');
	   	JToolBarHelper::title(   JText::_( 'Events' ), 'generic.png' ); 
       	JToolBarHelper::preferences('com_roombooking', '400');   
	   	JToolbarHelper::addNew('event.add');
	   	JToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'event.delete', 'JTOOLBAR_DELETE');    
       	parent::display($tpl);  
    }      
	
}
