"use strict";

//
var pages = {'API': './pages/API.html' , 'FILE': './pages/FILE.html' , 'RAND': './pages/RAND.html'};
var PHP = {'API': './php/?method=API' , 'FILE': './php/?method=FILE' , 'RAND': './php/?method=RAND'};
var page = { 'id' : '' };

const defaultCurrency = {c: "₴"};
var currency = {};
var selectedCurrency = {};

$(document).ready(function()
{
    var def = $('#focus_area').text();
    selectedCurrency = {'c': ''};
    
    $("button").click(function(){
        getPage(this.id);
    });
    
    // Show spinner upon page change
    $(document).ajaxSend(function(){ $("#spinner").show(); });
    
    function getPage(id)
    {
        page.id = id ;
        
        if (page.id === 'HOME')
        {
            $('#focus_area').text(def);
        }
        else
        {
            $('#focus_area').load(pages[page.id], function (statusTxt) { onPageLoad(statusTxt)});
        }
    }
    
    function onPageLoad(statusTxt)
    {
        if (page.id !== 'FILE')
                {
                    $.get (PHP[page.id], function(data, status)
                    { AJAXCallback(data, status); } , 'json') ;
                }
                else
                {
                    $('#FILE_INPUT').change(function (){ 
                        fileInitSend($(this));
                        });
                    $("#spinner").hide();
                }
        
    }
    
    function fileInitSend (input)
    {
        var file;
        var name = $(input).text();
        var formdata = new FormData();
        
        if ($(input).prop('files').length > 0)
            {
                file = $(input).prop('files')[0];
                formdata.append("file", file );
                
            }
        
        $.ajax ({ url: PHP[page.id], 
        type:"POST", 
        data: formdata,
        processData: false,
        contentType: false,
        success: function (result, status){ 
            if (result.trim() == "error" && ! JSON.parse(result))
            {
                $("#error").text("There's an error in the json file!");
                $("#spinner").hide();
            }
            else
            {
                result = JSON.parse (JSON.parse(result)); 
                AJAXCallback(result, status); 
                $(input).removeClass("btn-warning");
                $(input).addClass("btn-primary");
                $("#error").text("");
            }
            }
            
        });
    }
    
    function AJAXCallback(data, status)
    {
        if (page.id == "FILE")
        {
            $("#converterArea").show();
        }
        if (status == 'success')
        {
            currency = data;
            roundCurrency();
            
            initEvents();
            loadSelect();
            loadAvailCurrency();
        }
        
        $("#spinner").hide();
        
    }
    
    function roundCurrency()
    {
        for (var key in currency)
            currency[key] = currency[key].toFixed(2);
    }
    
    function initEvents() 
    {
        $("#TYPES").change( function() 
        { 
            selectedCurrency.c = $(this).val(); 
            $("#INPUT").keyup();
        });
        
        $("#INPUT").keyup(function(){
            $("#INPUT").keypress();
        })
        
        $("#INPUT").keypress( function () 
        { 
            var val = $(this).val(); 
            
            if (notUNN(val) && $("#TYPES").val() !== "None")
                {
                $('#RESULT').text( defaultCurrency.c + " " + ( parseFloat( (val * currency[selectedCurrency.c]).toFixed(2) ) ) ) ;
                }
            else
                {
                $('#RESULT').text( defaultCurrency.c + " " + "0");
                }
        } );
    }

    function loadSelect()
    {
        for (var key in currency)
            {
            $('#TYPES').append("<option>" + key + "</option>");
            }
    }

    function loadAvailCurrency()
    {
        $("#AVAIL_CURRENCY").text("");
        for (var key in currency)
            $("#AVAIL_CURRENCY").append("UAH/" + key + " " + currency[key]  + "  ");
    }
    
    function notUNN(value)
    {
        if (value && isNaN(value) !== true)
            return true;
        else
            return false;
    }
    
});