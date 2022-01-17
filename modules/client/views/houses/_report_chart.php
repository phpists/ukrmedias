<div class="biginfo-chart ">
    <div class="biginfo-chart--side"> <?php echo $unit; ?></div>
    <div class="biginfo-chart--body">
        <canvas id="report_chart" class="js-chart-columns"></canvas>
    </div>
</div>
<script>
//    drawReportChart({
//        labels: ["00:00", "01:00", "02:00", "03:00", "04:00", "05:00", "06:00", "07:00", "08:00", "09:00", "10:00", "11:00", "12:00", "13:00", "14:00", "15:00", "16:00", "17:00", "18:00", "19:00", "20:00", "21:00", "22:00", "23:00"],
//        datasets: [0.01, 0.006, 0.01, 0.002, 0.04, 0.01, 0.007, 0.01, 0.015, 0.08, 0.09, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.05]
//    });
    drawReportChart(<?php echo json_encode($reportData); ?>);
</script>
