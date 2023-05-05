<?php

$GA_VERSION = 'v1.0.0';


$GA_DB_PATH = dirname(__FILE__) . '/ga.db';
$GH_DB_PATH = dirname(__FILE__) . '/metrics.db';

$MAX = 20;

add_shortcode( 'ga', 'ga_dash_board');
function ga_dash_board($atts) {
    global $VERSION;
    
    return <<<EOT
    <div id="dashboard-app"></div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.2.1"></script>
    <script src="https://unpkg.com/mithril@2.2.2/mithril.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/jefd/ga@{$GA_VERSION}/js/ga.js"></script>
    EOT;
}



add_action('rest_api_init', function () {
    register_rest_route( 'ga/v1', '/event-types',array(
        'methods'  => 'GET',
        'callback' => 'get_event_types'
    ));
});

add_action('rest_api_init', function () {
    register_rest_route( 'ga/v1', '/(?P<metric>[a-z-_]+)',array(
        'methods'  => 'GET',
        'callback' => 'get_ga_data'
    ));
});


function isDate($value) {

    if (!$value) {
        return false;
    }

    try {
        new \DateTime($value);
        return true;
    } catch (\Exception $e) {
        return false;
    }
}



function new_labels($labels_list) {
    $l = [];
    foreach ($labels_list as $labels) {
        foreach ($labels as $lab) {
            if (! in_array($lab, $l)) 
                $l[] = $lab;
            
        }
    }
    sort($l); 
    return $l;
}


function new_data($new_labels, $labels, $data) {
    $new_dat = [];
    
    foreach ($new_labels as $lab) {
        $idx = array_search($lab, $labels);
        if ($idx !== false)
            $new_dat[] = $data[$idx];
        else
            $new_dat[] = 0;

    }
    return $new_dat;
}

function query_events($q) {
    global $GA_DB_PATH;
    try {
        $db = new PDO("sqlite:$GA_DB_PATH");

        $res = $db -> query($q);

        $lst = [];
        foreach ($res as $row) {
            $o = Array();

            $o['event_type_name'] = $row['type_name'];
            $o['name'] = $row['name'];
            $o['start'] = $row['start'];
            $o['end'] = $row['end'];
            $o['public'] = $row['public'];
            $o['academia'] = $row['academia'];
            $o['government'] = $row['government'];
            $o['industry'] = $row['industry'];

            $lst[] = $o;

        }
        
        $response = new WP_REST_Response($lst);
        $response->set_status(200);

        return $response;

    }
    catch(PDOException $e) {
        return new WP_Error( 'error', $e->getMessage(), array('status' => 404) );
    }
}

function get_events_sql($start, $end, $event_type_id=null) {
    if ($event_type_id) {
        $q = <<<QUERY
        select event_type.type_name, event.name, event.start, event.end, 
        event.public, event.academia, event.government, event.industry 
        from event 
        inner join event_type 
        on event_type.type_id = event.event_type_id 
        and 
        event_type.type_id = $event_type_id 
        where event.start >= "$start" and event.end <= "$end"
        order by start;
        QUERY;
    }
    else {
        $q = <<<QUERY
        select event_type.type_name, event.name, event.start, event.end, 
        event.public, event.academia, event.government, event.industry 
        from event 
        inner join event_type 
        on event_type.type_id = event.event_type_id 
        where event.start >= "$start" and event.end <= "$end"
        order by start;
        QUERY;
    }

    return $q;

    
}

function get_events($request) {
    global $GA_DB_PATH;

    $start = $request->get_param('start');
    $end = $request->get_param('end');

    if (!$start)
        $start = '1970-01-01';

    if (!$end) 
        $end = '2050-01-01';

    $event_type_id = intval($request['id']);

    $q = <<<QUERY
    select event_type.type_name, event.name, event.start, event.end, 
    event.public, event.academia, event.government, event.industry 
    from event 
    inner join event_type 
    on event_type.type_id = event.event_type_id 
    and 
    event_type.type_id = $event_type_id 
    where event.start >= "$start" and event.end <= "$end"
    order by start;
    QUERY;

    return query_events($q);

}

