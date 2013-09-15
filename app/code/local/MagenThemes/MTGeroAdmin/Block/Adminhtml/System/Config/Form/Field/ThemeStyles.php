<?php
class MagenThemes_MTGeroAdmin_Block_Adminhtml_System_Config_Form_Field_ThemeStyles extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element){ 
       	$html = parent::_getElementHtml($element);  
       	$config = Mage::getStoreConfig('mtgeroadmin/mtgeroadmin_appearance');
       	$bg_color = isset($config['bg_color']) ? $config['bg_color'] :''; 
       	$text_color = isset($config['text_color']) ? $config['text_color'] :'';
       	$link_color = isset($config['link_color']) ? $config['link_color'] :'';
       	$pattern_body = isset($config['pattern_body']) ? $config['pattern_body'] :'';
       	$menu_bg_color = isset($config['menu_bg_color']) ? $config['menu_bg_color'] :'';
       	$menu_text_color = isset($config['menu_text_color']) ? $config['menu_text_color'] :'';
       	$menu_link_color = isset($config['menu_link_color']) ? $config['menu_link_color'] :'';
       	$pattern_menu = isset($config['pattern_menu']) ? $config['pattern_menu'] :'';
       	
       	$main_home_bg = isset($config['main_home_bg']) ? $config['main_home_bg'] :'';
       	$main_text_color = isset($config['main_text_color']) ? $config['main_text_color'] :'';
       	$footer_pattern = isset($config['footer_pattern']) ? $config['footer_pattern'] :'';
       	$footer_static_text_color = isset($config['footer_static_text_color']) ? $config['footer_static_text_color'] :'';
       	$footer_static_link_color = isset($config['footer_static_link_color']) ? $config['footer_static_link_color'] :'';
       	
