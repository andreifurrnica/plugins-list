<?php

namespace Andrei\PluginsList\Model;

use Andrei\PluginsList\Api\Data\PluginInterface;
use Magento\Framework\App\Utility\Classes;
use Magento\Framework\App\Utility\Files;
use Magento\Framework\Filesystem\DirectoryList;
use ReflectionMethod;

/**
 * Class Scanner
 * @package Andrei\PluginsList\Model
 */
class Scanner
{
    /**
     * @var Files
     */
    protected $files;

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var PluginFactory
     */
    protected $pluginFactory;

    /**
     * PluginScanner constructor.
     *
     * @param Files         $files
     * @param DirectoryList $directoryList
     * @param PluginFactory $pluginFactory
     */
    public function __construct(
        Files $files,
        DirectoryList $directoryList,
        PluginFactory $pluginFactory
    ) {
        Files::setInstance($files);

        $this->files = $files;
        $this->directoryList = $directoryList;
        $this->pluginFactory = $pluginFactory;
    }

    /**
     * @param string    $filteredType
     *
     * @param bool|null $vendorOnly
     * @param bool|null $excludeVendor
     *
     * @return array|Plugin[]
     */
    public function getPluginList(
        ?string $filteredType = null,
        ?bool $vendorOnly = false,
        ?bool $excludeVendor = false
    ): array {
        $plugins = [];

        foreach ($this->files->getDiConfigs() as $file) {
            if ($this->shouldSkipFile($file, $excludeVendor, $vendorOnly)) {
                continue;
            }

            $dom = new \DOMDocument();
            $dom->load($file);

            $xpath = new \DOMXPath($dom);
            $pluginList = $xpath->query('//config/type/plugin');

            /** @var $node \DOMNode */
            foreach ($pluginList as $node) {
                $type = Classes::resolveVirtualType($node->parentNode->attributes->getNamedItem('name')->nodeValue);

                if ($filteredType && $filteredType !== $type) {
                    continue;
                }

                if ($typeItem = $node->attributes->getNamedItem('type')) {
                    $plugin = Classes::resolveVirtualType($typeItem->nodeValue);
                    $methods = $this->getPluginMethods($plugin);

                    if (empty($methods)) {
                        continue;
                    }

                    /** @var Plugin $row */
                    $row = $this->pluginFactory->create();

                    $row->setName($plugin);
                    $row->setType($type);
                    $row->setMethods($methods);

                    $plugins[] = $row;
                }
            }
        }

        usort($plugins, function (PluginInterface $a, PluginInterface $b) {
            return $a->getType() <=> $b->getType();
        });

        return $plugins;
    }

    /**
     * @param string $file
     * @param bool   $excludeVendor
     * @param bool   $vendorOnly
     *
     * @return bool
     */
    protected function shouldSkipFile(string $file, bool $excludeVendor, bool $vendorOnly): bool
    {
        $filePath = str_replace($this->directoryList->getRoot(), '', $file);
        $filePath = trim($filePath, '/');

        if ($excludeVendor && strpos($filePath, 'vendor') === 0) {
            return true;
        }

        if ($vendorOnly && 0 !== strpos($filePath, 'vendor')) {
            return true;
        }

        return false;
    }


    /**
     * @param string $plugin
     *
     * @return array
     */
    protected function getPluginMethods(string $plugin): array
    {
        try {
            $reflection = new \ReflectionClass($plugin);

            $methods = [];

            // keep only plugin methods
            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if (strpos($method->getName(), 'before') === 0 ||
                    strpos($method->getName(), 'after') === 0 ||
                    strpos($method->getName(), 'around') === 0
                ) {
                    $methods[] = $method->getName();
                }
            }

            return $methods;
        } catch (\Throwable $exception) {
            return [];
        }
    }
}