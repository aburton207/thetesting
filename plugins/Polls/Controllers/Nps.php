<?php

namespace Polls\Controllers;

class Nps extends \App\Controllers\Security_Controller {

    protected $Nps_surveys_model;
    protected $Nps_questions_model;
    protected $Nps_responses_model;

    function __construct() {
        parent::__construct();
        $this->Nps_surveys_model = new \Polls\Models\Nps_surveys_model();
        $this->Nps_questions_model = new \Polls\Models\Nps_questions_model();
        $this->Nps_responses_model = new \Polls\Models\Nps_responses_model();
    }

    // list surveys
    function index() {
        $view_data["surveys"] = $this->Nps_surveys_model->get_details()->getResult();
        return $this->template->rander("Polls\\Views\\nps\\index", $view_data);
    }

    // load survey add/edit modal
    function modal_form() {
        $id = $this->request->getPost('id');
        $view_data['model_info'] = $this->Nps_surveys_model->get_one($id);
        return $this->template->view('Polls\\Views\\nps\\modal_form', $view_data);
    }

    // save survey
    function save() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "title" => "required"
        ));

        $id = $this->request->getPost('id');
        $data = array(
            "title" => $this->request->getPost('title'),
            "description" => $this->request->getPost('description'),
            "status" => $this->request->getPost('status') ? $this->request->getPost('status') : 'active'
        );

        if (!$id) {
            $data['created_at'] = get_current_utc_time();
        }

        $save_id = $this->Nps_surveys_model->ci_save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "id" => $save_id, "message" => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, "message" => app_lang('error_occurred')));
        }
    }

    // load question add/edit modal
    function question_form() {
        $id = $this->request->getPost('id');
        $survey_id = $this->request->getPost('survey_id');
        $view_data['model_info'] = $this->Nps_questions_model->get_one($id);
        $view_data['survey_id'] = $survey_id;
        return $this->template->view('Polls\\Views\\nps\\question_form', $view_data);
    }

    // save question
    function save_question() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "survey_id" => "required|numeric",
            "title" => "required"
        ));

        $id = $this->request->getPost('id');
        $data = array(
            "survey_id" => $this->request->getPost('survey_id'),
            "title" => $this->request->getPost('title'),
            "sort_order" => $this->request->getPost('sort_order')
        );

        if (!$data['sort_order']) {
            $existing = $this->Nps_questions_model->get_details(array("survey_id" => $data['survey_id']))->getResult();
            $data['sort_order'] = count($existing) + 1;
        }

        $save_id = $this->Nps_questions_model->ci_save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "id" => $save_id, "message" => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, "message" => app_lang('error_occurred')));
        }
    }

    // list questions of a survey
    function questions($survey_id = 0) {
        validate_numeric_value($survey_id);

        $survey = $this->Nps_surveys_model->get_one($survey_id);
        if (!$survey || !$survey->id) {
            show_404();
        }

        $view_data = array(
            "survey" => $survey,
            "questions" => $this->Nps_questions_model->get_details(array("survey_id" => $survey_id))->getResult()
        );

        return $this->template->rander("Polls\\Views\\nps\\questions", $view_data);
    }

    // delete a question
    function delete_question() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');

        if ($this->Nps_questions_model->delete($id)) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

    // show NPS report
    function report($survey_id = 0) {
        $survey = $this->Nps_surveys_model->get_one($survey_id);
        if (!$survey || !$survey->id) {
            show_404();
        }

        $summary = $this->Nps_responses_model->get_summary($survey_id)->getResult();

        $promoters = $passives = $detractors = $total = 0;
        foreach ($summary as $row) {
            $score = (int) $row->score;
            $count = (int) $row->total;
            $total += $count;
            if ($score >= 9) {
                $promoters += $count;
            } else if ($score >= 7) {
                $passives += $count;
            } else {
                $detractors += $count;
            }
        }

        $nps_score = $total ? (($promoters - $detractors) / $total) * 100 : 0;

        $view_data = array(
            "survey" => $survey,
            "promoters" => $promoters,
            "passives" => $passives,
            "detractors" => $detractors,
            "nps_score" => $nps_score,
            "total" => $total,
            "promoters_percent" => $total ? ($promoters / $total) * 100 : 0,
            "passives_percent" => $total ? ($passives / $total) * 100 : 0,
            "detractors_percent" => $total ? ($detractors / $total) * 100 : 0,
            "poll_answers" => array(
                (object) ["title" => app_lang('promoters'), "total_vote" => $promoters],
                (object) ["title" => app_lang('passives'), "total_vote" => $passives],
                (object) ["title" => app_lang('detractors'), "total_vote" => $detractors]
            )
        );

        return $this->template->rander("Polls\\Views\\nps\\report", $view_data);
    }
}

/* End of file Nps.php */
/* Location: ./plugins/Polls/Controllers/Nps.php */
