const INITIAL_METRIC = 'new_users';

const DEFAULT_START_DATE = '2022-01-25';
const DEFAULT_END_DATE = new Date().toISOString().substring(0, 10)

const METRICS = [
    {name: 'new_users', title: 'New Users'}, 
    {name: 'social', title: 'Social Media'}, 
    {name: 'users_country', title: 'Users By Country'}, 
    {name: 'hackathons', title: 'Hackathons'}, 
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

    function initData() {
		model.chart_config = MOCK[model.metric]();
    }

    function metricCallback(e) {
        //e.redraw = false;
        model.chart.destroy();
        model.selectedMetric = e.target.value;
        console.log('selected metric = ' + model.selectedMetric)
    }

    function submitCallback(e) {
        model.chart.destroy();
        model.metric = model.selectedMetric;
        console.log('metric = ' + model.metric);
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
        console.log('model.startDate = ' + model.startDate);
        console.log('model.endDate = ' + model.endDate);

		model.chart_config = MOCK[model.metric]();
    }

    function startDateCallback(e) {
        
        model.chart.destroy();
        //model.showDLink = false;
        model.startDate = e.target.value;
        if (! model.startDate )
            model.startDate = DEFAULT_START_DATE;
        
        console.log('model.startDate = ' + model.startDate);
        
    }

    function endDateCallback(e) {
        model.chart.destroy();
        //model.showDLink = false;
        model.endDate = e.target.value;
        if (! model.endDate )
            model.endDate = DEFAULT_END_DATE;

        console.log('model.endDate = ' + model.endDate);
    }

    /************************** View Functions ***********************/
    function selectView(id, name,  lst, callback) {

        let opts = lst.map(function(option) {
            return m("option", {value: option.name}, option.title);
        });

        return m("select", {id: id, name: name, onchange: callback}, opts);
    }

    function formView(id, name, children) {

        return m("form", {id: id, name: name}, children);
    }

    function createChart(vnode) {
        const ctx = vnode.dom.getContext('2d');

        model.chart = new Chart(ctx, model.chart_config);
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






