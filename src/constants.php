<?php
/**
 * Define global constants.
 *
 * @link       https://www.skilltriks.com/
 * @since      1.0.0
 *
 * @package    ST\Lms
 *
 * phpcs:disable WordPress.NamingConventions.ValidHookName.UseUnderscores
 */

namespace ST\Lms;

const STLMS_SCRIPT_HANDLE = 'stlms-';
const PARENT_MENU_SLUG    = 'skilltriks';
// Define constants for custom post types.
const STLMS_COURSE_CPT   = 'stlms_course';
const STLMS_LESSON_CPT   = 'stlms_lesson';
const STLMS_ORDER_CPT    = 'stlms_order';
const STLMS_QUESTION_CPT = 'stlms_question';
const STLMS_QUIZ_CPT     = 'stlms_quiz';
const STLMS_RESULTS_CPT  = 'stlms_results';

// Define constants for custom taxonomies.
const STLMS_COURSE_CATEGORY_TAX   = 'stlms_course_category';
const STLMS_COURSE_TAXONOMY_TAG   = 'stlms_course_tag';
const STLMS_QUESTION_TAXONOMY_TAG = 'stlms_quesion_topics';
const STLMS_QUIZ_TAXONOMY_LEVEL_1 = 'stlms_quiz_level_1';
const STLMS_QUIZ_TAXONOMY_LEVEL_2 = 'stlms_quiz_level_2';
const STLMS_LESSON_TAXONOMY_TAG   = 'stlms_lesson_topics';

// Question meta keys.
const META_KEY_QUESTION_PREFIX   = '_stlms_question';
const META_KEY_QUESTION_TYPE     = META_KEY_QUESTION_PREFIX . '_type';
const META_KEY_QUESTION_SETTINGS = META_KEY_QUESTION_PREFIX . '_settings';
const META_KEY_QUESTION_GROUPS   = META_KEY_QUESTION_PREFIX . '_groups';
const META_KEY_RIGHT_ANSWERS     = META_KEY_QUESTION_PREFIX . '_%s_answers';
const META_KEY_ANSWERS_LIST      = META_KEY_QUESTION_PREFIX . '_%s';
const META_KEY_MANDATORY_ANSWERS = META_KEY_QUESTION_PREFIX . '_mandatory_answers';
const META_KEY_OPTIONAL_ANSWERS  = META_KEY_QUESTION_PREFIX . '_optional_answers';
const META_KEY_QUESTION_QUIZ_IDS = META_KEY_QUESTION_PREFIX . '_quiz_ids';

// Quiz meta keys.
const META_KEY_QUIZ_PREFIX       = '_stlms_quiz';
const META_KEY_QUIZ_QUESTION_IDS = META_KEY_QUIZ_PREFIX . '_question_ids';
const META_KEY_QUIZ_SETTINGS     = META_KEY_QUIZ_PREFIX . '_settings';
const META_KEY_QUIZ_GROUPS       = META_KEY_QUIZ_PREFIX . '_groups';

// Lesson meta keys.
const META_KEY_LESSON_PREFIX     = '_stlms_lesson';
const META_KEY_LESSON_SETTINGS   = META_KEY_LESSON_PREFIX . '_settings';
const META_KEY_LESSON_MEDIA      = META_KEY_LESSON_PREFIX . '_media';
const META_KEY_LESSON_MATERIAL   = META_KEY_LESSON_PREFIX . '_material';
const META_KEY_LESSON_COURSE_IDS = META_KEY_LESSON_PREFIX . '_course_ids';

// Course meta keys.
const META_KEY_COURSE_PREFIX      = '_stlms_course';
const META_KEY_COURSE_INFORMATION = META_KEY_COURSE_PREFIX . '_information';
const META_KEY_COURSE_ASSESSMENT  = META_KEY_COURSE_PREFIX . '_assessment';
const META_KEY_COURSE_MATERIAL    = META_KEY_COURSE_PREFIX . '_material';
const META_KEY_COURSE_CURRICULUM  = META_KEY_COURSE_PREFIX . '_curriculum';
const META_KEY_COURSE_SIGNATURE   = META_KEY_COURSE_PREFIX . '_signature';

// Frontend nonce.
const STLMS_LOGIN_NONCE             = '_stlms_login';
const STLMS_FILTER_NONCE            = '_stlms_filter';
const STLMS_QUESTION_VALIDATE_NONCE = '_stlms_question_validate';

// User meta keys.
const STLMS_COURSE_STATUS       = '_stlms_%d_course_status';
const STLMS_LESSON_VIEW         = '_stlms_lesson_view_%d';
const STLMS_COURSE_COMPLETED_ON = '_stlms_%d_course_completed_on';
const STLMS_ENROL_COURSES       = '_stlms_enrol_courses';

// Define constant for setting.
const STLMS_SETTING = 'stlms-setting';

// Import meta key.
const META_KEY_IMPORT = '_stlms_import_id';

// Tables.
const STLMS_CRON_TABLE = 'stlms_cron_jobs';
