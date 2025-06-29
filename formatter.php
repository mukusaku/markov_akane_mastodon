<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/mastodon/postActions/PostTootApi.php';
require 'convertEntity.php';
require 'originalList.php';
require __DIR__ . '/lib/RedditPostFetcher.php';
require __DIR__ . '/lib/Translator.php';
use YuzuruS\Mecab\Markovchain;

$formatter = new formatter();
$formatter->execToot();
class formatter {
    function execToot(){
        try {
            // マルコフ連鎖の元となるテキストを生成する
            $rawText = $this->generateText();
            //print_r($rawText, false);
            $convertedText = $this->convertToAko($rawText);
            //print_r($convertedText, false);
            $markovText = $this->convertToMarkov($convertedText);
            //print_r($markovText, false);
            if(isset($markovText)) {
                $this->toot($markovText);
                return;
            } else {
                return;
            }
        } catch (\RuntimeException $e) {
            // データ取得や翻訳でエラーが発生した場合は、ログ出力して何もせずに終了する
            error_log($e->getMessage());
            return;
        }
    }
    
    // Redditの投稿本文を翻訳してマルコフ連鎖の元テキストを生成する
    function generateText(): string
    {
        $redditFetcher = new RedditPostFetcher();
        $translator = new Translator();

        // Redditから連結された投稿タイトルを取得
        $postText = $redditFetcher->fetchSubredditTitles(150);

        // 取得した本文を翻訳
        $translatedText = $translator->translate($postText);

        return $translatedText;
    }
    // 変換リストに沿った文章の加工を行う
    function convertToAko($rawText) {
        $sentence = "";
        $convertEntity = new convertEntity();
        // 変換対象の用語リストを配列で取得
        $aryConvertList = $convertEntity->aryConvertList;
        foreach($aryConvertList as $sBefore => $sAfter) {
            $rawText = str_replace($sBefore, $sAfter, $rawText);
        }
        $sentence = $rawText;
        return $sentence;
    }
    // マルコフ連鎖を利用した変換を行う
    function convertToMarkov($rawText) {
        //return $rawText; // この行を有効化するとマルコフ連鎖をオフ
        $mc = new Markovchain();
        for($i=0; $i<=100; $i++){
            $markovText = $mc->makeMarkovText($rawText);
            // 最初に句点が出るところまで切り出す
            $markovText = substr($markovText,0,strpos($markovText, '。'));
            // 1文字以上50文字以下の文章が生成できた場合はループを抜ける
            if(mb_strlen($markovText) > 0 && mb_strlen($markovText) <= 50) {
                return $markovText;
            }
        }
    }
    // 接頭辞の追加
    function addPrefix($sentence) {
        $convertEntity = new convertEntity();
        $aryPrefix = $convertEntity->aryPrefixList;
        $rand = array_rand($aryPrefix);
        return $aryPrefix[$rand] . $sentence;
    }
    // 接尾辞の追加
    function addSuffix($sentence) {
        $convertEntity = new convertEntity();
        $arySuffix = $convertEntity->arySuffixList;
        $rand = array_rand($arySuffix);
        return $sentence . $arySuffix[$rand];
    }
    // 実際のトゥート処理
    function toot($sentence, $bAdding = true) {
        // 接頭辞、接尾辞の追加
        if($bAdding) {
            $sentence = $this->addPrefix($sentence);                    
            $sentence = $this->addSuffix($sentence);
        }
        // トゥートAPIを叩く
        $request = new postActions\PostTootApi();
        $request->toot($sentence);
    }
}
