<?php

require 'vendor/autoload.php';

// Fetch the saved access token from somewhere. A database for example.

$accessToken = 'BQCHl37SxTaDqS8Je7sgdAT2vuVOpKz_PJt2WChSfbhSIlQzBWDuPgjcNTSxyWbeXGr7O7GI33ktQ2jsqho';
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

    // printf( 'Search Params: %s - %s<br>', $albumData['artist'], $albumData['album'] );
    // printf( 'Artist Name: %s<br>', $album->artists[0]->name);
    // printf( 'Album Name: %s<br>', $album->name);
    // printf( 'Release Date: %s<br>', $album->release_date );
    // printf( 'Album Art URL: %s<br>', $album->images[0]->url );

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

$output = fopen("php://output",'w') or die("Can't open php://output");
header("Content-Type:application/csv");
header("Content-Disposition:attachment;filename=albumData.csv");

$headers = array('artist', 'album', 'number', 'decade', 'count', 'primaryColor', 'secondaryColor', '@art', 'searchParam', '@url' );
fputcsv( $output, $headers );

foreach($newData as $product) {
    fputcsv($output, $product);
}
fclose($output) or die("Can't close php://output");


function getAlbum() {

}

/**
 *
*/
function getAlbumArt( $url = '', $name = '' ) {
    $img = sprintf( '%s/tmp/%s', getcwd(), $name );
    file_put_contents( $img, file_get_contents( $url ) );
}

function getArtistInfo() {

}

function getArtistArt() {

}

function saveImage() {

}


function getImageColor( $url ) {
    // initiate with image
    $palette = new \BrianMcdo\ImagePalette\ImagePalette( $url );
    return $palette->colors;
}

function getPrimaryColor() {

}

function getSecondaryColor() {

}
