<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>

</head>
<body>

<div id="container">
	<h1>Download is in process. Please wait...</h1>
    <!-- <input type="hidden" name="dataCount" id="dataCount" value="<?=$dataCount?>"/> -->
    <input type="hidden" name="filePath" id="filePath" value="<?=$path?>"/>
    <input type="hidden" name="fromDate" id="fromDate" value="<?=$fromDate?>"/>
    <input type="hidden" name="toDate" id="toDate" value="<?=$toDate?>"/>
    <input type="hidden" name="searchField" id="searchField" value="<?=$searchField?>"/>
    <input type="hidden" name="searchValue" id="searchValue" value="<?=$searchValue?>"/>
    <input type="hidden" name="collection_point" id="collection_point" value="<?=$collection_point?>"/>
    <input type="hidden" name="perPage" id="perPage" value="<?=$perPage?>"/>
    <input type="hidden" name="page" id="page" value="<?=$page?>"/>
    <input type="hidden" name="addedData" id="addedData" value="<?=$page?>"/>
    <input type="hidden" name="totalData" id="totalData" value="<?=$totalData?>"/>
     

    <a href="<?=base_url()?>/orders/allorders/download/<?=$path?>" class="btn btn-success" id="downloadbtn" target="_blank" style="display:none">Download</a>
</div>

</body>
<script>
    // const totalDataCount    =   $('#dataCount').val();
    $(document).ready(function(){
        
        let perPage           =   $('#perPage').val();

        let addedData           =   $('#addedData').val();
        let totalData           =   $('#totalData').val();
        let moreData            =   parseInt(addedData) + parseInt(perPage);
        const filePath = $('#filePath').val();
        if(filePath && 100 > totalData){
            $('#downloadbtn').css('display','block')
        } else{
            loadMore(moreData);
        }

        // if(filePath && totalDataCount === addedData || totalDataCount < addedData){
        //     $('#downloadbtn').css('display','block')
        // }else{
        // }
    });

    function loadMore(next){
        let dataCount           = $('#dataCount').val();
        let filePath            = $('#filePath').val();
        let fromDate            = $('#fromDate').val();
        let toDate              = $('#toDate').val();
        let searchField         = $('#searchField').val();
        let searchValue         = $('#searchValue').val();
        let collection_point    = $('#collection_point').val();
        let perPage             = $('#perPage').val();
        let page                =   next
        
        let ur = '<?=base_url()?>/orders/allorders/addMoreData';
        $.ajax({
            url : ur,
            method: "POST", 
            data: {
                dataCount   :   dataCount,
                filePath    :   filePath,
                fromDate    :   fromDate,
                toDate      :   toDate,
                searchField :   searchField,
                searchValue :   searchValue,
                collection_point    :   collection_point,
                perPage     :   perPage,
                page        :   next
            },
            success: function(data){
                if(data.status === 200){
                    // alert(data.param)
                    // $('#addedData').val(data.param);
                    //     // console.log('page',data.param)
                    //     // console.log(perPage);
                        addedData = parseInt(data.param)+parseInt(perPage);
                    // if(totalDataCount > addedData){
                    //     loadMore(addedData);
                    // }else{
                    //     alert('complete');
                    //     $('#downloadbtn').css('display','block')
                    // }
                    // alert(addedData)
                    loadMore(addedData);
                }
                if(data.status === 202){
                    $('#downloadbtn').css('display','block')
                }
                if(data.status === 400){
                alertMessageModelPopup('data.message', 'warning');
                }
            }
        });
    }

    $('#downloadbtn').click(function(){
        window.top.close();
    });

</script>
</html>