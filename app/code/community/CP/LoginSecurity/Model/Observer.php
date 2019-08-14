<?php 
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Commerce Pundit Technologies
 * @package     CP_LoginSecurity
 * @copyright   Copyright (c) 2015 Commerce Pundit Technologies. (http://www.commercepundit.com)    
* @author   <<Dharmesh Hariyani>>    
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CP_LoginSecurity_Model_Observer extends Mage_Core_Model_Abstract 
{
	//When customer request new password and after changing new password, cutomer will loginsecurity from all other browser.
	public function loginsecurityOnPasswordChange($observer)
	{
        if(Mage::getStoreConfig('loginsecuritysetting/login/enable',Mage::app()->getStore()) == '1')
        {
            $session = Mage::getSingleton('customer/session');
		    if ($session->isLoggedIn()) {
		        // Load the customer's data
			    $newHashPasswrd = $session->getCustomer()->getPasswordHash();
			    $oldHashPasswrd = $session->getCurrentPasswordHash();

                if($newHashPasswrd!=$oldHashPasswrd && $oldHashPasswrd!="")
			    {
                    $session->logout();
                    $session->setCurrentPasswordHash('');
                    Mage::getSingleton('core/session')->addError('This account password is just changed from another device/browser. We recommend to login again.');
                    $url = Mage::getUrl('customer/account/login/passwordchanged/true');
                    $observer->getEvent()->getResponse()->setRedirect($url);
                }
            }
        }
	}
	
	//Set Current password on session
	public function setCurrentPassword($observer)
	{
        if(Mage::getStoreConfig('loginsecuritysetting/login/enable',Mage::app()->getStore()) == '1')
        {
		    $currentHashPassword = $observer->getCustomer()->getPasswordHash();
		    Mage::getSingleton('customer/session')->setCurrentPasswordHash($currentHashPassword);
        }
		
	}

    protected function _getHelper($path)
    {
        return Mage::helper($path);
    }
    protected function _getUrl($url, $params = array())
    {
        return Mage::getUrl($url, $params);
    }
    public function setErrorMessage($observer){
        if(Mage::getStoreConfig('loginsecuritysetting/login/enable',Mage::app()->getStore()) == '1')
        {
            $msg = Mage::app()->getFrontController()->getRequest()->getParam('passwordchanged');
            if($msg==true)
                Mage::getSingleton('customer/session')->addError('This account password is just changed from another device/browser. We                     recommend to login again.');
        }
    }
}		