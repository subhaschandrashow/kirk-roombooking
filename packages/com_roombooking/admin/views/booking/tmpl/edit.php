<?php
defined('_JEXEC') or die;
$db = JFactory::getDBO();
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'booking.refund')
		{
			if (confirm('Are you sure to refund the amount of the booking')) {
			  Joomla.submitform(task , document.getElementById('adminForm'));
			} else {
			  return false
			}
		}
		else {
			Joomla.submitform(task , document.getElementById('adminForm'));
		}
	}

	function addholiday()
	{
		var hdate = jQuery('#jform_add_holidays').val();
		if(hdate != '')
		{
			jQuery('#holidaytable').append('<tr><td>'+hdate+'<input type="hidden" name="holidays[]" value="'+hdate+'"></td><td><input type="button" value="<?php echo JText::_('DELETE') ; ?>"></td></tr>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_roombooking&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<div class="row">
		<div class="span6">
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
					<label><?php echo JText::_('Is this an event you wish us to publicise ?') ; ?></label>
				</div>
				<div class="controls">
				<?php
				if($this->item->id > 0)	{
					$db->setQuery("SELECT * FROM `#__kirk_booking_events` WHERE `booking_id` = ".$this->item->id);
					$is_public = $db->loadObject()->public ;
				}
				?>
					<input type="radio" name="jform[is_event]" id="jform_is_event_0" value="0" <?php if(isset($is_public) && $is_public == 0) { ?> checked <?php } elseif( !isset($is_public)) { ?> checked <?php }?>>No
					<br/>
					<input type="radio" name="jform[is_event]" id="jform_is_event_1" value="1" <?php if(isset($is_public) && $is_public == 1) { ?> checked <?php }?>>Yes
				</div>
			</div>

			<?php echo $this->form->getInput('booking_master_id') ; ?>
		</div>
		<div class="span6">
			<?php
			if ($this->item->id  > 0)
			{
				$db->setQuery("SELECT * FROM `#__kirk_transactions` WHERE `booking_id` =".$this->item->id);
				$transaction_details = $db->loadObject();
				?>
				<table cellpadding="0" cellspacing="0" class="table-striped" width="100%">
					<tr>
						<th align="left">Payment method</th>
						<th align="left">Amount</th>
						<th align="left">TRX ID</th>
						<th align="left">Transaction Date</th>
					</tr>

					<tr>
						<td><?php echo $transaction_details->payment_method ; ?></td>
						<td><?php echo $transaction_details->amt ; ?></td>
						<td><?php echo $transaction_details->transaction_id ; ?></td>
						<td><?php echo $transaction_details->transaction_date ; ?></td>
					</tr>
				</table>
			<?php
			}
			?>
		</div>
	</div>




   <?php /*?><div class="control-group">
		<div class="control-label">
			<?php echo $this->form->getLabel('holidays'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('add_holidays') ; ?>
			<input type="button" value="<?php echo JText::_('ADD_HOLIDAY');?>" onClick="addholiday();">
		</div>
	</div>

   <div class="control-group">
  		<div class="control-label">
			<?php echo JText::_('HOLIDAYS'); ?>
		</div>
		<div class="controls">
			<table cellpadding="0" cellspacing="0" border="0" class="table table-striped" id="holidaytable">
			<?php
				$holidays = json_decode($this->item->holidays);
				if(count($holidays) > 0)
				{
				foreach($holidays as $hd)
				{
					?>
						<tr>
							<td><?php echo $hd ; ?><input type="hidden" name="holidays[]" value="<?php echo $hd ; ?>"></td>
							<td><input type="button" value="<?php echo JText::_('DELETE') ; ?>" onClick="jQuery(this).parent().parent().remove();"></td>
						</tr>
					<?php
				}
				}
			?>
			</table>

		</div>
  </div><?php */?>



	<input type="hidden" name="task" id="task" value="booking.save">
    <input type="hidden" name="view" value="room">
	<?php echo JHtml::_('form.token'); ?>
</form>
