<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Import tax rates with import handler
 */
$filename = realpath(__DIR__ . '/tax_rates.csv');
$importHandler = new Magento\ToolkitFramework\ImportExport\Fixture\Tax\Import\CsvHandler;
$importHandler->importRates($filename);
