<?php
declare(strict_types=1);

namespace Threecommerce\Maintenance\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\ResourceConnection;

class Data extends AbstractHelper
{
    const DS = '/';
    const MAINTENANCE_FILENAME = '3com.Maintenance';
    protected $connection;
    protected $fileName;

    public function __construct(
        ResourceConnection $resource,
        DirectoryList      $directoryList
    )
    {
        $this->fileName = $directoryList->getRoot() . self::DS . self::MAINTENANCE_FILENAME;
        $this->connection = $resource->getConnection();
    }

    public function setExcludeIp($ip)
    {
        if ($this->connection->fetchOne("select count(ip) from 3com_maintenance_ip where ip = '$ip'") == 0)
            $this->connection->insert("3com_maintenance_ip", ['ip' => $ip]);
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
        $listIp = $this->connection->fetchAll("select ip from 3com_maintenance_ip");
        $stringIp = ',';
        foreach ($listIp as $ip) {
            $stringIp .= $ip['ip'] . ',';
        }
        return $stringIp;
    }
}
