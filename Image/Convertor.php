<?php
declare(strict_types=1);

namespace Excellence\NextGenImages\Image;

use Exception;
use Magento\Framework\View\Asset\File\NotFoundException;
use WebPConvert\WebPConvert;
use Excellence\NextGenImages\Config\Config;

/**
 * Class Convertor
 *
 * @package Excellence\NextGenImages\Image
 */
class Convertor
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var File
     */
    private $file;

    /**
     * Convertor constructor.
     *
     * @param Config $config
     * @param File $file
     */
    public function __construct(
        Config $config,
        File $file
    ) {
        $this->config = $config;
        $this->file = $file;
    }

    /**
     * @param string $sourceImageUrl
     * @param string $destinationImageUrl
     * @return bool
     * @throws NotFoundException
     * @throws Exception
     */
    public function convert(string $sourceImageUrl, string $destinationImageUrl): bool
    {
        $sourceImageFilename = $this->getPathFromUrl($sourceImageUrl);
        $destinationImageFilename = $this->getPathFromUrl($destinationImageUrl);

        if (!$this->needsConversion($sourceImageFilename, $destinationImageFilename)) {
            return false;
        }

        return WebPConvert::convert($sourceImageFilename, $destinationImageFilename, $this->getOptions());
    }

    /**
     * @param string $sourceImageUrl
     * @param string $destinationImageUrl
     * @return bool
     * @throws NotFoundException
     * @throws Exception
     */
    public function jpgxConvert(string $sourceImageUrl, string $destinationImageUrl): bool
    {
        $sourceImageFilename = $this->getPathFromUrl($sourceImageUrl);
        $destinationImageFilename = $this->getPathFromUrl($destinationImageUrl);
        if (!$this->needsConversion($sourceImageFilename, $destinationImageFilename)) {
            return false;
        }
        
        $ext = pathinfo($sourceImageFilename, PATHINFO_EXTENSION);
        if (preg_match('/jpg|jpeg/i', $ext)) {
            $image = imagecreatefromjpeg($sourceImageFilename);
            ob_start();
            imagejpeg($image,NULL,100);
            $cont=  ob_get_contents();
            ob_end_clean();
            imagedestroy($image);
            $content =  imagecreatefromstring($cont);
            imagewebp($content,$destinationImageFilename);
            return imagedestroy($content);
        }
    }

    /**
     * @param string $sourceImageFilename
     * @param string $destinationImageFilename
     * @return bool
     * @throws NotFoundException
     */
    private function needsConversion(string $sourceImageFilename, string $destinationImageFilename): bool
    {
        if (!file_exists($sourceImageFilename)) {
            throw new NotFoundException($sourceImageFilename . ' is not found');
        }

        if (!file_exists($destinationImageFilename)) {
            return true;
        }

        if ($this->file->isNewerThan($destinationImageFilename, $sourceImageFilename)) {
            return false;
        }

        return false;
    }

    /**
     * @param string $url
     * @return string
     * @throws Exception
     */
    private function getPathFromUrl(string $url): string
    {
        return $this->file->resolve($url);
    }

    /**
     * @param string $url
     * @return bool
     * @throws Exception
     */
    public function urlExists(string $url): bool
    {
        $filePath = $this->file->resolve($url);
        if (file_exists($filePath)) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return [
            'quality' => 'auto',
            'max-quality' => $this->config->getQualityLevel(),
            'converters' => $this->config->getConvertors(),
        ];
    }
}
