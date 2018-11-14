<?php
/**
 * Spotify App
 *
 * @category Spotify
 * @package  Kid-A
 * @author   Jake Spurlock <jspurloc@usc.edu>
 * @license  MIT https://opensource.org/licenses/MIT
 * @version  GIT: $Id$
 * @link     https://github.com/whyisjake/Kid-A-Data
 */

require 'vendor/autoload.php';
require 'albumData.php';

$session = new SpotifyWebAPI\Session(
    '',
    ''
);

$session->requestCredentialsToken();
$access_token = $session->getAccessToken();

$api = new SpotifyWebAPI\SpotifyWebAPI();
$api->setAccessToken( $access_token );

$new_data = [];

foreach ( $data as $album_data ) {

	// Build the search query.
	$search_query = sprintf(
		'artist:%s album:%s',
		str_replace( '\'', '', $album_data['artist'] ),
		str_replace( '\'', '', $album_data['album'] )
	);

	// Execute the search.
	$album_search = $api->search( $search_query, 'album' );
	$album        = $album_search->albums->items[0];


	// Setup some default data.
	$album_data['searchParam'] = $search_query;
	$album_data['artist']      = $album->artists[0]->name;
	$album_data['album']       = $album->name;
	$album_data['@url']        = $album->images[0]->url;

	$filename           = preg_replace( '/[^a-z0-9\.]/', '', strtolower( $album_data['album'] ) );
	$album_data['@art'] = sprintf( 'tmp/%s.jpg', $filename );

	// Save the album art.
	get_album_art( $album->images[0]->url, sprintf( '%s.jpg', $filename ) );

	$album_data['primaryColor']   = get_image_color( $album_data['@art'] )[0];
	$album_data['secondaryColor'] = get_image_color( $album_data['@art'] )[1];

	// Let's get the count of the cards.
	$count = $album_data['count'];

	// The data is going to be the same here, don't need to dulicate all of the functions.
	for ( $i = 0; $i < $count; $i++ ) {
		// Append to the new array.
		$new_data[] = $album_data;
	}
}

// Start the csv output.
$output = fopen( 'php://output', 'w' ) || die( "Can't open php://output" );
header( 'Content-Type:application/csv' );
header( 'Content-Disposition:attachment;filename=albumData.csv' );

// Output the header.
$headers = array( 'artist', 'album', 'number', 'decade', 'count', 'primaryColor', 'secondaryColor', '@art', 'searchParam', '@url' );
fputcsv( $output, $headers );

foreach ( $new_data as $product ) {
	fputcsv( $output, $product );
}

// We done here...
fclose( $output ) || die( "Can't close php://output" );


/**
 * Save the Album Art
 *
 * @param string $url  Remote URL to save.
 * @param string $name File name.
 *
 * @return void
 */
function get_album_art( $url = '', $name = '' ) {
	$img = sprintf( '%s/tmp/%s', getcwd(), $name );
	file_put_contents( $img, file_get_contents( $url ) );
}

/**
 * Get the color palette for an image.
 *
 * @param string $url Local image path.
 *
 * @return array
 */
function get_image_color( $url = '' ) {
	// Initiate with image.
	$palette = new \BrianMcdo\ImagePalette\ImagePalette( $url );
	return $palette->colors;
}
