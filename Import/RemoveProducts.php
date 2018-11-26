<?php

namespace MageSuite\SampleImport\Import;

class RemoveProducts extends \MageSuite\Importer\Parser\Generic implements \MageSuite\Importer\Command\Parser
{
    const CSV_DELIMITER = ",";

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    public function __construct(
        \Creativestyle\CSV\File\CsvReader $csvReader,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Registry $registry
    )
    {
        parent::__construct($csvReader);

        $this->productRepository = $productRepository;
        $this->registry = $registry;
    }

    /**
     * Removes imported before products
     * @param $configuration
     * @return mixed
     */
    public function parse($configuration)
    {
        $this->registry->unregister('isSecureArea');
        $this->registry->register('isSecureArea', true);
        $configuration['source_path'] = BP . '/'. $configuration['source_path'];

        foreach ($this->csvReader->getLinesFromFile($configuration['source_path'], self::CSV_DELIMITER, false) as $row) {
            try {
                $product = $this->productRepository->get($row['SKU']);

                $this->productRepository->delete($product);
            }
            catch(\Magento\Framework\Exception\NoSuchEntityException $exception) {
                continue;
            }
        }

        $this->registry->unregister('isSecureArea');
        $this->registry->register('isSecureArea', false);
    }
}
