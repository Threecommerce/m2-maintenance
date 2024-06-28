<?php
declare(strict_types=1);

namespace Threecommerce\Maintenance\Plugin;

use Magento\Framework\App\Area;
use Magento\Framework\App\FrontControllerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\State;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Threecommerce\Maintenance\Helper\Data;

class Redirect
{
    protected $resultFactory;
    protected $url;
    protected $storeManager;
    protected $helper;
    protected $state;
    protected $response;
    protected $currentUrl;
    protected $redirect;

    public function __construct(
        ResultFactory         $resultFactory,
        StoreManagerInterface $storeManager,
        Data                  $helper,
        HttpResponse          $response,
        State                 $state,
        UrlInterface          $url,
        RedirectInterface     $redirect
    )
    {
        $this->response = $response;
        $this->state = $state;
        $this->helper = $helper;
        $this->storeManager = $storeManager;
        $this->resultFactory = $resultFactory;
        $this->url = $url;
        $this->redirect = $redirect;
        $this->currentUrl = $this->url->getCurrentUrl();
    }

    public function beforeDispatch(FrontControllerInterface $subject, RequestInterface $request)
    {
        if ($request->isXmlHttpRequest())  return;
        if (!file_exists($this->helper->getFlagName())) return;
        if ($this->state->getAreaCode() === Area::AREA_ADMINHTML) return;
        if (strstr($this->helper->getListIp(), ',' . $_SERVER['REMOTE_ADDR'] . ',')) return;
        $targetUrl = $this->storeManager->getStore()->getBaseUrl() . 'sito-in-manutenzione';
        if ($this->currentUrl == $targetUrl) return;
        $this->response->setRedirect($targetUrl, 302);
        $this->response->sendHeaders();
        exit;
    }
}
