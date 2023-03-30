function get_new_users_config() {

    let labels = ["1/25/22", "1/26/22", "1/27/22", "1/28/22", "1/29/22", "1/30/22", "1/31/22", "2/1/22", "2/2/22", "2/3/22", "2/4/22", "2/5/22", "2/6/22", "2/7/22", "2/8/22", "2/9/22", "2/10/22", "2/11/22", "2/12/22", "2/13/22", "2/14/22", "2/15/22", "2/16/22", "2/17/22", "2/18/22", "2/19/22", "2/20/22", "2/21/22", "2/22/22", "2/23/22", "2/24/22", "2/25/22", "2/26/22", "2/27/22", "2/28/22", "3/1/22", "3/2/22", "3/3/22", "3/4/22", "3/5/22", "3/6/22", "3/7/22", "3/8/22", "3/9/22", "3/10/22", "3/11/22", "3/12/22", "3/13/22", "3/14/22", "3/15/22", "3/16/22", "3/17/22", "3/18/22", "3/19/22", "3/20/22", "3/21/22", "3/22/22", "3/23/22", "3/24/22", "3/25/22", "3/26/22", "3/27/22", "3/28/22", "3/29/22", "3/30/22", "3/31/22", "4/1/22", "4/2/22", "4/3/22", "4/4/22", "4/5/22", "4/6/22", "4/7/22", "4/8/22", "4/9/22", "4/10/22", "4/11/22", "4/12/22", "4/13/22", "4/14/22", "4/15/22", "4/16/22", "4/17/22", "4/18/22", "4/19/22", "4/20/22", "4/21/22", "4/22/22", "4/23/22", "4/24/22", "4/25/22", "4/26/22", "4/27/22", "4/28/22", "4/29/22", "4/30/22", "5/1/22", "5/2/22", "5/3/22", "5/4/22", "5/5/22", "5/6/22", "5/7/22", "5/8/22", "5/9/22", "5/10/22", "5/11/22", "5/12/22", "5/13/22", "5/14/22", "5/15/22", "5/16/22", "5/17/22", "5/18/22", "5/19/22", "5/20/22", "5/21/22", "5/22/22", "5/23/22", "5/24/22", "5/25/22", "5/26/22", "5/27/22", "5/28/22", "5/29/22", "5/30/22", "5/31/22", "6/1/22", "6/2/22", "6/3/22", "6/4/22", "6/5/22", "6/6/22", "6/7/22", "6/8/22", "6/9/22", "6/10/22", "6/11/22", "6/12/22", "6/13/22", "6/14/22", "6/15/22", "6/16/22", "6/17/22", "6/18/22", "6/19/22", "6/20/22", "6/21/22", "6/22/22", "6/23/22", "6/24/22", "6/25/22", "6/26/22", "6/27/22", "6/28/22", "6/29/22", "6/30/22", "7/1/22", "7/2/22", "7/3/22", "7/4/22", "7/5/22", "7/6/22", "7/7/22", "7/8/22", "7/9/22", "7/10/22", "7/11/22", "7/12/22", "7/13/22", "7/14/22", "7/15/22", "7/16/22", "7/17/22", "7/18/22", "7/19/22", "7/20/22", "7/21/22", "7/22/22", "7/23/22", "7/24/22", "7/25/22", "7/26/22", "7/27/22", "7/28/22", "7/29/22", "7/30/22", "7/31/22", "8/1/22", "8/2/22", "8/3/22", "8/4/22", "8/5/22", "8/6/22", "8/7/22", "8/8/22", "8/9/22", "8/10/22", "8/11/22", "8/12/22", "8/13/22", "8/14/22", "8/15/22", "8/16/22", "8/17/22", "8/18/22", "8/19/22", "8/20/22", "8/21/22", "8/22/22", "8/23/22", "8/24/22", "8/25/22", "8/26/22", "8/27/22", "8/28/22", "8/29/22", "8/30/22", "8/31/22", "9/1/22", "9/2/22", "9/3/22", "9/4/22", "9/5/22", "9/6/22", "9/7/22", "9/8/22", "9/9/22", "9/10/22", "9/11/22", "9/12/22", "9/13/22", "9/14/22", "9/15/22", "9/16/22", "9/17/22", "9/18/22", "9/19/22", "9/20/22", "9/21/22", "9/22/22", "9/23/22", "9/24/22", "9/25/22", "9/26/22", "9/27/22", "9/28/22", "9/29/22", "9/30/22", "10/1/22", "10/2/22", "10/3/22", "10/4/22", "10/5/22", "10/6/22", "10/7/22", "10/8/22", "10/9/22", "10/10/22", "10/11/22", "10/12/22", "10/13/22", "10/14/22", "10/15/22", "10/16/22", "10/17/22", "10/18/22", "10/19/22", "10/20/22", "10/21/22", "10/22/22", "10/23/22", "10/24/22", "10/25/22", "10/26/22", "10/27/22", "10/28/22", "10/29/22", "10/30/22", "10/31/22", "11/1/22", "11/2/22", "11/3/22", "11/4/22", "11/5/22", "11/6/22", "11/7/22", "11/8/22", "11/9/22", "11/10/22", "11/11/22", "11/12/22", "11/13/22", "11/14/22", "11/15/22", "11/16/22", "11/17/22", "11/18/22", "11/19/22", "11/20/22", "11/21/22", "11/22/22", "11/23/22", "11/24/22", "11/25/22", "11/26/22", "11/27/22", "11/28/22", "11/29/22", "11/30/22", "12/1/22", "12/2/22", "12/3/22", "12/4/22", "12/5/22", "12/6/22", "12/7/22", "12/8/22", "12/9/22", "12/10/22", "12/11/22", "12/12/22", "12/13/22", "12/14/22", "12/15/22", "12/16/22", "12/17/22", "12/18/22", "12/19/22", "12/20/22", "12/21/22", "12/22/22", "12/23/22", "12/24/22", "12/25/22", "12/26/22", "12/27/22", "12/28/22", "12/29/22", "12/30/22", "12/31/22", "1/1/23", "1/2/23", "1/3/23", "1/4/23", "1/5/23", "1/6/23", "1/7/23", "1/8/23", "1/9/23", "1/10/23", "1/11/23", "1/12/23", "1/13/23", "1/14/23", "1/15/23", "1/16/23", "1/17/23", "1/18/23", "1/19/23", "1/20/23", "1/21/23", "1/22/23", "1/23/23", "1/24/23", "1/25/23", "1/26/23", "1/27/23", "1/28/23", "1/29/23", "1/30/23", "1/31/23", "2/1/23", "2/2/23", "2/3/23", "2/4/23", "2/5/23", "2/6/23", "2/7/23", "2/8/23", "2/9/23", "2/10/23", "2/11/23", "2/12/23", "2/13/23", "2/14/23", "2/15/23", "2/16/23", "2/17/23", "2/18/23", "2/19/23", "2/20/23", "2/21/23", "2/22/23", "2/23/23", "2/24/23", "2/25/23", "2/26/23", "2/27/23", "2/28/23", "3/1/23", "3/2/23", "3/3/23", "3/4/23", "3/5/23", "3/6/23", "3/7/23", "3/8/23", "3/9/23", "3/10/23"];

    let dat = [120, 201, 237, 266, 279, 289, 303, 334, 344, 364, 374, 379, 381, 392, 408, 430, 438, 450, 463, 471, 496, 511, 560, 638, 668, 679, 689, 710, 735, 757, 775, 787, 805, 810, 830, 844, 877, 901, 928, 938, 957, 994, 1014, 1039, 1057, 1076, 1084, 1097, 1110, 1135, 1162, 1182, 1223, 1232, 1247, 1272, 1295, 1309, 1328, 1347, 1359, 1369, 1482, 1500, 1506, 1524, 1558, 1572, 1596, 1615, 1640, 1655, 1681, 1708, 1713, 1735, 1753, 1777, 1796, 1809, 1818, 1831, 1836, 1861, 1884, 1955, 1977, 1995, 1999, 2015, 2046, 2063, 2081, 2101, 2120, 2128, 2136, 2172, 2201, 2231, 2265, 2291, 2322, 2332, 2364, 2387, 2437, 2603, 2733, 2765, 2797, 2869, 2908, 2982, 3043, 3110, 3143, 3188, 3281, 3357, 3424, 3493, 3521, 3527, 3551, 3566, 3600, 3646, 3674, 3695, 3707, 3732, 3788, 3914, 3957, 4005, 4083, 4089, 4106, 4157, 4182, 4213, 4252, 4295, 4318, 4332, 4348, 4393, 4414, 4514, 4552, 4570, 4587, 4619, 4655, 4680, 4727, 4761, 4782, 4792, 4805, 4842, 4891, 4931, 4953, 4961, 4975, 5019, 5089, 5137, 5190, 5235, 5250, 5287, 5504, 5660, 5772, 5847, 5888, 5898, 5909, 5942, 5969, 5990, 6012, 6024, 6037, 6051, 6083, 6111, 6128, 6168, 6194, 6201, 6214, 6232, 6271, 6297, 6318, 6346, 6359, 6388, 6410, 6443, 6462, 6491, 6534, 6553, 6569, 6593, 6630, 6664, 6689, 6712, 6728, 6753, 6775, 6816, 6841, 6876, 6911, 6950, 6970, 6983, 7026, 7046, 7075, 7089, 7098, 7111, 7134, 7157, 7182, 7196, 7205, 7209, 7225, 7245, 7277, 7303, 7324, 7343, 7349, 7364, 7376, 7411, 7431, 7465, 7485, 7490, 7499, 7509, 7520, 7540, 7562, 7576, 7583, 7589, 7604, 7638, 7682, 7703, 7713, 7722, 7730, 7736, 7768, 7802, 7841, 7871, 8026, 8045, 8082, 8204, 8307, 8415, 8458, 8484, 8498, 8546, 8565, 8595, 8632, 8692, 8727, 8743, 8776, 8790, 8817, 8848, 8891, 8909, 8926, 8948, 8976, 8997, 9027, 9059, 9110, 9134, 9187, 9222, 9259, 9285, 9347, 9389, 9430, 9479, 9538, 9594, 9653, 9678, 9725, 9742, 9775, 9834, 9871, 9889, 9941, 9964, 10005, 10059, 10106, 10141, 10166, 10187, 10202, 10217, 10258, 10280, 10312, 10331, 10349, 10366, 10370, 10382, 10404, 10418, 10443, 10453, 10459, 10473, 10496, 10531, 10550, 10573, 10613, 10638, 10676, 10717, 10762, 10798, 10818, 10848, 10861, 10865, 10888, 10910, 10934, 10949, 10977, 10998, 11010, 11051, 11076, 11113, 11134, 11162, 11207, 11222, 11294, 11362, 11397, 11612, 11799, 11812, 11832, 11860, 11906, 11934, 11963, 11982, 12027, 12040, 12090, 12148, 12172, 12214, 12265, 12286, 12302, 12496, 12529, 12555, 12606, 12641, 12675, 12702, 12758, 12797, 12842, 12881, 12926, 12955, 12978, 13009, 13094, 13137, 13179, 13214];


    let decimation = 15;

    let pruned_labels = [];
    let pruned_data = [];

    for(const [i, val] of labels.entries()) {
        if (i % decimation === 0)
            pruned_labels.push(val);
    }

    for(const [i, val] of dat.entries()) {
        if (i % decimation === 0)
            pruned_data.push(val);
    }

    let data =  {
        labels: pruned_labels,
        datasets: [{
            label: 'New Users',
            data: pruned_data,
            backgroundColor: '#0099D8',
            //borderWidth: 1
        }]
    };



    let opts = {
        responsive: true,
        //animation: false,
        indexAxis: 'y',
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }; 

    let config = {
      type: 'bar',
      data: data,
      options: opts, 
    };

    return config;
}

function get_hakcathon_config() {

    let labels = ['2022-06-01', '2022-09-01', '2022-11-01',];

    let data = {
      labels: labels,
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


    let opts = {
        //animation: false,
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

    let config = {
        type: 'bar',
        data: data,
        options: opts, 
    };

    return config;
}

function get_users_country_config() {

    let data = {
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
        //hoverOffset: 4
      }]
    };

    let opts = {
        //animation: false,
        plugins: {
            title: {
                display: true,
                text: 'Users By Country'
            },
        },
        responsive: true,
        //maintainAspectRatio: false,
    }; 

    let config = {
        type: 'doughnut',
        data: data,
        options: opts,
    };

    return config;
}

function get_social_config() {

    let labels = [
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


    let twitter = [
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


    let instagram = [
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

    let facebook = [
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


    let data = {
      labels: labels,
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

    let opts = {
        //animation: false,
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

    let config = {
      type: 'bar',
      data: data,
      options: opts, 
    };

    return config;
}


const MOCK = {
    'new_users': get_new_users_config,
    'social': get_social_config,
    'users_country': get_users_country_config,
    'hackathons': get_hakcathon_config,
};


