<?php
defined('_JEXEC') or die;

//error_reporting(E_ALL); ini_set('display_errors', 1);
ini_set('max_execution_time', 30000); 
ini_set('memory_limit', '-1');
define('DS', DIRECTORY_SEPARATOR);  


jimport('joomla.application.component.controller');



// Execute the task.
$controller	= JControllerLegacy::getInstance('roombooking');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();

