<?php
// Load Google Maps API key from settings, fallback to environment variable
$googleMapsApiKey = get_setting('google_maps_api_key');
if (!$googleMapsApiKey) {
    $googleMapsApiKey = getenv('GOOGLE_MAPS_API_KEY');
}

if ($googleMapsApiKey) {
    ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $googleMapsApiKey; ?>&libraries=places"></script>
    <?php
}
?>

<script>
window.addAppTableDisplayOption = 10000;
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<?php
// add your custom header here.
