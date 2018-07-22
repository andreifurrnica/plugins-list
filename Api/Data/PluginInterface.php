<?php

namespace Andrei\PluginsList\Api\Data;

/**
 * Interface PluginInterface
 * @package Andrei\PluginsList\Api\Data
 */
interface PluginInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $plugin
     *
     * @return self
     */
    public function setName(string $plugin);

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param string $type
     *
     * @return self
     */
    public function setType(string $type);

    /**
     * @return array
     */
    public function getMethods(): array;

    /**
     * @param array $methods
     *
     * @return self
     */
    public function setMethods(array $methods);
}