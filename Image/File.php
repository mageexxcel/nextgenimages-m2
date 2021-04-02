<?php
declare(strict_types=1);

namespace Excellence\NextGenImages\Image;

use Exception;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class File
 *
 * @package Excellence\NextGenImages\Image
 */
class File
{
    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var ReadFactory
     */
    private $readFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManagerInterface;

    /**
     * File constructor.
     *
     * @param DirectoryList $directoryList
     * @param ReadFactory $readFactory
     */
    public function __construct(
        DirectoryList $directoryList,
        ReadFactory $readFactory,
        StoreManagerInterface $storeManagerInterface
    ) {
        $this->directoryList = $directoryList;
        $this->readFactory = $readFactory;
        $this->storeManagerInterface = $storeManagerInterface;
    }

    /**
     * @param string $url
     *
     * @return string
     * @throws Exception
     */
    public function resolve(string $url): string
    {
        $baseUrl = $this->storeManagerInterface->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
        $path = str_replace($baseUrl,$this->directoryList->getRoot().'/',$url); 
        return $path;
    }

    /**
     * @param string $sourceFilename
     *
     * @return string
     */
    public function toWebp(string $sourceFilename): string
    {
        return preg_replace('/\.(jpg|jpeg|png)/i', '.webp', $sourceFilename);
    }

    /**
     * @param string $sourceFilename
     *
     * @return string
     */
    public function toJpgx(string $sourceFilename): string
    {
        return preg_replace('/\.(jpg|jpeg)/i', '.jpgx', $sourceFilename);
    }

    /**
     * @param string $imagePath
     *
     * @return string
     */
    public function getAbsolutePathFromImagePath(string $imagePath) : string
    {
        return $this->directoryList->getRoot() . '/pub' . $imagePath;
    }

    /**
     * @param string $filePath
     *
     * @return int
     */
    public function getModificationTime(string $filePath): int
    {
        $read = $this->readFactory->create($filePath);

        if (!file_exists($filePath)) {
            return 0;
        }

        return filemtime($filePath);
    }

    /**
     * @param string $targetFile
     * @param string $comparisonFile
     *
     * @return bool
     */
    public function isNewerThan(string $targetFile, string $comparisonFile): bool
    {
        $targetFileModificationTime = $this->getModificationTime($targetFile);
        if ($targetFileModificationTime === 0) {
            return false;
        }

        $comparisonFileModificationTime = $this->getModificationTime($comparisonFile);
        if ($comparisonFileModificationTime === 0) {
            return true;
        }

        if ($targetFileModificationTime > $comparisonFileModificationTime) {
            return true;
        }

        return false;
    }
}
