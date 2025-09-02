<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo $survey->title; ?></h1>
            <?php if (!isset($is_pdf)) { ?>
                <div class="title-button-group">
                    <?php echo anchor(get_uri("nps/download_pdf/" . $survey->id), "<i data-feather='download' class='icon-16'></i> " . app_lang('download_pdf'), array("class" => "btn btn-default")); ?>
                </div>
            <?php } ?>
        </div>
        <div class="p15">
            <div class="mb15">
                <strong><?php echo app_lang('embed'); ?>:</strong>
                <pre class="mt5">&lt;iframe src="<?php echo get_uri('nps/embed/' . $survey->id); ?>"&gt;&lt;/iframe&gt;</pre>
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th><?php echo app_lang('category'); ?></th>
                        <th><?php echo app_lang('count'); ?></th>
                        <th><?php echo app_lang('percentage'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo app_lang('promoters'); ?></td>
                        <td><?php echo $promoters; ?></td>
                        <td><?php echo round($promoters_percent, 2); ?>%</td>
                    </tr>
                    <tr>
                        <td><?php echo app_lang('passives'); ?></td>
                        <td><?php echo $passives; ?></td>
                        <td><?php echo round($passives_percent, 2); ?>%</td>
                    </tr>
                    <tr>
                        <td><?php echo app_lang('detractors'); ?></td>
                        <td><?php echo $detractors; ?></td>
                        <td><?php echo round($detractors_percent, 2); ?>%</td>
                    </tr>
                </tbody>
            </table>

            <p class="mt20"><strong><?php echo app_lang('nps_score'); ?>:</strong> <?php echo round($nps_score, 2); ?></p>

            <?php if (!isset($is_pdf)) {
                echo view("Polls\\Views\\polls\\vote_pie_chart", array("poll_answers" => $poll_answers));
            } ?>

            <?php if (!empty($questions_summary)) { ?>
                <div class="mt30">
                    <?php foreach ($questions_summary as $question) { ?>
                        <h4 class="mt30"><?php echo $question["title"]; ?></h4>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th><?php echo app_lang("score"); ?></th>
                                    <th><?php echo app_lang("count"); ?></th>
                                    <th><?php echo app_lang("percentage"); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($i = 0; $i <= 10; $i++) { ?>
                                    <?php
                                    $count = $question["scores"][$i];
                                    $percent = $question["total"] ? round(($count / $question["total"]) * 100, 2) : 0;
                                    ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $count; ?></td>
                                        <td><?php echo $percent; ?>%</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th><?php echo app_lang('category'); ?></th>
                                    <th><?php echo app_lang('count'); ?></th>
                                    <th><?php echo app_lang('percentage'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo app_lang('promoters'); ?></td>
                                    <td><?php echo $question['promoters']; ?></td>
                                    <td><?php echo round($question['promoters_percent'], 2); ?>%</td>
                                </tr>
                                <tr>
                                    <td><?php echo app_lang('passives'); ?></td>
                                    <td><?php echo $question['passives']; ?></td>
                                    <td><?php echo round($question['passives_percent'], 2); ?>%</td>
                                </tr>
                                <tr>
                                    <td><?php echo app_lang('detractors'); ?></td>
                                    <td><?php echo $question['detractors']; ?></td>
                                    <td><?php echo round($question['detractors_percent'], 2); ?>%</td>
                                </tr>
                            </tbody>
                        </table>

                        <p class="mt20"><strong><?php echo app_lang('nps_score'); ?>:</strong> <?php echo round($question['nps_score'], 2); ?></p>

                        <?php if (!isset($is_pdf)) {
                            echo view("Polls\\Views\\polls\\vote_pie_chart", array("poll_answers" => $question['poll_answers'], "chart_id" => "vote-status-chart-" . $question['id']));
                        } ?>

                        <?php if (isset($comments_by_question[$question['id']])) { ?>
                            <h5 class="mt30"><?php echo app_lang('comments'); ?></h5>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th><?php echo app_lang('score'); ?></th>
                                        <th><?php echo app_lang('comment'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($comments_by_question[$question['id']] as $response) { ?>
                                        <tr>
                                            <td><?php echo $response->score; ?></td>
                                            <td><?php echo nl2br($response->comment); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php } ?>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
