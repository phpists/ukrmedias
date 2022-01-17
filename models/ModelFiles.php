<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\di\Instance;
use yii\web\View;
use app\components\Misc;
use app\components\Icons;

class ModelFiles extends \app\components\BaseActiveRecord {

    const EMPTY_PK = '';
    const KEY = 'fJv9g4Wmeqn1efcgbH10RaA4mWoH7rtj';
    const SCENARIO_IMG = 'img';
    const SCENARIO_PDF = 'pdf';
    const SCENARIO_DOCS = 'docs';

    public $file;
    public $label;
    public $ownerIsNew = true;
    protected $basePath;
    protected $baseUrl;
    protected $_tmp_dir;
    protected $resize = [];
    protected $multi = false;
    protected $default;

    public static function tableName() {
        return 'model_files';
    }

    public function rules() {
        return [
            ['file', 'file',
                'on' => self::SCENARIO_IMG,
                'skipOnEmpty' => true,
                'skipOnError' => false,
                'maxSize' => 10000000,
                'extensions' => ['jpg', 'png', 'jpeg', 'svg', 'bmp'],
                'mimeTypes' => ['image/jpg', 'image/jpeg', 'image/png', 'image/svg', 'image/x-ms-bmp'],
            ],
            ['file', 'file',
                'on' => self::SCENARIO_PDF,
                'skipOnEmpty' => true,
                'skipOnError' => false,
                'maxSize' => 30000000,
                'extensions' => 'pdf',
                'mimeTypes' => ['application/pdf'],
            ],
            ['file', 'file',
                'on' => self::SCENARIO_DOCS,
                'skipOnEmpty' => true,
                'skipOnError' => false,
                'maxSize' => 30000000,
                'extensions' => ['doc', 'docx', 'xls', 'xlsx', 'pdf'],
                'mimeTypes' => [
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.ms-office',
                    'application/pdf',
                ],
            ],
            ['pos', 'default', 'value' => 999],
        ];
    }

    static public function getInstance(&$owner, $attr, $data, $forDelete = false, $fileModel = null) {
        $pk = $owner->isNewRecord ? self::EMPTY_PK : json_encode($owner->getPrimaryKey());
        $instanse = $fileModel === null ? self::findModel(['entity' => get_class($owner), 'pk' => $pk, 'attr' => $attr]) : $fileModel;
        $instanse->setScenario($data['scenario']);
        $instanse->ownerIsNew = $owner->isNewRecord;
        $className = strtolower((new \ReflectionClass($owner))->getShortName());
        $instanse->entity = get_class($owner);
        $instanse->pk = $pk;
        $instanse->attr = $attr;
        $instanse->label = $data['label'];
        $instanse->basePath = isset($data['basePath']) ? $data['basePath'] : Yii::getAlias('@app/web' . Yii::$app->params['uploadBaseUrl'] . "/{$className}");
        $instanse->baseUrl = isset($data['baseUrl']) ? $data['baseUrl'] : Yii::$app->params['uploadBaseUrl'] . "/{$className}";
        if (isset($data['resize'])) {
            $instanse->resize = $data['resize'];
        }
        if (isset($data['multi'])) {
            $instanse->multi = $data['multi'];
        }
        if (isset($data['default'])) {
            $instanse->default = $data['default'];
        }
        if ($instanse->multi && $forDelete === false) {
            $instanse->setIsNewRecord(true);
            $instanse->id = null;
        }
        return $instanse;
    }

    static public function setModelFiles(&$owner, $attr, $data, $forDelete = false) {
        $pk = $owner->isNewRecord ? self::EMPTY_PK : json_encode($owner->getPrimaryKey());
        $instanse = self::findModel(['entity' => get_class($owner), 'pk' => $pk, 'attr' => $attr]);
        $instanse->setScenario($data['scenario']);
        $instanse->ownerIsNew = $owner->isNewRecord;
        $className = strtolower((new \ReflectionClass($owner))->getShortName());
        $instanse->entity = get_class($owner);
        $instanse->pk = $pk;
        $instanse->attr = $attr;
        $instanse->label = $data['label'];
        $instanse->basePath = isset($data['basePath']) ? $data['basePath'] : Yii::getAlias('@app/web' . Yii::$app->params['uploadBaseUrl'] . "/{$className}");
        $instanse->baseUrl = isset($data['baseUrl']) ? $data['baseUrl'] : Yii::$app->params['uploadBaseUrl'] . "/{$className}";
        if (isset($data['resize'])) {
            $instanse->resize = $data['resize'];
        }
        if (isset($data['multi'])) {
            $instanse->multi = $data['multi'];
        }
        if (isset($data['default'])) {
            $instanse->default = $data['default'];
        }
        if ($instanse->multi && $forDelete === false) {
            $instanse->setIsNewRecord(true);
            $instanse->id = null;
        }
        return $instanse;
    }

