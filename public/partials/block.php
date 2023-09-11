<?php
$o = '<ul class="hours-list-view">';
for ( $i = 0, $n = count( $days ); $i < $n; $i ++ ) {
	$day = $days[ $i ];

	/* @var \DateTime $date */
	$date = $day['date'];
	$o .= '<li' . ( $day['is_today'] ? ' class="today" ' : '' ) . '>';
	$o .= '<span class="hours-day">' . $date->format( 'l' ) . '</span> ' . $day['text'];
	$o .= '</li>';
}

$o .= '</ul>';