function get_all_events($request) {
    global $GA_DB_PATH;

    $start = $request->get_param('start');
    $end = $request->get_param('end');

    if (!$start)
        $start = '1970-01-01';

    if (!$end) 
        $end = '2050-01-01';

    $q = <<<QUERY
    select event_type.type_name, event.name, event.start, event.end, 
    event.public, event.academia, event.government, event.industry 
    from event 
    inner join event_type 
    on event_type.type_id = event.event_type_id 
    where event.start >= "$start" and event.end <= "$end"
    order by start;
    QUERY;

    return query_events($q);

}

function get_event_types($response) {
    global $GA_DB_PATH;

    try {
        $db = new PDO("sqlite:$GA_DB_PATH");

        $res = $db -> query('select * from event_type order by type_id;');

        $lst = [];
        foreach ($res as $row) {

            $o = Array();

            $o['id'] = intval($row['type_id']);
            $o['name'] = $row['type_name'];

            $lst[] = $o;

        }

        $response = new WP_REST_Response($lst);
        $response->set_status(200);

        return $response;

    }
    catch(PDOException $e) {
        return new WP_Error( 'error', $e->getMessage(), array('status' => 404) );
    }
    
}


function accumulate($data) {
    $d = [];
    $acc = 0;
    foreach($data as $value) {
        $acc += $value;
        $d[] = $acc;
    }
    return $d;

}

function filter($labels, $data, $n) {

    $filtered_labels = [];
    $filtered_data = [];

    foreach ($labels as $idx =>  $value) {
        if ($idx % $n === 0 || ! isDate($value)) {
            $filtered_labels[] = $value;
            $filtered_data[] = $data[$idx];
        }
    }

    return [$filtered_labels, $filtered_data];

}

function sample_interval($lst) {
    //return 1;
    global $MAX;
    $tot = count($lst);
    $interval =  intval($tot/$MAX);
    if ($interval < 1)
        $interval = 1;
    return $interval;
}
 

