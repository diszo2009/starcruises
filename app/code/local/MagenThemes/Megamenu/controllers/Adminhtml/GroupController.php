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
class MagenThemes_Megamenu_Adminhtml_GroupController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('megamenu/group')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Group Manager'), Mage::helper('adminhtml')->__('Group Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('megamenu/group')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('megamenu_group_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('megamenu/group');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Group Manager'), Mage::helper('adminhtml')->__('Group Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Group News'), Mage::helper('adminhtml')->__('Group News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('megamenu/adminhtml_group_edit'))
				->_addLeft($this->getLayout()->createBlock('megamenu/adminhtml_group_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('megamenu')->__('Group does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
	
	public function parentsGridAction() {
		$groupId = $this->getRequest()->getParam('id');
		if($groupId) {
			Mage::register('megamenu_group_data', Mage::getModel('megamenu/group')->load($groupId));
		}
		$this->getResponse()->setBody(
            $this->getLayout()->createBlock('megamenu/adminhtml_group_edit_tab_group_grid')->toHtml()
        );
	}
	
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			$model = Mage::getModel('megamenu/group');
			//save megamenu
			if(isset($data['megamenus'])) {
				$data['group']['megamenus'] = array();
				if($data['megamenus']) {
					$megamenus = Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['megamenus']);
					if(count($megamenus)) {
						$megamenuArray = array();
						foreach($megamenus as $megamenuId => $array) {
							$megamenuArray[] = array('megamenu_id' => $megamenuId, 'sort_order' => $array['sort_order']);
						}
						$data['group']['megamenus'] = $megamenuArray;
					}
				}
			}
			
			$model->setData($data['group'])
				->setId($this->getRequest()->getParam('id'));
			try {	
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('megamenu')->__('Group was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('megamenu')->__('Unable to find group to save'));
        $this->_redirect('*/*/');
	}
 
	public function megamenusAction() {
		$this->loadLayout();
		$this->getLayout()->getBlock('megamenu.group.megamenus')
			->setGroupMegamenu($this->getRequest()->getPost('group_megamenu', null));
		$this->renderLayout();
	}
	
	public function megamenusGridAction() {
		$this->loadLayout();
		$this->getLayout()->getBlock('megamenu.group.megamenus')
			->setGroupMegamenu($this->getRequest()->getPost('group_megamenu', null));
		$this->renderLayout();
	}
	
	public function deleteAction() { 
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('megamenu/group');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Group was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $megamenuIds = $this->getRequest()->getParam('megamenu_group');
        if(!is_array($megamenuIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($megamenuIds as $megamenuId) {
                    $megamenu = Mage::getModel('megamenu/group')->load($megamenuId);
                    $megamenu->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($megamenuIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $megamenuIds = $this->getRequest()->getParam('megamenu_group');
        if(!is_array($megamenuIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($megamenuIds as $megamenuId) {
                    $megamenu = Mage::getSingleton('megamenu/group')
                        ->load($megamenuId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($megamenuIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'megamenu.csv';
        $content    = $this->getLayout()->createBlock('megamenu/adminhtml_megamenu_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'megamenu.xml';
        $content    = $this->getLayout()->createBlock('megamenu/adminhtml_megamenu_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}