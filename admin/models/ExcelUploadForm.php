<?php


namespace admin\models;


use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

class ExcelUploadForm extends Model
{

    public $file;

    public $filePath;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['file', 'file', 'skipOnEmpty' => false, 'extensions' => ['xls', 'xlsx']],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'file' => Yii::t("app", "File (xlsx)")
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

        $columnsInExcel = [];
        $iterator = $worksheet->getRowIterator();
        $header = $iterator->current();
        $cellIterator = $header->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(FALSE);
        foreach ($cellIterator as $cell) {
            /* @var $cell Cell */
            $columnsInExcel[$cell->getColumn()] = $cell->getValue();
        }

        $models = [];
        for ($iterator->next(); $iterator->valid(); $iterator->next()) {
            try {
                $row = $iterator->current();
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE);
                $model = [];
                foreach ($cellIterator as $cell) {
                    $model[$columnsInExcel[$cell->getColumn()]] = $cell->getValue();
                }
                $models[] = $model;
            } catch (Exception $exception) {
                continue;
            }
        }
        unlink($this->filePath);
        return $models;
    }

    public function fromPost(array $post)
    {
        $errors = [];
        if ($this->load($post)) {
            $this->file = UploadedFile::getInstance($this, 'file');
            if ($this->upload()) {
                return $this->readFile();
            }
        }
        throw new ServerErrorHttpException("Error uploading file");
    }

}