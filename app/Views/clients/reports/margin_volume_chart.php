<div id="margin-volume-charts" class="row">
    <div class="text-end mb-3">
        <button id="download-margin-volume-pdf" class="btn btn-default">
            <i data-feather="download" class="icon-16"></i> <?php echo app_lang('download_pdf'); ?>
        </button>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="mb15"><?php echo app_lang('potential_margin'); ?></h4>
                <canvas id="potential-margin-chart" style="width:100%; height:350px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="mb15"><?php echo app_lang('volume'); ?></h4>
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

        $("#download-margin-volume-pdf").on("click", function () {
            var button = this;
            button.style.display = 'none';
            html2canvas(document.getElementById('margin-volume-charts')).then(function (canvas) {
                var imgData = canvas.toDataURL('image/png');
                var pdf = new jspdf.jsPDF();
                var width = pdf.internal.pageSize.getWidth();
                var height = canvas.height * width / canvas.width;
                pdf.addImage(imgData, 'PNG', 0, 0, width, height);
                pdf.save('report.pdf');
                button.style.display = '';
            });
        });
    });
</script>
