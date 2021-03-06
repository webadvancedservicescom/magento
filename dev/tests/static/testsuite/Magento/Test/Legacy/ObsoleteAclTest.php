<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Legacy tests to find obsolete acl declaration
 */
namespace Magento\Test\Legacy;

class ObsoleteAclTest extends \PHPUnit_Framework_TestCase
{
    public function testAclDeclarations()
    {
        $invoker = new \Magento\Framework\Test\Utility\AggregateInvoker($this);
        $invoker(
            /**
             * @param string $aclFile
             */
            function ($aclFile) {
                $aclXml = simplexml_load_file($aclFile);
                $xpath = '/config/acl/*[boolean(./children) or boolean(./title)]';
                $this->assertEmpty(
                    $aclXml->xpath($xpath),
                    'Obsolete acl structure detected in file ' . $aclFile . '.'
                );
            },
            \Magento\Framework\Test\Utility\Files::init()->getMainConfigFiles()
        );
    }
}
