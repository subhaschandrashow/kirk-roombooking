<?php
defined('_JEXEC') or die;
$db = JFactory::getDBO();

?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		Joomla.submitform(task , document.getElementById('adminForm'));
	}

	function addholiday()
	{
		var hdate_start = jQuery('#jform_holiday_start').val();
		var hdate_end = jQuery('#jform_holiday_end').val();
		if(hdate_start != '' && hdate_end != '')
		{
			jQuery('#holidaytable').append('<tr><td>'+hdate_start+'<input type="hidden" name="holiday_start[]" value="'+hdate_start+'"></td><td>'+hdate_end+'<input type="hidden" name="holiday_end[]" value="'+hdate_end+'"></td><td><input type="button" value="<?php echo JText::_('DELETE') ; ?>"></td></tr>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_roombooking&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">

	<div class="control-group">
		<div class="control-label">
			<?php echo $this->form->getLabel('id'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('id') ; ?>
		</div>
	</div>

	<div class="control-group">
		<div class="control-label">
			<?php echo $this->form->getLabel('event_title'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('event_title') ; ?>
		</div>
	</div>

	<div class="control-group">
		<div class="control-label">
			<?php echo $this->form->getLabel('event_description'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('event_description') ; ?>
		</div>
	</div>

	<div class="control-group">
		<div class="control-label">
			<?php echo $this->form->getLabel('room_id'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('room_id') ; ?>
		</div>
	</div>

    <div class="control-group">
		<div class="control-label">
			<?php echo $this->form->getLabel('booking_date'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('booking_date') ; ?>
		</div>
	</div>

   <div class="control-group">
		<div class="control-label">
			<label><?php echo JText::_('CHECKINTIME') ; ?></label>
			<?php echo $this->form->getLabel('checkin_time'); ?>
		</div>
		<div class="controls">
			<?php
				$ch1time = $this->item->checkin_time;
				$ch1time = explode('.' , $ch1time);
				$html = '';
				$selected = '';
				$html.="<select name='checkin_time_hour' class='input-small'>";
				for ($h = 0; $h <= 23; $h++) {
					$selected = '';
					if(isset($ch1time[0])) { if($ch1time[0] == $h) { $selected = 'selected' ; } }
					$html.="<option value='$h' $selected>".sprintf("%02d", $h)."</option>";
				}
				$html.="</select>";
				$html.="<select name='checkin_time_min' class='input-small'>";
				$timegap = array('00' , '15' , '30' , '45');
				foreach($timegap as $tgap) {
					$selected = '';
					if(isset($ch1time[1])) { if($ch1time[1] == $tgap) { $selected = 'selected' ; } }
					$html.="<option value='$tgap' $selected>$tgap</option>";
				}
				$html.="</select>";
				echo $html;
			?>
		</div>
	</div>

   <div class="control-group">
		<div class="control-label">
			<label><?php echo JText::_('CHECKOUTTIME') ; ?></label>
		</div>
		<div class="controls">
			<?php
				$ch2time = $this->item->checkout_time;
				$ch2time = explode('.' , $ch2time);
				$html = '';
				$selected = '';
				$html.="<select name='checkout_time_hour' class='input-small'>";
				for ($h = 0; $h <= 23; $h++) {
					$selected = '';
					if($ch2time[0] == $h) { if($ch2time[0] == $h) { $selected = 'selected' ; } }
					$html.="<option value='$h' $selected>".sprintf("%02d", $h)."</option>";
				}
				$html.="</select>";
				$html.="<select name='checkout_time_min' class='input-small'>";
				$timegap = array('00' , '15' , '30' , '45');
				foreach($timegap as $tgap) {
					$selected = '';
					if(isset($ch2time[1])) { if($ch2time[1] == $tgap) { $selected = 'selected' ; } }
					$html.="<option value='$tgap' $selected>$tgap</option>";
				}
				$html.="</select>";
				echo $html;
			?>
		</div>
	</div>

  	<div class="control-group">
		<div class="control-label">
			<?php echo $this->form->getLabel('promotion_starts'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('promotion_starts') ; ?>
		</div>
	</div>

	<div class="control-group">
		<div class="control-label">
			<?php echo $this->form->getLabel('regular'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('regular') ; ?>
		</div>
	</div>

	<div class="control-group">
		<div class="control-label">
			<?php echo $this->form->getLabel('event_period'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('event_period') ; ?>
		</div>
	</div>

   <div class="control-group">
		<div class="control-label">
			<?php echo $this->form->getLabel('event_enddate'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('event_enddate') ; ?>
		</div>
	</div>

   <div class="control-group">
		<div class="control-label">
			<?php echo $this->form->getLabel('public'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('public') ; ?>
		</div>
	</div>

   <div class="control-group">
		<div class="control-label">
			<?php echo $this->form->getLabel('customer_name'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('customer_name') ; ?>
		</div>
	</div>

   <div class="control-group">
		<div class="control-label">
			<?php echo $this->form->getLabel('customer_phone'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('customer_phone') ; ?>
		</div>
	</div>

   <div class="control-group">
		<div class="control-label">
			<?php echo $this->form->getLabel('customer_email'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('customer_email') ; ?>
		</div>
	</div>

   <div class="control-group">
		<div class="control-label">
			<?php echo $this->form->getLabel('customer_address'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('customer_address') ; ?>
		</div>
	</div>

   <div class="control-group">
		<div class="control-label">
			<?php echo $this->form->getLabel('booking_reason'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('booking_reason') ; ?>
		</div>
	</div>

   <div class="control-group">
		<div class="control-label">
			<?php echo $this->form->getLabel('admin_note'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('admin_note') ; ?>
		</div>
	</div>

	<div class="control-group">
		<div class="control-label">
			<?php echo $this->form->getLabel('holiday_start'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('holiday_start') ; ?>
		</div>
	</div>

	<div class="control-group">
		<div class="control-label">
			<?php echo $this->form->getLabel('holiday_end'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('holiday_end') ; ?>
		</div>
	</div>

	<div class="control-group">
		<div class="control-label">
			<?php echo $this->form->getLabel('add_holidays'); ?>
		</div>
		<div class="controls">
			<?php //echo $this->form->getInput('add_holidays') ; ?>
			<input type="button" value="<?php echo JText::_('ADD_HOLIDAY');?>" onClick="addholiday();">

			<b><?php echo JText::_('ADD_HOLIDAY_HELP');?></b>
		</div>
	</div>

	<div class="control-group">
  		<div class="control-label">
			<?php echo JText::_('HOLIDAYS'); ?>
		</div>
		<div class="controls">
			<table cellpadding="0" cellspacing="0" border="0" class="table table-striped" id="holidaytable">
			<?php
				if($this->item->id > 0)
				{
				$db->setQuery("SELECT `holidays` FROM `#__kirk_bookings` WHERE `id` = '".$this->item->booking_id."'");
				$holidays = $db->loadObject()->holidays;
				$holidays = json_decode($holidays);
				if(count($holidays) > 0)
				{
				foreach($holidays as $hd)
				{
				 	$holiday_ex = explode('***' , $hd);
					$start_date = $holiday_ex[0];
					$end_date = $holiday_ex[1];
					?>
						<tr>
							<td><?php echo $start_date ; ?><input type="hidden" name="holiday_start[]" value="<?php echo $start_date ; ?>"></td>
							<td><?php echo $end_date ; ?><input type="hidden" name="holiday_end[]" value="<?php echo $end_date ; ?>"></td>
							<td><input type="button" value="<?php echo JText::_('DELETE') ; ?>" onClick="jQuery(this).parent().parent().remove();"></td>
						</tr>
					<?php
				}
				}
				}
			?>
			</table>

		</div>
  </div>

   <?php
	$images = json_decode($this->item->images) ;
   ?>

   <?php
	if(isset($images[0]) && isset($images[0]) != '') {
	?>
	<div class="control-group">
		<div class="control-label">
			<label>Image 1</label>
		</div>
		<div class="controls">
			<img src="<?php echo JURI::base(); ?>components/com_roombooking/event_images/<?php echo $images[0] ; ?>" width="100"><br/><?php echo JText::_('DELETE'); ?>
			<br/>
			<input type="checkbox" name="delete_images[]" value="1">
		</div>
	</div>
   <?php
	}
	else {
	?>
	<div class="control-group">
		<div class="control-label">
			<label>Select Image 1</label>
		</div>
		<div class="controls">
			<input type="file" name="image_upload[]">
		</div>
	</div>
	<?php
	}
   ?>


    <?php
	if(isset($images[1]) && isset($images[1]) != '') {
	?>
	<div class="control-group">
		<div class="control-label">
			<label>Image 2</label>
		</div>
		<div class="controls">
			<img src="<?php echo JURI::base(); ?>components/com_roombooking/event_images/<?php echo $images[1] ; ?>" width="100"><br/><?php echo JText::_('DELETE'); ?>
			<br/>
			<input type="checkbox" name="delete_images[]" value="1">
		</div>
	</div>
   <?php
	}
	else {
	?>
	<div class="control-group">
		<div class="control-label">
			<label>Select Image 2</label>
		</div>
		<div class="controls">
			<input type="file" name="image_upload[]">
		</div>
	</div>
	<?php
	}
   ?>

   <?php
	if(isset($images[2]) && isset($images[2]) != '') {
	?>
	<div class="control-group">
		<div class="control-label">
			<label>Image 3</label>
		</div>
		<div class="controls">
			<img src="<?php echo JURI::base(); ?>components/com_roombooking/event_images/<?php echo $images[2] ; ?>" width="100"><br/><?php echo JText::_('DELETE'); ?>
			<br/>
			<input type="checkbox" name="delete_images[]" value="1">
		</div>
	</div>
   <?php
	}
	else {
	?>
	<div class="control-group">
		<div class="control-label">
			<label>Select Image 3</label>
		</div>
		<div class="controls">
			<input type="file" name="image_upload[]">
		</div>
	</div>
	<?php
	}
   ?>

   <?php
	if(isset($images[3]) && isset($images[3]) != '') {
	?>
	<div class="control-group">
		<div class="control-label">
			<label>Image 4</label>
		</div>
		<div class="controls">
			<img src="<?php echo JURI::base(); ?>components/com_roombooking/event_images/<?php echo $images[3] ; ?>" width="100"><br/><?php echo JText::_('DELETE'); ?>
			<br/>
			<input type="checkbox" name="delete_images[]" value="1">
		</div>
	</div>
   <?php
	}
	else {
	?>
	<div class="control-group">
		<div class="control-label">
			<label>Select Image 4</label>
		</div>
		<div class="controls">
			<input type="file" name="image_upload[]">
		</div>
	</div>
	<?php
	}
   ?>

   <?php
	if(isset($images[4]) && isset($images[4]) != '') {
	?>
	<div class="control-group">
		<div class="control-label">
			<label>Image 5</label>
		</div>
		<div class="controls">
			<img src="<?php echo JURI::base(); ?>components/com_roombooking/event_images/<?php echo $images[4] ; ?>" width="100"><br/><?php echo JText::_('DELETE'); ?>
			<br/>
			<input type="checkbox" name="delete_images[]" value="1">
		</div>
	</div>
   <?php
	}
	else {
	?>
	<div class="control-group">
		<div class="control-label">
			<label>Select Image 5</label>
		</div>
		<div class="controls">
			<input type="file" name="image_upload[]" id="jform_image4">
		</div>
	</div>
	<?php
	}
   ?>

    <div class="control-group">
		<div class="control-label">
			<?php echo $this->form->getLabel('images'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('images') ; ?>
		</div>
	</div>

	<input type="hidden" name="task" id="task" value="event.save">
    <input type="hidden" name="view" value="event">
    <input type="hidden" name="booking_id" value="<?php echo $this->item->booking_id ; ?>">
	<?php echo JHtml::_('form.token'); ?>
</form>
