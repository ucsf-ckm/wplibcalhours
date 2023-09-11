<?php
$o = '<table class="wplibcalhours">';
$o .= '<thead><tr><th colspan="3">' . __( 'Hours', 'wplibcalhours' ) . '</th></tr></thead>';
$o .= '<tbody>';
for ( $i = 0, $n = count( $days ); $i < $n; $i ++ ) {
	$day = $days[ $i ];
	if ( $i && ! ( $i % 7 ) ) {
		$o .= '</tbody><tbody class="hidden">';
	}
	/* @var \DateTime $date */
	$date = $day['date'];
	$o    .= '<tr' . ( $day['is_today'] ? ' class="today" ' : '' ) . '><td>' . $date->format( 'l' ) . '</td>';
	$o    .= '<td>' . $date->format( 'M j' ) . '</td>';
	$o    .= '<td>' . $day['text'] . '</td></tr>';
}
$o .= '</tbody>';

if ( 1 < $num_weeks ) {
	$o .= '<tfoot><tr><td colspan="3">';
	$o .= '<a class="prev hidden">&laquo; ' . __( 'previous', 'wplibcalhours' ) . '</a>';
	$o .= '<a class="next">' . __( 'next', 'wplibcalhours' ) . ' &raquo;</a>';
	$o .= '</td></tr></tfoot>';
}
$o .= '</table>';