<?php
defined( '_JEXEC' ) or die( '=;)' );
$db = JFactory::getDBO();
$user    = JFactory::getUser();
$db->setQuery("SELECT * FROM `#__foxcontact_enquiries` WHERE `form_id` = '-116'");
$fox_enquiries = $db->loadObjectList();
?>


<form action="<?php echo JRoute::_('index.php?option=com_roombooking&view=bookings'); ?>" method="post" name="adminForm" id="adminForm">
	
	<table class="table table-striped">
   		
    	<tr>
        	<th width="1%" class="nowrap center">
				<?php echo JHtml::_('grid.checkall'); ?>
			</th>
        	<th><?php echo JText::_('CUSTOMER_NAME'); ?></th>
        	<th><?php echo JText::_('CUSTOMER_EMAIL'); ?></th>
        	<th><?php echo JText::_('CUSTOMER_PHONE'); ?></th>
        	<th><?php echo JText::_('CHECKIN'); ?></th>
        	<th><?php echo JText::_('CHECKOUT'); ?></th>
        	<th><?php echo JText::_('BOOKINGDATE'); ?></th>
        	<th><?php echo JText::_('ROOM'); ?></th>
        </tr>
        
        <?php
		$i = 0;
		foreach($fox_enquiries as $enquiry) {
		$fields = json_decode($enquiry->fields);
		?>
        <tr>
        <td class="center"><?php echo JHtml::_('grid.id', $i, $enquiry->id); ?></td>
        <td><a href="index.php?option=com_roombooking&view=enquiry&layout=edit&id=<?php echo $enquiry->id ; ?>"><?php echo $fields[0][2] ; ?></a></td>
        <td><?php echo $fields[1][2] ; ?></td>
        <td><?php echo $fields[3][2] ; ?></td>
        <td><?php echo $fields[7][2] ; ?></td>
        <td><?php echo $fields[8][2] ; ?></td>
        <td><?php echo $fields[6][2] ; ?></td>
        <td><?php echo $fields[5][2] ; ?></td>
        <td></td>
        </tr>
        <?php
		$i++;	
		}
		?>
    </table>
    <input type="hidden" value="" name="task" id="task" />
    <input type="hidden" value="bookings" name="controller" id="controller" />
    <input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>
</form>