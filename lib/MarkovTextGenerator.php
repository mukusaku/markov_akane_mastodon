<?php
use YuzuruS\Mecab\Markovchain;

class MarkovTextGenerator
{
    public function generate(string $rawText): ?string
    {
        $mc = new Markovchain();
        for ($i = 0; $i <= 100; $i++) {
            $markovText = $mc->makeMarkovText($rawText);
            $markovText = substr($markovText, 0, strpos($markovText, '。'));
            if (mb_strlen($markovText) > 0 && mb_strlen($markovText) <= 50) {
                return $markovText;
            }
        }
        return null;
    }
}
