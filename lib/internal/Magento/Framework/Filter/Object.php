<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Framework\Filter;

class Object extends \Zend_Filter
{
    /**
     * @var array
     */
    protected $_columnFilters = [];

    /**
     * @var \Magento\Framework\Data\Collection\EntityFactoryInterface
     */
    protected $_entityFactory;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     */
    public function __construct(\Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory)
    {
        $this->_entityFactory = $entityFactory;
    }

    /**
     * @param \Zend_Filter_Interface $filter
     * @param string $column
     * @return null|\Zend_Filter
     */
    public function addFilter(\Zend_Filter_Interface $filter, $column = '')
    {
        if ('' === $column) {
            parent::addFilter($filter);
        } else {
            if (!isset($this->_columnFilters[$column])) {
                $this->_columnFilters[$column] = new \Zend_Filter();
            }
            $this->_columnFilters[$column]->addFilter($filter);
        }
    }

    /**
     * @param \Magento\Framework\Object $object
     * @return \Magento\Framework\Object
     * @throws \Exception
     */
    public function filter($object)
    {
        if (!$object instanceof \Magento\Framework\Object) {
            throw new \InvalidArgumentException('Expecting an instance of \Magento\Framework\Object');
        }
        $class = get_class($object);
        $out = $this->_entityFactory->create($class);
        foreach ($object->getData() as $column => $value) {
            $value = parent::filter($value);
            if (isset($this->_columnFilters[$column])) {
                $value = $this->_columnFilters[$column]->filter($value);
            }
            $out->setData($column, $value);
        }
        return $out;
    }
}
