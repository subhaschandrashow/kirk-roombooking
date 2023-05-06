<?php
defined( '_JEXEC' ) or die( '=;)' );

jimport( 'joomla.application.component.view');



class  RoombookingViewEnquiries extends JViewLegacy
{
	protected $items;
    function display($tpl = null)
    {
	 	
	   	JToolBarHelper::title(   JText::_( 'ENQUIRIES' ), 'generic.png' ); 
       	JToolBarHelper::preferences('com_roombooking', '400');   
		//JToolBarHelper::custom('enquiry.editandbook', 'book', '', 'Edit & Book', true);
	   	JToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'enquiry.delete', 'JTOOLBAR_DELETE');    
       	parent::display($tpl);  
    }      
	
}
