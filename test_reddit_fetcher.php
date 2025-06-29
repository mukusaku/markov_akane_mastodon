<?php

require_once __DIR__ . '/lib/RedditPostFetcher.php';

echo "RedditPostFetcher のテストを開始します。\n\n";

$fetcher = new RedditPostFetcher();

try {
    // 150文字制限でタイトルを取得
    $text = $fetcher->fetchSubredditTitles(150);

    echo "テスト成功！\n";
    echo "取得したテキスト（" . mb_strlen($text) . "文字）:\n";
    echo "----------------------------------------\n";
    echo $text;
    echo "\n----------------------------------------\n";

} catch (RuntimeException $e) {
    echo "テスト中にエラーが発生しました。\n";
    echo "エラーメッセージ: " . $e->getMessage() . "\n";
}