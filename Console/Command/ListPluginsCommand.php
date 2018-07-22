<?php

namespace Andrei\PluginsList\Console\Command;

use Andrei\PluginsList\Model\Scanner;
use Magento\Setup\Console\Style\MagentoStyle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ListPluginsCommand
 * @package Andrei\PluginsList\Console\Command
 */
class ListPluginsCommand extends Command
{
    /**
     * @var Scanner
     */
    protected $pluginScanner;

    /**
     * @param Scanner $pluginScanner
     */
    public function __construct(Scanner $pluginScanner)
    {
        parent::__construct();

        $this->pluginScanner = $pluginScanner;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('af:list-plugins')
            ->setDescription('Displays the list of defined plugins.')
            ->addOption(
                'vendor-only',
                null,
                InputOption::VALUE_NONE,
                'Display only vendor plugins.'
            )
            ->addOption(
                'exclude-vendor',
                null,
                InputOption::VALUE_NONE,
                'Hide vendor plugins.'
            )
            ->addOption(
                'type',
                null,
                InputOption::VALUE_OPTIONAL,
                'Filter by type (intercepted class).'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $style = new MagentoStyle($input, $output);

        $vendorOnly = $input->hasOption('vendor-only') ? $input->getOption('vendor-only') : false;
        $excludeVendor = $input->hasOption('exclude-vendor') ? $input->getOption('exclude-vendor') : false;
        $type = $input->hasOption('type') ? $input->getOption('type') : null;

        if ($vendorOnly && $excludeVendor) {
            $style->error('"vendor-only" and "exclude-vendor" options can not be used simultaneously.');

            return;
        }

        $style->title('Plugins list' . ($type ? ' for class ' . $type : null));

        $plugins = $this->pluginScanner->getPluginList($type, $vendorOnly, $excludeVendor);

        $table = new Table($output);
        $table->setHeaders(['Intercepted Class', 'Plugin', 'Methods']);

        foreach ($plugins as $plugin) {
            $table->addRow([
                $plugin->getType(),
                $plugin->getName(),
                implode('', $plugin->getMethods())
            ]);
        }

        $table->render();
    }
}

