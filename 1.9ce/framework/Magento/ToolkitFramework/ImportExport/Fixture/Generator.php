<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * A custom "Import" adapter for Magento_ImportExport module that allows generating arbitrary data rows
 */
namespace Magento\ToolkitFramework\ImportExport\Fixture;

class Generator extends SourceAbstract
{
    /**
     * Pattern for temporary file
     */
    const TEMP_FILE_PATTERN = 'import.csv';

    /**
     * Data row pattern
     *
     * @var array
     */
    protected $_pattern = array();

    /**
     * Which columns are determined as dynamic
     *
     * @var array
     */
    protected $_dynamicColumns = array();

    /**
     * @var int
     */
    protected $_limit = 0;

    /**
     * Read the row pattern to determine which columns are dynamic, set the collection size
     *
     * @param array $rowPattern
     * @param int $limit how many records to generate
     */
    public function __construct(array $rowPattern, $limit)
    {
        foreach ($rowPattern as $key => $value) {
            if (is_callable($value) || is_string($value) && (false !== strpos($value, '%s'))) {
                $this->_dynamicColumns[$key] = $value;
            }
        }

        $tmpDir = \Magento\ToolkitFramework\Helper\Cli::getOption('tmp_dir');
        if ($tmpDir) {
            $this->_filePath = $tmpDir . DIRECTORY_SEPARATOR . $this->_filePath;
        }

        $this->_pattern = $rowPattern;
        $this->_limit = (int)$limit;
        parent::__construct(array_keys($rowPattern));
    }

    /**
     * Whether limit of generated elements is reached (according to "Iterator" interface)
     *
     * @return bool
     */
    public function valid()
    {
        return $this->_key + 1 <= $this->_limit;
    }

    protected function _getNextRow()
    {
        $row = $this->_pattern;
        foreach ($this->_dynamicColumns as $key => $dynamicValue) {
            $index = $this->_key + 1;
            if (is_callable($dynamicValue)) {
                $row[$key] = call_user_func($dynamicValue, $index);
            } else {
                $row[$key] = str_replace('%s', $index, $dynamicValue);
            }
        }
        return $row;
    }

    /**
     * Write self data to file
     */
    protected function _loadToFile()
    {
        $fp = fopen($this->_getTemporaryFilePath(), 'w');
        fputcsv($fp, array_keys($this->_pattern));
        foreach ($this as $value) {
            fputcsv($fp, $value);
        }
        fclose($fp);
    }

    /**
     * To string
     *
     * @return string
     */
    public function __toString()
    {
        $this->_loadToFile();
        return $this->_getTemporaryFilePath();
    }

    /**
     * Get temporary file path
     *
     * @return string
     */
    protected function _getTemporaryFilePath()
    {
        return rtrim(\Magento\ToolkitFramework\Helper\Cli::getOption('tmp_dir', DEFAULT_TEMP_DIR), '\\/')
        . '/' . self::TEMP_FILE_PATTERN;
    }
}
