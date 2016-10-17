<?php
namespace TYPO3Fluid\SchemaGenerator;

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
            if (strlen($line) > 0 && strpos($line, '@') !== false) {
                continue;
            } else {
                $parsedDocComment .= preg_replace('/\\s*\\/?[\\\\*]*(.*)$/', '$1', $line) . chr(10);
            }
        }
        $parsedDocComment = trim($parsedDocComment);
        return $parsedDocComment;
    }
}
