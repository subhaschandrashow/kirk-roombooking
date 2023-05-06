<?php
defined( '_JEXEC' ) or die( '=;)' );

jimport( 'joomla.application.component.view');



class  RoombookingViewUpcoming_events extends JViewLegacy
{

    function display($tpl = null)
    {
	   $app = JFactory::getApplication();
	   parent::display($tpl); 
    }      
	
}
