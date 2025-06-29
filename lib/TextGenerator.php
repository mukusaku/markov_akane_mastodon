<?php
require_once __DIR__ . '/RedditPostFetcher.php';
require_once __DIR__ . '/Translator.php';

class TextGenerator
{
    public function generate(): string
    {
        $redditFetcher = new RedditPostFetcher();
        $translator = new Translator();

        $postText = $redditFetcher->fetchSubredditTitles(150);
        return $translator->translate($postText);
    }
}
