<div id="volume-by-status-chart-container" class="row">
    <div class="text-end mb-3">
        <button id="download-volume-by-status-pdf" class="btn btn-default">
            <i data-feather="download" class="icon-16"></i> <?php echo app_lang('download_pdf'); ?>
        </button>
    </div>
    <style>#volume-by-status-chart{height:550px!important;}</style>
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mb15"><?php echo app_lang('volume_by_opportunity_status'); ?> </h4>
                <canvas id="volume-by-status-chart" style="width:100%; height:350px;"></canvas>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var ctx = document.getElementById("volume-by-status-chart");
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo $labels; ?>,
                datasets: [{
                    label: '<?php echo app_lang('volume'); ?>',
                    data: <?php echo $volume_data; ?>,
                    backgroundColor: <?php echo $status_colors; ?>,
                    borderColor: <?php echo $status_colors; ?>,
                    borderWidth: 1
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

        $("#download-volume-by-status-pdf").on("click", function () {
            var button = this;
            button.style.display = 'none';
            html2canvas(document.getElementById('volume-by-status-chart-container')).then(function (canvas) {
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
