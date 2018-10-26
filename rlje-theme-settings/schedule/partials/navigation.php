
<ul class="subnav">
	<span class="page-subhead">FILTER BY:</span>
	<?php foreach ( $this->list_sections as $section_key => $section_name ) : ?>
	<?php $active_class = ( $section_key === $active_section ) ? 'active' : ''; ?>
	<li class="<?php echo sanitize_html_class( $active_class ); ?>">
		<?php $section = ( 'featured' !== $section_key ) ?  $section_key : ''; ?>
		<a href="<?php echo esc_url( home_url( trailingslashit( 'schedule/' . $section ) ) ); ?>">
			<?php echo esc_html( $section_name ); ?>
		</a>
	</li>
	<?php endforeach; ?>
</ul>
