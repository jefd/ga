
const hackathon_labels = ['2022-06-01', '2022-09-01', '2022-11-01',];

const hackathon_data = {
  labels: hackathon_labels,
  datasets: [
    {
      label: 'General Public',
      data: [1, 5, 0],
      backgroundColor: '#0A4595',
    },
    {
      label: 'Academia',
      data: [5, 0, 13],
      backgroundColor: '#0099D8',
    },
    {
      label: 'Government',
      data: [3, 0, 1],
      backgroundColor: '#D97200',
    },
    {
      label: 'Industry',
      data: [3, 1, 1],
      backgroundColor: '#00A54F',
    },
  ]
};


const hackathon_opts = {
    plugins: {
      title: {
        display: true,
        text: 'Hackathon Particpants'
      },
    },
    responsive: true,
    indexAxis: 'y',
        scales: {
            x: {
                stacked: true,
            },
            y: {
                stacked: true
            }
        }
    }; 

const hackathon_config = {
    type: 'bar',
    data: hackathon_data,
    options: hackathon_opts, 
};
