<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.4.0_2.0.3_467520
 * Purchase ID: JlNeBKBvSqsIsT7whc80ZpBA38zH86mwW38f4D7p5H
 * Generated:   2013-01-14 03:56:09
 * File path:   app/code/local/Aitoc/Aitpermissions/controllers/Adminhtml/RoleController.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ OsOwBYRrwNaMjgBd('44f4ac6d46f7290c80e47b33aeaeeeeb'); ?><?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/
class Aitoc_Aitpermissions_Adminhtml_RoleController extends Mage_Adminhtml_Controller_Action
{
    public function duplicateAction()
    {
        $roleModel    = Mage::getModel('admin/roles');
        $aitRoleModel = Mage::getModel('aitpermissions/advancedrole');
        $loadRole     = $roleModel->load($this->getRequest()->getParam('rid'));
        $roleName     = $loadRole->getRoleName();
        $ruleModel    = Mage::getModel("admin/rules");
        $loadRuleCollection = $ruleModel->getCollection()->addFilter('role_id',$this->getRequest()->getParam('rid'));
        //echo "<pre>"; print_r($loadRuleCollection->getSize()); exit;
        $loadAitRoleCollection  = $aitRoleModel->getCollection()->addFilter('role_id',$this->getRequest()->getParam('rid'));
        try
        {
            $roleModel->setId(null)
                ->setName('Copy of '.$loadRole->getRoleName())
                ->setPid($loadRole->getParentId())
                ->setTreeLevel($loadRole->getTreeLevel())
                ->setType($loadRole->getType())
                ->setUserId($loadRole->getUserId())
             ->save();
//            foreach (explode(",",$roleModel->getUserId()) as $nRuid) {
//                $this->_addUserToRole($nRuid, $roleModel->getId());
//            }
            
            foreach ($loadRuleCollection as $rule)
            {
                $ruleModel
                    ->setData($rule->getData())
                    ->setRuleId(null)
                    ->setRoleId($roleModel->getId())
                ->save();
            }
            $newRoleId =  $roleModel->getRoleId();
            foreach ($loadAitRoleCollection as $loadAitRole)
            {
                $aitRoleModel->setId(null)
                    ->setRoleId($newRoleId)
                    ->setWebsiteId($loadAitRole->getWebsiteId())
                    ->setStoreId($loadAitRole->getStoreId())
                    ->setStoreviewIds($loadAitRole->getStoreviewIds())
                    ->setCategoryIds($loadAitRole->getCategoryIds())
                    ->setCanEditGlobalAttr($loadAitRole->getCanEditGlobalAttr())
                    ->setCanEditOwnProductsOnly($loadAitRole->getCanEditOwnProductsOnly())
                ->save();
            }
        }
        catch (Exception $e)
        {
            $this->_getSession()->addError($this->__("Role %s wasn't duplicated. %s",$roleName,$e->getMessage()));
        }   
        $this->_getSession()->addSuccess($this->__("Role %s was duplicated",$roleName));
        $this->_redirect('adminhtml/permissions_role/index');
        
        return $this;
    }
    
//    protected function _addUserToRole($userId, $roleId)
//    {
//        $user = Mage::getModel("admin/user")->load($userId);
//        $user->setRoleId($roleId)->setUserId($userId);
//
//        if( $user->roleUserExists() === true ) {
//            return false;
//        } else {
//            $user->add();
//            return true;
//        }
//    }
} } 