<?php

class ConversionBug_SocialPopup_Block_Adminhtml_Socialpopup_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    protected $_countTotals = true;

    public function __construct() {
        parent::__construct();
        //$this->setTemplate('M2ePro/ebay/synchronization/help.phtml');
        $this->setId('socialpopupGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setFilterVisibility(false);
    }

    protected function _toHtml_() {
        $orderTotals = Mage::getModel('sales/order')->getCollection()
                ->addAttributeToFilter('status', Mage_Sales_Model_Order::STATE_COMPLETE)
                //->addAttributeToFilter('created_at', array('from'  => '2012-07-01'))
                ->addAttributeToSelect('grand_total')
                ->getColumnValues('grand_total')
        ;

        $grandTotal = array_sum($orderTotals);


        $collection = Mage::getResourceModel('customer/customer_collection')
                ->addAttributeToSelect('cb_socialpopup_customer')
                ->addAttributeToFilter('cb_socialpopup_customer', 1);
        $sql = 'SELECT SUM(base_grand_total)'
                . ' FROM ' . Mage::getSingleton('core/resource')->getTableName('sales/order') . ' AS o'
                . ' WHERE o.customer_id = e.entity_id ';
        $expr = new Zend_Db_Expr('(' . $sql . ')');

        $collection->getSelect()->from(null, array('total' => $expr));
        //echo "<pre>";print_r($collection->getColumnValues('total'));exit;
        $cb = array_sum($collection->getColumnValues('total'));
        $cbPercentage = round(($cb / $grandTotal) * 100, 2);
        $TotalPercentage = 100 - $cbPercentage;
        $grandTotal = $grandTotal - $cb;
        $totalSum = Mage::helper('core')->currency($grandTotal, true, false);
        $cbTotal = Mage::helper('core')->currency($cb, true, false);

        $javascriptsMain = <<<JAVASCRIPT

  <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js'></script>
  <script src="http://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.2/raphael-min.js"></script>
  <script src="http://localhost/socialpopup/media/piechart/morris.js"></script>
  <script src="http://cdnjs.cloudflare.com/ajax/libs/prettify/r224/prettify.min.js"></script>
  
  <link rel="stylesheet" href="http://localhost/socialpopup/media/piechart/example.css">
  <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/prettify/r224/prettify.min.css">
  <link rel="stylesheet" href="http://localhost/socialpopup/media/piechart/morris.css">

                <h1>Order Increased By CB Exit Intent Chart</h1>
<div id="graph"></div>
<script>
                $(function () {
  eval(Morris.Donut({
  element: 'graph',
  data: [
    {value: $TotalPercentage, label: 'Without CB Exit Intent: $totalSum', formatted: 'approx. $TotalPercentage%' },
    {value: $cbPercentage , label: 'With CB Exit Intent: $cbTotal', formatted: 'approx. $cbPercentage%' },
    
  ],
  formatter: function (x, data) { return data.formatted; }
}));
  prettyPrint();
});
                </script>


JAVASCRIPT;

        return $javascriptsMain .
                '<div id="synchronization_progress_bar"></div>' .
                '<div id="synchronization_content_container">' .
                parent::_toHtml() .
                '</div>';
    }

    protected function _prepareCollection() {
        $collection = Mage::getResourceModel('customer/customer_collection')
                ->addNameToSelect()
                ->addAttributeToSelect('email')
                ->addAttributeToSelect('created_at')
                ->addAttributeToSelect('group_id')
                ->addAttributeToSelect('cb_socialpopup_customer')
                ->addAttributeToFilter('cb_socialpopup_customer', 1)
                ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
                ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
                ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
                ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
                ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left');

        // add 2 new fields as sub queries      
        $sql = 'SELECT MAX(o.created_at)'
                . ' FROM ' . Mage::getSingleton('core/resource')->getTableName('sales/order') . ' AS o'
                . ' WHERE o.customer_id = e.entity_id ';
        $expr = new Zend_Db_Expr('(' . $sql . ')');

        $collection->getSelect()->from(null, array('last_order_date' => $expr));

        $sql = 'SELECT COUNT(*)'
                . ' FROM ' . Mage::getSingleton('core/resource')->getTableName('sales/order') . ' AS o'
                . ' WHERE o.customer_id = e.entity_id ';
        $expr = new Zend_Db_Expr('(' . $sql . ')');

        $collection->getSelect()->from(null, array('orders_count' => $expr));

        $sql = 'SELECT SUM(base_grand_total)'
                . ' FROM ' . Mage::getSingleton('core/resource')->getTableName('sales/order') . ' AS o'
                . ' WHERE o.customer_id = e.entity_id ';
        $expr = new Zend_Db_Expr('(' . $sql . ')');

        $collection->getSelect()->from(null, array('total' => $expr));

        //echo $collection->getSelect(); exit;      

        $this->setCollection($collection);
        $this->_prepareTotals('total,orders_count'); //Add this Line with all the columns you want to have in totals bar
        return parent::_prepareCollection();
    }

    //Add following function
    protected function _prepareTotals($columns = null) {
        $columns = explode(',', $columns);
        if (!$columns) {
            return;
        }
        $this->_countTotals = true;
        $totals = new Varien_Object();
        $fields = array();
        foreach ($columns as $column) {
            $fields[$column] = 0;
        }
        foreach ($this->getCollection() as $item) {
            foreach ($fields as $field => $value) {
                $fields[$field]+=$item->getData($field);
            }
        }
        $totals->setData($fields);
        $this->setTotals($totals);
        return;
    }

    protected function _prepareColumns() {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('socialpopup')->__('ID'),
            'width' => '50px',
            'index' => 'entity_id',
            'type' => 'number',
            'totals_label' => $this->__('Total'), //Add this line to show "Total" in the beginning of the row
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('socialpopup')->__('Name'),
            'index' => 'name'
        ));
        $this->addColumn('email', array(
            'header' => Mage::helper('socialpopup')->__('Email'),
            'width' => '150',
            'index' => 'email'
        ));
        $this->addColumn('last_order_date', array(
            'header' => Mage::helper('customer')->__('Last Order Date'),
            'type' => 'datetime',
            'align' => 'center',
            'index' => 'last_order_date',
            'gmtoffset' => true,
        ));

        $this->addColumn('orders_count', array(
            'header' => Mage::helper('customer')->__('Orders Count'),
            'index' => 'orders_count',
        ));
        $this->addColumn('total', array(
            'header' => Mage::helper('customer')->__('Total Revenue'),
            'index' => 'total',
            'type' => 'currency',
            'currency_code' => Mage::app()->getStore()->getCurrentCurrencyCode(),
        ));
        $groups = Mage::getResourceModel('customer/group_collection')
                ->addFieldToFilter('customer_group_id', array('gt' => 0))
                ->load()
                ->toOptionHash();

        $this->addColumn('group', array(
            'header' => Mage::helper('socialpopup')->__('Group'),
            'width' => '100',
            'index' => 'group_id',
            'type' => 'options',
            'options' => $groups,
        ));

        $this->addColumn('Telephone', array(
            'header' => Mage::helper('socialpopup')->__('Telephone'),
            'width' => '100',
            'index' => 'billing_telephone'
        ));

        $this->addColumn('billing_postcode', array(
            'header' => Mage::helper('socialpopup')->__('ZIP'),
            'width' => '90',
            'index' => 'billing_postcode',
        ));

        $this->addColumn('billing_country_id', array(
            'header' => Mage::helper('socialpopup')->__('Country'),
            'width' => '100',
            'type' => 'country',
            'index' => 'billing_country_id',
        ));

        $this->addColumn('billing_region', array(
            'header' => Mage::helper('socialpopup')->__('State/Province'),
            'width' => '100',
            'index' => 'billing_region',
        ));

        $this->addColumn('customer_since', array(
            'header' => Mage::helper('socialpopup')->__('Customer Since'),
            'type' => 'datetime',
            'align' => 'center',
            'index' => 'created_at',
            'gmtoffset' => true
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header' => Mage::helper('socialpopup')->__('Website'),
                'align' => 'center',
                'width' => '80px',
                'type' => 'options',
                'options' => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
                'index' => 'website_id',
            ));
        }

//        $this->addColumn('action',
//            array(
//                'header'    =>  Mage::helper('socialpopup')->__('Action'),
//                'width'     => '100',
//                'type'      => 'action',
//                'getter'    => 'getId',
//                'actions'   => array(
//                    array(
//                        'caption'   => Mage::helper('socialpopup')->__('Edit'),
//                        'url'       => array('base'=> '*/*/edit'),
//                        'field'     => 'id'
//                    )
//                ),
//                'filter'    => false,
//                'sortable'  => false,
//                'index'     => 'stores',
//                'is_system' => true,
//                'totals_label'      => ''
//        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('socialpopup')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('socialpopup')->__('Excel XML'));
        return parent::_prepareColumns();
    }

}
