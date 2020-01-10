$("#config-form").submit(ajaxForm);

function ajaxForm(formEvent)
{
    formEvent.preventDefault();

    var form = $(this);
    var url = form.attr('action');

    $.ajax({
        type: "POST",
        url: url,
        data: form.serialize(),
        success: function(data) {
            form.after(data);
        }
    });
}

$(".server-command").on('click touchend', function(e) {
    e.preventDefault();

    sendServerCommand($(this).attr('data-command'));
})

var websocket = null;

function connect() {
    websocket = new WebSocket(wsString);
    websocket.onmessage = onMessage; 
}

function sendServerCommand(command)
{
    $.ajax({
        type: "POST",
        url: 'servicecommand.php',
        data: {
            'command' : command,
            'service_id' : serverID
        },
        success: function(data) {
            alert(data);
        }
    });
}

function onMessage(event) {
    var textbox = $("#console");
    console.log(event.data);
    var data = JSON.parse(event.data);
    if(data.Type === "Chat")
    {
        var message = JSON.parse(data.Message);
        addMessageToConsole(message.Username + ": " + message.Message);
    } else if(data.Type === "Generic" && !data.Message.startsWith("[CHAT]"))
    {
        addMessageToConsole(data.Message);
    }
}

function addMessageToConsole(message)
{
    var console = $("#console");
    if(!console.val())
    {
        console.val(message);
    } else {
        if(console.val().endsWith('\n'))
        {
            console.val(console.val() + message);
        } else {
            console.val(console.val() + "\n" + message);
        }
    }
}

function send()
{
    var message = $("#command").val();
    $("#command").val("");
    var packet = {
        Identifier: 1,
        Message: message,
        Name: "WebRcon"
    };
    websocket.send(JSON.stringify(packet));
}


function checkConnection() {
    if(websocket != null)
    {
        if(websocket.readyState  == WebSocket.OPEN)
        {
            $("#status").html("Status: connected");
        } else if(websocket.readyState == WebSocket.CLOSED) {
            $("#status").html("Status: disconnected");
        } else if (websocket.readyState == WebSocket.CONNECTING) {
            $("#status").html("Status: connecting");
        }
    }
}

function pingServer()
{
    $.ajax({
        type: "POST",
        url: 'serverstatus.php',
        dataType: 'html',
        timeout: 5000,
        data: {
            'service_id' : serverID,
            'action' : "ping"
        },
        success: function(data) {

            if(data.indexOf("Running") > -1)
            {
                $("#serverStatus").html(data);
                $("#serverStatus").css({'color': '#a2964e'});
            } else {
                $("#serverStatus").html(data);
                $("#serverStatus").css({'color': '#812719'});
            }
        },
        error: function() {
            $("#serverStatus").html("Stopped");
            $("#serverStatus").css({'color': '#812719'});
        }
    });
}

function updateServerLogs()
{
    $.ajax({
        type: "POST",
        url: 'serverstatus.php',
        dataType: 'html',
        timeout: 5000,
        data: {
            'service_id' : serverID,
            'action' : "logs"
        },
        success: function(data) {
            $("#server-logs").html(data.replace());
            console.log(data);
        }
    });
}

pingServer();
updateServerLogs();

setInterval(updateServerLogs, 300000);
setInterval(pingServer, 30000);
setInterval(checkConnection, 3000);