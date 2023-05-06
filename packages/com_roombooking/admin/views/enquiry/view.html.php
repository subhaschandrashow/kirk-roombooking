<?php
defined( '_JEXEC' ) or die( '=;)' );

jimport( 'joomla.application.component.view');

class  RoombookingViewEnquiry extends JViewLegacy
{
	protected $form;
    function display($tpl = null)
    {
	   	JToolBarHelper::title(   JText::_( 'ENQUIRY' ), 'generic.png' ); 
       	JToolBarHelper::preferences('com_roombooking', '400');   
	   	JToolBarHelper::custom('enquiry.book' , 'Book');
	   	//JToolbarHelper::cancel('enquiry.cancel');          
       	parent::display($tpl);  
    }      
	
}
