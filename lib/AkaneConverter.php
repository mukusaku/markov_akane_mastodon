<?php
require_once __DIR__ . '/../convertEntity.php';

class AkaneConverter
{
    private $convertEntity;

    public function __construct()
    {
        $this->convertEntity = new convertEntity();
    }

    public function convert(string $rawText): string
    {
        $aryConvertList = $this->convertEntity->aryConvertList;
        foreach ($aryConvertList as $before => $after) {
            $rawText = str_replace($before, $after, $rawText);
        }
        return $rawText;
    }

    public function addPrefix(string $sentence): string
    {
        $aryPrefix = $this->convertEntity->aryPrefixList;
        $rand = array_rand($aryPrefix);
        return $aryPrefix[$rand] . $sentence;
    }

    public function addSuffix(string $sentence): string
    {
        $arySuffix = $this->convertEntity->arySuffixList;
        $rand = array_rand($arySuffix);
        return $sentence . $arySuffix[$rand];
    }
}
