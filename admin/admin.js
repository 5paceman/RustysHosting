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
            updateChart();
        }
        
    })
}

var colors = [
    'rgba(0, 200, 0, 1)',
    'rgba(0, 0, 255, 1)',
    'rgba(200, 0, 0, 1)',
    'rgba(120, 200, 0, 1)',
    'rgba(0, 200, 120, 1)',
    'rgba(200, 0, 120, 1)',
    'rgba(0, 120, 200, 1)',
    'rgba(80, 80, 80, 1)',
    'rgba(20, 20, 20, 1)',
    'rgba(80, 80, 0, 1)',
    'rgba(80, 80, 180, 1)',
    'rgba(80, 180, 80, 1)',
    'rgba(180, 80, 80, 1)'
];

function getRoundedTime() {
    var timeToReturn = new Date();

    timeToReturn.setMilliseconds(Math.floor(timeToReturn.getMilliseconds() / 1000) * 1000);
    timeToReturn.setSeconds(Math.floor(timeToReturn.getSeconds() / 60) * 60);
    timeToReturn.setMinutes(Math.floor(timeToReturn.getMinutes() / 30) * 30);
    return timeToReturn;
}

function getDatasets(statistic)
{
    var datasets = [];
    for(i = 0; i < Object.keys(statistics).length; i++)
    {
        var dataset = {
            label: Object.keys(statistics)[i],
            data: statistics[Object.keys(statistics)[i]][statistic],
            backgroundColor: colors[i],
            borderColor: colors[i]
        }
        datasets.push(dataset);
    }
    return datasets;
}

function getLabels()
{
    var labels = [];
    var roundedTime = getRoundedTime();
    labels.push(roundedTime.toLocaleDateString("en-US", {day: "2-digit", hour: "numeric", minute: "numeric"}));
    for(t = 0; t < 47; t++)
    {
        roundedTime.setMinutes(roundedTime.getMinutes() - 30);
        labels.push(roundedTime.toLocaleDateString("en-US", {day: "2-digit", hour: "numeric", minute: "numeric"}));
    }
    return labels;
}

function updateChart()
{
    var cpu = document.getElementById('cpu').getContext('2d');
    var memory = document.getElementById('memory').getContext('2d');
    var hdd = document.getElementById('hdd').getContext('2d');
    var labels = getLabels();
    var cpuDataset = getDatasets('cpu');
    var memoryDataset = getDatasets('memory');
    var hddDataset = getDatasets('hdd');

    var cpuChart = new Chart(cpu, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: cpuDataset
        },
        options: {
            scales: {
                xAxes: [{ stacked: true }],
                yAxes: [{ stacked: true }]
            }
        }
    });

    var memoryChart = new Chart(memory, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: memoryDataset
        },
        options: {
            scales: {
                xAxes: [{ stacked: true }],
                yAxes: [{ stacked: true }]
            }
        }
    });

    var hddMemory = new Chart(hdd, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: hddDataset
        },
        options: {
            scales: {
                xAxes: [{ stacked: true }],
                yAxes: [{ stacked: true }]
            }
        }
    });
}

function update()
{
    updateStatistics();
    $.ajax({
        method: 'get',
        url: 'stats.php',
        data: {
            statistic: 'logs'
        },
        success: function(data) {
            $(".event-log").html(data);                
        }
    });
}

update();
setInterval(update, 900000);