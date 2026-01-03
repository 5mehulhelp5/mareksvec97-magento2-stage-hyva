<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CategoryTemplateListCommand extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate\CollectionFactory
     */
    private $collectionFactory;

    /**
     * CategoryTemplateListCommand constructor.
     *
     * @param \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate\CollectionFactory $collectionFactory
     * @param string|null $name
     */
    public function __construct(
        \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate\CollectionFactory $collectionFactory,
        string $name = null
    ) {
        parent::__construct($name);

        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('ecwhim-seo:seotemplates:category-template:list');
        $this->setDescription('Displays the list of Category SEO Templates');

        parent::configure();
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $table = new \Symfony\Component\Console\Helper\Table($output);
            $table->setHeaders(
                [
                    'ID',
                    'Name',
                    'Is Active',
                    'Scope',
                    'Store IDs',
                    'Type',
                    'Apply by Cron',
                    'Priority',
                    'Apply to all Categories',
                    'Application Time'
                ]
            );

            $collection = $this->collectionFactory->create();

            /** @var \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface $template */
            foreach ($collection->getItems() as $template) {
                $table->addRow(
                    [
                        $template->getTemplateId(),
                        $template->getName(),
                        $template->getIsActive(),
                        $template->getScope(),
                        implode(', ', $template->getStoreIds()),
                        $template->getType(),
                        $template->getApplyByCron(),
                        $template->getPriority(),
                        $template->getApplyToAllCategories(),
                        $template->getApplicationTime()
                    ]
                );
            }

            $table->render();

            return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');

            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $output->writeln($e->getTraceAsString());
            }

            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }
    }
}
