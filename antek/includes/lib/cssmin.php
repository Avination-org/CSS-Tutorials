<?php

class CSSMin {

	public static function compressCSS( $code ) {
		$patterns     = [];
		$replacements = [];

		/* remove multiline comments */
		$patterns[]     = '/\/\*.*?\*\//s';
		$replacements[] = '';

		/* remove tabs, spaces, newlines, etc. */
		$patterns[]     = '/\r\n|\r|\n|\t|\s\s+/';
		$replacements[] = '';

		/* remove whitespace on both sides of colons :*/
		$patterns[]     = '/\s?\:\s?/';
		$replacements[] = ':';

		/* remove whitespace on both sides of curly brackets {} */
		$patterns[]     = '/\s?\{\s?/';
		$replacements[] = '{';
		$patterns[]     = '/\s?\}\s?/';
		$replacements[] = '}';

		/* remove whitespace on both sides of commas , */
		$patterns[]     = '/\s?\,\s?/';
		$replacements[] = ',';

		/* compress */

		return preg_replace( $patterns, $replacements, $code );
	}
}
