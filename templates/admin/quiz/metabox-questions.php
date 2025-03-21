<?php
/**
 * Template: Quiz Questions Metabox.
 *
 * @package ST\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="stlms-quiz-qus-wrap">
	<div class="stlms-snackbar-notice"><p></p></div>
	<ul class="stlms-quiz-qus-list stlms-sortable-answers">
		<?php require_once STLMS_TEMPLATEPATH . '/admin/quiz/question-list.php'; ?>
	</ul>
	<div class="stlms-quiz-qus-footer">
		<a href="javascript:;" class="add-new-question button button-secondary"><?php esc_html_e( 'Add More Question', 'skilltriks-lms' ); ?></a>
	</div>
</div>
<?php require_once STLMS_TEMPLATEPATH . '/admin/quiz/modal-popup.php'; ?>
