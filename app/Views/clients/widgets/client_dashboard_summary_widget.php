<div class="row">
    <div class="col-md-3 col-sm-6 widget-container">
        <div class="card dashboard-icon-widget">
            <div class="card-body">
                <div class="widget-icon bg-primary">
                    <i data-feather="users" class="icon"></i>
                </div>
                <div class="widget-details">
                    <h1><?php echo number_format($summary->total_clients); ?></h1>
                    <span class="bg-transparent-white"><?php echo $owner_name ? $owner_name . ' - ' : ''; ?><?php echo app_lang('total_clients'); ?></span>
                    <div class="mt5">
                        <span class="<?php echo $summary->trend->clients_percent >= 0 ? 'text-success' : 'text-danger'; ?>">
                            <i data-feather="<?php echo $summary->trend->clients_percent >= 0 ? 'trending-up' : 'trending-down'; ?>" class="icon-14"></i>
                            <?php echo round($summary->trend->clients_percent); ?>%
                        </span>
                    </div>
                    <!-- removed chart canvas -->
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
                    <span class="bg-transparent-white"><?php echo $owner_name ? $owner_name . ' - ' : ''; ?>Total Volume</span>
                    <div class="mt5">
                        <span class="<?php echo $summary->trend->volume_percent >= 0 ? 'text-success' : 'text-danger'; ?>">
                            <i data-feather="<?php echo $summary->trend->volume_percent >= 0 ? 'trending-up' : 'trending-down'; ?>" class="icon-14"></i>
                            <?php echo round($summary->trend->volume_percent); ?>%
                        </span>
                    </div>
                    <!-- removed chart canvas -->
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
                    <span class="bg-transparent-white"><?php echo $owner_name ? $owner_name . ' - ' : ''; ?>Potential Margin</span>
                    <div class="mt5">
                        <span class="<?php echo $summary->trend->margin_percent >= 0 ? 'text-success' : 'text-danger'; ?>">
                            <i data-feather="<?php echo $summary->trend->margin_percent >= 0 ? 'trending-up' : 'trending-down'; ?>" class="icon-14"></i>
                            <?php echo round($summary->trend->margin_percent); ?>%
                        </span>
                    </div>
                    <!-- removed chart canvas -->
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
                    <span class="bg-transparent-white"><?php echo $owner_name ? $owner_name . ' - ' : ''; ?>Weighted Forecast</span>
                    <div class="mt5">
                        <span class="<?php echo $summary->trend->forecast_percent >= 0 ? 'text-success' : 'text-danger'; ?>">
                            <i data-feather="<?php echo $summary->trend->forecast_percent >= 0 ? 'trending-up' : 'trending-down'; ?>" class="icon-14"></i>
                            <?php echo round($summary->trend->forecast_percent); ?>%
                        </span>
                    </div>
                    <!-- removed chart canvas -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- removed chart scripts -->
