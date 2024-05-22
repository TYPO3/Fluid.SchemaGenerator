<?php

declare(strict_types=1);

/*
 * This file belongs to the package "TYPO3 FluidSchemaGenerator".
 * See LICENSE.txt that was shipped with this package.
 */

namespace TYPO3\FluidSchemaGenerator;

/**
 * A little parser which creates tag objects from doc comments
 */
class DocCommentParser
{
    /**
     * Parses the given doc comment and saves the result (description and
     * tags) in the parser's object. They can be retrieved by the
     * getTags() getTagValues() and getDescription() methods.
     *
     * @param string $docComment A doc comment as returned by the reflection getDocComment() method
     * @return string The parsed doc comment.
     */
    public function parseDocComment(string $docComment): string
    {
        $parsedDocComment = '';
        $deprecatedTag = '';
        $lines = explode(chr(10), $docComment);
        foreach ($lines as $line) {
            if ($this->isDocCommentTag($line)) {
                // Check if the line contains @deprecated, handle it
                if (stripos($line, '@deprecated') !== false) {
                    $line = str_replace('@deprecated', '**Deprecated**', $line);
                    $line = $this->parsedDocCommentLine($line);
                    $deprecatedTag = '..  attention::' . chr(10) . '    ' . $line;
                }
                continue;
            }
            $parsedDocComment .= $this->parsedDocCommentLine($line);
        }
        $parsedDocComment = trim($parsedDocComment, " \n\r\t\v\x00\/") . chr(10);

        // Append the deprecated tag if present
        if ($deprecatedTag) {
            $parsedDocComment .= chr(10) . $deprecatedTag;
        }

        // TODO: Check whether the forward slash at the end is necessary.
        $parsedDocComment .= '/';

        return $parsedDocComment;
    }

    private function isDocCommentTag(string $line): bool
    {
        return preg_match('/^\s*\*?\s*@/', $line) === 1;
    }

    private function parsedDocCommentLine(string $line): string
    {
        return preg_replace('/\\s*\\/?[\\\\*]*(.*)$/', '$1', $line) . chr(10);
    }
}
