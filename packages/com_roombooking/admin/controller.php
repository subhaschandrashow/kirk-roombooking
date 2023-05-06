<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
$view		= JRequest::getCmd('view', 'rooms');

if($view == 'rooms' || $view == 'room') {
	JSubMenuHelper::addEntry(JTEXT::_('ROOMS'), 'index.php?option=com_roombooking' , true);
	JSubMenuHelper::addEntry(JTEXT::_('BOOKINGS'), 'index.php?option=com_roombooking&view=bookings');
	JSubMenuHelper::addEntry(JTEXT::_('ENQUIRIES'), 'index.php?option=com_roombooking&view=booking_enquiries');
	JSubMenuHelper::addEntry(JTEXT::_('EVENTS'), 'index.php?option=com_roombooking&view=events');
	JSubMenuHelper::addEntry(JTEXT::_('ADDONS'), 'index.php?option=com_roombooking&view=addons');
}
elseif($view == 'bookings' || $view == 'booking')  {
	JSubMenuHelper::addEntry(JTEXT::_('ROOMS'), 'index.php?option=com_roombooking');
	JSubMenuHelper::addEntry(JTEXT::_('BOOKINGS'), 'index.php?option=com_roombooking&view=bookings' , true);
	JSubMenuHelper::addEntry(JTEXT::_('ENQUIRIES'), 'index.php?option=com_roombooking&view=booking_enquiries');
	JSubMenuHelper::addEntry(JTEXT::_('EVENTS'), 'index.php?option=com_roombooking&view=events');
	JSubMenuHelper::addEntry(JTEXT::_('ADDONS'), 'index.php?option=com_roombooking&view=addons');
}
elseif($view == 'booking_enquiries' || $view == 'booking_enquiry')  {
	JSubMenuHelper::addEntry(JTEXT::_('ROOMS'), 'index.php?option=com_roombooking');
	JSubMenuHelper::addEntry(JTEXT::_('BOOKINGS'), 'index.php?option=com_roombooking&view=bookings');
	JSubMenuHelper::addEntry(JTEXT::_('ENQUIRIES'), 'index.php?option=com_roombooking&view=booking_enquiries' , true);
	JSubMenuHelper::addEntry(JTEXT::_('EVENTS'), 'index.php?option=com_roombooking&view=events');
	JSubMenuHelper::addEntry(JTEXT::_('ADDONS'), 'index.php?option=com_roombooking&view=addons');
}
elseif($view == 'addons' || $view == 'addon')  {
	JSubMenuHelper::addEntry(JTEXT::_('ROOMS'), 'index.php?option=com_roombooking');
	JSubMenuHelper::addEntry(JTEXT::_('BOOKINGS'), 'index.php?option=com_roombooking&view=bookings');
	JSubMenuHelper::addEntry(JTEXT::_('ENQUIRIES'), 'index.php?option=com_roombooking&view=booking_enquiries');
	JSubMenuHelper::addEntry(JTEXT::_('EVENTS'), 'index.php?option=com_roombooking&view=events');
	JSubMenuHelper::addEntry(JTEXT::_('ADDONS'), 'index.php?option=com_roombooking&view=addons' , true);
}
else {
	JSubMenuHelper::addEntry(JTEXT::_('ROOMS'), 'index.php?option=com_roombooking');
	JSubMenuHelper::addEntry(JTEXT::_('BOOKINGS'), 'index.php?option=com_roombooking&view=bookings');
	JSubMenuHelper::addEntry(JTEXT::_('ENQUIRIES'), 'index.php?option=com_roombooking&view=booking_enquiries');
	JSubMenuHelper::addEntry(JTEXT::_('EVENTS'), 'index.php?option=com_roombooking&view=events' , true);
	JSubMenuHelper::addEntry(JTEXT::_('ADDONS'), 'index.php?option=com_roombooking&view=addons');
}
																	 
   
class RoombookingController extends JControllerLegacy
{
	protected $default_view = 'rooms';


public function __construct($config = array())
{
	parent::__construct($config);
}
    
public function display($cachable = false, $urlparams = false)
{
	$view		= JRequest::getCmd('view', 'rooms');
	$layout 	= JRequest::getCmd('layout', 'default');
	$id			= JRequest::getInt('id');
 
	parent::display();

	return $this;
}
	

}