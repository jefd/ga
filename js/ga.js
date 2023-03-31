//const BASE_URL =  "https://epic.noaa.gov";
const BASE_URL =  "https://rayv-webix4.jpl.nasa.gov/devel/ep";
//const BASE_URL =  "";
const TEST_URL = "https://jsonplaceholder.typicode.com/todos/1";

const API_PATH = "/wp-json/ga/v1";

const INITIAL_METRIC = 'new_users';

const DEFAULT_START_DATE = '2022-01-25';
const DEFAULT_END_DATE = new Date().toISOString().substring(0, 10)

const METRICS = [
    {name: 'new_users', type: 'web', title: 'New Users'}, 
    {name: 'users_country', type: 'web', title: 'Users By Country'}, 
    {name: 'followers', type: 'social', title: 'Social Media Followers'}, 
    {name: 'hackathon', type: 'event', title: 'Hackathon Participants'}, 
    {name: 'codesprint', type: 'event', title: 'Code Sprint Participants'}, 
    {name: 'codefest', type: 'event', title: 'Code Fest Participants'}, 
];

function Dash(initialVnode) {

    let model = {
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
        for (metric of METRICS) {
            if (metric['name'] === name) {
                return metric
            }
        }
    }

    function getUrl() {
        let current_metric = get_metric(model.metric);

        if (current_metric['type'] === 'event') {
            return `${BASE_URL}${API_PATH}/events/?start=${model.startDate}&end=${model.endDate}&type=${model.metric}`;
        }
        else {
            return `${BASE_URL}${API_PATH}/${model.metric}/?start=${model.startDate}&end=${model.endDate}`;
        }

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
            model.chart_config = data
            //model.chart_config = MOCK[model.metric]();
            model.loaded = true;
            console.log("**** RESPONSE **** ", data);
		})
        .catch(function(e) {
            model.error = "Error loading data";
        })
	}

    function initData() {
        let url = getUrl();
        updateData(url);
    }

    function metricCallback(e) {
        //e.redraw = false;
        model.selectedMetric = e.target.value;
    }

    function submitCallback(e) {
        // destroy chart and set to null to trigger 
        // chart re-creation.
        model.chart.destroy();
        model.chart = null;
        model.metric = model.selectedMetric;
        //model.chart.update();

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
        if (! model.startDate )
            model.startDate = DEFAULT_START_DATE;
    }

    function endDateCallback(e) {
        //model.chart.destroy();
        //model.showDLink = false;
        model.endDate = e.target.value;
        if (! model.endDate )
            model.endDate = DEFAULT_END_DATE;
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
        let metricSelect = selectView('metric-select', 'metric-select', METRICS, metricCallback);

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






