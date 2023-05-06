<?php
defined( '_JEXEC' ) or die( '=;)' );

jimport( 'joomla.application.component.view');

class  RoombookingViewAddon extends JViewLegacy
{
	protected $form;
    function display($tpl = null)
    {
		$this->form			= $this->get('Form');
		$this->item			= $this->get('Item');
	   	JToolBarHelper::title(   JText::_( 'ADDON' ), 'generic.png' ); 
       	JToolBarHelper::preferences('com_roombooking', '400');   
	   	JToolbarHelper::apply('addon.apply'); 
	   	JToolbarHelper::save('addon.save');
	   	JToolbarHelper::cancel('addon.cancel');          
       	parent::display($tpl);  
    }      
	
}
