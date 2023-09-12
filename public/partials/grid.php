<?php
$first_day = $days[array_key_first($days)]['text']['status'];
$open_class = ($first_day == 'open') ? 'circle-green' : 'circle-red';
$stacked = $attrs['display_type'] == 'stacked';
$hours_display = ($stacked) ? '-stacked' : '';
$hours_list_view_block = ($stacked) ? 'hours-list-view-stacked' : '';
?>

<div class="hours-display<?php echo $hours_display ?>">
    <?php if ($stacked): ?>
        <h2><?php echo $location ?> Hours</h2>
    <?php endif ?>
    <div class="open-indicator">
        <div class="circle <?php echo $open_class ?>"></div>
        <div>
            <span class="open-text"><?php echo $first_day ?></span>
            <?php if ($first_day == 'open'):
                echo $this->openUntil($days[array_key_first($days)]);
            endif ?>
        </div>
    </div>
    <ul class="hours-list-view <?php echo $hours_list_view_block ?>">
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