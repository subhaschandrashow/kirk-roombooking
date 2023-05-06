<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('_JEXEC') or die();


class JFormFieldWater extends JFormField
{
	protected $type 		= 'water';


protected function getInput() {
		
	 
        
       $rnd = rand(100,10000); 
        
        $html = "<script>function changeDisplayImage".$rnd."(id) {
     
			if (document.getElementById(id).value !='') {
				document.adminForm.imagelib".$rnd.".src='".JURI::root()."images/' + document.getElementById(id).value;
			} else {
				document.adminForm.imagelib".$rnd.".src='images/blank.png';
			}
		}</script>"; 
 $javascript			= 'onchange="changeDisplayImage'.$rnd.'(this.id);" ';
 $directory			= '/images/';
  
 
 $html.=JHTML::_('list.images',  $this->name, $this->value, $javascript, $directory, "bmp|gif|jpg|png|swf"  );
   
  $html.= '<br /><img src="'.JURI::root().'/images/'.$this->value.'" name="imagelib'.$rnd.'"  border="2" alt="" />';
  return $html;
        
	}


}

 
 