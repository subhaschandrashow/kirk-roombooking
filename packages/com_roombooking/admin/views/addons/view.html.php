<?php
defined( '_JEXEC' ) or die( '=;)' );

jimport( 'joomla.application.component.view');



class  RoombookingViewAddons extends JViewLegacy
{
	protected $items;
    function display($tpl = null)
    {
	 	$this->items    = $this->get('Items'); 
	   	JToolBarHelper::title(   JText::_( 'ADDONS' ), 'generic.png' ); 
       	JToolBarHelper::preferences('com_roombooking', '400');   
	   	JToolbarHelper::addNew('addon.add');
	   	JToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'addon.delete', 'JTOOLBAR_DELETE');    
       	parent::display($tpl);  
    }      
	
}