       	$html .='
       		<script type="text/javascript" src="'.$this->getJsUrl('magenthemes/mt_gero/js/jquery.min.1.7.1.js').'"></script>
       		<script type="text/javascript" src="'.$this->getJsUrl('magenthemes/mt_gero/js/mColorPicker.js').'"></script>
            <style>#mColorPickerImg{background-image: url("'.$this->getJsUrl('magenthemes/mt_gero/images/').'picker.png") !important;}</style>
			<script type="text/javascript"> 
       				$mtkb(document).ready(function($) { 
       					$mtkb.fn.mColorPicker.defaults.currentId=false;
		            	$mtkb.fn.mColorPicker.defaults.currentInput = false;
		            	$mtkb.fn.mColorPicker.defaults.currentColor = false;
		            	$mtkb.fn.mColorPicker.defaults.changeColor = false;
		            	$mtkb.fn.mColorPicker.init.showLogo = false;
		            	$mtkb.fn.mColorPicker.defaults.color = true;
		            	$mtkb.fn.mColorPicker.defaults.imageFolder = "'.$this->getJsUrl('magenthemes/mt_gero/images/').'"; 
       					var value = "'.$config['theme_styles'].'"; 
    					var styles = {
							red : {
								bg_color: "#ffffff", 
								text_color: "#333430",
								link_color: "#D21D2B",
								pattern_body: "pattern_3",
								menu_bg_color: "#CD1507",
								menu_text_color: "#333430",
								menu_link_color: "#ffffff",
								pattern_menu: "pattern_4",
       							main_home_bg: "pattern_2",
       							main_text_color: "#333430",
       							footer_pattern: "pattern_2",
       							footer_static_text_color: "#333430",
       							footer_static_link_color: "#D21D2B"
							},
							green : {
								bg_color: "#ffffff", 
								text_color: "#333430",
								link_color: "#41A006",
								pattern_body: "pattern_5",
								menu_bg_color: "#4BB10C",
								menu_text_color: "#333430",
								menu_link_color: "#41A006",
								pattern_menu: "pattern_4",
       							main_home_bg: "pattern_8",
       							main_text_color: "#333430",
       							footer_pattern: "pattern_8",
       							footer_static_text_color: "#333430",
       							footer_static_link_color: "#41A006"
							},
							blue : {
								bg_color: "#ffffff", 
								text_color: "#333430",
								link_color: "#00B0AE",
								pattern_body: "pattern_6",
								menu_bg_color: "#08BBB9",
								menu_text_color: "#333430",
								menu_link_color: "#00B0AE",
								pattern_menu: "pattern_4",
       							main_home_bg: "pattern_7",
       							main_text_color: "#333430",
       							footer_pattern: "pattern_7",
       							footer_static_text_color: "#333430",
       							footer_static_link_color: "#00B0AE"
							},
       						custom : {
    							bg_color: "'.$bg_color.'", 
								text_color: "'.$text_color.'",
								link_color: "'.$link_color.'",
								pattern_body: "'.$pattern_body.'",
								menu_bg_color: "'.$menu_bg_color.'",
								menu_text_color: "'.$menu_text_color.'",
								menu_link_color: "'.$menu_link_color.'",
								pattern_menu: "'.$pattern_menu.'",
								main_home_bg: "'.$main_home_bg.'",
       							main_text_color: "'.$main_text_color.'",
       							footer_pattern: "'.$footer_pattern.'",
       							footer_static_text_color: "'.$footer_static_text_color.'",
       							footer_static_link_color: "'.$footer_static_link_color.'"
    						} 
						} 
       					changeStyle(value,styles);
       					$mtkb("#'.$element->getHtmlId().'").bind("change", function() {  
       						value = $mtkb("#'.$element->getHtmlId().'").val(); 
       						changeStyle(value,styles); 
						}); 
       					function changeStyle(apper,styles){ 
       						if(apper=="red" || apper=="blue" || apper=="green"){
    							$mtkb("#mtgeroadmin_mtgeroadmin_appearance_bg_color").attr("readonly","");
       							$mtkb("#mtgeroadmin_mtgeroadmin_appearance_text_color").attr("readonly","");
       							$mtkb("#mtgeroadmin_mtgeroadmin_appearance_link_color").attr("readonly","");
       							$mtkb("#mtgeroadmin_mtgeroadmin_appearance_pattern_body").css("display","none");
       							$mtkb("#mtgeroadmin_mtgeroadmin_appearance_menu_bg_color").attr("readonly","");
       							$mtkb("#mtgeroadmin_mtgeroadmin_appearance_menu_text_color").attr("readonly","");
       							$mtkb("#mtgeroadmin_mtgeroadmin_appearance_menu_link_color").attr("readonly","");
       							$mtkb("#mtgeroadmin_mtgeroadmin_appearance_pattern_menu").css("display","none");
       							$mtkb("#mtgeroadmin_mtgeroadmin_appearance_main_home_bg").css("display","none"); 
       							$mtkb("#mtgeroadmin_mtgeroadmin_appearance_main_text_color").attr("readonly","");
       							$mtkb("#mtgeroadmin_mtgeroadmin_appearance_footer_pattern").css("display","none");
       							$mtkb("#mtgeroadmin_mtgeroadmin_appearance_footer_static_text_color").attr("readonly","");
       							$mtkb("#mtgeroadmin_mtgeroadmin_appearance_footer_static_link_color").attr("readonly","");
       							 
    						}else{
    							$mtkb("#mtgeroadmin_mtgeroadmin_appearance_bg_color").removeAttr("readonly");
       							$mtkb("#mtgeroadmin_mtgeroadmin_appearance_text_color").removeAttr("readonly");
       							$mtkb("#mtgeroadmin_mtgeroadmin_appearance_link_color").removeAttr("readonly");
       							$mtkb("#mtgeroadmin_mtgeroadmin_appearance_pattern_body").css("display","block");
       							$mtkb("#mtgeroadmin_mtgeroadmin_appearance_menu_bg_color").removeAttr("readonly");
       							$mtkb("#mtgeroadmin_mtgeroadmin_appearance_menu_text_color").removeAttr("readonly");
       							$mtkb("#mtgeroadmin_mtgeroadmin_appearance_menu_link_color").removeAttr("readonly");
       							$mtkb("#mtgeroadmin_mtgeroadmin_appearance_pattern_menu").css("display","block"); 
       							$mtkb("#mtgeroadmin_mtgeroadmin_appearance_main_home_bg").css("display","block"); 
       							$mtkb("#mtgeroadmin_mtgeroadmin_appearance_main_text_color").removeAttr("readonly");
       							$mtkb("#mtgeroadmin_mtgeroadmin_appearance_footer_pattern").css("display","block"); 
       							$mtkb("#mtgeroadmin_mtgeroadmin_appearance_footer_static_text_color").removeAttr("readonly");
       							$mtkb("#mtgeroadmin_mtgeroadmin_appearance_footer_static_link_color").removeAttr("readonly");
								 
    						} 
       						$mtkb.fn.mColorPicker.setInputColor("mtgeroadmin_mtgeroadmin_appearance_bg_color", styles[apper]["bg_color"]);
       						$mtkb.fn.mColorPicker.setInputColor("mtgeroadmin_mtgeroadmin_appearance_text_color", styles[apper]["text_color"]); 
       						$mtkb.fn.mColorPicker.setInputColor("mtgeroadmin_mtgeroadmin_appearance_link_color", styles[apper]["link_color"]);	
       						$mtkb("#mtgeroadmin_mtgeroadmin_appearance_pattern_body option").each(function(i,opt){
       							if(opt.value==styles[apper].pattern_body){ 
       								$mtkb("#mtgeroadmin_mtgeroadmin_appearance_pattern_body option[value="+opt.value+"]").attr("selected","selected");
       								$mtkb("#bg_mtgeroadmin_mtgeroadmin_appearance_pattern_body").css("background","url('. Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/default/mt_gero/images/pattern/"+opt.value+".png) repeat");     
    							}   
    						});  
       						$mtkb.fn.mColorPicker.setInputColor("mtgeroadmin_mtgeroadmin_appearance_menu_bg_color", styles[apper]["menu_bg_color"]);
       						$mtkb.fn.mColorPicker.setInputColor("mtgeroadmin_mtgeroadmin_appearance_menu_text_color", styles[apper]["menu_text_color"]); 
       						$mtkb.fn.mColorPicker.setInputColor("mtgeroadmin_mtgeroadmin_appearance_menu_link_color", styles[apper]["menu_link_color"]);
       						$mtkb("#mtgeroadmin_mtgeroadmin_appearance_pattern_menu option").each(function(i,opt){
       							if(opt.value==styles[apper].pattern_menu){ 
       								$mtkb("#mtgeroadmin_mtgeroadmin_appearance_pattern_menu option[value="+opt.value+"]").attr("selected","selected");
       								$mtkb("#bg_mtgeroadmin_mtgeroadmin_appearance_pattern_menu").css("background","url('. Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/default/mt_gero/images/pattern/"+opt.value+".png) repeat");     
    							}   
    						});  	
       						$mtkb("#mtgeroadmin_mtgeroadmin_appearance_main_home_bg option").each(function(i,opt){
       							if(opt.value==styles[apper].main_home_bg){ 
       								$mtkb("#mtgeroadmin_mtgeroadmin_appearance_main_home_bg option[value="+opt.value+"]").attr("selected","selected");
       								$mtkb("#bg_mtgeroadmin_mtgeroadmin_appearance_main_home_bg").css("background","url('. Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/default/mt_gero/images/pattern/"+opt.value+".png) repeat");     
    							}   
    						});	
       						$mtkb("#mtgeroadmin_mtgeroadmin_appearance_footer_pattern option").each(function(i,opt){
       							if(opt.value==styles[apper].footer_pattern){ 
       								$mtkb("#mtgeroadmin_mtgeroadmin_appearance_footer_pattern option[value="+opt.value+"]").attr("selected","selected");
       								$mtkb("#bg_mtgeroadmin_mtgeroadmin_appearance_footer_pattern").css("background","url('. Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/default/mt_gero/images/pattern/"+opt.value+".png) repeat");     
    							}   
    						});		
       						$mtkb.fn.mColorPicker.setInputColor("mtgeroadmin_mtgeroadmin_appearance_main_text_color", styles[apper]["main_text_color"]);
       						$mtkb.fn.mColorPicker.setInputColor("mtgeroadmin_mtgeroadmin_appearance_footer_static_text_color", styles[apper]["footer_static_text_color"]); 
       						$mtkb.fn.mColorPicker.setInputColor("mtgeroadmin_mtgeroadmin_appearance_footer_static_link_color", styles[apper]["footer_static_link_color"]); 
    					 }
    				});
       				
            </script>
       	'; 
        return $html;
    }
}
?>