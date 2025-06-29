<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/lib/TextGenerator.php';
require __DIR__ . '/lib/AkaneConverter.php';
require __DIR__ . '/lib/MarkovTextGenerator.php';
require __DIR__ . '/lib/TootClient.php';

$generator = new TextGenerator();
$converter = new AkaneConverter();
$markov = new MarkovTextGenerator();
$tootClient = new TootClient();

try {
    $rawText = $generator->generate();
    $converted = $converter->convert($rawText);
    $markovText = $markov->generate($converted);

    if ($markovText) {
        $sentence = $converter->addPrefix($markovText);
        $sentence = $converter->addSuffix($sentence);
        $tootClient->toot($sentence);
    }
} catch (\RuntimeException $e) {
    error_log($e->getMessage());
}
