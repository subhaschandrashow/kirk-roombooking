<?php
defined( '_JEXEC' ) or die( '=;)' );

//define ('DS', DIRECTORY_SEPARATOR);
JHtml::_('bootstrap.framework');
$document = JFactory::getDocument();
//$document->addStyleSheet(JURI::root().'components/com_vmapi/assets/css/bootstrap.css');
$document->addStyleSheet(JURI::root().'components/com_roombooking/assets/css/style.css');


require_once( JPATH_COMPONENT.DIRECTORY_SEPARATOR.'controller.php' );

$controller	= JControllerLegacy::getInstance('roombooking');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();

