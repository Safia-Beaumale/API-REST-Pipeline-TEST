<?php

declare(strict_types=1);

if ($argc < 3) {
    fwrite(STDERR, "Usage: php tools/check-coverage-threshold.php <clover-file> <min-percent>\n");
    exit(1);
}

$cloverFile = $argv[1];
$minPercent = (float) $argv[2];

if (!is_file($cloverFile)) {
    fwrite(STDERR, "Coverage file not found: {$cloverFile}\n");
    exit(1);
}

$xml = simplexml_load_file($cloverFile);
if ($xml === false || !isset($xml->project->metrics)) {
    fwrite(STDERR, "Invalid Clover XML format: {$cloverFile}\n");
    exit(1);
}

$metrics = $xml->project->metrics;
$statements = (int) ($metrics['statements'] ?? 0);
$coveredStatements = (int) ($metrics['coveredstatements'] ?? 0);

if ($statements === 0) {
    fwrite(STDERR, "No statements found in coverage report.\n");
    exit(1);
}

$coverage = round(($coveredStatements / $statements) * 100, 2);
echo "Line coverage: {$coverage}% (minimum required: {$minPercent}%)\n";

if ($coverage < $minPercent) {
    fwrite(STDERR, "Coverage threshold not met.\n");
    exit(1);
}

echo "Coverage threshold met.\n";
exit(0);
