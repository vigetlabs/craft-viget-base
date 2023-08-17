<?php

namespace viget\base\models;

use yii\base\BaseObject;

class PartsKitNavItem extends BaseObject
{
    public string $title;
    public string $url;
    public array $children = [];
}
