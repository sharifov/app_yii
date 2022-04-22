<?php


namespace common\components;


use yii\grid\DataColumn;

class NumberColumn extends DataColumn
{
    private $_total = 0;

    /**
     * @param mixed $model
     * @param mixed $key
     * @param int $index
     * @return string
     */
    public function getDataCellValue($model, $key, $index)
    {
        $value = parent::getDataCellValue($model, $key, $index);
        $this->_total += (int)$value;

        return $value;
    }

    /**
     * @return string
     */
    protected function renderFooterCellContent()
    {
        return $this->grid->formatter->format($this->_total, $this->format);
    }
}