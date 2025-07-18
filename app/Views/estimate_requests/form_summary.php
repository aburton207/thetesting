<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo app_lang('estimate_request_summary'); ?></h1>
        </div>
        <div class="card-body">
            <div class="text-end mb-3">
                <button id="download-summary-pdf" class="btn btn-default">
                    <i data-feather="download" class="icon-16"></i> <?php echo app_lang('download_pdf'); ?>
                </button>
            </div>
            <canvas id="summary-chart" height="200"></canvas>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
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

        document.getElementById('download-summary-pdf').addEventListener('click', function () {
            html2canvas(document.getElementById('summary-chart')).then(function (canvas) {
                var imgData = canvas.toDataURL('image/png');
                var pdf = new jspdf.jsPDF();
                var width = pdf.internal.pageSize.getWidth();
                var height = canvas.height * width / canvas.width;
                pdf.addImage(imgData, 'PNG', 0, 0, width, height);
                pdf.save('summary.pdf');
            });
        });
    });
</script>
