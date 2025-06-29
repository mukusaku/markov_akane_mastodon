<?php
require_once __DIR__ . '/../mastodon/postActions/PostTootApi.php';
use postActions\PostTootApi;

class TootClient
{
    public function toot(string $sentence): void
    {
        $request = new PostTootApi();
        $request->toot($sentence);
    }
}
