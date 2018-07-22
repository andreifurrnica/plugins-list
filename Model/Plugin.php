<?php

namespace Andrei\PluginsList\Model;

use Andrei\PluginsList\Api\Data\PluginInterface;

/**
 * Class Plugin
 * @package Andrei\PluginsList\Model
 */
class Plugin implements PluginInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $methods = [];

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $plugin)
    {
        $this->name = $plugin;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * {@inheritdoc}
     */
    public function setMethods(array $methods)
    {
        $this->methods = $methods;

        return $this;
    }
}