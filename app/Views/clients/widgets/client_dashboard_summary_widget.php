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
                </div>
            </div>
        </div>
    </div>
</div>
