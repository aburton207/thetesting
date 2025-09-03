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

/**
 * Prepare NPS report PDF
 *
 * @param array $data
 * @param string $mode
 * @return void|string
 */
if (!function_exists('prepare_nps_report_pdf')) {

    function prepare_nps_report_pdf($data, $mode = "download") {
        $pdf = new \App\Libraries\Pdf();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();

        if ($data) {
            $data["mode"] = clean_data($mode);
            $data["is_pdf"] = true;
            $html = view("Polls\\Views\\nps\\report", $data);
            if ($mode !== "html") {
                $pdf->writeHTML($html, true, false, true, false, '');
            }

            $survey = get_array_value($data, "survey");
            $pdf_file_name = "nps-report-" . $survey->id . ".pdf";

            if ($mode === "download") {
                $pdf->Output($pdf_file_name, "D");
            } else if ($mode === "view") {
                $pdf->SetTitle($pdf_file_name);
                $pdf->Output($pdf_file_name, "I");
                exit;
            } else if ($mode === "html") {
                return $html;
            }
        }
    }
}

