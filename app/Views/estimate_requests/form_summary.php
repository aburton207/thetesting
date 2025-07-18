<style type="text/css">
    .summary-container h1 {
        font-family: "Poppins", sans-serif;
        font-weight: 600;
    }

    .summary-container h4 {
        font-family: "Inter", sans-serif;
        font-weight: 600;
    }

    .summary-list li {
        display: flex;
        justify-content: space-between;
        border-bottom: 1px solid #eee;
        padding: 6px 0;
        font-family: "Inter", sans-serif;
    }

    .summary-list .count {
        font-weight: 600;
    }
</style>

<div id="page-content" class="page-wrapper clearfix">
    <div class="card summary-container" id="summary-card">
        <div class="page-title clearfix">
            <h1><?php echo app_lang('estimate_request_summary'); ?></h1>
            <?php if (!empty($form_title)) { ?>
                <h4 class="mt-2"><?php echo $form_title; ?></h4>
            <?php } ?>
        </div>
        <div class="card-body" id="summary-section">
            <div class="text-end mb-3">
                <button id="download-summary-pdf" class="btn btn-default">
                    <i data-feather="download" class="icon-16"></i> <?php echo app_lang('download_pdf'); ?>
                </button>
            </div>
            <div id="summary-content">
                <div class="row">
                    <div class="col-md-3">
                        <ul class="list-unstyled summary-list">
                            <?php foreach ($labels_array as $index => $label) { ?>
                                <li><span><?php echo $label; ?></span><span class="count"><?php echo $data_array[$index]; ?></span></li>
                            <?php } ?>
                        </ul>
                    </div>
                    <div class="col-md-9">
                        <canvas id="summary-chart" height="150"></canvas>
                    </div>
                </div>
            </div>
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
            var button = document.getElementById('download-summary-pdf');
            button.style.display = 'none';
            html2canvas(document.getElementById('summary-card')).then(function (canvas) {
                var imgData = canvas.toDataURL('image/png');
                var pdf = new jspdf.jsPDF();
                var width = pdf.internal.pageSize.getWidth();
                var height = canvas.height * width / canvas.width;
                pdf.addImage(imgData, 'PNG', 0, 0, width, height);
                pdf.save('summary.pdf');
                button.style.display = '';
            });
        });
    });
</script>
