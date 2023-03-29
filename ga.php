<?php

$VERSION = 'v1.0.0';


$GA_DB_PATH = dirname(__FILE__) . '/ga.db';

add_shortcode( 'ga', 'ga_dash_board');
function ga_dash_board($atts) {
    global $VERSION;
    
    return <<<EOT
    <div id="dashboard-app"></div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.2.1"></script>
    <script src="https://unpkg.com/mithril@2.2.2/mithril.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/jefd/dash@{$VERSION}/wp-content/themes/hello-elementor-child/js/dash.js"></script>
    EOT;
}


add_action('rest_api_init', function () {
    register_rest_route( 'ga/v1', '/(?P<metric>[a-z-_]+)',array(
        'methods'  => 'GET',
        'callback' => 'get_ga_data'
    ));
});

function get_new_users($table_name, $start, $end) {
    global $GA_DB_PATH;
    try {

        $db = new PDO("sqlite:$GA_DB_PATH");
        //$res = $db -> query("select * from \"$table_name\";");
        //$res = $db -> query("select * from \"$table_name\" where timestamp>=\"2022-08-23\" and timestamp<=\"2022-08-27\";");
        //$start .= 'T00:00:00Z'; $end .= 'T00:00:00Z';
        //$res = $db -> query("select * from \"$table_name\" where timestamp>=\"$start\" and timestamp<=\"$end\" order by timestamp;");
        $res = $db -> query('select * from new_users order by timestamp;');

        $lst = [];
        foreach ($res as $row) {

            $o = Array();

            $o['timestamp'] = $row['timestamp'];
            $o['count'] = intval($row['count']);

            $lst[] = $o;

        }
        $body = json_decode(json_encode(["new_users" => $lst]));
        //$data = get_data($body);
        //$chart_data = format_data($data);
    
    }
    catch(PDOException $e) {
        //$chart_data = ["message" => $e->getMessage()];
        $body = ["message" => $e->getMessage()];
    }

    
    //return $chart_data;
    return $body;
    //return ['data' => 'hi'];
}

function get_ga_data($request) {
    $metric = $request['metric'];

    $start = $request->get_param('start');
    $end = $request->get_param('end');

    if (!$start)
        $start = '1970-01-01';

    if (!$end) 
        $end = '2050-01-01';

    if ($metric == "new_users") {
        //$data = get_view_chart_data($url, $args);
        $data = get_new_users('new_users', $start, $end); 
    }
    else {
        $data = ['metric' => $metric];
        
    }

    

    //$data = ['metric' => $metric];

    $response = new WP_REST_Response($data);
    $response->set_status(200);

    return $response;
}
