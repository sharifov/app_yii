<?php

namespace modules\sales\backend\controllers;

use backend\base\Controller;
use modules\sales\common\models\Brand;
use modules\sales\common\models\Category;
use modules\sales\common\models\Product;
use modules\sales\common\models\Type;
use yii\helpers\Html;
use yz\admin\import\BatchImportAction;
use yz\admin\import\ImportForm;
use yz\admin\import\InterruptImportException;

/**
 * Class ImportProfilesController
 */
class ImportCatalogController extends Controller
{
    const FIELD_TYPE = 'тип';
    const FIELD_CATEGORY = 'вид';
    const FIELD_BRAND = 'бренд';
    const FIELD_NAME = 'наименование';
    const FIELD_PACKING = 'фасовка';

    public function actions()
    {
        return [
            'index' => [
                'class' => BatchImportAction::className(),
                //'extraView' => '@modules/sales/backend/views/import-catalog/partials/_catalog.php',
                'importConfig' => [
                    'availableFields' => [
                        self::FIELD_TYPE => 'Тип товара',
                        self::FIELD_CATEGORY => 'Категория товара',
                        self::FIELD_BRAND => 'Бренд товара',
                        self::FIELD_NAME => 'Название товара',
                        self::FIELD_PACKING => 'Фасовка, кг',
                    ],
                    'rowImport' => [$this, 'rowImport'],
                ]
            ]
        ];
    }

    public function rowImport(ImportForm $form, array $row)
    {
        $type = $this->importType($row);
        $category = $this->importCategory($row);
        $brand = $this->importBrand($row);
        $product = $this->importProduct($row, $type, $category, $brand);
    }

    /**
     * @param $row
     * @return Type
     * @throws InterruptImportException
     */
    private function importType($row)
    {
        $type = Type::findOne(['name' => $row[self::FIELD_TYPE]]);

        if ($type === null) {
            $type = new Type();
            $type->name = $row[self::FIELD_TYPE];
            if ($type->save() === false) {
                throw new InterruptImportException('Ошибка при импорте типа: ' . implode(', ', $type->getFirstErrors()), $row);
            }
        }

        return $type;
    }

    /**
     * @param $row
     * @return Category
     * @throws InterruptImportException
     */
    private function importCategory($row)
    {
        $category = Category::findOne(['name' => $row[self::FIELD_CATEGORY]]);

        if ($category === null) {
            $category = new Category();
            $category->name = $row[self::FIELD_CATEGORY];
            if ($category->save() === false) {
                throw new InterruptImportException('Ошибка при импорте вида: ' . implode(', ', $category->getFirstErrors()), $row);
            }
        }

        return $category;
    }

    /**
     * @param $row
     * @return Brand
     * @throws InterruptImportException
     */
    private function importBrand($row)
    {
        $brand = Brand::findOne(['name' => $row[self::FIELD_BRAND]]);

        if ($brand === null) {
            $brand = new Brand();
            $brand->name = $row[self::FIELD_BRAND];
            if ($brand->save() === false) {
                throw new InterruptImportException('Ошибка при импорте бренда: ' . implode(', ', $brand->getFirstErrors()), $row);
            }
        }

        return $brand;
    }

    /**
     * @param $row
     * @param Type $type
     * @param Category $category
     * @param Brand $brand
     * @return Product
     * @throws InterruptImportException
     */
    private function importProduct(array $row, Type $type, Category $category, Brand $brand)
    {
        $product = Product::findOne(['type_id' => $type->id, 'category_id' => $category->id, 'brand_id' => $category->id, 'name' => $row[self::FIELD_NAME]]);

        if ($product === null) {
            $product = new Product();
            $product->name = $row[self::FIELD_NAME];
            $product->type_id = $type->id;
            $product->category_id = $category->id;
            $product->brand_id = $brand->id;
            $product->packing = intval(floatval($row[self::FIELD_PACKING]) * 100);

            if ($product->save() == false) {
                throw new InterruptImportException('Ошибка при импорте товара: ' . implode(', ', $product->getFirstErrors()), $row);
            }
        }

        return $product;
    }
}