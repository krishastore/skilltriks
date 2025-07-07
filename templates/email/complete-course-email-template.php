<?php
/**
 * Email template for delete assigned course.
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
												Good news! Your assigned learner <?php echo esc_html( $args['from_user'] ); ?> has completed the course
												<a href="<?php echo esc_url( $args['course_link'] ); ?>" style="color: #0F5AA7; text-decoration: none;"><?php echo esc_html( $args['course_name'] ); ?></a>
											</td>
										</tr>
										<tr>
											<td style="padding-bottom: 16px;">
												Keep up the great mentorship!
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
