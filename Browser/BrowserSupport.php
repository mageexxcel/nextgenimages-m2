<?php
declare(strict_types=1);

namespace Excellence\NextGenImages\Browser;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\HTTP\Header;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\LayoutInterface;
use Excellence\NextGenImages\Config\Config;

/**
 * Class BrowserSupport
 *
 * @package Excellence\NextGenImages\Browser
 */
class BrowserSupport implements ArgumentInterface
{
    /**
     * @var Header
     */
    private $headerService;

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * BrowserSupport constructor.
     *
     * @param Header $headerService
     * @param CookieManagerInterface $cookieManager
     * @param RequestInterface $request
     * @param Config $config
     * @param LayoutInterface $layout
     */
    public function __construct(
        Header $headerService,
        CookieManagerInterface $cookieManager,
        RequestInterface $request,
        Config $config,
        LayoutInterface $layout
    ) {
        $this->headerService = $headerService;
        $this->cookieManager = $cookieManager;
        $this->request = $request;
        $this->config = $config;
        $this->layout = $layout;
    }

    /**
     * @return bool
     */
    public function hasWebpSupport(): bool
    {
        if ($this->config->hasFullPageCacheEnabled($this->layout) === true) {
            return false;
        }

        if ($this->acceptsWebpHeader()) {
            return true;
        }

        if ($this->isChromeBrowser()) {
            return true;
        }

        if ($this->hasCookie()) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function acceptsWebpHeader(): bool
    {
        if (strstr('image/webp', $this->request->getHeader('ACCEPT'))) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isChromeBrowser(): bool
    {
        $userAgent = $this->headerService->getHttpUserAgent();
        // Chrome 9 or higher
        if (preg_match('/Chrome\/([0-9]+)/', $userAgent, $match)) {
            if ($match[1] > 9) { 
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function hasCookie(): bool
    {
        if ((int)$this->cookieManager->getCookie('webp') === 1) {
            return true;
        }

        return false;
    }
}
