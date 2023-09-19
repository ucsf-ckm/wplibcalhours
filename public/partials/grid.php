<?php
$first_day = $days[array_key_first($days)]['text']['status'];
$open_class = ($first_day == 'open') ? 'circle-green' : 'circle-red';
$stacked = $attrs['display_type'] == 'stacked';
$hours_display = ($stacked) ? '-stacked' : '';
$base_location = $this->getBaseLocation($location);
?>
    <div id="<?php echo $base_location ?>-hours" class="hours-display<?php echo $hours_display ?>">
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
        <?php $chunked_weeks = $this->weekBlocks($days); ?>

        <?php foreach ($chunked_weeks as $week_number => $chunked_week): ?>
            <?php if ($week_number == 1): // Add wrapper div around hidden hours ?>
                <div class="closed">
            <?php endif ?>
            <ul aria-hidden="<?php echo ($week_number > 0) ? 'true' : 'false' ?>"
                class="hours-list-view <?php echo $this->setClassList($stacked) ?>">
                <?php for ($i = 0, $n = count($chunked_week); $i < $n; $i++):
                    if ($today_only && $i > 0) { // Only show first day
                        break;
                    }

                    $day = $chunked_week[$i];
                    $today = $day['is_today'];
                    $date = $day['date'];
                    $day_text = ($today) ? 'Today' : $date->format('D'); ?>

                    <li <?php echo $this->setToday($today) ?>>
                        <?php if ($week_number > 0): ?>
                            <div class="hours-day"><?php echo $date->format( 'M j' )  ?></div>
                        <?php else: ?>
                            <div class="hours-day"><?php echo $day_text ?></div>
                        <?php endif ?>
                        <div class="hours-text"><?php echo $this->hoursText($day) ?></div>
                    </li>
                <?php endfor ?>
            </ul>
            <?php if ($week_number > 0 && $week_number == array_key_last($chunked_weeks)): // Close wrapper div around hidden hours ?>
                </div>
            <?php endif ?>
        <?php endforeach ?>
    </div>
<?php if (count($chunked_weeks) > 1): ?>
    <div class="hours-more">
        <button id="<?php echo $base_location ?>">View all hours</button>
    </div>
<?php endif ?>