<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */

namespace fg\Essence\Dom;

use fg\Essence\Dom;
use fg\Essence\Exception;
use fg\Essence\Utility\Hash;



/**
 *	Handles HTML related operations through DomDocument.
 *
 *	@package fg.Essence.Dom
 */

class Native implements Dom {

	/**
	 *	{@inheritDoc}
	 */

	public function extractAttributes( $html, array $options ) {

		$reporting = error_reporting( 0 );
		$Document = \DomDocument::loadHTML( $html );
		error_reporting( $reporting );

		if ( $Document === false ) {
			throw new Exception( 'Unable to load HTML document.' );
		}

		$options = Hash::normalize( $options, array( ));
		$data = array( );

		foreach ( $options as $name => $required ) {
			$tags = $Document->getElementsByTagName( $name );
			$required = Hash::normalize( $required, '' );
			$data[ $name ] = array( );

			foreach ( $tags as $Tag ) {
				if ( $Tag->hasAttributes( )) {
					$attributes = $this->_extractAttributesFromTag( $Tag, $required );

					if ( !empty( $attributes )) {
						$data[ $name ][ ] = $attributes;
					}
				}
			}
		}

		return $data;
	}



	/**
	 *	Extracts attributes from the given tag.
	 *
	 *	@param \DOMNode $Tag Tag to extract attributes from.
	 *	@param array $required Required attributes.
	 *	@return array Extracted attributes.
	 */

	protected function _extractAttributesFromTag( \DOMNode $Tag, array $required ) {

		$attributes = array( );

		foreach ( $Tag->attributes as $name => $Attribute ) {
			if ( !empty( $required )) {
				if ( isset( $required[ $name ])) {
					$pattern = $required[ $name ];

					if ( $pattern && !preg_match( $pattern, $Attribute->value )) {
						return array( );
					}
				} else {
					continue;
				}
			}

			$attributes[ $name ] = $Attribute->value;
		}

		$diff = array_diff_key( $required, $attributes );

		return empty( $diff )
			? $attributes
			: array( );
	}
}
