<?php
defined('_JEXEC') or die();

class JFormFieldRooms extends JFormField
{
   protected $type      = 'rooms';
 
   protected function getInput() {
      
      $db =  JFactory::getDBO();
	  $id = JRequest::getVar('id');
	  $extraidstring = '';
	   
	  
	   
	  if($id > 0) {
	  	$extraidstring = ' WHERE `id` != '.$id;
	  }
      $query = 'SELECT * '
      . ' FROM `#__kirk_rooms` WHERE `status` = 1' ;
      
      $db->setQuery( $query );
      $allpublishedrooms = $db->loadObjectList();
	   
	  $rooms = array();
	  foreach($allpublishedrooms as $room) {
		  $rooms[] = array('text'=>$room->room_name , 'value'=>$room->id);
	  }
      
      //array_unshift($cashdeskusers, JHTML::_('select.option', '', '- '.JText::_('SELECT').' -', 'value', 'text'));

      return JHTML::_('select.genericlist',  $rooms, $this->name, 'class="inputbox"', 'value', 'text', $this->value, $this->id );

      
   }
}
?>