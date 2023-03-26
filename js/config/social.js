const social_labels = [
    '2022-02-13',
    '2022-02-24',
    '2022-03-01',
    '2022-03-21',
    '2022-04-01',
    '2022-04-25',
    '2022-05-08',
    '2022-05-19',
    '2022-06-06',
    '2022-06-17',
    '2022-07-25',
    '2022-08-04',
    '2022-08-22',
    '2022-09-22',
    '2022-10-11',
    '2022-10-24',
    '2022-11-04',
    '2022-11-29',
    '2023-01-31',
    '2023-02-20',
    '2023-03-05',
    '2023-03-15',
];


const twitter = [
    765,
    774,
    776,
    804,
    811,
    827,
    844,
    871,
    886,
    902,
    942,
    950,
    955,
    968,
    979,
    986,
    983,
    983,
    1027,
    1039,
    1037,
    1049,
];


const instagram = [
    49,
    69,
    78,
    94,
    108,
    114,
    125,
    132,
    134,
    136,
    147,
    152,
    158,
    182,
    193,
    209,
    218,
    254,
    317,
    334,
    343,
    365,
];

const facebook = [
    13,
    18,
    21,
    34,
    41,
    43,
    44,
    45,
    46,
    46,
    57,
    58,
    60,
    69,
    70,
    71,
    71,
    71,
    71,
    71,
    71,
    71,
];


const social_data = {
  labels: social_labels,
  datasets: [
    {
      label: 'Twitter',
      data: twitter,
      backgroundColor: '#0099D8',
    },
    {
      label: 'Instagram',
      data: instagram,
      backgroundColor: '#D97200',
    },
    {
      label: 'Facebook',
      data: facebook,
      backgroundColor: '#00A54F',
    },
  ]
};

const social_opts = {
    plugins: {
      title: {
        display: true,
        text: 'Social Media'
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

const social_config = {
  type: 'bar',
  data: social_data,
  options: social_opts, 
};



/*
colors 
#0A4595
#0099D8
#D97200
#00A54F
*/
