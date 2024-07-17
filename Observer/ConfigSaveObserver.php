<?php
declare(strict_types=1);

namespace Threecommerce\Maintenance\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Threecommerce\Maintenance\Helper\Data;

class ConfigSaveObserver implements ObserverInterface
{
    protected $helper;

    public function __construct(
        Data $helper
    )
    {
        $this->helper = $helper;
    }

    public function execute(Observer $observer)
    {
        if ($this->helper->getEnable() == 1)
            $this->helper->addFlag();
        else
            $this->helper->removeFlag();
    }
}
