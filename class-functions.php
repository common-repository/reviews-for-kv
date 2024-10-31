<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'KBP_functions' ) ) {

	class KBP_functions {

		# This function creates an array from the fields data for Klantenvertellen
		function getArrayFromFields( $data, $replace_char, $trim_char = null ) {

			# Set up our placeholder
			$placeholder = array();

			# Loop through our data
			foreach( $data as $key => $array ) {

				if ( empty( $array ) ) {
					# This is a main field with nothing inside
					$placeholder[] = $key;

				} else {

					# This is an array which has our fields
					# Loop through it
					foreach( $array as $index => $name ) {

						# Replace spaces in the name with the replace character
						$res = strtolower( str_replace( ' ', $replace_char, $name ) );

						# If there is a trim character, trim the name
						if ($trim_char !== null) {

							$res = rtrim($res, $trim_char);

						}

						# Add it to the placeholder
						$placeholder[] = $res;
					}
				}
			}

			# Return our result
			return $placeholder;

		}


		# This function replaces all %tag% tags with their real values
		function changeTagsToHTML( $html, $value_array, $data_array, $replace_char = '_', $trim_char = null ) {

			# Loop through our value array and check if our tags are being used
			foreach( $value_array as $key => $value ) {

				# Set up the tag we should match with
				$tmp_key = '%'. $value .'%';

				# Check if our HTML contains the key
				if ( strpos($html, $tmp_key) !== false ) {

					# Loop through our data to see if we have this value
					foreach($data_array as $key => $value) {

						# Create our key from the real key by replacing spaces with the replace char
						$key = strtolower(str_replace(' ', $replace_char, $key));

						# Check if we need to trim the key with a character
						if ($trim_char !== null) {

							$key = rtrim($key, $trim_char);

						}

						# Check if our value is an array, if so, loop through it.
						if ( is_array($value) ) {

							# Set data to loop through
							$tmp_data = $value;

							# This is an index based array, loop again.
							foreach($tmp_data as $newkey => $newvalue) {

								# Loop again
								foreach($newvalue as $index => $realvalue) {

									# Create a value that we can use
									$res = strtolower(str_replace(' ', $replace_char, $index));

									if ($trim_char !== null) {

										$res = rtrim($res, $trim_char);

									}

									# Create a tag from our result
									$res = '%'. $res .'%';

									# Check if our key matches our tag
									if ($tmp_key == $res) {

										# Run our value through a filter incase we need to change the input
										$realvalue = apply_filters( 'kbp_filter_data', $realvalue, $tmp_key );

										# Replace the string inside the HTML with our result
										$html = str_replace($tmp_key, $realvalue, $html);

										# Stop now that we've replaced this string.
										break;
									}

								}
							}

						} else {

							# Create a tag from our value
							$tmp_val = '%'. $key .'%';

							# Check if our key matches our tag
							if ($tmp_key == $tmp_val) {

								# Run our value through a filter incase we need to change the input
								$value = apply_filters( 'kbp_filter_data', $value, $tmp_key );

								# Replace the string inside the HTML with our result
								$html = str_replace($tmp_key, $value, $html);

								# Stop now that we've replaced this string.
								break;
							}
						}
					}
				}
			}

			return $html;
		}


		# This function fetches the XML data from the database
		# It also refreshes the data if it needs to be refreshed
		function getXmlData() {

			# Get our data from the database
			$data = get_option( 'kbp_xml_data' );

			# Check if our data is not corrupted
			try {

				# If the serialised data corrupted it's still a string and not an array.
				if (!is_array( $data )) {

					# Try to unserialize
					$data = unserialize( $data );

				}

			} catch (Exception $e) {

				# We got an error when we tried to unserialize.
				# Delete the option and let the system refresh the data.
				delete_option( 'kbp_xml_data' );
				$data = false;

			}

			# Get our fields from the database
			$current_fields = get_option('kbp_fields');

			# We should never end up here due to checks prior to this function
			if ( $current_fields === false ) {

				return -1;

			}

			# Check if we don't have old data or if our last refresh was longer than 2 hours ago we refresh it.
			if ( isset( $data ) === false || ( isset( $data['timestamp'] ) && ( time() - $data['timestamp'] ) > ( (60 * 60) * 2 ) ) ) {

				# Fetch new data
				$result = $GLOBALS['KBP_fetchXML']->get_fields($current_fields['url'], false);

				# Parse the data
				$result = $this->parseXmlData($result);

				# Overwrite the XML from the old data with the new data
				$data['xml'] = $result;
				# Set a new timestamp
				$data['timestamp'] = time();

				# Update our data in the database with the new data
				update_option('kbp_xml_data', $data);

			}

			# Return our data
			return $data['xml'];

		}


		# Turn the XML object into an array we can use
		function parseXmlData($result) {

			$result_types = array();
			foreach ($result->children() as $child) {
				if ($child->count()) {
			    	$result_types[$child->getName()] = array();
				} else {
					$result_types[$child->getName()] = $child->__toString();
				}

	    		$count = 0;
			    foreach ($child->children() as $grandchild) {

			    	if ($grandchild->count()) {
			    		$result_types[$child->getName()][$grandchild->getName()][$count] = array();


				    	foreach ($grandchild->children() as $greatgrandchild) {
				    		$attributes = $greatgrandchild->attributes();

							if(empty($attributes)) {
				    			$result_types[$child->getName()][$grandchild->getName()][$count][$greatgrandchild->getName()] = (string)$greatgrandchild;
							} else {
				    			$result_types[$child->getName()][$grandchild->getName()][$count][$greatgrandchild->attributes()->__toString()] = $greatgrandchild->__toString();
							}
				    	}

						# Increase the count
				    	$count++;

					} else {
						$result_types[$child->getName()][$grandchild->getName()] = $grandchild->__toString();
					}

			    }
			}

			return $result_types;
		}

	}
}


if ( !function_exists( 'dd' ) ) {

	/**
	 * Debug function
	 */
	function dd() {
		array_map(function($x) {
			echo '<pre>';
			echo print_r( $x, true );
			echo '</pre>';
		}, func_get_args() ); die;
	}

}


if(!function_exists('starts_with_upper') && !function_exists('translate_month')) {


	/**
	 * Translates an English month name to a Dutch month
	 *
	 * @param String $str The date
	 *
	 * @return String
	 */
	function translate_month($str) {

		if(!starts_with_upper($str)) {

			$english = array('january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december');
			$dutch = array('januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december');

		} else {

			$english = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
			$dutch = array('Januari', 'Februari', 'Maart', 'April', 'Mei', 'Juni', 'Juli', 'Augustus', 'September', 'Oktober', 'November', 'December');

		}

		return str_ireplace($english, $dutch, $str);

	}

	/**
	 * Checks if given string starts with an Uppercase
	 *
	 * @param String $str The string to check
	 *
	 * @return Bool
	 */
	function starts_with_upper($str) {
	    $chr = mb_substr ($str, 0, 1, "UTF-8");
	    return mb_strtolower($chr, "UTF-8") != $chr;
	}
}