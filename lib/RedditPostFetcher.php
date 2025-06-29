<?php

class RedditPostFetcher
{
    /**
     * r/visualnovelsサブレディットから複数の投稿タイトルを取得し、指定文字数まで連結して返します。
     *
     * @param int $maxLength 連結後の最大文字数
     * @return string 連結された投稿タイトル
     * @throws \RuntimeException 投稿が見つからなかった場合やAPIエラーの場合
     */
    public function fetchSubredditTitles(int $maxLength = 150): string
    {
        $url = 'https://www.reddit.com/r/visualnovels/.json';

        // User-Agentを設定
        $options = [
            'http' => [
                'header' => "User-Agent: php:markov_akane_bot:0.1\r\n"
            ]
        ];
        $context = stream_context_create($options);

        $json = @file_get_contents($url, false, $context);
        if ($json === false) {
            throw new \RuntimeException('Failed to fetch data from Reddit API.');
        }

        $data = json_decode($json, true);

        if (!isset($data['data']['children'])) {
            throw new \RuntimeException('Invalid JSON structure from Reddit API.');
        }

        $concatenatedTitles = '';
        foreach ($data['data']['children'] as $post) {
            if (isset($post['data']['title']) && !empty(trim($post['data']['title']))) {
                $title = trim($post['data']['title']);
                $titleWithPeriod = $title . '.'; // 各タイトルの終わりに句点を追加

                if (mb_strlen($concatenatedTitles) + mb_strlen($titleWithPeriod) <= $maxLength) {
                    $concatenatedTitles .= $titleWithPeriod;
                } else {
                    // 文字数制限に達したらループを抜ける
                    break;
                }
            }
        }

        if (empty($concatenatedTitles)) {
            throw new \RuntimeException('No titles found in r/visualnovels subreddit.');
        }

        return $concatenatedTitles;
    }
}