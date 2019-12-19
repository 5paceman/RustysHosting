var url = $(location).attr('href');
if(url.indexOf("#accountsettings") > -1)
{
    $("#acc-settings-btn").trigger('click');
} else if(url.indexOf("#servers") > -1)
{
    $("#servers-btn").trigger('click');
} else if(url.indexOf("#support") > -1)
{
    $("#support-btn").trigger('click');
} else if (url.indexOf("#faq") > -1)
{
    $("#faq-btn").trigger('click');
}

$("#account-settings").submit(ajaxForm);
$("#password-form").submit(ajaxForm);

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