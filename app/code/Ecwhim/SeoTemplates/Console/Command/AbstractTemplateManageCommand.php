<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractTemplateManageCommand extends \Symfony\Component\Console\Command\Command
{
    const INPUT_KEY_IDS = 'ids';

    /**
     * @var bool
     */
    protected $needInteract = false;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * AbstractTemplateManageCommand constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\State $appState
     * @param string|null $name
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\State $appState,
        string $name = null
    ) {
        parent::__construct($name);

        $this->objectManager = $objectManager;
        $this->appState      = $appState;
    }

    /**
     * @return \Ecwhim\SeoTemplates\Model\ResourceModel\AbstractTemplateCollection
     */
    abstract protected function getCollection(): \Ecwhim\SeoTemplates\Model\ResourceModel\AbstractTemplateCollection;

    /**
     * Perform a Template management action
     *
     * @param \Ecwhim\SeoTemplates\Api\Data\TemplateInterface $template
     * @return void
     */
    abstract protected function performAction(\Ecwhim\SeoTemplates\Api\Data\TemplateInterface $template): void;

    /**
     * Get display message
     *
     * @return string
     */
    abstract protected function getDisplayMessage(): string;

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->addArgument(
            self::INPUT_KEY_IDS,
            \Symfony\Component\Console\Input\InputArgument::IS_ARRAY,
            'Space-separated list of Template IDs or omit to apply to all Templates.'
        );

        parent::configure();
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->appState->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);

            $collection   = $this->getCollection();
            $requestedIds = $this->getRequestedIds($input);
            $ids          = [];

            if ($requestedIds) {
                $collection->addFieldToFilter($collection->getIdFieldName(), ['in' => $requestedIds]);
            } elseif ($this->needInteract) {
                $question = new \Symfony\Component\Console\Question\Question(
                    '<question>Do you really want to apply the action to all Templates?[Y/n]</question>',
                    'n'
                );
                $dialog   = $this->getHelperSet()->get('question');

                if (strtolower($dialog->ask($input, $output, $question)) !== 'y') {
                    return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
                }
            }

            /** @var \Ecwhim\SeoTemplates\Api\Data\TemplateInterface $template */
            foreach ($collection->getItems() as $template) {
                $ids[] = $template->getTemplateId();

                $this->performAction($template);
            }

            $output->writeln($this->getDisplayMessage() . ' ' . join(", ", $ids));

            return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');

            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $output->writeln($e->getTraceAsString());
            }

            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }
    }

    /**
     * @param InputInterface $input
     * @return array
     */
    protected function getRequestedIds(InputInterface $input): array
    {
        $requestedIds = $input->getArgument(self::INPUT_KEY_IDS) ?: [];

        if ($requestedIds) {
            $requestedIds = array_unique(array_filter(array_map('trim', $requestedIds), 'strlen'));

            if ($requestedIds) {
                $validIds   = $this->getCollection()->getAllIds();
                $invalidIds = array_diff($requestedIds, $validIds);

                if ($invalidIds) {
                    throw new \InvalidArgumentException(
                        "The following requested Template IDs are invalid: '" . join("', '", $invalidIds)
                        . "'." . PHP_EOL . 'Valid Template IDs: ' . join(", ", $validIds)
                    );
                }

                $requestedIds = array_values(array_intersect($requestedIds, $validIds));
            }
        }

        return $requestedIds;
    }
}