function new_users_config($table_name, $start, $end) {
    global $GA_DB_PATH;

    function datasets($data) {
        $ds = [
            'type' => 'bar',
            'label' => 'Epic Site New Users (cumulative)',
            'data' => $data,
            'backgroundColor' => '#0099D8',
            'borderRadius' => 50,
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
            'indexAxis' => 'x',
            'scales' => ['x' => ['beginAtZero' => true]],
        ];

        return $opts;

    }

    function config($formatted_data, $opts) {
        return [
            //'type' => 'bar',
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

        $n = sample_interval($labels);
        //$n = 15;
        
        $accumulated_data = accumulate($data);
        $filtered = filter($labels, $accumulated_data, $n);
        
        $filtered_labels = $filtered[0];
        $filtered_data = $filtered[1];

        $datasets = datasets($filtered_data); 
        $formatted_data =  format_data($filtered_labels, $datasets);
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
            'type' => 'bar',
            'label' => 'Users by Country',
            'data' => $data,
            'borderRadius' => 50,
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
            //'type' => 'doughnut',
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

        $ds[] = ['type' => 'bar',
                 'label' => 'Twitter',
                 'data' => $twitter,
                 'backgroundColor' => '#0099D8',
                 'borderRadius' => 50,
                ];

        $ds[] = ['type' => 'bar',
                 'label' => 'Instagram',
                 'data' => $ig,
                 'backgroundColor' => '#D97200',
                 'borderRadius' => 50,
                ];

        $ds[] = ['type' => 'bar',
                 'label' => 'Facebook',
                 'data' => $fb,
                 'backgroundColor' => '#00A54F',
                 'borderRadius' => 50,
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
            'indexAxis' => 'x',
            'scales' => ['x' => ['stacked' => true], 'y' => ['stacked' => true]],
        ];

        return $opts;

    }

    function config($formatted_data, $opts) {
        return [
            //'type' => 'bar',
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


        /******************************************************/
        //$n = sample_interval($labels);
        $n = sample_interval($labels);
        
        
        $filtered_twitter = filter($labels, $twitter, $n);
        $filtered_instagram = filter($labels, $instagram, $n);
        $filtered_facebook = filter($labels, $facebook, $n);
        
        $filtered_labels = $filtered_twitter[0];
        $filtered_twitter_data = $filtered_twitter[1];
        $filtered_instagram_data = $filtered_instagram[1];
        $filtered_facebook_data = $filtered_facebook[1];
        /******************************************************/

        //$datasets = datasets($twitter, $instagram, $facebook); 
        $datasets = datasets($filtered_twitter_data, $filtered_instagram_data, $filtered_facebook_data); 
        //$formatted_data =  format_data($labels, $datasets);
        $formatted_data =  format_data($filtered_labels, $datasets);
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

function events_config($event_type_id, $start, $end) {
    global $GA_DB_PATH;

    function datasets($gp, $ac, $gov, $ind) {
        $ds = [];

        $ds[] = ['type' => 'bar',
                 'label' => 'General Public',
                 'data' => $gp,
                 'backgroundColor' => '#0A4595',
                 'borderRadius' => 50,
                ];

        $ds[] = ['type' => 'bar',
                 'label' => 'Academia',
                 'data' => $ac,
                 'backgroundColor' => '#0099D8',
                 'borderRadius' => 50,
                ];

        $ds[] = ['type' => 'bar',
                 'label' => 'Government',
                 'data' => $gov,
                 'backgroundColor' => '#D97200',
                 'borderRadius' => 50,
                ];

        $ds[] = ['type' => 'bar',
                 'label' => 'Industry',
                 'data' => $ind,
                 'backgroundColor' => '#00A54F',
                 'borderRadius' => 50,
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

    function opts($event_type_name) {
        $opts = [
            'responsive' => true,
            'plugins' => ['title' => ['display' => true, 'text' => $event_type_name . ' Participants']],
            'indexAxis' => 'x',
            'scales' => ['x' => ['stacked' => true], 'y' => ['stacked' => true]],
        ];

        return $opts;

    }

    function config($formatted_data, $opts) {
        return [
            //'type' => 'bar',
            'data' => $formatted_data,
            'options' => $opts
        ];
    }

    try {

        $db = new PDO("sqlite:$GA_DB_PATH");
        //$res = $db -> query("select * from \"$table_name\" where type=\"$event_type\" and start>=\"$start\" and start<=\"$end\" order by start;");
        $res = $db -> query(get_events_sql($start, $end, $event_type_id));

        $labels = [];
        $event_names = [];
        $public = [];
        $academia = [];
        $government = [];
        $industry = [];
        foreach ($res as $row) {

            $labels[] = $row['start'];
            $event_names[] = $row['name'];
            $public[] = intval($row['public']);
            $academia[] = intval($row['academia']);
            $government[] = intval($row['government']);
            $industry[] = intval($row['industry']);

            $event_type_name = $row['type_name'];

        }

        
        $new_labels = [];
        foreach($labels as $idx => $lab) {
            //$new_labels[] = $lab . ' - ' . $event_names[$idx];
            $new_labels[] = $event_names[$idx] . ' - ' . $lab;
        }
         

        $datasets = datasets($public, $academia, $government, $industry); 
        $formatted_data =  format_data($new_labels, $datasets);
        $opts = opts($event_type_name);
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
                 'borderRadius' => 50,
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
            //'scales' => ['y' => ['type' => 'logarithmic']],
            //'scales' => ['x' => ['ticks' => ['autoSkip' => false]]],
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
        //$res = $db -> query("select * from events;");
        //$res = $db -> query("select * from events where start>=\"$start\" and start<=\"$end\" order by start;");
        $res = $db -> query(get_events_sql($start, $end));

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
            //$ev_values[] = 750;

        }

        // github views
        $db = new PDO("sqlite:$GH_DB_PATH");
        //$start .= 'T00:00:00Z'; $end .= 'T00:00:00Z';
        //$start = $ev_labels[0];
        //$end = $ev_labels[count($ev_labels)-1];
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

        $new_labels = new_labels([$ev_labels, $gh_labels]);

        // Add event names to labels on dates where events occurred 
        $labels = [];
        foreach($new_labels as $idx => $lab) {
            if (array_key_exists($lab, $ev_labels_map)) {
                //$labels[] = $lab . ' - ' . $ev_labels_map[$lab][0];
                $labels[] = $ev_labels_map[$lab][0] . ' - ' . $lab;
            }
            else {
                $labels[] = $lab;
            }

        }

        $new_ev_values = new_data($new_labels, $ev_labels, $ev_values);
        $new_gh_values = new_data($new_labels, $gh_labels, $gh_values);

        
        /******************************************************/
        // Filter data
        $n = sample_interval($labels);
        //$n = 15;
        $filtered_gh = filter($labels, $new_gh_values, $n);
        $filtered_ev = filter($labels, $new_ev_values, $n);

        $filtered_labels = $filtered_gh[0];
        $filtered_gh_values = $filtered_gh[1];
        $filtered_ev_values = $filtered_ev[1];
        /******************************************************/
        

        $datasets = datasets($filtered_ev_values, $filtered_gh_values); 
        $formatted_data =  format_data($filtered_labels, $datasets);
        $opts = opts();
        $config = config($formatted_data, $opts);

    }
    catch(PDOException $e) {
        //$chart_data = ["message" => $e->getMessage()];
        $config = ["message" => $e->getMessage()];
    }

    return $config;
}

function impressions_config($start, $end) {
    global $GA_DB_PATH;

    function datasets($imp_values, $pv_values) {
        $ds = [];

        $ds[] = ['type' => 'bar',
                 'label' => 'Twitter Impressions',
                 'data' => $imp_values,
                 'backgroundColor' => '#0A4595',
                 'borderRadius' => 50,
                 'order' => 1,
                ];

        $ds[] = ['type' => 'line',
                 'tension' => 0.4,
                 'label' => 'Epic Page Views',
                 'data' => $pv_values,
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
            'plugins' => ['title' => ['display' => true, 'text' => 'Page Views/Impressions']],
            'indexAxis' => 'x',
            //'scales' => ['y' => ['type' => 'logarithmic']],
            //'scales' => ['y' => ['type' => 'logarithmic'],
            //             'x' => ['ticks' => ['autoSkip' => false]],
            //            ],
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
        // impressions data
        $db = new PDO("sqlite:$GA_DB_PATH");
        //$res = $db -> query("select * from twitter;");
        $res = $db -> query("select * from twitter where timestamp>=\"$start\" and timestamp<=\"$end\" order by timestamp;");
        //$res = $db -> query("select * from events where start>=\"$start\" and start<=\"$end\" order by start;");

        $imp_labels = [];
        $imp_values = [];

        foreach ($res as $row) {
            
            $imp_labels[] = $row['timestamp'];

            $imp_values[] = intval($row['impressions']);

        }


        // page views
        // $db = new PDO("sqlite:$GH_DB_PATH");
        //$start .= 'T00:00:00Z'; $end .= 'T00:00:00Z';
        //$start = $imp_labels[0];
        //$end = $imp_labels[count($imp_labels)-1];

        $res = $db -> query("select * from page_views where timestamp>=\"$start\" and timestamp<=\"$end\" order by timestamp;");

        $pv_labels = [];
        $pv_values = [];
        foreach ($res as $row) {
            
            $pv_labels[] = $row['timestamp'];
            $pv_values[] = intval($row['count']);

        }

        // map of dates to impression values
        $imp_labels_map = [];
        foreach($imp_labels as $idx => $lab) {
            $imp_labels_map[$lab] = $imp_values[$idx]; 
        }


        $new_imp_values = [];
        foreach($pv_labels as $idx => $lab) {
            if (array_key_exists($lab, $imp_labels_map)) {
                $new_imp_values[] = $imp_labels_map[$lab];
            }
            else {
                $new_imp_values[] = 0;
            }

        }


        $datasets = datasets($new_imp_values, $pv_values); 
        $formatted_data =  format_data($pv_labels, $datasets);
        $opts = opts();
        $config = config($formatted_data, $opts);

    }
    catch(PDOException $e) {
        //$chart_data = ["message" => $e->getMessage()];
        $config = ["message" => $e->getMessage()];
    }

    return $config;
}


function all_config($start, $end) {
    global $GA_DB_PATH;
    global $GH_DB_PATH;

    function datasets($ev_values, $imp_values, $pv_values, $ghv_values, $ghc_values) {
    //$datasets = datasets($new_ev_values, $new_imp_values, $pv_values, $ghv_values, $ghc_values); 
    
        $ds = [];

        $ds[] = ['type' => 'bar',
                 'label' => 'Event Participants',
                 'data' => $ev_values,
                 'backgroundColor' => '#0A4595',
                 'borderRadius' => 50,
                 'order' => 1,
                ];

        

        /*
        $ds[] = ['type' => 'bar',
                 'label' => 'Twitter Impressions',
                 'data' => $imp_values,
                 'backgroundColor' => '#6983CE',
                 'borderRadius' => 50,
                 'order' => 1,
                ];
         */
         

        $ds[] = ['type' => 'line',
                 'tension' => 0.4,
                 'label' => 'Epic Page Views',
                 'data' => $pv_values,
                 'borderColor' => '#00A54F',
                 'backgroundColor' => '#00A54F',
                 'order' => 0,

                ];

        $ds[] = ['type' => 'line',
                 'tension' => 0.4,
                 'label' => 'UFS Weather Model Repository Views',
                 'data' => $ghv_values,
                 'borderColor' => '#3B7877',
                 'backgroundColor' => '#3B7877',
                 'order' => 0,

                ];

        $ds[] = ['type' => 'line',
                 'tension' => 0.4,
                 'label' => 'UFS Weather Model Repository Clones',
                 'data' => $ghc_values,
                 'borderColor' => '#FF0000',
                 'backgroundColor' => '#FF0000',
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
            'plugins' => ['title' => ['display' => true, 'text' => 'All']],
            'indexAxis' => 'x',
            //'scales' => ['y' => ['type' => 'logarithmic']],
            //'scales' => ['x' => ['ticks' => ['autoSkip' => false]]],
            //'scales' => ['y' => ['type' => 'logarithmic'],
            //           'x' => ['ticks' => ['autoSkip' => false]],
            //          ],

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
        //$res = $db -> query("select * from events;");
        //$res = $db -> query("select * from events where start>=\"$start\" and start<=\"$end\" order by start;");
        $res = $db -> query(get_events_sql($start, $end));

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


        // impressions data
        //$db = new PDO("sqlite:$GA_DB_PATH");
        //$res = $db -> query("select * from twitter;");
        $res = $db -> query("select * from twitter where timestamp>=\"$start\" and timestamp<=\"$end\" order by timestamp;");
        //$res = $db -> query("select * from events where start>=\"$start\" and start<=\"$end\" order by start;");

        $imp_labels = [];
        $imp_values = [];

        foreach ($res as $row) {
            
            $imp_labels[] = $row['timestamp'];

            $imp_values[] = intval($row['impressions']);

        }

        //$start = min($ev_labels[0], $imp_labels[0]);
        //$end = max($ev_labels[count($ev_labels)-1], $imp_labels[count($imp_labels)-1]);

        // page views
        $res = $db -> query("select * from page_views where timestamp>=\"$start\" and timestamp<=\"$end\" order by timestamp;");

        $pv_labels = [];
        $pv_values = [];
        foreach ($res as $row) {
            
            $pv_labels[] = $row['timestamp'];
            $pv_values[] = intval($row['count']);

        }

        // github views
        $db = new PDO("sqlite:$GH_DB_PATH");

        //$start = min($ev_labels[0], $imp_labels[0]);
        //$end = max($ev_labels[count($ev_labels)-1], $imp_labels[count($imp_labels)-1]);
        $start .= 'T00:00:00Z'; $end .= 'T00:00:00Z';

        $res = $db -> query("select * from \"ufs-community/ufs-weather-model/views\" where timestamp>=\"$start\" and timestamp<=\"$end\" order by timestamp;");

        $ghv_labels = [];
        $ghv_values = [];
        foreach ($res as $row) {
            
            $ghv_labels[] = substr($row['timestamp'], 0, 10);
            $ghv_values[] = $row['count'];

        }

        // github clones 
        //$db = new PDO("sqlite:$GH_DB_PATH");

        $res = $db -> query("select * from \"ufs-community/ufs-weather-model/clones\" where timestamp>=\"$start\" and timestamp<=\"$end\" order by timestamp;");

        $ghc_labels = [];
        $ghc_values = [];
        foreach ($res as $row) {
            
            $ghc_labels[] = substr($row['timestamp'], 0, 10);
            $ghc_values[] = $row['count'];

        }


        // map of dates to [event_names, event_values]
        $ev_labels_map = [];
        foreach($ev_labels as $idx => $lab) {
            $ev_labels_map[$lab] = [$ev_names[$idx], $ev_values[$idx]]; 
        }

        $new_labels = new_labels([$ev_labels, $imp_labels, $pv_labels, $ghv_labels, $ghc_labels]);

        $new_ev_values = new_data($new_labels, $ev_labels, $ev_values);
        $new_imp_values = new_data($new_labels, $imp_labels, $imp_values);
        $new_pv_values = new_data($new_labels, $pv_labels, $pv_values);
        $new_ghv_values = new_data($new_labels, $ghv_labels, $ghv_values);
        $new_ghc_values = new_data($new_labels, $ghc_labels, $ghc_values);

        // Add event names to labels on dates where events occurred 
        $labels = [];
        foreach($new_labels as $idx => $lab) {
            if (array_key_exists($lab, $ev_labels_map)) {
                //$labels[] = $lab . ' - ' . $ev_labels_map[$lab][0];
                $labels[] = $ev_labels_map[$lab][0] . ' - ' . $lab;
            }
            else {
                $labels[] = $lab;
            }

        }

        //$datasets = datasets($new_ev_values, $gh_values); 

        /******************************************************/
        // Filter data
        //
        $n = sample_interval($labels);
        
        $filtered_ghv = filter($labels, $new_ghv_values, $n);
        $filtered_ghc = filter($labels, $new_ghc_values, $n);
        $filtered_pv = filter($labels, $new_pv_values, $n);
        $filtered_ev = filter($labels, $new_ev_values, $n);
        $filtered_imp = filter($labels, $new_imp_values, $n);

        $filtered_labels = $filtered_pv[0];
        $filtered_ghv_values = $filtered_ghv[1];
        $filtered_ghc_values = $filtered_ghc[1];
        $filtered_pv_values = $filtered_pv[1];
        $filtered_ev_values = $filtered_ev[1];
        $filtered_imp_values = $filtered_imp[1];
        /******************************************************/

        
        //$datasets = datasets($new_ev_values, $new_imp_values, $new_pv_values, $new_ghv_values, $new_ghc_values); 
        $datasets = datasets($filtered_ev_values, $filtered_imp_values, $filtered_pv_values, $filtered_ghv_values, $filtered_ghc_values); 
        //$formatted_data =  format_data($labels, $datasets);
        $formatted_data =  format_data($filtered_labels, $datasets);
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
        $event_type_id = intval($request->get_param('type'));
        if (!$event_type_id) {
            $event_type_id = 1;
        }
        $data = events_config($event_type_id, $start, $end); 
        //$data = ['type' => $event_type_id, 'start' => $start, 'end' => $end];
    }

    else if ($metric == "ghe") {
        $data = gh_events_config($start, $end); 
    }

    else if ($metric == "impressions") {
        $data = impressions_config($start, $end); 
    }

    else if ($metric == "all") {
        $data = all_config($start, $end); 
    }

    else {
        $data = ['metric' => $metric];
        
    }

    $response = new WP_REST_Response($data);
    $response->set_status(200);

    return $response;
}
