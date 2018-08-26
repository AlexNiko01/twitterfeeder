<?php

namespace app\models;

use \yii\db\ActiveRecord;

/**
 * This is the model class for table "twitter_users".
 *
 * @property int $id
 * @property string $src_id
 * @property string $user
 */
class TwitterUser extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'twitter_users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['src_id'], 'required'],
            [['user'], 'string'],
            [['src_id'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'src_id' => 'Src ID',
            'user' => 'User',
        ];
    }
}
