<?php

namespace app\models;

use \yii\db\ActiveRecord;

/**
 * This is the model class for table "twitter_users".
 *
 * @property int $id
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
            [['user'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user' => 'User',
        ];
    }
}
