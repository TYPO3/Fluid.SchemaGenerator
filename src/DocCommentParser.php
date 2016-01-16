<?php
namespace TYPO3Fluid\SchemaGenerator;

/**
 * A little parser which creates tag objects from doc comments
 */
class DocCommentParser {

	/**
	 * @var string The description as found in the doc comment
	 */
	protected $description = '';

	/**
	 * @var array An array of tag names and their values (multiple values are possible)
	 */
	protected $tags = array();

	/**
	 * Parses the given doc comment and saves the result (description and
	 * tags) in the parser's object. They can be retrieved by the
	 * getTags() getTagValues() and getDescription() methods.
	 *
	 * @param string $docComment A doc comment as returned by the reflection getDocComment() method
	 * @return void
	 */
	public function parseDocComment($docComment) {
		$this->description = '';
		$this->tags = array();
		$lines = explode(chr(10), $docComment);
		foreach ($lines as $line) {
			if (strlen($line) > 0 && strpos($line, '@') !== FALSE) {
				$this->parseTag(substr($line, strpos($line, '@')));
			} elseif (count($this->tags) === 0) {
				$this->description .= preg_replace('/\\s*\\/?[\\\\*]*(.*)$/', '$1', $line) . chr(10);
			}
		}
		$this->description = trim($this->description);
	}

	/**
	 * Returns the description which has been previously parsed
	 *
	 * @return string The description which has been parsed
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Parses a line of a doc comment for a tag and its value.
	 * The result is stored in the interal tags array.
	 *
	 * @param string $line A line of a doc comment which starts with an @-sign
	 * @return void
	 */
	protected function parseTag($line) {
		$tagAndValue = preg_split('/\\s/', $line, 2);
		$tag = substr($tagAndValue[0], 1);
		if (count($tagAndValue) > 1) {
			$this->tags[$tag][] = trim($tagAndValue[1]);
		} else {
			$this->tags[$tag] = array();
		}
	}

}
