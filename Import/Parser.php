<?php

namespace MageSuite\SampleImport\Import;

class Parser extends \MageSuite\Importer\Parser\Generic implements \MageSuite\Importer\Command\Parser
{
    const CSV_DELIMITER = ",";

    /**
     * Parses input files and outputs unified file
     * @param $configuration
     * @return mixed
     */
    public function parse($configuration)
    {
        $configuration['source_path'] = BP . '/'. $configuration['source_path'];
        $configuration['target_path'] = BP . '/' . $configuration['target_path'];

        $targetFileWriter = $this->getFileWriter($configuration['target_path']);

        foreach ($this->csvReader->getLinesFromFile($configuration['source_path'], self::CSV_DELIMITER, false) as $row) {
            $row = $this->prepareRow($row);
            $line = json_encode($row);

            $targetFileWriter->writeLine($line);

            // simulate that this step takes in total one minute
            sleep(30);
        }
    }

    protected function prepareRow($values)
    {
        $row = [
            'product_type' => 'simple',
            'attribute_set_code' => 'Default',
            'product_websites' => 'base',
        ];

        $row['sku'] = $values['SKU'];
        $row['name'] = $values['product_title'];
        $row['description'] = $values['product_description'];
        $row['price'] = $values['price'];
        $row['url_key'] = $this->slug($values['product_title'].'-'.$values['SKU']);
        $row['base_image'] = $row['thumbnail_image'] = $row['small_image'] = $values['image'];
        $row['qty'] = $values['quantity'];

        $categories = [];

        foreach(['category1','category2','category2','category3'] as $categoryField) {
            if(empty($values[$categoryField])) {
                continue;
            }

            $categories[] = 'Default Category/' . $values[$categoryField];
        }

        $row['categories'] = implode(',', $categories);

        return $row;
    }
}
