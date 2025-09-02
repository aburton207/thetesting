<?php

namespace Polls\Controllers;

class Nps_public extends \App\Controllers\App_Controller {

    protected $Nps_surveys_model;
    protected $Nps_questions_model;
    protected $Nps_responses_model;

    public function __construct() {
        parent::__construct();
        $this->Nps_surveys_model = new \Polls\Models\Nps_surveys_model();
        $this->Nps_questions_model = new \Polls\Models\Nps_questions_model();
        $this->Nps_responses_model = new \Polls\Models\Nps_responses_model();
    }

    // render survey form
    public function view($survey_id = 0) {
        validate_numeric_value($survey_id);

        $survey = $this->Nps_surveys_model->get_details(["id" => $survey_id, "status" => "active"])->getRow();
        if (!$survey || !$survey->id) {
            show_404();
        }

        $view_data = [
            "survey" => $survey,
            "questions" => $this->Nps_questions_model->get_details(["survey_id" => $survey_id])->getResult(),
            "topbar" => "includes/public/topbar",
            "left_menu" => false,
            "embedded" => false
        ];

        return $this->template->rander("Polls\\Views\\nps\\public_survey", $view_data);
    }

    // store responses
    public function submit() {
        $this->validate_submitted_data([
            "survey_id" => "required|numeric"
        ]);

        $survey_id = $this->request->getPost("survey_id");
        $session = \Config\Services::session();
        $token = $session->getId();
        if (!$token) {
            $token = bin2hex(random_bytes(16));
        }

        $existing = $this->Nps_responses_model->get_details(["survey_id" => $survey_id, "token" => $token])->getRow();
        if ($existing) {
            echo json_encode(["success" => false, "message" => app_lang("error_occurred")]);
            return;
        }

        $scores = $this->request->getPost("score");
        $question_ids = array_map(function($q) { return $q->id; }, $this->Nps_questions_model->get_details(["survey_id" => $survey_id])->getResult());
        $submitted_ids = $scores && is_array($scores) ? array_map('intval', array_keys($scores)) : [];
        $missing_questions = array_diff($question_ids, $submitted_ids);

        if ($missing_questions) {
            echo json_encode(["success" => false, "message" => app_lang("please_input_all_required_fields")]);
            return;
        }

        foreach ($scores as $question_id => $score) {
            $data = [
                "survey_id" => $survey_id,
                "question_id" => $question_id,
                "score" => $score,
                "token" => $token,
                "created_at" => get_current_utc_time()
            ];
            $this->Nps_responses_model->save_score($data);
        }

        echo json_encode(["success" => true, "message" => app_lang("thank_you")]);
    }

    // lightweight view for iframe embedding
    public function embed($survey_id = 0) {
        validate_numeric_value($survey_id);

        $survey = $this->Nps_surveys_model->get_details(["id" => $survey_id, "status" => "active"])->getRow();
        if (!$survey || !$survey->id) {
            show_404();
        }

        $view_data = [
            "survey" => $survey,
            "questions" => $this->Nps_questions_model->get_details(["survey_id" => $survey_id])->getResult()
        ];

        return $this->template->view("Polls\\Views\\nps\\embed", $view_data);
    }
}

/* End of file Nps_public.php */
/* Location: ./plugins/Polls/Controllers/Nps_public.php */
