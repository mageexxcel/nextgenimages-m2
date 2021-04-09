<?php
declare(strict_types=1);

namespace Excellence\NextGenImages\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Backend\Block\Template\Context;

/**
 * Class ModuleVersion
 *
 * @package Excellence\NextGenImages\Block\Adminhtml\System\Config
 */
class ModuleVersion extends Field
{
    /**
     * @var ComponentRegistrar
     */
    private $componentRegistrar;

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * ModuleVersion constructor.
     * @param ComponentRegistrar $componentRegistrar
     * @param ModuleListInterface $moduleList
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        ComponentRegistrar $componentRegistrar,
        ModuleListInterface $moduleList,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->componentRegistrar = $componentRegistrar;
        $this->moduleList = $moduleList;
    }

    /**
     * Override to set a different PHTML template
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setTemplate('config/module_version.phtml');

        return $this;
    }

    /**
     * Override to render the template instead of the regular output
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->toHtml();
    }

    /**
     * Check if GD supports WebP
     *
     * @return bool
     */
    public function getModuleVersion(): string
    {
        $modulePath = $this->componentRegistrar->getPath('module', 'Excellence_NextGenImages');

        $composerJsonFile = $modulePath . '/composer.json';
        if (file_exists($composerJsonFile)) {
            $jsonContents = file_get_contents($composerJsonFile);
            $data = json_decode($jsonContents, true);
            if (isset($data['version'])) {
                return (string) $data['version'];
            }
        }

        $module = $this->moduleList->getOne('Excellence_NextGenImages');
        if (isset($module['setup_version'])) {
            return (string) $module['setup_version'];
        }

        return '';
    }
}
