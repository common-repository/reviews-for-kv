<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'KBP_fetchXML' ) ) {

	# This class contains the export part of the AAP plugin
	class KBP_fetchXML {

		# Let's fire up the class
		function __construct() {
			# Add action for our hook

		}

        function get_fields($url, $noCDATA) {

            # Start Curl
            $ch = curl_init();

            # Set the url
            curl_setopt($ch, CURLOPT_URL, $url);

            # Set the timeout
            curl_setopt($ch, CURLOPT_TIMEOUT, 60); //timeout after 60 seconds

            # Returns raw data
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            # Check for auth
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);

            # Execute the curl request, and return the result
            $result = curl_exec($ch);

            # Close curl connection
            curl_close($ch);

            # Return the XML as array
            $result = $this->xml_to_array($result, $noCDATA);

            return $result;
        }

        function xml_to_array($data, $noCDATA) {

            if ( $noCDATA ) {

                $result = simplexml_load_string( $data, 'SimpleXMLElement', LIBXML_NOCDATA );

            } else {

                $result = simplexml_load_string( $data );

            }

            return $result;

        }

    }

}