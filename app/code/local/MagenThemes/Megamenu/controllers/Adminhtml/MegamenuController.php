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
class MagenThemes_Megamenu_Adminhtml_MegamenuController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction() {
	$this->_title($this->__('Manage Megamenu Items'))
	    ->_title($this->__('Megamenu'));
	    
	$this->loadLayout()
		->_setActiveMenu('magenthemes/megamenu')
		->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
	
	return $this;
    }   
 
    public function indexAction()
    {
        $this->_forward('edit');
    }
    
    public function editAction() {
	$id     = $this->getRequest()->getParam('id');
	$megamenu  = Mage::getModel('megamenu/megamenu')->load($id);
	Mage::register('current_megamenu', $megamenu);
	if ($this->getRequest()->getQuery('isAjax')) {
	    $result = array();
	    Mage::getSingleton('admin/session')
		    ->setLastViewedStore($this->getRequest()->getParam('storeId'));
	    Mage::getSingleton('admin/session')
		    ->setLastEditedMegamenu($megamenu->getId());
	    $this->loadLayout();
	    $result['messages'] = $this->getLayout()->getMessagesBlock()->getGroupedHtml();
	    $result['content'] = $this->getLayout()->getBlock('megamenu.edit')->getFormHtml();
	    if(!$id)
		$result['tree'] = $this->getLayout()->createBlock('megamenu/adminhtml_megamenu_tree')->getTreeHtml();
	    $this->getResponse()->setBody(Zend_Json::encode($result));
	    return;
	}
	
	$this->_initAction();

	$this->renderLayout();
    }
    
    private function _importCategory($category)
    {
	$category = $category->load($category->getId());
	$parentCategoryId = $category->getParentId();
	$parentMegamenu = Mage::getModel('megamenu/megamenu')->loadByCategoryId($parentCategoryId);
	$megamenu = Mage::getModel('megamenu/megamenu')
			->setTitle($category->getName())
			->setLevel($category->getLevel() - 1)
			->setIsContent(2)
			->setShowTitle(1)
			->setStatus(2)
			->setType('category')
			->setArticle($category->getId())
			->setParentId($parentMegamenu->getId());
	try {
	    $megamenu->save();
	} catch(Exception $e) {
	    throw Exception($e);
	}
    }
    
    public function importCategoriesAction()
    {
	$root = Mage::getModel('megamenu/megamenu')
			->getCollection()
			->addFieldToFilter('parent_id', 0)
			->setStoreFilter($this->getRequest()->getParam('store_id'));
	if(!count($root)) {
	    $existsIds = array();
	    $existsCategory = Mage::getModel('megamenu/megamenu')
				->getCollection()
				->addFieldToFilter('type', 'category');
	    foreach($existsCategory as $category) {
		$existsIds[] = $category->getArticle();
	    }
	    
	    $collection = Mage::getModel('catalog/category')
			    ->getCollection()
			    ->setOrder('level', 'ASC')
			    ->addFieldToFilter('level', array('gt' => 0));
	    try{
		foreach($collection as $category) {
		    if(!in_array($category->getId(), $existsIds))
		    $this->_importCategory($category);
		}
	    } catch(Exception $e) {
		Mage::getSingleton('adminhtml/session')->addError('Have error when import categories.');
		return;
	    }
	} else {
	    Mage::getSingleton('adminhtml/session')->addError('Only one root Menu on Store');
	    return;
	}
    }
    
    public function moveAction()
    {
	$ids = $this->getRequest()->getParam('ids');
	if($ids) {
	    $moveId = $this->getRequest()->getParam('move_id');
	    $parentId = $this->getRequest()->getParam('parent_id');
	    try{
		$move = Mage::getModel('megamenu/megamenu')->load($moveId);
		if($move->getParentId() != $parentId) {
		    $move->setParentId($parentId)
			->save();
		}
		$ids = explode(',', $ids);
		$i = 0;
		foreach($ids as $id) {
		    if($id) {
			Mage::getModel('megamenu/megamenu')->load($id)->setPosition($i)->save();
			$i++;
		    }
		}
	    } catch(Exception $e) {
		Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		return;
	    }
	} else {
	    Mage::getSingleton('adminhtml/session')->addError('Have error when move megamenus.');
	    return;
	}
    }
    
    public function saveAction() {
	$result = array();
	if ($data = $this->getRequest()->getPost()) {
	    $model = Mage::getModel('megamenu/megamenu');
	    if ($id = $this->getRequest()->getParam('id')) {
		$model->load($id);
	    }
	    $image = '';
	    if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
		try {
		    $uploader = new Varien_File_Uploader('image');
		    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
		    $uploader->setAllowRenameFiles(true);
		    $uploader->setFilesDispersion(true);
		    $path = Mage::getBaseDir('media') . DS . 'megamenu';
		    $uploader->save($path, $_FILES['image']['name']);
		} catch (Exception $e) {
		    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		    return;
		}
		$image = 'megamenu'.$uploader->getUploadedFileName();
	    }
	    elseif ($model->getImage() != '') {
		$image = $model->getImage();
	    }
							    				    
	    if(isset($data['image']['delete']) && $data['image']['delete'] == 1) {
		$image = '';
	    }
	    
	    $data['megamenu']['image'] = $image;
	    
	    if($this->getRequest()->getParam('type')) {
		$data['megamenu']['type'] = $this->getRequest()->getParam('type');
	    }
	    
	    if($data['megamenu']['parent_id']) {
		$level = Mage::getModel('megamenu/megamenu')->load($data['megamenu']['parent_id'])->getLevel();
		$level += 1;
		$data['megamenu']['level'] = $level;
	    } else {
		$root = Mage::getModel('megamenu/megamenu')
			    ->getCollection()
			    ->addFieldToFilter('parent_id', 0)
			    ->setStoreFilter($data['store_id']);
		if(count($root)) {
		    Mage::getSingleton('adminhtml/session')->addError('Only one root Menu on Store');
		    $result['redirect'] = $this->getUrl('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
		    $this->getResponse()->setBody(
			'<script type="text/javascript">parent.window.location.href = "'.$result['redirect'].'";</script>'
		    );
		    return;
		} else {
		    $data['megamenu']['level'] = 0;
		}
	    }
	    
	    if(isset($data['groups'])) {
		$data['megamenu']['groups'] = array();
		if($data['groups']) {
		    $groups = Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['groups']);
		    foreach($groups as $group) {
			$data['megamenu']['groups'][] = array('megamenu_group_id' => $group);
		    }
		}
	    }
	    
	    $model->setData($data['megamenu'])
		    ->setStoreId($data['store_id']);
	    if($this->getRequest()->getParam('id'))
		$model->setId($this->getRequest()->getParam('id'));
	    try {
		$model->save();
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('megamenu')->__('Megamenu was successfully saved'));
		$result['redirect'] = $this->getUrl('*/*/edit', array('id' => $model->getId()));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $result['redirect'] = $this->getUrl('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        } else {
	    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('megamenu')->__('Unable to find megamenu to save'));
	    $result['redirect'] = $this->getUrl('*/*/edit');
	}
        $this->getResponse()->setBody(
            '<script type="text/javascript">parent.window.location.href = "'.$result['redirect'].'";</script>'
        );
    }
    
    public function deleteAction($menuid = null) {
    	$menuid = $menuid===null ? $this->getRequest()->getParam('id') : $menuid;
    	if($menuid) {
    		try {
    			$model = Mage::getModel('megamenu/megamenu');
    			if ($model->setId($menuid)->hasChild()) {
    				$childs = $model->setId($menuid)->getChildItem();
    				foreach ($childs as $child) {
    					$this->deleteAction($child->getId());
    				}
    			} else {
    				$model->setId($menuid)
    				->delete();
    			}
    			$model->setId($menuid)
    			->delete();
    			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
    			$this->_redirect('*/*/');
    		} catch (Exception $e) {
    			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
    			$this->_redirect('*/*/edit', array('id' => $menuid));
    		}
    	}
    	$this->_redirect('*/*/');
    } 
}