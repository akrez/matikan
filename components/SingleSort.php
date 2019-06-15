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
            $sort = $this->sort;
        } else {
            $sort = key($this->sortAttributes);
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
