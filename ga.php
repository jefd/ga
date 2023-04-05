<?php

$VERSION = 'v1.0.0';


$GA_DB_PATH = dirname(__FILE__) . '/ga.db';
$GH_DB_PATH = dirname(__FILE__) . '/metrics.db';

$DECIMATION = 15;


/************************************* Constants *******************************************/
// map of event types to titles
$EVENTS = ["hackathon" => "Hackathon Participants",
           "codesprint" => "Code Sprint Participants",
           "codefest" => "Code Fest Participants",
];

/*******************************************************************************************/



add_shortcode( 'ga', 'ga_dash_board');
function ga_dash_board($atts) {
    global $VERSION;
    
    return <<<EOT
    <div id="dashboard-app"></div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/mithril/mithril.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/jefd/dash@{$VERSION}/wp-content/themes/hello-elementor-child/js/dash.js"></script>
    EOT;
}


add_action('rest_api_init', function () {
    register_rest_route( 'ga/v1', '/(?P<metric>[a-z-_]+)',array(
        'methods'  => 'GET',
        'callback' => 'get_ga_data'
    ));
});


function accumulate($data) {
    $d = [];
    $acc = 0;
    foreach($data as $value) {
        $acc += $value;
        $d[] = $acc;
    }
    return $d;

}

function prune($labels, $data) {
    global $DECIMATION;

    $pruned_labels = [];
    $pruned_data = [];

    foreach ($labels as $idx => $value) {
        if ($idx % $DECIMATION === 0) {
            $pruned_labels[] = $value;
            $pruned_data[] = $data[$idx];
        }
    }

    return [$pruned_labels, $pruned_data];

}

function new_users_config($table_name, $start, $end) {
    global $GA_DB_PATH;

    function datasets($data) {
        $ds = [
            'label' => 'New Users',
            'data' => $data,
            'backgroundColor' => '#0099D8',
            'borderWidth' => 1
        ];

        return [$ds];
    }

    function format_data($labels, $datasets)
    {
        $data = [
            'labels' => $labels,
            'datasets' => $datasets
        ];
        return $data;
    }

    function opts() {
        $opts = [
            'responsive' => true,
            'indexAxis' => 'y',
            'scales' => ['y' => ['beginAtZero' => true]],
        ];

        return $opts;

    }

    function config($formatted_data, $opts) {
        return [
            'type' => 'bar',
            'data' => $formatted_data,
            'options' => $opts
        ];
    }
    try {

        $db = new PDO("sqlite:$GA_DB_PATH");
        $res = $db -> query("select * from \"$table_name\" where timestamp>=\"$start\" and timestamp<=\"$end\" order by timestamp;");

        $labels = [];
        $data = [];
        foreach ($res as $row) {

            $labels[] = $row['timestamp'];
            $data[] = intval($row['count']);

        }

        $accumulated_data = accumulate($data);
        $pruned = prune($labels, $accumulated_data);
        
        $pruned_labels = $pruned[0];
        $pruned_data = $pruned[1];

        $datasets = datasets($pruned_data); 
        $formatted_data =  format_data($pruned_labels, $datasets);
        $opts = opts();
        $config = config($formatted_data, $opts);

    }
    catch(PDOException $e) {
        //$chart_data = ["message" => $e->getMessage()];
        $config = ["message" => $e->getMessage()];
    }

    return $config;
}

function users_country_config($table_name, $start, $end) {
    global $GA_DB_PATH;

    function datasets($data) {
        $ds = [
            'label' => 'Users by Country',
            'data' => $data,
            'backgroundColor' => [
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(255, 205, 86)',
            'rgb(255, 150, 86)',
            'rgb(200, 150, 86)',
            'rgb(100, 150, 86)',
            '#0A4595',
            '#0099D8',
            '#D97200',
            '#00A54F',
            ],
        ];

        return [$ds];
    }

    function format_data($labels, $datasets)
    {
        $data = [
            'labels' => $labels,
            'datasets' => $datasets
        ];
        return $data;
    }

    function opts() {
        $opts = [
            'responsive' => true,
            'plugins' => ['title' => ['display' => true, 'text' => 'Users by Country']],
            //'maintainAspectRatio': false,
        ];

        return $opts;

    }

    function config($formatted_data, $opts) {
        return [
            'type' => 'doughnut',
            //'type' => 'bar',
            'data' => $formatted_data,
            'options' => $opts
        ];
    }

    try {

        $db = new PDO("sqlite:$GA_DB_PATH");
        $res = $db -> query("select * from \"$table_name\" order by count DESC;");

        $labels = [];
        $data = [];
        foreach ($res as $row) {

            $labels[] = $row['country'];
            $data[] = intval($row['count']);

        }
        $datasets = datasets($data); 
        $formatted_data =  format_data($labels, $datasets);
        $opts = opts();
        $config = config($formatted_data, $opts);

        //$config = config(format_data($labels, datasets($data)), opts());

    }
    catch(PDOException $e) {
        //$chart_data = ["message" => $e->getMessage()];
        $config = ["message" => $e->getMessage()];
    }

    return $config;
}

