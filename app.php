<?php

require 'vendor/autoload.php';

$session = new SpotifyWebAPI\Session(
    '',
    ''
);

$session->requestCredentialsToken();
$accessToken = $session->getAccessToken();

$api = new SpotifyWebAPI\SpotifyWebAPI();
$api->setAccessToken($accessToken);

require 'albumData.php';

$newData = [];

foreach ( $data as $albumData ) {

    // Build the Search Query
    $searchQuery = sprintf(
        'artist:%s album:%s',
        str_replace( '\'', '', $albumData['artist'] ),
        str_replace( '\'', '', $albumData['album'] ) );

    // Execute the search
    $albumSearch = $api->search( $searchQuery, 'album' );
    $album = $albumSearch->albums->items[0];


    // Setup some default data.
    $albumData['searchParam'] = $searchQuery;
    $albumData['artist']      = $album->artists[0]->name;
    $albumData['album']       = $album->name;
    $albumData['@url']        = $album->images[0]->url;

    $filename = preg_replace("/[^a-z0-9\.]/", "", strtolower( $albumData['album'] ) );
    $albumData['@art'] = sprintf( 'tmp/%s.jpg', $filename );

    // Save the album art
    getAlbumArt( $album->images[0]->url, sprintf( '%s.jpg', $filename ) );

    $albumData['primaryColor'] = getImageColor( $albumData['@art'] )[0];
    $albumData['secondaryColor'] = getImageColor( $albumData['@art'] )[1];

    error_log( $albumData['@art'] );
    error_log( $albumData['primaryColor'] );

    // Let's get the count of the cards.
    $count = $albumData['count'];

    // The data is going to be the same here, don't need to dulicate all of the functions.
    for ( $i=0; $i < $count; $i++ ) {

        // Append to the new array.
        $newData[] = $albumData;
    }
}

// Start the CSV Output
$output = fopen("php://output",'w') or die("Can't open php://output");
header("Content-Type:application/csv");
header("Content-Disposition:attachment;filename=albumData.csv");

// Output the header.
$headers = array('artist', 'album', 'number', 'decade', 'count', 'primaryColor', 'secondaryColor', '@art', 'searchParam', '@url' );
fputcsv( $output, $headers );

foreach($newData as $product) {
    fputcsv($output, $product);
}

// We done here...
fclose( $output ) or die( "Can't close php://output" );


/**
 *
*/
function getAlbumArt( $url = '', $name = '' ) {
    $img = sprintf( '%s/tmp/%s', getcwd(), $name );
    file_put_contents( $img, file_get_contents( $url ) );
}


function getImageColor( $url ) {
    // initiate with image
    $palette = new \BrianMcdo\ImagePalette\ImagePalette( $url );
    return $palette->colors;
}
