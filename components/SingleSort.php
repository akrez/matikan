<?php

namespace app\components;

use yii\base\Component;

class SingleSort extends Component
{
    public $sort;
    //
    public $defaultSort;
    public $sortAttributes;
    //
    public $order;
    public $attribute;

    public function init()
    {
        parent::init();

        if (empty($this->sort)) {
            $this->sort = $this->defaultSort;
        }

        if (empty($this->sort) || !isset($this->sortAttributes[$this->sort])) {
            $sort = key($this->sortAttributes);
        } else {
            $sort = $this->sort;
        }

        if ($sort[0] == '-') {
            $order = SORT_DESC;
            $attribute = substr($sort, 1);
        } else {
            $order = SORT_ASC;
            $attribute = $sort;
        }

        $this->order = $order;
        $this->attribute = $attribute;
    }
}
