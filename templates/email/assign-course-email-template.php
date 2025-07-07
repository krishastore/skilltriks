<?php
/**
 * Email template for assign new course.
 *
 * @package ST\Lms
 *
 * phpcs:disable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$options      = get_option( 'stlms_settings' );
$company_logo = isset( $options['company_logo'] ) ? $options['company_logo'] : 0;
$date_format  = get_option( 'date_format' );

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>STLMS - Mailer</title>
</head>

<body style="padding: 0; margin: 0;  font-family: Arial, Helvetica, sans-serif;">
	<table cellpadding="0" cellspacing="0" align="center" border="0" width="640">
		<tbody>
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0" align="center" border="0" width="600"
						style="color: #131520; font-size: 16px; line-height: 26px;">
						<tbody>
							<tr style="vertical-align: top;">
								<td style="padding-bottom: 20px; padding-top: 20px; text-align: center; background-color: #ffffff;"
									bgcolor="#ffffff">
									<?php if ( $company_logo ) : ?>
									<a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="display: block;">
										<img src="<?php echo esc_url( wp_get_attachment_image_url( $company_logo ) ); ?>" width="208"
											height="35" style="display: block; margin: 0 auto;" alt="">
									</a>
									<?php endif; ?>
								</td>
							</tr>
							<tr>
								<td style="background-color: #F6F6F7; padding: 30px;">
									<table cellspacing="0" cellpadding="0" width="100%">
										<tr>
											<td style="padding-bottom: 16px;">Hi <?php echo esc_html( $args['to_user'] ); ?></td>
										</tr>
										<tr>
											<td style="padding-bottom: 16px;">
												You’ve been assigned a new course:
												<a href="<?php echo esc_url( $args['course_link'] ); ?>" style="color: #0F5AA7; text-decoration: none;"><?php echo esc_html( $args['course_name'] ); ?></a>, assigned
												by <?php echo esc_html( $args['from_user'] ); ?>.
											</td>
										</tr>
										<tr>
											<td style="padding-bottom: 16px;">
												<strong>Due Date:</strong> <?php echo ! empty( $args['due_date'] ) ? esc_html( wp_date( $date_format, strtotime( $args['due_date'] ) ) ) : esc_html_e( 'No due date set', 'skilltriks' ); ?>
											</td>
										</tr>
										<tr>
											<td style="padding-bottom: 16px;">Start learning today to build new skills and grow your knowledge!
											</td>
										</tr>
										<tr>
											<td style="padding-bottom: 16px;">Thanks</td>
										</tr>
										<tr>
											<td style="padding-top: 04px;">
												<a href="<?php echo esc_url( $args['course_link'] ); ?>"
													style="display: block; text-align: center; background-color: #0F5AA7; color: #ffffff; text-decoration: none; border-radius: 4px; width: 104px; height: 34px; font-size: 13px; line-height: 34px;">
													Start Course
												</a>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</body>
</html>
<?php
