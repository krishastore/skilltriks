<?php
/**
 * Template: Course Settings Metabox.
 *
 * @package ST\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$setting_tags = apply_filters(
	'stlms_course_setting_tabs',
	array(
		'course-info' => array(
			'name'     => esc_html__( 'Course Information', 'skilltriks' ),
			'template' => STLMS_TEMPLATEPATH . '/admin/course/setting-course-info.php',
		),
		'assessment'  => array(
			'name'     => esc_html__( 'Assessment', 'skilltriks' ),
			'template' => STLMS_TEMPLATEPATH . '/admin/course/setting-assessment.php',
		),
		'author'      => array(
			'name'     => esc_html__( 'Author', 'skilltriks' ),
			'template' => STLMS_TEMPLATEPATH . '/admin/course/setting-author.php',
		),
	)
);
?>
<div class="stlms-course-settings">
	<div class="stlms-tab-container">
		<?php if ( is_array( $setting_tags ) ) : ?>
			<div class="stlms-tabs-nav">
				<?php
				$count = 0;
				foreach ( $setting_tags as $key => $setting_tag ) :
					?>
					<a href="javascript:;" class="stlms-tab<?php echo 0 === $count ? ' active' : ''; ?>" data-tab="<?php echo esc_attr( $key ); ?>"><?php echo isset( $setting_tag['name'] ) ? esc_html( $setting_tag['name'] ) : ''; ?></a>
					<?php
					++$count;
				endforeach;
				?>
			</div>
			<?php
			$setting_templates = array_column( $setting_tags, 'template' );
			foreach ( $setting_templates as $key => $setting_template ) {
				if ( is_readable( $setting_template ) ) {
					$active_class = 0 === $key ? ' active' : '';
					require $setting_template;
				}
			}
			?>
		<?php endif; ?>
	</div>	
</div>
