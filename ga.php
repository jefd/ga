<?php

$VERSION = 'v1.0.0';


$GA_DB_PATH = dirname(__FILE__) . '/ga.db';

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
        $datasets = datasets($data); 
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
        $opts = [
            'responsive' => true,
            'plugins' => ['title' => ['display' => true, 'text' => $event_type]],
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

function get_ga_data($request) {
    $metric = $request['metric'];

    $start = $request->get_param('start');
    $end = $request->get_param('end');


    /****************** Testing Only ********************/
    $start = '2022-01-25';
    $end = '2023-03-30';
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
    else {
        $data = ['metric' => $metric];
        
    }

    $response = new WP_REST_Response($data);
    $response->set_status(200);

    return $response;
}
