<?php
defined( '_JEXEC' ) or die( '=;)' );
$db = JFactory::getDBO();
$user    = JFactory::getUser();
$db->setQuery("SELECt * FROM `#__kirk_rooms`");
$roomarr = $db->loadObjectList();
$rooms = array();
foreach($roomarr as $r) {
	$rooms[$r->id] = $r->room_name;
}
?>


<form action="<?php echo JRoute::_('index.php?option=com_roombooking&view=events'); ?>" method="post" name="adminForm" id="adminForm">
	
	<table class="table table-striped">
   		<tr>
   			<td colspan="7">
   				<div class="filter-search fltlft">
					<select name="filter_roomid" id="filter_roomid">
						<option value=""><?php echo JText::_('SELECTROOM'); ?></option>
						<?php
							foreach($rooms as $key => $val) {
							?>
							<option value="<?php echo $key ; ?>" <?php if($this->escape($this->state->get('filter.roomid')) == $key) { ?> selected <?php } ?>><?php echo $val ; ?></option>
							<?php	
							}
						?>
					</select>
					<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
					<button type="button" onclick="document.getElementById('filter_roomid').value='';this.form.submit();"><?php echo JText::_('JSEARCH_RESET'); ?></button>
				</div>
   			</td>
   		</tr>
    	<tr>
        	<th width="1%" class="nowrap center">
				<?php echo JHtml::_('grid.checkall'); ?>
			</th>
        	<th><?php echo JText::_('Event Ref'); ?></th>
        	<th><?php echo JText::_('ROOM'); ?></th>
        	<th><?php echo JText::_('Event Title'); ?></th>
        	<th><?php echo JText::_('BOOKINGDATE'); ?></th>
        	<th><?php echo JText::_('Event Start'); ?></th>
        	<th><?php echo JText::_('Event End'); ?></th>
        </tr>
        
        <?php
		$i = 0;
		foreach($this->items as $item) { ;
		$canChange  = $user->authorise('core.edit.state',  'com_roombooking.event'.$item->id);
		?>
        <tr>
        <td class="center"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
        <td><a href="index.php?option=com_roombooking&view=event&layout=edit&id=<?php echo $item->id ; ?>"><?php echo 'RB#'.sprintf("%04d", $item->id) ; ?></a></td>
        <td><?php echo $rooms[$item->room_id] ; ?></td>
        <td><?php echo $item->event_title; ?></td>
        <td><?php echo $item->booking_date ; ?></td>
        <td><?php echo $item->checkin_time; ?></td>
        <td><?php echo $item->checkout_time; ?></td>

        </tr>
        <?php
		$i++;	
		}
		?>
   		<tr>
   			<td colspan="4" align="center">
   				<?php echo $this->pagination->getListFooter(); ?>
   			</td>
   			<td colspan="3" align="center">
   				<?php echo $this->pagination->getLimitBox(); ?>
   			</td>
   		</tr>
    </table>
    <input type="hidden" value="" name="task" id="task" />
    <input type="hidden" value="events" name="controller" id="controller" />
    <input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>
</form>