    public function isSubmitted() {
        $this->file = UploadedFile::getInstanceByName((new \ReflectionClass($this))->getShortName() . $this->attr);
        return $this->file instanceof UploadedFile;
    }

    public function moveTmpFile($pk) {
        $this->pk = json_encode($pk);
        $files = $this->multi ? \yii\helpers\FileHelper::findFiles($this->getTmpDir()) : [$this->getTmpDir() . $this->fname];
        foreach (\yii\helpers\FileHelper::findFiles($this->getTmpDir()) as $file) {
            $filename = basename($file);
            if (!preg_match("@^_{$this->attr}_@", $filename)) {
                continue;
            }
            $fileModel = self::findModel(['entity' => $this->entity, 'pk' => self::EMPTY_PK, 'attr' => $this->attr, 'fname' => "/{$filename}"]);
            if (!is_array($pk)) {
                $pk = [$pk];
            }
            $ext = strtolower(substr($file, strrpos($file, '.')));
            $fileModel->ownerIsNew = false;
            $fileModel->pk = $this->pk;
            $fileModel->fname = uniqid('/' . mt_rand(0, 255) . '/' . mt_rand(0, 255) . '/' . implode('_', $pk) . "_{$this->attr}_") . $ext;
            $fileModel->title = str_replace($ext, '', $filename);
            $fileModel->scenario = $this->scenario;
            $fileModel->resize = $this->resize;
            $fileModel->basePath = $this->basePath;
            $newFile = $this->basePath . $fileModel->fname;
            if (!is_dir(dirname($newFile))) {
                mkdir(dirname($newFile), 0775, true);
            }
            rename($file, $newFile);
            $fileModel->resize($newFile, false);
            $fileModel->save(false);
        }
    }

    public function saveFile($web = true) {
        if ($this->validate()) {
            $oldFile = $this->getDir() . $this->fname;
            $pk = (array) json_decode($this->pk, true);
            $subDir = $this->pk === self::EMPTY_PK ? '' : '/' . mt_rand(0, 255) . '/' . mt_rand(0, 255);
            $this->fname = uniqid("{$subDir}/" . implode('_', $pk) . "_{$this->attr}_") . '.' . strtolower($this->file->getExtension());
            $newFile = $this->getDir() . $this->fname;
            if (!is_dir(dirname($newFile))) {
                mkdir(dirname($newFile), 0775, true);
            }
            if (!$this->file->saveAs($newFile, $web)) {
                return false;
            }
            $this->resize($newFile, $this->ownerIsNew);
            $this->clean($newFile, $oldFile);
            $this->is_img = $this->getScenario() === self::SCENARIO_IMG;
            $this->title = $this->file->getBaseName();
            return $this->save(false);
        } elseif ($this->file->getHasError()) {
            Yii::dump(__METHOD__ . ': upload file error. ' . var_export($this->file, true));
            return false;
        } else {
            Yii::dump('Scenario "' . $this->getScenario() . '": ' . $this->file->getBaseName() . '(' . $this->file->size . '): sent as ' . $this->file->type . ' => real as ' . mime_content_type($this->file->tempName));
            Yii::dump($this->getErrors());
            return false;
        }
    }

    protected function resize($newFile, $skip) {
        if ($this->getScenario() !== self::SCENARIO_IMG || $skip) {
            return;
        }
        foreach ($this->resize as $size => $dim) {
            $this->createImage($newFile, $size, $dim);
        }
    }

    public function resizeSelf() {
        if ($this->getScenario() !== self::SCENARIO_IMG) {
            return;
        }
        foreach ($this->resize as $size => $dim) {
            $this->createImage($this->getFile(), $size, $dim);
        }
    }

    protected function createImage($fname, $size, $dim) {
        if (preg_match('/\.svg$/', $fname)) {
            return copy($fname, $this->getDir() . $this->getFname($size));
        }
        $config = [
            'original' => $fname,
            'target' => $this->getDir() . $this->getFname($size),
            'width' => $dim[0],
            'height' => $dim[1],
            'crop' => isset($dim[2]) ? $dim[2] : false,
            'canvas' => isset($dim[3]) ? $dim[3] : false,
            'watermark' => isset($dim[4]) && isset(Yii::$app->params['watermark']) ? Yii::getAlias(Yii::$app->params['watermark']) : false,
            'size' => $size,
        ];
        try {
            switch ($this->entity):
                case 'app\models\Goods':
                    $resizer = new image_resizer\Goods($config);
                    break;
                default:
                    $resizer = new image_resizer\General($config);
            endswitch;
            $resizer->save();
        } catch (\Exception $ex) {
            Yii::dump(__METHOD__, $ex->getMessage());
        }
    }

