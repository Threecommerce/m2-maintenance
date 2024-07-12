<?php
declare(strict_types=1);

namespace Threecommerce\Maintenance\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    const DS = '/';
    const ENABLE = 'maintenance/general/enable';
    const LISTIP = 'maintenance/general/ipList';
    const MAINTENANCE_FILENAME = '3com.Maintenance';
    protected $connection;
    protected $fileName;
    protected $storeManager;
    protected $resource;
    protected $scopeConfig;

    public function __construct(
        ScopeConfigInterface  $scopeConfig,
        ResourceConnection    $resource,
        StoreManagerInterface $storeManager,
        DirectoryList         $directoryList
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->fileName = $directoryList->getRoot() . self::DS . self::MAINTENANCE_FILENAME;
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
    }

    public function setExcludeIp($ip)
    {
        $table = $this->resource->getTableName('core_config_data');
        if ($this->connection->fetchOne("select count(value) from $table where scope = 'default' && scope_id = 0 path = '" . self::LISTIP . "' && value like '%$ip%'") > 0) return;
        $listIpOld = $this->connection->fetchOne("select value from $table where path = '" . self::LISTIP . "'");
        $data = array(
            'scope' => 'default',
            'scope_id' => 0,
            'path' => self::LISTIP,
            'value' => $listIpOld . ',' . $ip,
        );
        $this->connection->insert($table, $data);
    }

    public function addFlag()
    {
        file_put_contents($this->fileName, '');
    }

    public function removeFlag()
    {
        if (file_exists($this->fileName))
            unlink($this->fileName);
    }

    public function getFlagName()
    {
        return $this->fileName;
    }

    public function getListIp()
    {
        return (string)$this->scopeConfig->getValue(self::LISTIP, ScopeInterface::SCOPE_STORE, $this->storeManager->getStore()->getId());
    }

    public function getEnable()
    {
        return $this->scopeConfig->getValue(self::ENABLE, ScopeInterface::SCOPE_STORE, $this->storeManager->getStore()->getId());
    }
}
