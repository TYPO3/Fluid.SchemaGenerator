<?php
namespace TYPO3\FluidSchemaGenerator;

/*
 * This file belongs to the package "TYPO3 FluidSchemaGenerator".
 * See LICENSE.txt that was shipped with this package.
 */

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
     * @return void
     */
    public function parseDocComment($docComment)
    {
        $parsedDocComment = '';
        $lines = explode(chr(10), $docComment);
        foreach ($lines as $line) {
            if ($this->isDocCommentTag($line)) {
                continue;
            }
            $parsedDocComment .= preg_replace('/\\s*\\/?[\\\\*]*(.*)$/', '$1', $line) . chr(10);
        }
        $parsedDocComment = trim($parsedDocComment);
        return $parsedDocComment;
    }

    private function isDocCommentTag(string $line)
    {
        return preg_match('/^\s*\*?\s*@/', $line) === 1;
    }
}