    public function deleteModel($where = null, $params = []) {
        if ($where === null) {
            $where = ['entity' => $this->entity, 'pk' => $this->pk, 'attr' => $this->attr];
        }
        foreach (ModelFiles::find()->where($where, $params)->all() as $model) {
            $model->basePath = $this->basePath;
            $model->ownerIsNew = false;
            $model->resize = $this->resize;
            $model->scenario = $this->scenario;
            $model->delete();
        }
    }

    public function afterDelete() {
        $file = $this->getFile();
        $this->clean($file, $file, true);
    }

    protected function clean($newFile, $oldFile, $force = false) {
        if (($oldFile === $newFile || $this->multi) && $force !== true) {
            return;
        }
        if (is_file($oldFile)) {
            unlink($oldFile);
        }
        if ($this->getScenario() !== self::SCENARIO_IMG) {
            return;
        }
        foreach (array_keys($this->resize) as $size) {
            $old = dirname($oldFile) . '/' . $this->getFname($size, basename($oldFile));
            if (is_file($old)) {
                unlink($old);
            }
        }
    }

    protected function getFname($size = null, $fname = null) {
        if ($size === null) {
            return $this->fname;
        }
        if ($fname === null) {
            $fname = $this->fname;
        }
        if ($size !== null) {
            $size = "-{$size}";
        }
        return str_replace('.', $size . '.', $fname);
    }

    protected function getDir() {
        if ($this->ownerIsNew) {
            return $this->getTmpDir();
        }
        if ($this->basePath === null) {
            $config = Instance::ensure($this->entity)->filesConfig();
            $this->basePath = $config[$this->attr]['basePath'];
        }
        return $this->basePath;
    }

    protected function getTmpDir() {
        if ($this->_tmp_dir === null) {
            $this->_tmp_dir = $this->basePath . '/tmp_upload/' . (Yii::$app->user->identity ? Yii::$app->user->identity->id : null);
            if (!is_dir($this->_tmp_dir)) {
                mkdir($this->_tmp_dir, 0775, true);
            }
        }
        return $this->_tmp_dir;
    }

    public function getFile() {
        return $this->getDir() . $this->fname;
    }

    public function fileField($label = true) {
        if ($this->multi && !Yii::$app->request->isAjax) {
            \app\components\assets\AdminSortableAssets::register(Yii::$app->view);
        }
        $name = (new \ReflectionClass($this))->getShortName() . $this->attr;
        $id = uniqid($name);
        $sortId = "sort_{$name}";
        echo '<div class="js-file-form">';
        if ($label) {
            echo '<label class="control-label">' . $this->label . '</label>';
        }
        echo '<div class="alert alert-warning" style="display:none;"></div>';
        echo '<span class="indicator" style="display:none;">' . Icons::$loading . '</span>';
        echo '<div id="' . $sortId . '" class="' . ($this->multi ? 'model_files_sortable' : '') . '" style="display:flex;flex-wrap:wrap;">';
        echo $this->getAdminIcon();
        echo '</div>';
        echo '<label for="' . $id . '" class="btn btn-xs btn-primary"><i class="fa fa-plus"></i></label>';
        echo Html::fileInput($name, '', array(
            'id' => $id,
            'style' => 'display:none;',
            'multiple' => $this->multi ? true : null,
            'onChange' => 'uploadFile(this,"#' . $sortId . '",' . intval($this->multi) . ')',
            'data-url' => $this->getUploadUrl_admin(),
        ));
        echo '</div>';
        Yii::$app->view->registerCss('label.btn{margin-right:15px;}', [], __CLASS__);
        Yii::$app->view->registerJs('
            function uploadFile(input,sortDiv,multi) {
                if (input.files.length === 0) {
                    return;
                }
                var $div=$(input).closest("div.js-file-form");
                var qty=input.files.length;
                var $mess=$div.find("div.alert").slideUp().html("");
                var $ctrl = $div.find("label,.indicator").toggle();
                var i=0;
                function sendFile(input,i,total){
                        var fd = new FormData();
                        fd.append(input.name, input.files[i]);
                        $.ajax({
                            method: "post",
                            url: input.dataset.url,
                            dataType: "json",
                            data: fd,
                            processData: false,
                            contentType: false,
                            complete: function (resp) {
                                if(resp.responseJSON.mess.length>0){
                                    $mess.append(resp.responseJSON.mess).slideDown();
                                }
                                if(resp.responseJSON.link.length>0){
                                    if(multi){
                                        $(sortDiv).append(resp.responseJSON.link);
                                    }
                                    else{
                                        $(sortDiv).html(resp.responseJSON.link);
                                    }
                                }
                                i++;
                                if(i<total){
                                    sendFile(input,i,total);
                                }
                                else{
                                    $ctrl.toggle();
                                    input.value = "";
                                }
                            }
                        });

                }
                sendFile(input,i,qty);
            }
            function deleteFile(){
                $(this).closest("div.js-file-div").remove();
            }
                ',
                View::POS_END,
                __CLASS__
        );
    }

//    public function getDownloadUrl_public() {
//        if (empty($this->id)) {
//            return;
//        }
//        return Url::toRoute(['/frontend/data/modelfiledownload', 'id' => $this->getPrimaryKey()], true);
//    }

