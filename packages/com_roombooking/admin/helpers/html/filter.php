<?php
 
 
class Wine_Filter
{
	public static function pak_options($pref = null)
	{
		$db = &JFactory::getDBO();

       //build the list of categories
		$query = 'SELECT a.title AS text, a.id AS value'
		. ' FROM #__shop_pak'.$pref.' AS a'
	 
		. ' ORDER BY a.title';
       
       
		$db->setQuery( $query );
		$pgbc = $db->loadObjectList();
	
	  
	 
		return $pgbc;

	}
    
    	public static function region_options($lander = null)
	{
		$db = JFactory::getDBO();

 if ($lander)
 $lander = ' WHERE lander='.$lander;
 else
 $lander = null;
 
       //build the list of categories
		$query = 'SELECT a.title AS text, a.id AS value'
		. ' FROM #__wine_region  AS a '.$lander
	 
		. ' ORDER BY a.title';
       
       
		$db->setQuery( $query );
		$pgbc = $db->loadObjectList();
	
	   
	 
		return $pgbc;

	}
    
    	public static function manuf_options($region = null)
	{
		$db = JFactory::getDBO();

 if ($region)
 $region = ' WHERE region='.$region;
 else
 $region = null;
 
       //build the list of categories
		$query = 'SELECT a.title AS text, a.id AS value'
		. ' FROM #__wine_manuf  AS a '.$region
	 
		. ' ORDER BY a.title';
       
       
		$db->setQuery( $query );
		$pgbc = $db->loadObjectList();
	
	   
	 
		return $pgbc;

	}
    
    public static function type_options()
	{
	 
  	$db = JFactory::getDBO();
		  
		$pgbc = null;
        $pgbc[0]->text =JText::_('WHILE');
        $pgbc[0]->value =1;
    
    	$pgbc[1]->text =JText::_('RED');
        $pgbc[1]->value =2;
    
        $pgbc[2]->text =JText::_('PINK');
        $pgbc[2]->value =3;
	   
       
       	$query = 'SELECT a.title AS text, a.id AS value'
		. ' FROM #__wine_tip1  AS a '.$region
	 
		. ' ORDER BY a.ordering';
       
       
		$db->setQuery( $query );
		$pgbc = $db->loadObjectList();
	 
		return $pgbc;

	}
    
    public static function type1_options()
	{ 	$db = JFactory::getDBO();
	    $pgbc = null;
        $pgbc[0]->text =JText::_('BRUT');
        $pgbc[0]->value =1;
    
    	$pgbc[1]->text =JText::_('SEMISWEET');
        $pgbc[1]->value =2;
    
        $pgbc[2]->text =JText::_('SEMIDRY');
        $pgbc[2]->value =3;
        
        $pgbc[3]->text =JText::_('SWEET');
        $pgbc[3]->value =4;
        
        $pgbc[4]->text =JText::_('DRY');
        $pgbc[4]->value =5;
        
         $pgbc[5]->text =JText::_('QUIET');
         $pgbc[5]->value =6;
	    
	 
     
     	$query = 'SELECT a.title AS text, a.id AS value'
		. ' FROM #__wine_tip2  AS a '.$region
	 
		. ' ORDER BY a.ordering';
       
       
		$db->setQuery( $query );
		$pgbc = $db->loadObjectList();
     
		return $pgbc;

	}
}
