<?php
use \Smalot\PdfParser\Parser;
use NlpTools\Tokenizers\WhitespaceTokenizer;
use NlpTools\Similarity\CosineSimilarity;

$this->title = "Pdf GPT";

$parser = new Parser();

$pdf = $parser->parseFile($path);

$text = $pdf->getText();
echo $text;

// Tokenize the paragraph
$tokenizer = new WhitespaceTokenizer();
$tokens = $tokenizer->tokenize($text);

// Calculate the similarity between tokens (you may need to fine-tune this)
$similarity = new CosineSimilarity();

// Get the summary by selecting the most relevant tokens
$summarySize = 5; // Number of tokens in the summary
$summaryTokens = array_slice($tokens, 0, $summarySize);

// Output the summary
$summary = implode(' ', $summaryTokens);
echo "Summary: $summary\n";
?>