    public function isExists($default = false) {
        if (!empty($this->id)) {
            return true;
        }
        return $default;
    }

    public function getSrc($size = null) {
        if (empty($this->id)) {
            return $this->default === null ? '/files/images/system/empty.png' : $this->default;
        }
        $tmp = $this->ownerIsNew ? '/tmp_upload/' . Yii::$app->user->identity->id : '';
        return $this->baseUrl . $tmp . $this->getFname($size);
    }

    public function getSrcFirst($size = null) {
        $data = $this->findMulti(1);
        $dataModel = array_shift($data);
        if ($dataModel === null) {
            return $this->default === null ? '/files/images/system/empty.png' : $this->default;
        }
        return $dataModel->baseUrl . $dataModel->getFname($size);
    }

    public function findMulti($limit = null, $andWhere = null) {
        $query = $this->find()->where(['entity' => $this->entity, 'pk' => $this->pk, 'attr' => $this->attr])->orderBy('pos');
        if (is_numeric($limit)) {
            $query->limit($limit);
        }
        if ($andWhere !== null) {
            $query->andWhere($andWhere);
        }
        $data = [];
        foreach ($query->all() as $fileModel) {
            $fileModel->scenario = $this->scenario;
            $fileModel->ownerIsNew = $this->ownerIsNew;
            $fileModel->basePath = $this->basePath;
            $fileModel->baseUrl = $this->baseUrl;
            $fileModel->resize = $this->resize;
            $data[] = $fileModel;
        }
        return $data;
    }

    public function getAdminIconItem() {
        $html = '<div class="js-file-div" style="position:relative;" data-id="' . $this->id . '">';
        $url = $this->getDownloadUrl_admin();
        $delBtn = '<label class="btn btn-xs btn-danger" style="position:absolute;top:5px;left:5px;z-index:1;" data-url="' . $this->getDeleteUrl_admin() . '" data-callback="deleteFile" data-confirm-jsclick="Ви підтверджуєте видалення ' . $this->label . '?"><i class="fa fa-trash"></i></label>';
        $item = $this->getScenario() === self::SCENARIO_IMG ? '<img src="' . $this->getSrc() . '?' . time() . '" class="img-thumbnail" style="width:auto;max-height:200px;" />' : '<a class="table-btn" href="' . $url . '">' . $this->getIcon() . '</a>';
        $html .= "<div style='margin:0 10px 10px 0;'>{$item}<div>{$delBtn}</div></div></div>";
        return $html;
    }

    public function getAdminIcon() {
        if ($this->multi) {
            $html = '';
            foreach ($this->findMulti() as $fileModel) {
                $html .= $fileModel->getAdminIconItem();
            }
            return $html;
        } elseif (!is_file($this->getDir() . $this->getFname()) || $this->hasErrors()) {
            return '';
        } else {
            $url = $this->getDownloadUrl_admin();
            $html = '<div class="js-file-div" style="position:relative;">';
            $html .= $this->getScenario() === self::SCENARIO_IMG ? '<img src="' . $this->getSrc() . '?' . time() . '" class="img-thumbnail" style="width:auto;max-height:200px;" />' : '<a class="table-btn" href="' . $url . '">' . $this->getIcon() . '</a>';
            $delBtn = '<label class="btn btn-xs btn-danger" style="position:absolute;top:5px;left:5px;z-index:1;" data-url="' . $this->getDeleteUrl_admin() . '" data-callback="deleteFile" data-confirm-jsclick="Ви підтверджуєте видалення ' . $this->label . '?"><i class="fa fa-trash"></i></label>';
            return "{$html}{$delBtn}</div>";
        }
    }

