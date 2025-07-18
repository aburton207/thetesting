<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo app_lang('estimate_request_summary'); ?></h1>
        </div>
        <div class="card-body">
            <canvas id="summary-chart" height="300"></canvas>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        new Chart(document.getElementById('summary-chart'), {
            type: 'bar',
            data: {
                labels: <?php echo $labels; ?>,
                datasets: [{
                    data: <?php echo $data; ?>,
                    backgroundColor: '#6b8de3'
                }]
            },
            options: {
                responsive: true,
                legend: {display: false},
                scales: {
                    yAxes: [{ticks: {beginAtZero: true}}]
                }
            }
        });
    });
</script>
