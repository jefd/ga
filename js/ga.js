//const BASE_URL =  "https://epic.noaa.gov";
const BASE_URL =  "https://rayv-webix4.jpl.nasa.gov/devel/ep";
//const BASE_URL =  "";
const TEST_URL = "https://jsonplaceholder.typicode.com/todos/1";

const API_PATH = "/wp-json/ga/v1";

const INITIAL_METRIC = 'new_users';

const DEFAULT_START_DATE = '2022-01-25';
const DEFAULT_END_DATE = new Date().toISOString().substring(0, 10)



function getMetrics(eventTypes) {
    let met0 = [
        {name: 'new_users', type: 'web', title: 'New Users'}, 
        {name: 'users_country', type: 'web', title: 'Users By Country'}, 
        {name: 'followers', type: 'social', title: 'Social Media Followers'}, 
    ];

    let met1 = [
        //{name: 'mixed', type: 'mixed', title: 'Events / GitHub Views'}, 
        //{name: 'impressions', type: 'imp', title: 'Twitter Impressions / Page Views'}, 
        {name: 'all', type: 'all', title: 'All'}, 
    ];

    let n = 0;
    for (et of eventTypes) {
        //let nm = et.name.replace(/\s+/g, '').toLowerCase();
        met0.push(
            {name: `event${n}`, id: et.id, type: 'event', title: et.name}, 
            //{name: nm, id: et.id, type: 'event', title: et.name}, 
        );
        n += 1;
    }

    return met0.concat(met1);
}

