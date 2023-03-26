const users_country_pie_data = {
  labels: [
    'United States',
    'China',
    'India',
    'United Kingdom',
    'Germany',
    'Canada',
    'Brazil',
    'Australia',
    'Japan',
    'Singapore',
  ],
  datasets: [{
    label: 'Users by country',
    data: [
        8856,
        1549,
        366,
        214,
        204,
        162,
        135,
        126,
        125,
        116,
    ],
    backgroundColor: [
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
    hoverOffset: 4
  }]
};

const users_country_pie_opts = {
    plugins: {
        title: {
            display: true,
            text: 'Users By Country'
        },
    },
    responsive: true,
    maintainAspectRatio: false,
}; 

const users_country_pie_config = {
    type: 'pie',
    data: users_country_pie_data,
    options: users_country_pie_opts,
};


/*
colors 
#0A4595
#0099D8
#D97200
#00A54F
*/
