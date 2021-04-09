<?php
declare(strict_types=1);

namespace Excellence\NextGenImages\Plugin;

use Exception as ExceptionAlias;
use Magento\Framework\View\LayoutInterface;
use Excellence\NextGenImages\Block\Picture;
use Excellence\NextGenImages\Config\Config;
use Excellence\NextGenImages\Image\Convertor;
use Excellence\NextGenImages\Image\File;
use Excellence\NextGenImages\Logger\Debugger;

/**
 * Class ReplaceTags
 *
 * @package Excellence\NextGenImages\Plugin
 */
class ReplaceTags
{
    /**
     * @var Convertor
     */
    private $convertor;

    /**
     * @var File
     */
    private $file;

    /**
     * @var Debugger
     */
    private $debugger;
    
    /**
     * @var Config
     */
    private $config;

    /**
     * ReplaceTags constructor.
     *
     * @param Convertor $convertor
     * @param File $file
     * @param Debugger $debugger
     * @param Config $config
     */
    public function __construct(
        Convertor $convertor,
        File $file,
        Debugger $debugger,
        Config $config
    ) {
        $this->convertor = $convertor;
        $this->file = $file;
        $this->debugger = $debugger;
        $this->config = $config;
    }
    
    /**
     * Interceptor of getOutput()
     *
     * @param LayoutInterface $layout
     * @param string $output
     * @return string
     * @throws ExceptionAlias
     */
    public function afterGetOutput(LayoutInterface $layout, string $output): string
    {
        $handles = $layout->getUpdate()->getHandles();
        if (empty($handles)) {
            return $output;
        }
        
        $skippedHandles = [
            'webp_skip',
            'sales_email_order_invoice_items'
        ];
        if (array_intersect($skippedHandles, $handles)) {
            return $output;
        }

        if ($this->config->enabled() === false) {
            return $output;
        }

        $regex = '/<([^<]+)\ src=\"([^\"]+)\.(png|jpg|jpeg)([^>]+)>/mi';
        if (preg_match_all($regex, $output, $matches, PREG_OFFSET_CAPTURE) === false) {
            return $output;
        }
        
        $accumulatedChange = 0;

        foreach ($matches[0] as $index => $match) {
            $offset = $match[1] + $accumulatedChange;
            $htmlTag = $matches[0][$index][0];
            $imageUrl = $matches[2][$index][0] . '.' . $matches[3][$index][0];

            $ext = pathinfo($imageUrl, PATHINFO_EXTENSION);

            $altText = $this->getAttributeText($htmlTag, 'alt');
            $width = $this->getAttributeText($htmlTag, 'width');
            $height = $this->getAttributeText($htmlTag, 'height');
            $class = $this->getAttributeText($htmlTag, 'class');

            try {
                if($this->config->isJpgxEnable() && preg_match('/jpg|jpeg/i', $ext) ){
                    $webpUrl = $this->file->toJpgx($imageUrl);
                    $result = $this->convertor->jpgxConvert($imageUrl, $webpUrl);
                }else{
                    $webpUrl = $this->file->toWebp($imageUrl);
                    $result = $this->convertor->convert($imageUrl, $webpUrl);
                }
            } catch (ExceptionAlias $e) {
            
                if ($this->config->isDebugging()) {
                    throw $e;
                }

                $result = false;
                $this->debugger->debug($e->getMessage(), [$imageUrl, $webpUrl]);
            }

            if (!$result && !$this->convertor->urlExists($webpUrl)) {
                continue;
            }

            $newHtmlTag = $this->getPictureBlock($layout)
                ->setOriginalImage($imageUrl)
                ->setWebpImage($webpUrl)
                ->setAltText($altText)
                ->setOriginalTag($htmlTag)
                ->setClass($class)
                ->setWidth($width)
                ->setHeight($height)
                ->toHtml();

            $output = substr_replace($output, $newHtmlTag, $offset, strlen($htmlTag));
            $accumulatedChange = $accumulatedChange + (strlen($newHtmlTag) - strlen($htmlTag));
        }

        return $output;
    }

    /**
     * @param string $htmlTag
     * @param string $attribute
     * @return string
     */
    private function getAttributeText(string $htmlTag, string $attribute): string
    {
        if (preg_match('/\ ' . $attribute . '=\"([^\"]+)/', $htmlTag, $match)) {
            $altText = $match[1];
            $altText = strtr($altText, ['"' => '', "'" => '']);
            return $altText;
        }

        return '';
    }

    /**
     * Get Picture Block-class from the layout
     *
     * @param LayoutInterface $layout
     * @return Picture
     */
    private function getPictureBlock(LayoutInterface $layout): Picture
    {
        /** @var Picture $block */
        $block = $layout->createBlock(Picture::class);
        $block->setDebug($this->config->isDebugging());
        return $block;
    }
}