function Dash(initialVnode) {

    let model = {
        metrics: null,
        eventTypes: null,
        selectedMetric: INITIAL_METRIC,
        metric: INITIAL_METRIC,
        chart_config: null,
        chart: null,
	    loaded: false,	
        error: "",
        startDate: DEFAULT_START_DATE,
        endDate: DEFAULT_END_DATE,
        showDatePicker: true,
    };

    function get_metric(name) {
        for (metric of model.metrics) {
            if (metric['name'] === name) {
                return metric
            }
        }
    }

    function isDate(s) {
        d = Date.parse(s.trim());
        return (isNaN(d) ? false : true);
    }

    function getDefaultStartDate() {
        let d = new Date();
        let y = d.getDate() - 30;
        d.setDate(y);
        return d.toISOString().substring(0, 10);
    }

    function ticks_callback(val, index) {

        let label = this.getLabelForValue(val);

        //if( index % 5 === 0 || label.trim().length > 10) {
        if(label.trim().length > 10) {
            return label;
        }
        else
            return '';

    }

    function ticks_callback0(val, index) {

        let label = this.getLabelForValue(val);
        return label;

    }

    function getUrl() {
        let current_metric = get_metric(model.metric);

        if (current_metric['type'] === 'event') {
            const event_id = current_metric['id'];

            return `${BASE_URL}${API_PATH}/events/?start=${model.startDate}&end=${model.endDate}&type=${event_id}`;
            //return `${BASE_URL}${API_PATH}/events/${event_id}/?start=${model.startDate}&end=${model.endDate}`;
        }
        else if (current_metric['type'] === 'mixed')
            //return `${BASE_URL}${API_PATH}/ghe/`;
            return `${BASE_URL}${API_PATH}/ghe/?start=${model.startDate}&end=${model.endDate}`;

        else if (current_metric['type'] === 'imp')
            //return `${BASE_URL}${API_PATH}/ghe/`;
            return `${BASE_URL}${API_PATH}/impressions/?start=${model.startDate}&end=${model.endDate}`;

        else if (current_metric['type'] === 'all')
            //return `${BASE_URL}${API_PATH}/ghe/`;
            return `${BASE_URL}${API_PATH}/all/?start=${model.startDate}&end=${model.endDate}`;

        else {
            return `${BASE_URL}${API_PATH}/${model.metric}/?start=${model.startDate}&end=${model.endDate}`;
        }

    }


    function initData() {
        model.loaded = false;
        let url = `${BASE_URL}${API_PATH}/event-types`;
		headers = {};
		console.log("**** sending request **** " + url)
		return m.request({
			method: "GET",
			url: url,
			headers: headers,
		})
		.then(function(data){
            model.eventTypes = data
            model.metrics = getMetrics(data);
            //model.chart_config = MOCK[model.metric]();
            model.loaded = true;
            console.log("**** RESPONSE **** ", data);
            //console.log("**** model **** ", model);
            let url = getUrl();
            updateData(url);
		})
        .catch(function(e) {
            model.error = "Error loading data";
        })
    }

	function updateData(url) {
        model.loaded = false;
		headers = {};
		console.log("**** sending request **** " + url)
		return m.request({
			method: "GET",
			url: url,
			headers: headers,
		})
		.then(function(data){
            let current_metric = get_metric(model.metric);
            
            // set autoSkip to true if dataset is larger than 100
            let skip = (data.data.labels.length > 75);

            let cb = skip ? ticks_callback0 : ticks_callback;

            if (current_metric['type'] === 'mixed' || 
                current_metric['type'] === 'all') {
                // set scales.x.ticks to autoskip = false
                // set ticks callback
                
                data.options.scales = {
                    x: {ticks: {autoSkip: skip, callback: cb} },
                    //y: {type: 'logarithmic'},
                };
            
            }

            model.chart_config = data
            
            //model.chart_config = MOCK[model.metric]();
            model.loaded = true;
            console.log("**** RESPONSE **** ", data);
		})
        .catch(function(e) {
            model.error = "Error loading data";
        })
	}


    function metricCallback(e) {
        //e.redraw = false;
        model.selectedMetric = e.target.value;
        
        if (model.selectedMetric === 'users_country')
            model.showDatePicker = false
        else
            model.showDatePicker = true
        
    }

    function submitCallback(e) {
        // destroy chart and set to null to trigger 
        // chart re-creation.
        model.chart.destroy();
        model.chart = null;
        model.metric = model.selectedMetric;
        //model.chart.update();
        //
        if (! model.startDate )
            model.startDate = DEFAULT_START_DATE;

        if (! model.endDate )
            model.endDate = DEFAULT_END_DATE;

         

        // Date sanity checks
        if (model.startDate < DEFAULT_START_DATE)
            model.startDate = DEFAULT_START_DATE;

        if (model.endDate > DEFAULT_END_DATE)
            model.endDate = DEFAULT_END_DATE;

        if (model.startDate >= model.endDate) {
            model.startDate = DEFAULT_START_DATE;
            model.endDate = DEFAULT_END_DATE;
        }

        let url = getUrl();
        updateData(url);
    }

    function startDateCallback(e) {
        
        //model.chart.destroy();
        //model.showDLink = false;
        model.startDate = e.target.value;
        //if (! model.startDate )
        //    model.startDate = DEFAULT_START_DATE;
    }

    function endDateCallback(e) {
        //model.chart.destroy();
        //model.showDLink = false;
        model.endDate = e.target.value;
        //if (! model.endDate )
        //    model.endDate = DEFAULT_END_DATE;
    }

    /************************** View Functions ***********************/
    function selectView(id, name,  lst, callback) {

        /*
        let opts = lst.map(function(option) {
            return m("option", {value: option.name}, option.title);
        });
        */
        let opts = lst.map(function(option) {
            if (option.name === model.selectedMetric)
                return m("option", {value: option.name, selected: true}, option.title);
            else
                return m("option", {value: option.name}, option.title);
        });

        return m("select", {id: id, name: name, onchange: callback}, opts);
    }

    function formView(id, name, children) {

        return m("form", {id: id, name: name}, children);
    }

    function createChart(vnode) {
        if (model.chart === null) {
            const ctx = vnode.dom.getContext('2d');
            model.chart = new Chart(ctx, model.chart_config);
        }
    }

    function chartView(vnode) {
        return m("canvas#chart", {oncreate: createChart, onupdate: createChart});
    }

    function buttonView(label, callback){
        return m("button", {type: "button", onclick: callback}, label);
    }

    function datePickerView(name, value, start, end, cb) {
        let st = {visibility: model.showDatePicker ? "visible" : "hidden"};
        //let st = {display: model.showDatePicker ? "inline" : "none"};
        let attrs = {type: "date",
            id: name, 
            name: name, 
            value: value, 
            min: start, 
            max: end, 
            onchange: cb,
            style: st,
        }
        return m("input", attrs);
    }


    function view(vnode) {

        if (! model.loaded) {
            return m('div.loader');
        }

        let metricLabel = m("label", {for: 'metric-select'}, "Metric");
        let metricSelect = selectView('metric-select', 'metric-select', model.metrics, metricCallback);

        let btn = buttonView('Submit', submitCallback);


        let st = {visibility: model.showDatePicker ? "visible" : "hidden"};
        let startLabel = m("label", {for: 'start', style: st}, "Start Date");
        let endLabel = m("label", {for: 'end', style: st}, "End Date");
        let startDp = datePickerView('start', model.startDate, DEFAULT_START_DATE, DEFAULT_END_DATE, startDateCallback);
        let endDp = datePickerView('end', model.endDate, DEFAULT_START_DATE, DEFAULT_END_DATE, endDateCallback);

        let frm = formView('dash-form', 'dash-form', [metricLabel, metricSelect, startLabel, startDp, endLabel, endDp, btn]);

        let dv = null;
        dv = chartView(vnode);

        return [
            frm, 
            dv,
        ];


    }
    /*****************************************************************/

	function init(vnode){
        // let url = "https://jsonplaceholder.typicode.com/todos/1";
        //let url = "https://rayv-webix4.jpl.nasa.gov/devel/ep/wp-json/dash/v1/ufs-weather-model/views/";

        return initData();
	}

    return {
        oninit: init,
        view: view,
        }
}

let root = document.getElementById('ga-app');


m.mount(root, Dash);






