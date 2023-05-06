<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.modeladmin');

class RoombookingModelRoombooking extends JModelAdmin
{
	
	public function getTable($type = 'Onlineorder', $prefix = 'Table', $config = array())
	{
		$table = JTable::getInstance($type, $prefix, $config);
		return $table;
	}

	public function getItem($pk = NULL)
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$virtuemart_order_id = $jinput->get('id');
		$db = JFactory::getDBO();
		$params = JComponentHelper::getParams('com_vmapi');
		$virtuemart_lang 	= $params->get('virtuemart_lang');
		$query	= $db->getQuery(true);
		 
		$query->select(
			 "a.* , b.* , c.* , d.* "
		);
		$query->from(' `#__virtuemart_orders` as a , `#__virtuemart_order_userinfos` as b , `#__virtuemart_shipmentmethods_'.$virtuemart_lang.'` as c , `#__virtuemart_paymentmethods_'.$virtuemart_lang.'` as d ');
		$query->where(' a.virtuemart_order_id = b.virtuemart_order_id AND a.virtuemart_shipmentmethod_id = c.virtuemart_shipmentmethod_id AND a.virtuemart_paymentmethod_id = d.virtuemart_paymentmethod_id AND a.virtuemart_order_id = '.$virtuemart_order_id);
		
		$db->setQuery($query);
		$item = $db->loadObject();
		return $item;
	}
	
	public function getProducts()
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$virtuemart_order_id = $jinput->get('id');
		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM `#__virtuemart_order_items` WHERE `virtuemart_order_id` = '".$virtuemart_order_id."'");
		$item = $db->loadObjectList();
		return $item;
	}
	
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_vmapi.onlineorder', 'onlineorder', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}
		return $form;
	}

	
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_vmapi.edit.onlineorder.data', array());
		if (empty($data)) {
			$data = $this->getItem();
		}
		return $data;
	}

	
	protected function preprocessForm(JForm $form, $data, $group = 'onlineorder')
	{
		parent::preprocessForm($form, $data, $group);
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
