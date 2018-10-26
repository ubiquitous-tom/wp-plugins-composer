<div class="active-features">
	<?php if ( $future_date ) : ?>
	<div class="navbar-future-date"><span>Future Date: <?php echo $future_date; ?></span></div>
	<?php endif; ?>

	<?php if ( $country_filter ) : ?>
	<div class="navbar-country-filter"><span>Country: <?php echo $country_filter; ?></span></div>
	<?php endif; ?>

	<?php if ( $is_video_debugger_on ) : ?>
	<div class="navbar-video-debugging"><span>Video Debugger</span></div>
	<?php endif; ?>
</div>
