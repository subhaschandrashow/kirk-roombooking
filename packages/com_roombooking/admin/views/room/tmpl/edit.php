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
			<?php echo $this->form->getLabel('room_name'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('room_name') ; ?>
		</div>
	</div>
    
    <div class="control-group">
		<div class="control-label">
			<?php echo $this->form->getLabel('room_description'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('room_description') ; ?>
		</div>
	</div>
   
   <div class="control-group">
		<div class="control-label">
			<?php echo $this->form->getLabel('status'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('status') ; ?>
		</div>
	</div>
    
    
	<input type="hidden" name="task" id="task" value="room.save">
    <input type="hidden" name="view" value="room">
	<?php echo JHtml::_('form.token'); ?>
</form>
