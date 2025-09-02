<?php

/**
 * Fetch NPS surveys
 * @param array $options
 * @return \CodeIgniter\Database\ResultInterface
 */
if (!function_exists('nps_get_surveys')) {
    function nps_get_surveys($options = array()) {
        $model = new \Polls\Models\Nps_surveys_model();
        return $model->get_details($options);
    }
}

/**
 * Fetch NPS questions
 * @param array $options
 * @return \CodeIgniter\Database\ResultInterface
 */
if (!function_exists('nps_get_questions')) {
    function nps_get_questions($options = array()) {
        $model = new \Polls\Models\Nps_questions_model();
        return $model->get_details($options);
    }
}

/**
 * Save NPS score
 * @param array $data
 * @return int|bool
 */
if (!function_exists('nps_save_score')) {
    function nps_save_score($data = array()) {
        $model = new \Polls\Models\Nps_responses_model();
        return $model->save_score($data);
    }
}

