<?php

function checkColumn($code){
    $attr = Mage::getModel('catalog/resource_eav_attribute')->loadByCode('customer',$code);
	if(NULL){
		return false;
	}
	return true;
}

//Attribute to add
$newAttributeName = "membershipid"; //modify this with the name of your attribute
if(checkColumn($newAttributeName)){
	 
	//a) Add EAV Attributes (modify as you needed)
	$attribute  = array(
		'type'          => 'varchar',
		'label'         => 'Membership ID',
		'visible'       => true,
		'required'      => false,
		'user_defined'  => false,
		'searchable'    => false,
		'filterable'    => false,
		'comparable'    => false,
	);
	 
	$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
	//Add to customer
	$setup->addAttribute('customer', $newAttributeName, $attribute);
}
?>