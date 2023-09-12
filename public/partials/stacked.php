<?php
$first_day = $days[array_key_first($days)]['text']['status'];
$is_open = $first_day == 'pen';
$open_class = ($is_open) ? 'circle-green' : 'circle-red';
?>

<div class="hours-display-block">
	<h2><?php echo $location ?> Hours</h2>
	<div class="open-indicator">
		<div class="circle <?php echo $open_class ?>"></div>
		<div>
			<span class="open-text"><?php echo $first_day ?></span>
			<?php if ($is_open):
				echo $this->openUntil($days[array_key_first($days)]);
			endif ?>
		</div>
	</div>
	<ul class="hours-list-view hours-list-view-block">
		<?php for ($i = 0, $n = count($days); $i < $n; $i ++):
			$day = $days[$i];
			$today = $day['is_today'];
			$date = $day['date'];
			$day_text = ($today) ? 'Today' : $date->format( 'D' ); ?>

			<li <?php echo ($today ? ' class="today" ' : '' ) ?>>
				<div class="hours-day"><?php echo $day_text ?></div>
				<div class="hours-text"><?php echo $this->hoursText($day) ?></div>
			</li>
		<?php endfor ?>
	</ul>
</div>