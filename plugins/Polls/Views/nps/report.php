<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo $survey->title; ?></h1>
        </div>
        <div class="p15">
            <div class="mb15">
                <strong><?php echo app_lang('embed'); ?>:</strong>
                <pre class="mt5">&lt;iframe src="<?php echo get_uri('nps/embed/' . $survey->id); ?>"&gt;&lt;/iframe&gt;</pre>
            </div>
            <p><strong><?php echo app_lang('nps_score'); ?>:</strong> <?php echo round($nps_score, 2); ?></p>
            <ul class="list-group">
                <li class="list-group-item"><?php echo app_lang('promoters'); ?>: <?php echo $promoters; ?></li>
                <li class="list-group-item"><?php echo app_lang('passives'); ?>: <?php echo $passives; ?></li>
                <li class="list-group-item"><?php echo app_lang('detractors'); ?>: <?php echo $detractors; ?></li>
            </ul>
        </div>
    </div>
</div>
