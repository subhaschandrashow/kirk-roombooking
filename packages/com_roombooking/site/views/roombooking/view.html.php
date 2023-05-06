<?php
defined( '_JEXEC' ) or die( '=;)' );

jimport( 'joomla.application.component.view');



class  RoombookingViewRoombooking extends JViewLegacy
{

    function display($tpl = null)
    {
	   $app = JFactory::getApplication();
	   parent::display($tpl); 
    }      
	
}
