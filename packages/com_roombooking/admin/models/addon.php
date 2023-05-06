<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.modeladmin');

class RoombookingModelAddon extends JModelAdmin
{

	
	public function getTable($type = 'Addon', $prefix = 'Table', $config = array())
	{
		$table = JTable::getInstance($type, $prefix, $config);

		return $table;
	}

	
	public function getForm($data = array(), $loadData = true)
	{
		
		// Get the form.
		$form = $this->loadForm('com_roomname.addon', 'addon', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		
		return $form;
	}

	
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_roomname.edit.addon.data', array());
		if (empty($data)) {
			$data = $this->getItem();
		}
		return $data;
	}

	
	protected function preprocessForm(JForm $form, $data, $group = 'addon')
	{
		parent::preprocessForm($form, $data, $group);
	}

	
	public function save($data)
	{
		if (parent::save($data)) {
			return true;
		}
	}

	
	public function delete(&$pks)
	{
		$user  = JFactory::getUser();
		$table = $this->getTable();
		$pks   = (array) $pks;

		// Check if I am a Super Admin
		$iAmSuperAdmin = $user->authorise('core.admin');

		JPluginHelper::importPlugin($this->events_map['delete']);
		$dispatcher = JEventDispatcher::getInstance();

		if (in_array($user->id, $pks))
		{
			$this->setError(JText::_('COM_USERS_USERS_ERROR_CANNOT_DELETE_SELF'));

			return false;
		}

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{
			if ($table->load($pk))
			{
				// Access checks.
				$allow = $user->authorise('core.delete', 'com_users');

				// Don't allow non-super-admin to delete a super admin
				$allow = (!$iAmSuperAdmin && JAccess::check($pk, 'core.admin')) ? false : $allow;

				if ($allow)
				{
					// Get users data for the users to delete.
					$user_to_delete = JFactory::getUser($pk);

					// Fire the before delete event.
					$dispatcher->trigger($this->event_before_delete, array($table->getProperties()));

					if (!$table->delete($pk))
					{
						$this->setError($table->getError());

						return false;
					}
					else
					{
						// Trigger the after delete event.
						$dispatcher->trigger($this->event_after_delete, array($user_to_delete->getProperties(), true, $this->getError()));
					}
				}
				else
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					JError::raiseWarning(403, JText::_('JERROR_CORE_DELETE_NOT_PERMITTED'));
				}
			}
			else
			{
				$this->setError($table->getError());

				return false;
			}
		}

		return true;
	}

	
}
