<?php
defined( '_JEXEC' ) or die( '=;)' );

jimport( 'joomla.application.component.view');



class  RoombookingViewEventdetails extends JViewLegacy
{

    function display($tpl = null)
    {
	   $app = JFactory::getApplication();
	   parent::display($tpl); 
    }      
	
}
