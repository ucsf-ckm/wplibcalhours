<?php
$first_day = $days[array_key_first($days)]['text']['status'];
$open_class = ($first_day == 'open') ? 'circle-green' : 'circle-red';
?>

<div class="hours-display">'
    <div class="open-indicator">
        <div class="circle <?php echo $open_class ?>"></div>
        <div>
            <span class="open-text"><?php echo $first_day ?></span>
            <?php echo $this->openUntil($days[array_key_first($days)]); ?>
        </div>
    </div>
    <ul class="hours-list-view">
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