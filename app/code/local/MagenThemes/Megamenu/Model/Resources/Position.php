<?php
/******************************************************
 * @package Megamenu module for Magento 1.4.x.x and Magento 1.5.x.x
 * @version 1.5.0.4
 * @author http://www.9magentothemes.com
 * @copyright (C) 2011- 9MagentoThemes.Com
 * @license PHP files are GNU/GPL
*******************************************************/
?>
<?php
class MagenThemes_Megamenu_Model_Resources_Position
{
    public function toOptionArray() {
        return array(
                    array('label' => 'Main Menu', 'value' => 'top'),
                    //array('label' => 'Top Left', 'value' => 'top-left'),
                    //array('label' => 'Top Right', 'value' => 'top-right'),
                    //array('label' => 'Top Content', 'value' => 'top-content'),
                    //array('label' => 'Bottom Left', 'value' => 'bottom-left'),
                    //array('label' => 'Bottom Right', 'value' => 'bottom-right'),
                    //array('label' => 'Bottom Content', 'value' => 'bottom-content')
                );
    }
    
    public function toOptionArrayGrid() {
        return array(
                    'top'               => 'Main Menu',
                    //'top-left'          => 'Top Left',
                    //'top-right'         => 'Top Right',
                    //'top-content'       => 'Top Content',
                    //'bottom-left'       => 'Bottom Left',
                    //'bottom-right'      => 'Bottom Right',
                    //'bottom-content'    => 'Bottom Content'
                );
    }
}