<?php
defined('_JEXEC') or die;
$app = JFactory::getApplication();
$input = $app->input;
$db = JFactory::getDBO();
$id = $input->get('id');
$db->setQuery("SELECT * FROM `#__foxcontact_enquiries` WHERE `id` = '".$id."' ");
$enquiry = $db->loadObject();
$fields = json_decode($enquiry->fields);
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		Joomla.submitform(task , document.getElementById('adminForm'));
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_roombooking&layout=edit&id='.(int) $id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">


	
	<div class="control-group">
		<div class="control-label">
			<label><?php echo JText::_('SELECTROOM') ; ?></label>
		</div>
		<div class="controls">
			<?php
				$db->setQuery("SELECT * FROM `#__kirk_rooms`");
				$rooms = $db->loadObjectList();
			?>
			<select name="room_id">
				<?php
				foreach($rooms as $r) {
				?>
				<option value="<?php echo $r->id ; ?>" <?php if(strtolower($fields[5][2]) == strtolower($r->room_name)) { ?> selected <?php } ?>><?php echo $r->room_name ; ?></option>
				<?php	
				}
				?>
			</select>
		</div>
	</div>
    
    <div class="control-group">
		<div class="control-label">
			<span><?php echo JText::_('BOOKINGDATE') ; ?></span>
		</div>
		<div class="controls">
			<?php
			$bdate = date('Y-m-d' , strtotime($fields[6][2]));
			echo JHtml::calendar($bdate, 'booking_date', 'booking_date', '%Y-%m-%d', 'size="9"'); ?>
			
		</div>
	</div>
  
   <div class="control-group">
		<div class="control-label">
			<label><?php echo JText::_('CHECKINTIME') ; ?></label>
		</div>
		<div class="controls">
			<?php
				$ch1time = $fields[7][2];
				$ch1time = explode(':' , $ch1time);
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
				$ch2time = $fields[8][2];
				$ch2time = explode(':' , $ch2time);
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
			<label><?php echo JText::_('CUSTOMER_NAME') ; ?></label>
		</div>
		<div class="controls">
			<input type="text" value="<?php echo $fields[0][2] ; ?>" name="customer_name">
		</div>
	</div>
   
   <div class="control-group">
		<div class="control-label">
			<label><?php echo JText::_('CUSTOMER_PHONE') ; ?></label>
		</div>
		<div class="controls">
			<input type="text" value="<?php echo $fields[3][2] ; ?>" name="customer_phone">
		</div>
	</div>
   
   <div class="control-group">
		<div class="control-label">
			<label><?php echo JText::_('CUSTOMER_EMAIL') ; ?></label>
		</div>
		<div class="controls">
			<input type="text" value="<?php echo $fields[1][2] ; ?>" name="customer_email">
		</div>
	</div>
   
   <div class="control-group">
		<div class="control-label">
			<label><?php echo JText::_('BOOKING_REASON') ; ?></label>
		</div>
		<div class="controls">
			<textarea name="booking_reason"><?php echo $fields[9][2] ; ?></textarea>
		</div>
	</div>
   
   <div class="control-group">
		<div class="control-label">
			<label><?php echo JText::_('ADMIN_NOTE') ; ?></label>
		</div>
		<div class="controls">
			<textarea name="admin_note"></textarea>
		</div>
	</div>
    
    
	<input type="hidden" name="task" id="task" value="enquiry.book">
    <input type="hidden" name="view" value="enquiry">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
	<?php echo JHtml::_('form.token'); ?>
</form>
