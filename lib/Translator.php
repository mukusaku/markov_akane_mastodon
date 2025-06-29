<?php

class Translator
{
    private const SOURCE_LANG = 'en';
    private const TARGET_LANG = 'ja';

    /**
     * 英語のテキストを日本語に翻訳します。
     *
     * @param string $text 翻訳するテキスト
     * @return string 翻訳されたテキスト
     * @throws \RuntimeException 翻訳に失敗した場合
     */
    public function translate(string $text): string
    {
        // 注意: この実装はAPIキー不要の簡易的なサービスを利用しています。
        // 本番環境で利用する場合、より安定した翻訳API（DeepL, Google Translate等）の
        // 契約と、SDKまたはライブラリの利用を強く推奨します。

        $url = sprintf(
            'https://api.mymemory.translated.net/get?q=%s&langpair=%s|%s',
            urlencode($text),
            self::SOURCE_LANG,
            self::TARGET_LANG
        );

        // User-Agentを設定
        $options = [
            'http' => [
                'header' => "User-Agent: php:markov_akane_bot:0.1\r\n"
            ]
        ];
        $context = stream_context_create($options);

        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            throw new \RuntimeException('Failed to fetch data from translation API.');
        }

        $data = json_decode($response, true);

        // レスポンスコードが200以外はエラー
        if (!isset($data['responseStatus']) || $data['responseStatus'] !== 200) {
            throw new \RuntimeException('Translation API returned an error. Response: ' . $response);
        }

        if (isset($data['responseData']['translatedText']) && !empty($data['responseData']['translatedText'])) {
            return $data['responseData']['translatedText'];
        }

        // その他の理由で翻訳に失敗した場合
        throw new \RuntimeException('Failed to translate text. Response: ' . $response);
    }
}
