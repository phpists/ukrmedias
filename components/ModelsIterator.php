<?php

namespace app\components;

class ModelsIterator extends \yii\base\BaseObject {

    public $query;
    public $offset = 0;
    public $limit = 500;
    public $beforeOffset;
    public $callback;
    public $afterOffset;

    public function run() {
        while ($dataset = $this->query->offset($this->offset)->limit($this->limit)->all()) {
            if ($this->beforeOffset !== null) {
                ($this->beforeOffset)($this);
            }
            foreach ($dataset as $data) {
                ($this->callback)($data);
            }
            $this->offset += $this->limit;
            if ($this->afterOffset !== null) {
                ($this->afterOffset)($this);
            }
        }
    }

}
