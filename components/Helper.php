<?php

namespace app\components;

use yii\helpers\VarDumper;
use yii\base\Component;

class Helper extends Component
{

    public static function rulesDumper($scenariosRules, $attributesRules)
    {
        $rules = [];
        foreach ($scenariosRules as $scenario => $scenarioAttributesRules) {
            foreach ($scenarioAttributesRules as $attributeLabel => $scenarioRules) {
                $attribute = ($attributeLabel[0] == '!' ? substr($attributeLabel, 1) : $attributeLabel);
                foreach ($scenarioRules as $scenarioRule) {
                    $rules[] = array_merge([[$attributeLabel]], $scenarioRule, ['on' => $scenario]);
                }
                if (isset($attributesRules[$attribute])) {
                    foreach ($attributesRules[$attribute] as $attributeRule) {
                        $rules[] = array_merge([[$attributeLabel]], $attributeRule, ['on' => $scenario]);
                    }
                }
            }
        }
        return VarDumper::export($rules);
    }

    public static function generateRandomString($length = 6)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString = $randomString . $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function normalizeArray($arr, $arrayOut = false)
    {
        if (is_array($arr)) {
            $arr = implode(",", $arr);
        }
        $arr = str_ireplace("\n", ",", $arr);
        $arr = str_ireplace(",", ",", $arr);
        $arr = str_ireplace("،", ",", $arr);
        $arr = explode(",", $arr);
        $arr = array_map("trim", $arr);
        $arr = array_unique($arr);
        $arr = array_filter($arr);
        sort($arr);
        if ($arrayOut) {
            return $arr;
        }
        return implode(",", $arr);
    }

    public function normalizeEmail($email)
    {
        $email = explode('@', $email);
        $email[0] = str_replace('.', '', $email[0]);
        return implode('@', $email);
    }

}