function followers_config($table_name, $start, $end) {
    global $GA_DB_PATH;

    function datasets($twitter, $ig, $fb) {
        $ds = [];

        $ds[] = ['label' => 'Twitter',
                 'data' => $twitter,
                 'backgroundColor' => '#0099D8',
                ];

        $ds[] = ['label' => 'Instagram',
                 'data' => $ig,
                 'backgroundColor' => '#D97200',
                ];

        $ds[] = ['label' => 'Facebook',
                 'data' => $fb,
                 'backgroundColor' => '#00A54F',
                ];

        return $ds;
    }

    function format_data($labels, $datasets)
    {
        $data = [
            'labels' => $labels,
            'datasets' => $datasets
        ];
        return $data;
    }

    function opts() {
        $opts = [
            'responsive' => true,
            'plugins' => ['title' => ['display' => true, 'text' => 'Social Media Followers']],
            'indexAxis' => 'y',
            'scales' => ['x' => ['stacked' => true], 'y' => ['stacked' => true]],
        ];

        return $opts;

    }

    function config($formatted_data, $opts) {
        return [
            'type' => 'bar',
            'data' => $formatted_data,
            'options' => $opts
        ];
    }

    try {

        $db = new PDO("sqlite:$GA_DB_PATH");
        $res = $db -> query("select * from \"$table_name\" where timestamp>=\"$start\" and timestamp<=\"$end\" order by timestamp;");

        $labels = [];
        $twitter = [];
        $instagram = [];
        $facebook = [];
        foreach ($res as $row) {

            $labels[] = $row['timestamp'];
            $twitter[] = intval($row['twitter']);
            $instagram[] = intval($row['instagram']);
            $facebook[] = intval($row['facebook']);

        }
        $datasets = datasets($twitter, $instagram, $facebook); 
        $formatted_data =  format_data($labels, $datasets);
        $opts = opts();
        $config = config($formatted_data, $opts);

        //$config = config(format_data($labels, datasets($data)), opts());

    }
    catch(PDOException $e) {
        //$chart_data = ["message" => $e->getMessage()];
        $config = ["message" => $e->getMessage()];
    }

    return $config;
}

function events_config($table_name, $event_type, $start, $end) {
    global $GA_DB_PATH;

    function datasets($gp, $ac, $gov, $ind) {
        $ds = [];

        $ds[] = ['label' => 'General Public',
                 'data' => $gp,
                 'backgroundColor' => '#0A4595',
                ];

        $ds[] = ['label' => 'Academia',
                 'data' => $ac,
                 'backgroundColor' => '#0099D8',
                ];

        $ds[] = ['label' => 'Government',
                 'data' => $gov,
                 'backgroundColor' => '#D97200',
                ];

        $ds[] = ['label' => 'Industry',
                 'data' => $ind,
                 'backgroundColor' => '#00A54F',
                ];

        return $ds;
    }

    function format_data($labels, $datasets)
    {
        $data = [
            'labels' => $labels,
            'datasets' => $datasets
        ];
        return $data;
    }

    function opts($event_type) {
        global $EVENTS;
        $opts = [
            'responsive' => true,
            'plugins' => ['title' => ['display' => true, 'text' => $EVENTS[$event_type]]],
            'indexAxis' => 'y',
            'scales' => ['x' => ['stacked' => true], 'y' => ['stacked' => true]],
        ];

        return $opts;

    }

    function config($formatted_data, $opts) {
        return [
            'type' => 'bar',
            'data' => $formatted_data,
            'options' => $opts
        ];
    }

    try {

        $db = new PDO("sqlite:$GA_DB_PATH");
        $res = $db -> query("select * from \"$table_name\" where type=\"$event_type\" and start>=\"$start\" and start<=\"$end\" order by start;");

        $labels = [];
        $public = [];
        $academia = [];
        $government = [];
        $industry = [];
        foreach ($res as $row) {

            $labels[] = $row['start'];
            $public[] = intval($row['public']);
            $academia[] = intval($row['academia']);
            $government[] = intval($row['government']);
            $industry[] = intval($row['industry']);

        }
        $datasets = datasets($public, $academia, $government, $industry); 
        $formatted_data =  format_data($labels, $datasets);
        $opts = opts($event_type);
        $config = config($formatted_data, $opts);

        //$config = config(format_data($labels, datasets($data)), opts());

    }
    catch(PDOException $e) {
        //$chart_data = ["message" => $e->getMessage()];
        $config = ["message" => $e->getMessage()];
    }

    return $config;
}

