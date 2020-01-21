$("#usersTable").fancyTable({
    sortColumn:0,
    sortOrder:'descending',
    sortable:true,
    pagination:true,
    searchable:true,
    globalSearch:true,
    inputStyle:"width: 30%; color: black;",
    paginationClass:"pagination",
    paginationClassActive:"paginationActive"
});
$("#servicesTable").fancyTable({
    sortColumn:0,
    sortOrder:'descending',
    sortable:true,
    pagination:true,
    searchable:true,
    globalSearch:true,
    inputStyle:"width: 30%; color: black;",
    paginationClass:"pagination",
    paginationClassActive:"paginationActive"
});

var machines = [];
var statistics = [];

function getStats(statistic, machine)
{
    var statisticData;
    $.ajax({
        type: "GET",
        url: 'stats.php',
        data: {
            'statistic' : statistic,
            'machine' : machine
        },
        success: function(data) {
            if(statistics[machine] == null)
            {
                statistics.push(machine);
            }

            if(statistics[machine][statistic] == null)
            {
                statistics[machine].push(statistic);
                statistics[machine][statistic] = data;
            }
        }
    });
    return statisticData;
}


function getMachines()
{
    $.ajax({
        type: "GET",
        url: 'stats.php',
        data: {
            'statistic' : 'machines'
        },
        success: function(data) {
            data.forEach(function(element) {
                getStats('cpu', element);
                getStats('memory', element);
                getStats('hdd', element);
                getStats('users', element);
            });
        }
    });
}


var ctx = document.getElementById('bar').getContext('2d');
var chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [],
        datasets: []
    },
    options: {
        scales: {
            xAxes: [{ stacked: true }],
            yAxes: [{ stacked: true }]
        }
    }
});