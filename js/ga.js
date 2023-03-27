const INITIAL_METRIC = 'new_users';

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
    };

    function initData() {
		//model.chart_config = new_users_config;
		model.chart_config = get_new_users_config();
        console.log("**** CHART CONFIG **** ", model.chart_config);
    }

    function metricCallback(e) {
        model.selectedMetric = e.target.value;
        console.log('selected metric = ' + model.selectedMetric)
    }

    function submitCallback(e) {
        model.metric = model.selectedMetric;
        console.log('metric = ' + model.metric);
		//model.chart_config = users_country_pie_config;
		model.chart_config = get_users_country_config();
        console.log("**** CHART CONFIG **** ", model.chart_config);
        console.log("**** CHART **** ", model.chart);
        //model.chart.update();
        model.chart.destroy();
    }

    /************************** View Functions ***********************/
    function selectView(id, name,  repo_titles, callback) {


        let opts = repo_titles.map(function(option) {

            if (option.hasOwnProperty('owner')) {
                return m("option", {value: `${option.owner}/${option.name}`}, option.title);
            }
            else {
                return m("option", {value: option.name}, option.title);
            }

        });

        return m("select", {id: id, name: name, onchange: callback}, opts);
    }

    function formView(id, name, children) {

        return m("form", {id: id, name: name}, children);
    }


    function createChart(vnode) {
        console.log('In createChart function');
        const ctx = vnode.dom.getContext('2d');

        model.chart = new Chart(ctx, model.chart_config);
    }

    function chartView(vnode) {
        console.log('In chartView function');
        return m("canvas#chart", {oncreate: createChart, onupdate: createChart});
    }

    function buttonView(label, callback){
        return m("button", {type: "button", onclick: callback}, label);
    }


    function view(vnode) {
        console.log('In view function');

        let metricLabel = m("label", {for: 'metric-select'}, "Metric");
        let metricSelect = selectView('metric-select', 'metric-select', METRICS, metricCallback);

        let btn = buttonView('Submit', submitCallback);


        let frm = formView('dash-form', 'dash-form', [metricLabel, metricSelect, btn]);

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






