<?php
defined( '_JEXEC' ) or die( '=;)' );
$db = JFactory::getDBO();
$user    = JFactory::getUser();
?>


<form action="<?php echo JRoute::_('index.php?option=com_roombooking&view=rooms'); ?>" method="post" name="adminForm" id="adminForm">
	<table class="table table-striped">
    	<tr>
        	<th width="1%" class="nowrap center">
				<?php echo JHtml::_('grid.checkall'); ?>
			</th>
        	<th><?php echo JText::_('ROOMNAME'); ?></th>
        	<th><?php echo JText::_('ROOMDESCRIPTION'); ?></th>
        	<th><?php echo JText::_('STATUS'); ?></th>
        </tr>
        
        <?php
		$i = 0;
		foreach($this->items as $item) {
		$canChange  = $user->authorise('core.edit.state',  'com_roombooking.room'.$item->id);
		?>
        <tr>
        <td class="center"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
        <td><a href="index.php?option=com_roombooking&view=room&layout=edit&id=<?php echo $item->id ; ?>"><?php echo $item->room_name ; ?></a></td>
        <td><?php echo $item->room_description ; ?></td>
        <td><?php echo JHtml::_('jgrid.published', $item->status, $i, 'rooms.', $canChange); ?></td>
        </tr>
        <?php
		$i++;	
		}
		?>
    </table>
    <input type="hidden" value="" name="task" id="task" />
    <input type="hidden" value="rooms" name="controller" id="controller" />
    <input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>
</form>