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

var statistics = [];

function updateStatistics()
{
    $.ajax({
        type: 'get',
        url: 'stats.php',
        data: {
            statistic: 'all'
        },
        success: function(data)
        {
            statistics = data;
        }
        
    })
}


function getRoundedTime() {
    var timeToReturn = new Date();

    timeToReturn.setMilliseconds(Math.round(timeToReturn.getMilliseconds() / 1000) * 1000);
    timeToReturn.setSeconds(Math.round(timeToReturn.getSeconds() / 60) * 60);
    timeToReturn.setMinutes(Math.round(timeToReturn.getMinutes() / 30) * 30);
    return timeToReturn.toDateString();
}

function updateChart()
{
    var datasets = [];
    for(i = 0; i < length(statistics); i++)
    {
        var dataset = {
            label: Object.keys(statistics)[i],
            data: statistics[i]['cpu'],
            backgroundColor: 'rgba(125, 125, 0, 1)',
            borderColor: 'rgba(125, 125, 0, 1)'
        }
        datasets.push(dataset);
    }
    var labels = [];
    var roundedTime = getRoundedTime();
    labels.push(roundedTime);
    for(t = 0; t < 47; t++)
    {
        var time = new Date(roundedTime);
        time.setMinutes(-30);
        labels.push(time.toDateString());
    }

    var ctx = document.getElementById('bar').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            scales: {
                xAxes: [{ stacked: true }],
                yAxes: [{ stacked: true }]
            }
        }
    });
}