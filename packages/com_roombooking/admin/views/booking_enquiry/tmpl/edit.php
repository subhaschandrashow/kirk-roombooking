<?php
defined('_JEXEC') or die;
$db = JFactory::getDBO();
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		Joomla.submitform(task , document.getElementById('adminForm'));
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_roombooking&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">

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
			<?php echo $this->form->getLabel('business_name'); ?>
        </div>
        <div class="controls">
			<?php echo $this->form->getInput('business_name') ; ?>
        </div>
    </div>

    <div class="control-group">
        <div class="control-label">
			<?php echo $this->form->getLabel('add_info'); ?>
        </div>
        <div class="controls">
			<?php echo $this->form->getInput('add_info') ; ?>
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
			<?php echo $this->form->getLabel('event_required'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('event_required') ; ?>
		</div>
	</div>


	<input type="hidden" name="task" id="task" value="booking_enquiry.save">
    <input type="hidden" name="view" value="room">
	<?php echo JHtml::_('form.token'); ?>
</form>
