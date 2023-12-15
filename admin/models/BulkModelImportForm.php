<?php


namespace admin\models;


use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class BulkModelImportForm extends Model
{

    public $file;

    public $filePath;

    public $override_existing;

    public $columns_map = [];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['columns_map', 'safe'],
            ['file', 'file', 'skipOnEmpty' => false, 'extensions' => ['xls', 'xlsx']],
            ['override_existing', 'boolean'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'file'              => Yii::t("app", "File (xlsx)"),
            'override_existing' => Yii::t("app", "Override if already exists?")
        ];
    }


    /**
     * Upload the file
     *
     * @return boolean
     */
    public function upload()
    {
        if ($this->validate()) {
            $fileName = "{$this->file->baseName}_" . time() . ".{$this->file->extension}";
            $path = Yii::getAlias("@runtime") . DIRECTORY_SEPARATOR . "tmp";
            if (!file_exists($path)) {
                if (!mkdir($path, 0777, true) && !is_dir($path)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
                }
            }
            $this->filePath = $path . DIRECTORY_SEPARATOR . $fileName;
            $this->file->saveAs($this->filePath);
            return true;
        } else {
            return false;
        }
    }


    /**
     * read the uploaded file and extract list of products based on provided
     * columns map
     *
     * @return array
     */
    public function readFile()
    {
        $spreadsheet = IOFactory::load($this->filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $columnsMap = array_flip($this->columns_map);
        $reverseMap = [];
        $columnsInExcel = [];
        $iterator = $worksheet->getRowIterator();
        $header = $iterator->current();
        $cellIterator = $header->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(FALSE);
        foreach ($cellIterator as $cell) {
            /* @var $cell Cell */
            if (array_key_exists($cell->getValue(), $columnsMap)) {
                $columnsInExcel[$cell->getValue()] = $cell->getValue();
                $reverseMap[$cell->getColumn()] = $columnsMap[$cell->getValue()];
            }
        }

        foreach ($columnsMap as $code => $item) {
            if (!array_key_exists($code, $columnsInExcel)) {
                Yii::$app->session->addFlash("danger", "Columns in excel are not as expected");
                Yii::$app->session->addFlash("info", "Expected columns are: <b>[" . implode(" | ", array_keys($columnsMap)) . ']</b>');
                return [];
            }
        }

        $models = [];
        for ($iterator->next(); $iterator->valid(); $iterator->next()) {
            try {
                $row = $iterator->current();
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE);
                $model = [];
                foreach ($cellIterator as $cell) {
                    if (!empty($reverseMap[$cell->getColumn()])) {
                        $model[$reverseMap[$cell->getColumn()]] = $cell->getValue();
                    }
                }
                $models[] = $model;
            } catch (Exception $exception) {
                continue;
            }
        }
        unlink($this->filePath);
        return $models;
    }

    public function import($columnMap, $post, $primaryKey, $className, $preprocess = null, $checkUniqueness = true, $primaryPrefixes = [], $prefixAsColumn = false, $postprocess = null)
    {
        $this->columns_map = $columnMap;
        $errors = [];
        if ($this->load($post)) {
            $this->file = UploadedFile::getInstance($this, 'file');
            if ($this->upload()) {
                $rows = $this->readFile();

                if (!empty($rows)) {
                    //print_r($rows);exit();
                    $codes = ArrayHelper::getColumn($rows, $primaryKey, false);
                    $existingModelss = [];
                    if ($checkUniqueness) {
                        if (!empty($primaryPrefixes)) {
                            $prefixedCodes = [];
                            foreach ($primaryPrefixes as $primaryPrefix) {
                                foreach ($codes as $code) {
                                    $prefixedCodes[] = "{$primaryPrefix}/{$code}";
                                }
                            }
                            $codes = $prefixedCodes;
                        }
                        $existingModelss = ArrayHelper::map($className::find()->where([$primaryKey => $codes])->all(), $primaryKey, function ($model) {
                            return $model;
                        });
                    }
                    $i = 1;
                    $success = 0;
                    $failed = 0;
                    $empty = 0;
                    $old = 0;
                    $new = 0;
                    $existing = 0;
                    $existingPrimaries = [];
                    $indexedRows = [];
                    if (!empty($primaryPrefixes)) {
                        $newRows = [];
                        foreach ($primaryPrefixes as $key => $primaryPrefix) {
                            foreach ($rows as $index => $row) {
                                if (empty($row[$primaryKey])) {
                                    $empty++;
                                    continue;
                                }
                                $prefixedPrimary = "{$primaryPrefix}/{$row[$primaryKey]}";
                                $row[$primaryKey] = $prefixedPrimary;
                                if ($prefixAsColumn) {
                                    $row[$prefixAsColumn] = $primaryPrefix;
                                }
                                $newRows[] = $row;
                            }
                        }
                        $rows = $newRows;
                    }
                    foreach ($rows as $index => $row) {
                        if (empty($row[$primaryKey])) {
                            $empty++;
                        }

                        // $indexedRows[$row[$primaryKey]] = $row; // to prevent duplicates within file itself

                        // Group rows by primary key
                        $code = $row[$primaryKey];
                        $indexedRows[$code][] = $row;
                    }
                    Yii::error($indexedRows, 'IMPORT');

                    // print_r($indexedRows);
                    // exit;

                    foreach ($indexedRows as $code => $groupedRows) {
                        foreach ($groupedRows as $index => $row) {
                            $i++;

                            $preprocessedRow = $row;

                            if ($preprocess !== null) {
                                // Preprocess the row and check if it's null
                                $preprocessedRow = $preprocess($row);
                                if ($preprocessedRow === null) {
                                    $failed++;
                                    continue; // Skip this row and move to the next one
                                } else if ($preprocessedRow == -1) {
                                    $success++;
                                    continue;
                                }
                            }

                            if (empty($row[$primaryKey])) {
                                $empty++; //count rows with empty key (should be 1 or 0 because we removed duplicates)
                                continue;
                            }

                            $importModel = new $className();

                            // Handle duplicates
                            if (array_key_exists($code, $existingModelss)) {
                                if (!$this->override_existing) {
                                    $existing++;
                                    $failed++;
                                    $existingPrimaries[] = $code;
                                    continue;
                                } else {
                                    $importModel = $existingModelss[$code];
                                    $old++;
                                }
                            } else {
                                $new++;
                            }

                            $importModel->load($preprocessedRow, '');
                            if (!$importModel->save(false)) {
                                $errors[$i][] = $importModel->getFirstErrors();
                                $failed++;
                            } else {
                                $success++;

                                if ($postprocess !== null && is_callable($postprocess)) {
                                    $postprocess($importModel, $row);
                                }
                            }
                        }
                    }

                    Yii::$app->getSession()->addFlash("info", \Yii::t("app", "{$new} new rows"));
                    Yii::$app->getSession()->addFlash("info", \Yii::t("app", "{$old} existing rows"));
                    Yii::$app->getSession()->addFlash("success", \Yii::t("app", "{$success} rows added/updated successfully"));
                    Yii::$app->getSession()->addFlash("danger", \Yii::t("app", "{$failed} rows failed to be added/updated"));
                    Yii::$app->getSession()->addFlash("danger", \Yii::t("app", "{$empty} empty rows"));

                    if ($existing > 0) {
                        Yii::$app->getSession()->addFlash("danger", \Yii::t("app", "{$existing} row(s) already exist and have been skipped skipped") .
                            '<br/>' . \Yii::t("app", "Existing rows are {codes}", [
                                'codes' => implode("<br/>", $existingPrimaries)
                            ]));
                    }
                }
                //                Yii::$app->getSession()->addFlash("danger", \Yii::t("app", Json::encode($errors)));

                return [
                    'model'  => $this,
                    'errors' => $errors
                ];
            }
        }
        return false;
    }

    public function importNew($columnMap, $post, $primaryKey, $className, $preprocess = null, $checkUniqueness = true, $primaryPrefixes = [], $prefixAsColumn = false, $postprocess = null)
    {
        $this->columns_map = $columnMap;
        $errors = [];
        if ($this->load($post)) {
            $this->file = UploadedFile::getInstance($this, 'file');
            if ($this->upload()) {
                $rows = $this->readFile();

                if (!empty($rows)) {
                    //print_r($rows);exit();
                    $codes = ArrayHelper::getColumn($rows, $primaryKey, false);
                    $existingModelss = [];
                    if ($checkUniqueness) {
                        if (!empty($primaryPrefixes)) {
                            $prefixedCodes = [];
                            foreach ($primaryPrefixes as $primaryPrefix) {
                                foreach ($codes as $code) {
                                    $prefixedCodes[] = "{$primaryPrefix}/{$code}";
                                }
                            }
                            $codes = $prefixedCodes;
                        }
                        $existingModelss = ArrayHelper::map($className::find()->where([$primaryKey => $codes])->all(), $primaryKey, function ($model) {
                            return $model;
                        });
                    }
                    $i = 1;

                    $success = 0;
                    $failed = 0;
                    $empty = 0;
                    $old = 0;
                    $new = 0;
                    $existing = 0;
                    $existingPrimaries = [];
                    $indexedRows = [];
                    if (!empty($primaryPrefixes)) {
                        $newRows = [];
                        foreach ($primaryPrefixes as $key => $primaryPrefix) {
                            foreach ($rows as $index => $row) {
                                if (empty($row[$primaryKey])) {
                                    $empty++;
                                    continue;
                                }
                                $prefixedPrimary = "{$primaryPrefix}/{$row[$primaryKey]}";
                                $row[$primaryKey] = $prefixedPrimary;
                                if ($prefixAsColumn) {
                                    $row[$prefixAsColumn] = $primaryPrefix;
                                }
                                $newRows[] = $row;
                            }
                        }
                        $rows = $newRows;
                    }
                    foreach ($rows as $index => $row) {
                        if (empty($row[$primaryKey])) {
                            $empty++;
                        }

                        // $indexedRows[$row[$primaryKey]] = $row; // to prevent duplicates within file itself

                        // Group rows by primary key
                        $code = $row[$primaryKey];
                        $indexedRows[$code][] = $row;
                    }
                    Yii::error($indexedRows, 'IMPORT');

                    foreach ($indexedRows as $code => $groupedRows) {
                        foreach ($groupedRows as $index => $row) {
                            $i++;

                            $preprocessedRow = $row;

                            if ($preprocess !== null) {
                                // Preprocess the row and check if it's null
                                $preprocessedRow = $preprocess($row);
                                if ($preprocessedRow === null) {
                                    $failed++;
                                    continue; // Skip this row and move to the next one
                                } else if ($preprocessedRow == -1) {
                                    $success++;
                                    continue;
                                }
                            }

                            if (empty($primaryKey)) {
                                $new++;
                                $importModel = new $className();
                                $importModel->load($preprocessedRow, '');
                            } else {
                                $importModel = $className::findOne([$primaryKey => $row[$primaryKey]]);
                                if ($importModel) {
                                    $old++;

                                    unset($row[$primaryKey]);
                                    $importModel->load($preprocessedRow, '');
                                } else { // If the primary key is provided but doesn't exist, consider it a new row
                                    $new++;
                                    $importModel = new $className();
                                    $importModel->load($preprocessedRow, '');
                                }
                            }

                            if (!$importModel->save(false)) {
                                $errors[$i][] = $importModel->getFirstErrors();
                                $failed++;
                            } else {
                                $success++;

                                if ($postprocess !== null && is_callable($postprocess)) {
                                    $postprocess($importModel, $row);
                                }
                            }
                        }
                    }

                    Yii::$app->getSession()->addFlash("info", \Yii::t("app", "{$new} new rows"));
                    Yii::$app->getSession()->addFlash("info", \Yii::t("app", "{$old} existing rows"));
                    Yii::$app->getSession()->addFlash("success", \Yii::t("app", "{$success} rows added/updated successfully"));
                    Yii::$app->getSession()->addFlash("danger", \Yii::t("app", "{$failed} rows failed to be added/updated"));
                    Yii::$app->getSession()->addFlash("danger", \Yii::t("app", "{$empty} empty rows"));

                    if ($existing > 0) {
                        Yii::$app->getSession()->addFlash("danger", \Yii::t("app", "{$existing} row(s) already exist and have been skipped skipped") .
                            '<br/>' . \Yii::t("app", "Existing rows are {codes}", [
                                'codes' => implode("<br/>", $existingPrimaries)
                            ]));
                    }
                }
                //                Yii::$app->getSession()->addFlash("danger", \Yii::t("app", Json::encode($errors)));

                return [
                    'model'  => $this,
                    'errors' => $errors
                ];
            }
        }
        return false;
    }
}
