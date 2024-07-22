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
    const MAINTENANCE_FILENAME = '3com.maintenance';
    protected $connection;
    protected $fileName;
    protected $storeManager;
    protected $resource;
    protected $scopeConfig;
    protected $configTable;

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
        $this->configTable = $resource->getTableName('core_config_data');
        $this->connection = $resource->getConnection();
    }

    public function setExcludeIp($ip)
    {
        $config = $this->getConfigInfo(self::LISTIP, $ip);
        if (!$config) return;
        $value = $ip;
        if($config['operation']=='update') {
            $listIpOld = $config['value'];
            if ($listIpOld)
                $value = $listIpOld . ',' . $ip;
        }
        $this->setConfigInfo(self::LISTIP, $value, $config['operation']);
    }

    protected function setConfigInfo($path, $value, $operation, $scope = 'default', $scopeId = '0')
    {
        $data = array(
            'scope' => $scope,
            'scope_id' => '0',
            'path' => $path,
            'value' => $value,
        );
        $where = array(
            'scope=?' => $scope,
            'scope_id=?' => $scopeId,
            'path=?' => $path
        );
        if ($operation == 'update')
            $this->connection->update($this->configTable, $data, $where);
        else
            $this->connection->insert($this->configTable, $data);
    }

    protected function getConfigInfo($path, $value, $scope = 'default', $scopeId = '0')
    {
        if ($this->connection->fetchOne("select count(*) from $this->configTable where scope = '$scope' && scope_id = $scopeId && path = '$path' && value like '%$value%'") > 0) return false;
        $listIpOld = $this->connection->fetchOne("select value from $this->configTable where scope = '$scope' && scope_id = $scopeId && path = '$path'");
        if (!empty($listIpOld))
            return array('operation' => 'update', 'value' => $listIpOld);
        return array('operation' => 'insert', 'value' => $value);
    }

    public function addFlag()
    {
        file_put_contents($this->fileName, '');
    }

    public function setEnable()
    {
        if (!$this->getConfigInfo(self::ENABLE, '1')) return;
        $this->setConfigInfo(self::ENABLE, '1', $config['operation']);
    }
    public function setDisable()
    {
        if (!$this->getConfigInfo(self::ENABLE, '0')) return;
        $this->setConfigInfo(self::ENABLE, '0', $config['operation']);
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
