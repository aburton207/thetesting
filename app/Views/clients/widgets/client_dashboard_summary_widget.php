<div class="row">
    <div class="col-md-3 col-sm-6 widget-container">
        <div class="card dashboard-icon-widget">
            <div class="card-body">
                <div class="widget-icon bg-primary">
                    <i data-feather="users" class="icon"></i>
                </div>
                <div class="widget-details">
                    <h1><?php echo number_format($summary->total_clients); ?></h1>
                    <span class="bg-transparent-white"><?php echo app_lang('total_clients'); ?></span>
                    <div class="mt5">
                        <span class="<?php echo $summary->trend->clients_percent >= 0 ? 'text-success' : 'text-danger'; ?>">
                            <i data-feather="<?php echo $summary->trend->clients_percent >= 0 ? 'trending-up' : 'trending-down'; ?>" class="icon-14"></i>
                            <?php echo round($summary->trend->clients_percent); ?>%
                        </span>
                    </div>
                    <canvas id="client-count-chart" height="30"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 widget-container">
        <div class="card dashboard-icon-widget">
            <div class="card-body">
                <div class="widget-icon bg-info">
                    <i data-feather="database" class="icon"></i>
                </div>
                <div class="widget-details">
                    <h1><?php echo number_format($summary->total_volume, 2); ?></h1>
                    <span class="bg-transparent-white">Total Volume</span>
                    <div class="mt5">
                        <span class="<?php echo $summary->trend->volume_percent >= 0 ? 'text-success' : 'text-danger'; ?>">
                            <i data-feather="<?php echo $summary->trend->volume_percent >= 0 ? 'trending-up' : 'trending-down'; ?>" class="icon-14"></i>
                            <?php echo round($summary->trend->volume_percent); ?>%
                        </span>
                    </div>
                    <canvas id="client-volume-chart" height="30"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 widget-container">
        <div class="card dashboard-icon-widget">
            <div class="card-body">
                <div class="widget-icon bg-success">
                    <i data-feather="trending-up" class="icon"></i>
                </div>
                <div class="widget-details">
                    <h1><?php echo to_currency($summary->potential_margin); ?></h1>
                    <span class="bg-transparent-white">Potential Margin</span>
                    <div class="mt5">
                        <span class="<?php echo $summary->trend->margin_percent >= 0 ? 'text-success' : 'text-danger'; ?>">
                            <i data-feather="<?php echo $summary->trend->margin_percent >= 0 ? 'trending-up' : 'trending-down'; ?>" class="icon-14"></i>
                            <?php echo round($summary->trend->margin_percent); ?>%
                        </span>
                    </div>
                    <canvas id="client-margin-chart" height="30"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 widget-container">
        <div class="card dashboard-icon-widget">
            <div class="card-body">
                <div class="widget-icon bg-coral">
                    <i data-feather="activity" class="icon"></i>
                </div>
                <div class="widget-details">
                    <h1><?php echo to_currency($summary->weighted_forecast); ?></h1>
                    <span class="bg-transparent-white">Weighted Forecast</span>
                    <div class="mt5">
                        <span class="<?php echo $summary->trend->forecast_percent >= 0 ? 'text-success' : 'text-danger'; ?>">
                            <i data-feather="<?php echo $summary->trend->forecast_percent >= 0 ? 'trending-up' : 'trending-down'; ?>" class="icon-14"></i>
                            <?php echo round($summary->trend->forecast_percent); ?>%
                        </span>
                    </div>
                    <canvas id="client-forecast-chart" height="30"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        var labels = <?php echo json_encode($summary->months); ?>;
        new Chart(document.getElementById('client-count-chart'), {
            type: 'line',
            data: {labels: labels, datasets: [{data: <?php echo json_encode($summary->monthly_clients); ?>, borderColor: '#2196f3', backgroundColor: 'rgba(33,150,243,0.1)', borderWidth: 1, pointRadius: 0}]},
            options: {responsive: true, maintainAspectRatio: false, legend: {display: false}, scales:{xAxes:[{display:false}], yAxes:[{display:false}]}}
        });
        new Chart(document.getElementById('client-volume-chart'), {
            type: 'line',
            data: {labels: labels, datasets: [{data: <?php echo json_encode($summary->monthly_volume); ?>, borderColor: '#17a2b8', backgroundColor: 'rgba(23,162,184,0.1)', borderWidth: 1, pointRadius: 0}]},
            options: {responsive: true, maintainAspectRatio: false, legend: {display: false}, scales:{xAxes:[{display:false}], yAxes:[{display:false}]}}
        });
        new Chart(document.getElementById('client-margin-chart'), {
            type: 'line',
            data: {labels: labels, datasets: [{data: <?php echo json_encode($summary->monthly_margin); ?>, borderColor: '#28a745', backgroundColor: 'rgba(40,167,69,0.1)', borderWidth: 1, pointRadius: 0}]},
            options: {responsive: true, maintainAspectRatio: false, legend: {display: false}, scales:{xAxes:[{display:false}], yAxes:[{display:false}]}}
        });
        new Chart(document.getElementById('client-forecast-chart'), {
            type: 'line',
            data: {labels: labels, datasets: [{data: <?php echo json_encode($summary->monthly_forecast); ?>, borderColor: '#ff7043', backgroundColor: 'rgba(255,112,67,0.1)', borderWidth: 1, pointRadius: 0}]},
            options: {responsive: true, maintainAspectRatio: false, legend: {display: false}, scales:{xAxes:[{display:false}], yAxes:[{display:false}]}}
        });
    });
</script>