    public function getDownloadUrl($module = 'frontend') {
        return Url::toRoute(["/{$module}/data/model-file-download", 'id' => $this->getPrimaryKey()], true);
    }

    protected function getDownloadUrl_admin() {
        return Url::toRoute(['/admin/data/model-file-download', 'id' => $this->getPrimaryKey()], true);
    }

    protected function getDeleteUrl_admin() {
        $id = Yii::$app->security->hashData(implode(' ', [$this->entity, $this->pk, $this->attr, $this->getPrimaryKey()]), self::KEY);
        return Url::toRoute(['/admin/data/model-file-delete', 'id' => $id], true);
    }

    protected function getUploadUrl_admin() {
        $id = Yii::$app->security->hashData(implode(' ', [$this->entity, $this->pk, $this->attr]), self::KEY);
        return Url::toRoute(['/admin/data/model-file-upload', 'id' => $id], true);
    }

    static public function download($id) {
        $fileModel = self::findOne($id);
        if ($fileModel === null) {
            $fileModel = new self();
            $fileModel->addError('id', 'Некорректная ссылка на файл.');
            return $fileModel;
        }
        $owner = Instance::ensure($fileModel->entity)->findOne($fileModel->pk);
        $owner->initInstance($fileModel);
        $file = $fileModel->getFile();
        $ext = substr($file, strrpos($file, '.'));
        if (!is_file($file)) {
            $fileModel->addError('id', 'Файл отсутствует на диске.');
            return $fileModel;
        }
        if (!empty($owner->title)) {
            $fileModel->title = $owner->title;
        }
        Misc::sendFile($file, $fileModel->title . $ext, false);
        return $fileModel;
    }

    static public function upload($id) {
        $d = Yii::$app->security->validateData($id, self::KEY);
        if (!$d) {
            return false;
        }
        $data = explode(' ', $d);
        $pk = json_decode($data[1], true);
        $entity = empty($pk) ? new $data[0] : Instance::ensure($data[0])->findModel($pk);
#$entity->setPrimaryKey($pk);
        $entity->initFiles();
        $file = $entity->getModelFiles($data[2]);
        if ($file->isSubmitted()) {
            $file->saveFile();
        }
        return $file;
    }

    static public function del($id) {
        $d = Yii::$app->security->validateData($id, self::KEY);
        if (!$d) {
            return false;
        }
        $data = explode(' ', $d);
        $pk = json_decode($data[1], true);
        $entity = empty($pk) ? new $data[0] : Instance::ensure($data[0])->findModel($pk);
        $entity->initFiles(true);
        $file = $entity->getModelFiles($data[2]);
        $toDel = ModelFiles::findOne($data[3]);
        $toDel->basePath = $file->basePath;
        $toDel->ownerIsNew = $entity->isNewRecord;
        $toDel->resize = $file->resize;
        $toDel->setScenario($file->getScenario());
        $toDel->delete();
    }

    public function getIcon() {
        $file = $this->getFile();
        $ext = substr($file, strrpos($file, '.') + 1);
        switch ($ext):
            case 'doc':
            case 'docx':
                $icon = '<img src="/files/images/system/doc.svg?' . time() . '" style="width:auto;height:100px;" width="100" height="100"/>';
                break;
            case 'xls':
            case 'xlsx':
                $icon = '<img src="/files/images/system/xlsx.svg?' . time() . '" style="width:auto;height:100px;" width="100" height="100"/>';
                break;
            case 'pdf':
                $icon = '<img src="/files/images/system/pdf.svg?' . time() . '" style="width:auto;height:100px;" width="100" height="100"/>';
                break;
            default:
                $icon = strtoupper($ext) . ' &#8690;';
        endswitch;

//        switch ($ext):
//            case 'jpg':
//            case 'jpeg':
//            case '7z':
//            case '7zip':
//            case 'zip':
//            case 'rar':
//            case 'doc':
//            case 'docx':
//            case 'xls':
//            case 'xlsx':
//            case 'pdf':
//            case 'txt':
//                $icon = '<img src="/files/images/system/' . $ext . '.png?' . time() . '" style="width:auto;height:45px;"/>';
//                break;
//            default:
//                $icon = strtoupper($ext) . ' &#8690;';
//        endswitch;
        return $icon;
    }

}
