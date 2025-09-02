<div class="card mt50 b-a">
    <div class="card-body">
        <canvas id="vote-status-chart" width="400"></canvas>
    </div>
</div>

<?php
$vote_label = array();
$vote_data = array();
foreach ($poll_answers as $vote_status) {
    $vote_label[] = $vote_status->title;
    $vote_data[] = $vote_status->total_vote;
}
?>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        function votePieChart() {
            //for vote status chart
            var labels = <?php echo json_encode($vote_label) ?>;
            var voteData = <?php echo json_encode($vote_data) ?>;
            var voteStatusChart = document.getElementById("vote-status-chart");

            //get background color for chart
            var colorPlate = [
                '#14BAA0', '#FF3D67', '#3B81F6', '#6165F2', '#F59F0F', '#FBCD16', '#E84C3D', '#40E0D0', '#E67F22',
                '#36A2EB', '#FF6283', '#4BC0C0', '#FF9F40', '#32CD32', '#9370DB', '#FFD700', '#008080', '#FF6347', '#7B68EE',
                '#40E0D0', '#FF4500', '#6A5ACD', '#00FF7F', '#8B008B', '#FF8C00', '#00CED1', '#FF69B4', '#48D1CC', '#FF1493',
                '#1E90FF', '#ADFF2F', '#8A2BE2', '#00FF00', '#9932CC', '#228B22', '#BA55D3', '#3CB371', '#800000', '#7FFFD4',
                '#8B0000', '#00FFFF', '#DC143C', '#00FF8C', '#FF0000', '#7FFF00', '#B22222', '#00FA9A', '#FF7F50', '#ADFF2F',
                '#8B4513', '#20B2AA', '#CD5C5C', '#98FB98', '#800080', '#66CDAA', '#FA8072', '#9ACD32', '#FF4500', '#8FBC8B'
            ];

            new Chart(voteStatusChart, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            data: voteData,
                            backgroundColor: colorPlate,
                            borderWidth: 0
                        }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            fontColor: "#898fa9"
                        }
                    },
                    animation: {
                        animateScale: true
                    }
                }
            });
        }

        setTimeout(function () {
            votePieChart();
        }, 200);
    });
</script>
