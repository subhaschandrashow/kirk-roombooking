<?php

defined('_JEXEC') or die();


class JFormFieldTime extends JFormField
{
	protected $type 		= 'time';


	protected function getInput() {
		$html = '';
		$selected = '';
		$html.="<select name='".$this->name."_hour' class='input-small'>";
		for ($h = 0; $h <= 23; $h++) {
			$selected = '';
			if($this->value == $h) { $selected = 'selected' ; }
			$html.="<option value='$h' $selected>".sprintf("%02d", $h)."</option>";
		}
		$html.="</select>";
		$html.="<select name='".$this->name."_min' class='input-small'>";
		$html.="<option value='00'>00</option>";
		$html.="<option value='15'>15</option>";
		$html.="<option value='30'>30</option>";
		$html.="<option value='45'>45</option>";
		$html.="</select>";
	  	return $html;

	}


}

 
 