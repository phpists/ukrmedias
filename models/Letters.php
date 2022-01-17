<?php

namespace app\models;

use \Yii;

class Letters extends \app\components\BaseActiveRecord {

    const SEND_LIMIT = 100;

    public static function tableName() {
        return 'letters';
    }

    static public function getDb() {
        return Yii::$app->db;
    }

    protected function start($id) {
        $sql = 'UPDATE ' . self::tableName() . ' SET status=:id WHERE status IS NULL LIMIT ' . self::SEND_LIMIT;
        return self::getDb()->createCommand($sql, [':id' => $id])->execute();
    }

    protected function end($id) {
        $sql = 'UPDATE ' . self::tableName() . ' SET status="sent" WHERE id=:id';
        return self::getDb()->createCommand($sql, [':id' => $id])->execute();
    }

    protected function getData($id) {
        return self::find()
                        ->where('status=:id', [':id' => $id])
                        ->limit(self::SEND_LIMIT)
                        ->asArray()
                        ->all();
    }

    public function send() {
        $id = uniqid();
        $this->start($id);
        $dir = Yii::$app->getRuntimePath() . '/attachments';
        if (!is_dir($dir)) {
            mkdir($dir, true, 0775);
        }
        foreach ($this->getData($id) as $d) {
            try {
                $message = Yii::$app->mailer->compose()
                        ->setFrom([$d['from'] => Yii::$app->name])
                        ->setTo($d['to'])
                        ->setSubject($d['subject'])
                        ->setHtmlBody($d['body']);
                if (!empty($d['attachments'])) {
                    foreach (json_decode($d['attachments'], true) as $row) {
                        $file = "{$dir}/{$row['file']}";
                        $q = file_put_contents($file, base64_decode($row['content']));
                        if ($q) {
                            #$attachment = new \Swift_Attachment(base64_decode($row['content']), $row['file'], 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                            $message->attach($file);
                        }
                    }
                }
                $message->send();
                $this->end($d['id']);
            } catch (\Throwable $ex) {
                Yii::dump($ex->getMessage());
            }
        }
    }

    static public function setupTask($data) {
        $body = isset($data ['filter']) ? call_user_func_array($data['filter'], array($data['body'])) : $data['body'];
        return self::getDb()->createCommand('INSERT INTO `' . self::tableName() . '` (`id`,`from`,`to`,`subject`,`body`,attachments) VALUES (0,:from,:to,:subj,:body,:attachments)', [
                    ':from' => Yii::$app->params['systemEmail'],
                    ':to' => $data['to'],
                    ':subj' => $data['subject'],
                    ':body' => '<!DOCTYPE html><html lang="' . Yii::$app->language . '"><body>' . $body . '</body></html>',
                    ':attachments' => isset($data['attachments']) ? $data['attachments'] : null,
                ])->execute();
    }

}
