<?php
/**
 * A collection of multi-use functions
 */

/**
 * Get the genres
 * returns a formatted list of genres (comma-separated by default) with links to each
 *
 * @since 	2.0.0
 *
 * @param 	$before 	string 		string to display before the genre
 * @param 	$after 		string		string to display after the genre (comma by default)
 * @param 	$forced 	boolean 	by default, if the item is the last in the list, the $after variable doesn't render. If $forced is set to TRUE it 									 will bypass this and render it anyway (e.g. if passing $before = '<li>' / $after = '</li>')
 * @return 	$genre_list				sanitized string of the results
 */
if ( !function_exists( 'get_the_genres' ) ) {
	function get_the_genres($before = null, $after = ', ', $forced = false) {
		global $post;
		$genres = get_the_terms( $post->ID, 'genre' );
		$genre_list = null;
		if ( $genres && !is_wp_error( $genres ) ) {
			$genre_out = array();
			foreach ( $genres as $genre ) {
				$genre_out[] = sprintf( '<a href="%s">%s</a>',
					home_url() . '/?genre=' . $genre->slug,
					$genre->name);
			}
			$count = 0;
			foreach ( $genre_out as $out ) {
				$genre_list .= $before . $out;
				$count++;
				if ( ( count($genre_out) > 1 ) && ( $after == ', ' ) && ( count($genre_out) != $count ) || $forced ) {
					$genre_list .= $after;
				}
			}
		}
		if ( $genre_list )
			return wp_kses_post($genre_list);
	}
}

if ( !function_exists( 'get_the_artist_list' ) ) {
	function get_the_artist_list( $before = null, $after = ', ', $forced = false ) {
		global $post;
		// get the artist(s)
		$artists = get_the_terms( $post->ID, 'artist' );
		$artist_list = null;
		if ( $artists && !is_wp_error( $artists ) ) {
			$artists_out = array();
			foreach ( $artists as $artist ) {
				$artists_out[] = $artist->name;
			}
			$count = 0;
			foreach ( $artists_out as $out ) {
				$artist_list .= $out;
				$count++;
				if ( count($artists_out) > 1 ) {
					$artist_list .= ', ';
				}
			}
			$artist_list = wp_kses_post( $artist_list );
		}
		return $artist_list;
	}
}

if ( !function_exists( 'get_the_labels' ) ) {
	function get_the_labels($before = null, $after = ', ', $forced = false) {
		global $post;
		$labels = get_the_terms( $post->ID, 'label' );
		$label_list = null;
		if ( $labels && !is_wp_error( $labels ) ) {
			$label_out = array();
			foreach ( $labels as $label ) {
				$label_out[] = sprintf( '<a href="%s">%s</a>',
					home_url() . '/?label=' . $label->slug,
					$label->name);
			}
			$count = 0;
			foreach ( $label_out as $out ) {
				$label_list .= $before . $out;
				$count++;
				if ( ( count($label_out) > 1 ) && ( $after == ', ' ) && ( count($label_out) != $count ) || $forced ) {
					$label_list .= $after;
				}
			}
		}
		if ( $label_list )
			return wp_kses_post($label_list);
	}
}