<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <canvas id="potential-margin-chart" style="width:100%; height:350px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <canvas id="volume-chart" style="width:100%; height:350px;"></canvas>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var marginCtx = document.getElementById("potential-margin-chart");
        new Chart(marginCtx, {
            type: 'bar',
            data: {
                labels: <?php echo $labels; ?>,
                datasets: [{
                    label: '<?php echo app_lang('potential_margin'); ?>',
                    data: <?php echo $margin_data; ?>,
                    backgroundColor: '#28a745'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {display: false},
                scales: {
                    xAxes: [{
                        gridLines: {color: 'rgba(127,127,127,0.1)'},
                        ticks: {fontColor: '#898fa9'}
                    }],
                    yAxes: [{
                        gridLines: {color: 'rgba(127,127,127,0.1)'},
                        ticks: {fontColor: '#898fa9'}
                    }]
                }
            }
        });

        var volumeCtx = document.getElementById("volume-chart");
        new Chart(volumeCtx, {
            type: 'bar',
            data: {
                labels: <?php echo $labels; ?>,
                datasets: [{
                    label: '<?php echo app_lang('volume'); ?>',
                    data: <?php echo $volume_data; ?>,
                    backgroundColor: '#3B81F6'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {display: false},
                scales: {
                    xAxes: [{
                        gridLines: {color: 'rgba(127,127,127,0.1)'},
                        ticks: {fontColor: '#898fa9'}
                    }],
                    yAxes: [{
                        gridLines: {color: 'rgba(127,127,127,0.1)'},
                        ticks: {fontColor: '#898fa9'}
                    }]
                }
            }
        });
    });
</script>