function gh_events_config($start, $end) {
    global $GA_DB_PATH;
    global $GH_DB_PATH;

    function datasets($ev_values, $gh_values) {
        $ds = [];

        $ds[] = ['type' => 'bar',
                 'label' => 'Event Participants',
                 'data' => $ev_values,
                 'backgroundColor' => '#0A4595',
                 'order' => 1,
                ];

        $ds[] = ['type' => 'line',
                 'tension' => 0.4,
                 'label' => 'UFS Weather Model Repository Views',
                 'data' => $gh_values,
                 'borderColor' => '#00A54F',
                 'backgroundColor' => '#00A54F',
                 'order' => 0,

                ];


        return $ds;
    }

    function format_data($labels, $datasets)
    {
        $data = [
            'labels' => $labels,
            'datasets' => $datasets
        ];
        return $data;
    }

    function opts() {
        $opts = [
            'responsive' => true,
            'plugins' => ['title' => ['display' => true, 'text' => 'GitHub/Events']],
            'indexAxis' => 'x',
            'scales' => ['y' => ['type' => 'logarithmic']],
        ];


        return $opts;

    }

    function config($formatted_data, $opts) {
        return [
            'data' => $formatted_data,
            'options' => $opts
        ];
    }

    try {
        // events data
        $db = new PDO("sqlite:$GA_DB_PATH");
        $res = $db -> query("select * from events;");
        //$res = $db -> query("select * from events where start>=\"$start\" and start<=\"$end\" order by start;");

        $ev_labels = [];
        $ev_names = [];
        $ev_values = [];

        foreach ($res as $row) {
            
            $ev_names[] = $row['name'];
            $ev_labels[] = $row['start'];

            $public = intval($row['public']);
            $academia = intval($row['academia']);
            $government = intval($row['government']);
            $industry = intval($row['industry']);

            $ev_values[] = $public + $academia + $government + $industry;

        }

        // github views
        $db = new PDO("sqlite:$GH_DB_PATH");
        //$start .= 'T00:00:00Z'; $end .= 'T00:00:00Z';
        $start = $ev_labels[0];
        $end = $ev_labels[count($ev_labels)-1];
        $start .= 'T00:00:00Z'; $end .= 'T00:00:00Z';

        $res = $db -> query("select * from \"ufs-community/ufs-weather-model/views\" where timestamp>=\"$start\" and timestamp<=\"$end\" order by timestamp;");

        $gh_labels = [];
        $gh_values = [];
        foreach ($res as $row) {
            
            $gh_labels[] = substr($row['timestamp'], 0, 10);
            $gh_values[] = $row['count'];

        }

        // map of dates to event names
        $ev_labels_map = [];
        foreach($ev_labels as $idx => $lab) {
            $ev_labels_map[$lab] = [$ev_names[$idx], $ev_values[$idx]]; 
        }

        // Add event names to labels on dates where events occurred 
        $labels = [];
        foreach($gh_labels as $idx => $lab) {
            if (array_key_exists($lab, $ev_labels_map)) {
                $labels[] = $lab . ' - ' . $ev_labels_map[$lab][0];
            }
            else {
                $labels[] = $lab;
            }

        }

        $new_ev_values = [];
        foreach($gh_labels as $idx => $lab) {
            if (array_key_exists($lab, $ev_labels_map)) {
                $new_ev_values[] = $ev_labels_map[$lab][1];
            }
            else {
                $new_ev_values[] = 0;
            }

        }


        $datasets = datasets($new_ev_values, $gh_values); 
        $formatted_data =  format_data($labels, $datasets);
        $opts = opts();
        $config = config($formatted_data, $opts);

    }
    catch(PDOException $e) {
        //$chart_data = ["message" => $e->getMessage()];
        $config = ["message" => $e->getMessage()];
    }

    return $config;
}

function get_ga_data($request) {
    $metric = $request['metric'];

    $start = $request->get_param('start');
    $end = $request->get_param('end');


    /****************** Testing Only ********************/
    //$start = '2022-01-25';
    //$end = '2023-03-30';
    /****************** Testing Only ********************/

    if (!$start)
        $start = '1970-01-01';

    if (!$end) 
        $end = '2050-01-01';

    if ($metric == "new_users") {
        $table = "new_users";
        $data = new_users_config($table, $start, $end); 
    }
    else if ($metric == "users_country") {
        $table = "users_country";
        $data = users_country_config($table, $start, $end); 
    }
    else if ($metric == "followers") {
        $table = "followers";
        $data = followers_config($table, $start, $end); 
    }

    else if ($metric == "events") {
        $table = "events";
        $event_type = $request->get_param('type');
        if (!$event_type) {
            $event_type = 'hackathon';
        }
        $data = events_config($table, $event_type, $start, $end); 
    }
    else if ($metric == "ghe") {
        $data = gh_events_config($start, $end); 
    }
    else {
        $data = ['metric' => $metric];
        
    }

    $response = new WP_REST_Response($data);
    $response->set_status(200);

    return $response;
}
