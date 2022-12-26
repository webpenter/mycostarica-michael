<?php
$agents = WP3D_Models()->get_associated_agents();

foreach ($agents as $agent) {
    if (!empty($agent['calendly_enabled'])) {
        echo '<link href="https://assets.calendly.com/assets/external/widget.css" rel="stylesheet">';
        echo '<script src="https://assets.calendly.com/assets/external/widget.js" type="text/javascript"></script>';
        
        if ($agent['calendly_type'] == 'widget') {
            if (!empty($agent['calendly_event_link']) && strpos($agent['calendly_event_link'], 'https://calendly.com/') !== false) {
                $url = $agent['calendly_event_link'];
                $text = !empty($agent['custom_link_title']) ? $agent['custom_link_title'] : 'Schedule Tour';
                $back = !empty($agent['calendly_color_back']) ? $agent['calendly_color_back'] : '#00a2ff';
                $color = !empty($agent['calendly_color_text']) ? $agent['calendly_color_text'] : '#ffffff';
                $border = !empty($agent['calendly_color_border']) ? $agent['calendly_color_border'] : false;
                $position = !empty($agent['calendly_popup_location']) ? $agent['calendly_popup_location'] : 'right';

                echo '<script type="text/javascript">Calendly.initBadgeWidget({ url: "' . $url . '", text: "' . $text . '", color: "' . $back . '", textColor: "' . $color . '", branding: false });</script>';
                
                if (!empty($border)) {
                    echo '<style>.calendly-badge-content{border: 1px solid ' . $border . '}</style>';
                }
                
                if ($position == 'left') {
                    echo '<style>.calendly-badge-widget{left:20px;right:auto}</style>';
                }

                if ($position == 'center') {
                    echo '<style>.calendly-badge-widget{left: 50%;transform: translate(-50%, -50%);display: inline-table;}</style>';
                }
            }
        }
        
        break;
    }
}


