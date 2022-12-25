$(document).ready(function(){
    var folderName = './docs/';
    var fileName = '';
    var localerrmessage = '';
    var statesrepository,districtrepository,talukrepository,officerepository,grievancerepository;
    var currentpageurl = window.location.href;
    var CONSTINDEX = 'index';
    var CONSTGRIEVANCE = 'grienvancegenerator';
    var CONSTARCHIVES = 'archives';
    var CONSTGOVERNMENTCONTACTS = 'governmentcontactlist';
    var CONSTCONTACTUS = 'contactus';
    var CONSTGOVERNMENTDECISIONS= 'governmentdecisions';
    var CONSTJUDICIALDECISIONS = 'judicialdecisions';

    $( ".scrollcontents" ).scroll();
    /*$(document).on("click", '.menuitem', function() {
        alert($(this).text());
    });*/
    $(document).on("click", '#generate', function() {
        var printabledata = validate($(this).attr('pageid'));
        if(printabledata != ''){
            content = generatehtml(printabledata);
        }
    });

    $(".faqcontent").hide();

    $( ".faqheading" ).click(function() {
        $(".faqcontent").hide( "slow" );
        var currentheadingid = $(this).attr('id')
        $("#" + currentheadingid + "content").show( "slow");
    });

    $(".popuperrormessageclose").click(function() {
        var href = $(".popuplink").attr('href');
        if(href != undefined && href.length > 0)
        {
            var filename = href.split('/').pop().split('#')[0].split('?')[0];
            $.ajax
            ({
                type: "POST",
                url: "domainservices.php",
                data: 
                    { 
                        datarequestedby: 'deletefile',
                        filename :  filename
                    },
                success: function(retstatus)
                {
                    $(".popuplink").attr('');
                },
                error:function(retudata)
                {
                   
                }
            });
        }

        var parent = $(this).attr('parentcontrol');
        $("." + parent).css("display","none");
    });
    
    function generatehtml(printabledata){
        $.ajax
            ({
                type: "POST",
                url: "domainservices.php",
                data: 
                    { datarequestedfrom : $(this).attr('pagename'),
                        datarequestedby: 'generatagrievance',
                        language :  $('#ddllanguage').val(),
                        fromname :  $('#txtfromname').val(),
                        fromhousenumber :  $('#txtfromhousenumber').val(),
                        fromhousename :  $('#txtfromhousename').val(),
                        fromstreetname :  $('#txtfromstreetname').val(),
                        fromcity :  $('#txtfromcity').val(),
                        fromvillagename :  $('#txtfromvillagename').val(),
                        frompostalname :  $('#txtfrompostalname').val(),
                        districtname :  $('#txtdistrictname').val(),
                        fromstatename :  $('#txtfromstatename').val(),
                        frompostalcode :  $('#txtfrompostalcode').val(),
                        mobilenumber :  $('#txtmobilenumber').val(),
                        emailaddress :  $('#txtemailaddress').val(),
                        grievanceid : $('#ddlgrievance').val(),
                        grievancename : $('#ddlgrievance option:selected').text(),
                        state: $("#ddlstate").val(), 
                        district: $("#ddldistrict").val(), 
                        taluk: $("#ddltaluk").val(), 
                        village :$("#ddlvillage").val()
                    },
                success: function(returl)
                {
                    $('.popup').css("display", "block");
                    $('.popuplink').css("display", "none");
                    $('.popuperrormessage').css("display", "none");
                    if(returl.length > 0 && returl.substr(0, 4) === 'http')
                    {
                        $('.popuplink').attr("href", returl);
                        $('.popuplink').text('Download your Grievance!.');
                        $('.popuplink').css("display", "block");
                    }
                    else
                    {
                        $('.popuperrormessage').css("display", "block");
                    }
                },
                error:function(retudata)
                {
                    $('.popuperrormessage').css("display", "block");
                }
            });
    }

    $("select").change (function(){ 
        var ctrlid = $(this).attr('id');
        var shouldprocessdomaincall = false;
        switch(ctrlid) { 
            case 'ddlstate':
            {
                shouldprocessdomaincall = true;
                $("#ddldistrict").find("option").remove(); 
                $("#ddltaluk").find("option").remove(); 
                $("#ddlvillage").find("option").remove(); 
                $("#ddloffice").find("option").remove(); 
                break;
            }
            case 'ddldistrict':
            {
                shouldprocessdomaincall = true;
                $("#ddltaluk").find("option").remove(); 
                $("#ddlvillage").find("option").remove(); 
                $("#ddloffice").find("option").remove(); 
                break;
            }
            case 'ddltaluk':
            {
                shouldprocessdomaincall = true;
                $("#ddlvillage").find("option").remove(); 
                $("#ddloffice").find("option").remove(); 
                break;
            }
            case 'ddlvillage':
            {
                shouldprocessdomaincall = true;
                $("#ddloffice").find("option").remove(); 
                break;
            }
            //$('#grievances').find("option").remove(); 
            //districtrepository = statesrepository.filter(obj=> obj.id == $("#states").val())[0]['districts'];
            //loadDroptownFromRepository('','districts', districtrepository);
            //loadDroptown($("#states").val() +  'districts','districts');
        }
        if(shouldprocessdomaincall)
        {
            $.ajax
            ({
                type: "POST",
                url: "domainservices.php",
                data: { datarequestedfrom : $(this).attr('pagename'), datarequestedby: ctrlid, 
                                            state: $("#ddlstate").val(), 
                                            district: $("#ddldistrict").val(), 
                                            taluk: $("#ddltaluk").val(), 
                                            village :$("#ddlvillage").val() },
                success: function(retdata)
                {
                    switch(ctrlid) { 
                        case 'ddlstate':
                        {
                            $("#ddldistrict").append(retdata);
                            break;
                        }
                        case 'ddldistrict':
                        {
                            $("#ddltaluk").append(retdata);
                            break;
                        }
                        case 'ddltaluk':
                        {
                            $("#ddlvillage").append(retdata);
                            break;
                        }
                        case 'ddlvillage':
                        {
                            $("#ddloffice").append(retdata);
                            break;
                        }
                    }

                    
                }
            });
        }
    });

    $("#districts").change (function(){   
        $("#taluks").find("option").remove(); 
        $("#villages").find("option").remove(); 
        $("#officers").find("option").remove(); 
        $('#grievances').find("option").remove(); 
    });

    $("#taluks").change (function(){ 
        $("#villages").find("option").remove();   
        $("#officers").find("option").remove(); 
        $('#grievances').find("option").remove(); 
    });

    $("#villages").change (function(){  
        $("#officers").find("option").remove();  
        $('#grievances').find("option").remove(); 
    });

    $("#officers").change (function(){  
        $('#grievances').find("option").remove(); 
    });

    function validate(pageid){
        var isvalid = true;

        var formdata =  '';

        $('.' + pageid + 'data').each(function(){
            var localid = $(this).attr('id');
            if($(this).attr('required') != undefined && 
                (
                    (
                        ($(this).prop('nodeName') == "SELECT" || $(this).prop('nodeName') == "select") && 
                        ($(this).val() == 'select' || $(this).val() == '--- Select ---' || $(this).val() == null)
                    ) ||
                    (($(this).prop('nodeName') == "INPUT" || $(this).prop('nodeName') == "input") && $(this).val() == '')
                )
                )
            {
                $('#' + localid).addClass('redborder');
                $('#' + localid + 'errormessage').css("display", "block");
                isvalid = false;
            }
            else {
                if(formdata.trim().length > 0) {formdata = formdata + ','}
                formdata = formdata + $(this).attr('id') + ' : ' + $(this).val()
                $('#' + localid).removeClass('redborder');
                $('#' + localid + 'errormessage').css("display", "none");
            }
        });
        if(isvalid){
            return formdata;
        }
        else {
            return '';
        }
    }
});