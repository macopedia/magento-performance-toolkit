<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ToolkitFramework\ImportExport\Fixture\Complex;

/**
 * Class Generator
 *
 * @package Magento\ToolkitFramework\ImportExport\Fixture\Complex
 *
 */
class Generator extends \Magento\ToolkitFramework\ImportExport\Fixture\SourceAbstract
{
    /**
     * Pattern for temporary file
     */
    const TEMP_FILE_PATTERN = 'import.csv';

    /**
     * Data row pattern
     *
     * @var \Magento\ToolkitFramework\ImportExport\Fixture\Complex\Pattern
     */
    protected $_pattern;

    /**
     * Entities limit
     *
     * @var int
     */
    protected $_limit = 0;

    /**
     * Entities Count
     *
     * @var int
     */
    protected $_count = 0;

    /**
     * Array of template variables (static values or callables)
     *
     * @var array
     */
    protected $_variables = array();

    /**
     * Current index
     *
     * @var int
     */
    protected $_index = 1;

    /**
     * Rows count in pattern
     *
     * @var int
     */
    protected $_patternRowsCount = 0;

    /**
     * Read the row pattern to determine which columns are dynamic, set the collection size
     *
     * @param Pattern $rowPattern
     * @param int $count how many records to generate
     */
    public function __construct(Pattern $rowPattern, $count)
    {
        $this->_pattern = $rowPattern;
        $this->_count = $count;
        $this->_patternRowsCount = $this->_pattern->getRowsCount();
        $this->_limit = (int)$count * $this->_patternRowsCount;
        parent::__construct($this->_pattern->getHeaders());
    }

    /**
     * Get row index for template
     *
     * @param int $key
     *
     * @return float
     */
    public function getIndex($key)
    {
        return floor($key / $this->_patternRowsCount) + 1;
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

    /**
     * Get next row in set
     *
     * @return array|bool
     */
    protected function _getNextRow()
    {
        $key = $this->key();
        $this->_index = $this->getIndex($key);

        if ($key > $this->_limit) {
            return false;
        }
        return $this->_pattern->getRow($this->_index, $key);
    }

    /**
     * Return the current element
     *
     * Returns the row in associative array format: array(<col_name> => <value>, ...)
     *
     * @return array
     */
    public function current()
    {
        return $this->_row;
    }

    /**
     * Write self data to file
     */
    protected function _loadToFile()
    {
        $fp = fopen($this->_getTemporaryFilePath(), 'w');
        fputcsv($fp, $this->_pattern->getHeaders());
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
