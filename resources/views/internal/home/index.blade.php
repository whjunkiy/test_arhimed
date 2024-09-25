<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="{{ app()->getLocale() }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Test</title>
    <link href="/css/jquery.autocomplete.css" type="text/css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css"/>
    <script src="/scripts/jquery.js" type="text/javascript">
    </script>
    <script src="https://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
    <script src="/scripts/jquery.autocomplete.js" type="text/javascript">
    </script>
    <script src="/scripts/base.js" type="text/javascript">
    </script>
    <script src="/scripts/jquery.tablesorter.min.new.js" type="text/javascript"></script>
</head>
<style>
    .nav {
        margin: 6px;
        padding: 2px;
        cursor: pointer;
        border: 1px saddlebrown solid;
    }
    tfoot tr td {
        padding: 6px;
    }
    .bold {
        font-weight: bold;
    }
</style>
<body>
<div id="prep" style="display: none;">
    <table>
        <tbody>
        @foreach($tasks as $task)
            <tr>
                <td>{{ $task->login }}</td>
                <td>{{ $task->email }}</td>
                <td><textarea data-id="{{$task->id}}" id="txt_{{$task->id}}" class="txtar">{{ $task->description }}</textarea></td>
                <td><input data-id="{{$task->id}}" type="checkbox" class="chck" id="chck_{{$task->id}}" @if( $task->is_complete ) checked @endif /></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<div id="tbl">
<table border="2" cellpadding="2" cellspacing="2" class="table1">
    <thead>
    <tr>
        <th>login</th>
        <th>email</th>
        <th>description</th>
        <th>status</th>
    </tr>
    </thead>
    <tbody>
    @foreach($tasks as $task)
        <tr>
            <td>{{ $task->login }}</td>
            <td>{{ $task->email }}</td>
            <td>{{ $task->description }}</td>
            <td>@if( $task->is_complete ) <div>отредактировано администратором</div> @else <div></div> @endif</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" class="pager"></td>
        </tr>
    </tfoot>
</table>
</div>
<div id="enterForm" style="margin-top: 20px;">
    Login:<input type="text" id="login"/><br>
    Password:<input type="password" id="pass"/><br>
    <input type="button" value="Admin" id="enter"/>
    <div id="errors"></div>
</div>
<script>
    $(function() {
        let is_admin = 0;
        $('#enter').on('click', ()=>{
            $.ajax({
                url: '/api/login',
                type: 'POST',
                data: {login: $('#login').val(),password: $('#pass').val()},
                success: function (response) {
                    console.log('resp:');
                    console.log(response);
                    if (parseInt(response.login) === 1) {
                        makeAdmin();
                    } else {
                        $('#errors').html('WRONG!!!');
                    }
                },
                error: function (e) {
                    e.responseText
                        ? alert(`${e.status} | ${e.responseText}`)
                        : alert(`HTTP Error: ${e.status}`)
                }
            });
        });
        let trs = [];
        let trs_admin = [];

        const tbl = '<table border="2" cellpadding="2" cellspacing="2" class="table1"><thead><tr><th>login</th><th>email</th><th>description</th><th>status</th></tr></thead><tbody></tbody><tfoot><tr><td colspan="4" class="pager"></td></tr></tfoot></table>';
        setTimeout(()=>{
            //console.log('checking...');
            $("#prep table tbody tr").each((indx, el)=>{
                //console.log('indx = ' + el);
                trs_admin.push(el);
            });
            $('#prep').remove();

            $(".table1 tbody tr").each((indx, el)=>{
               //console.log('indx = ' + el);
                trs.push(el);
               if (indx > 2) {
                   $(el).remove();
               }
            });
            $(".table1").tablesorter({
                theme : 'blue',
                // sort on the first column and second column in ascending order
                sortList: [[0,0],[1,0]]
            });
        }, 10);
        let tasks = @json($json);
        let pages = [];
        let lastp = 0;
        let pl = 3;
        let curPage = 1;
        let counter=1;
        for (let i in tasks) {
            if (i % pl === 0) {lastp++; pages.push(lastp);}
        }
        setCurPager();

        function onNav(dat){
            counter++;
            curPage = parseInt($(dat).html());

            let lim1 = (curPage - 1)*3;
            let lim2 = lim1 + 3;

            $(".table1 tbody").html("");
            $("#tbl").html(tbl.replace('table1', 'table'+counter));
            for (let i in trs) {
                if (i >= lim1 && i < lim2) {
                    if (!is_admin) {
                        $("#tbl table tbody").append(trs[i]);
                    } else {
                        $("#tbl table tbody").append(trs_admin[i]);
                    }
                }
            }

            $(".table"+counter).tablesorter({
                theme : 'blue',
                sortList: [[0,0],[1,0]]
            });
            setCurPager();
        }

        function setCurPager() {
            let htmlP = '';
            for(let i in pages) {
                let cc = (parseInt(pages[i]) === parseInt(curPage)) ? 'bold' : '';
                htmlP += '<span class="nav ' + cc + '">' + pages[i] + '</span>';
            }
            $(".pager").html(htmlP);
            $(".nav").on('click', function(ev) {
                onNav(this);
            });
            $('.txtar').on('change', function(ev){
                let id = parseInt($(this).data('id'));
                let val = $(this).val();
                console.log('val = ' + val);
                $.ajax({
                    url: '/api/tskupd',
                    type: 'POST',
                    data: {id: id, val: val},
                    success: function (response) {
                        console.log('resp111:');
                        console.log(response);
                    },
                    error: function (e) {
                        e.responseText
                            ? alert(`${e.status} | ${e.responseText}`)
                            : alert(`HTTP Error: ${e.status}`)
                    }
                });
            });
            $('.chck').on('click', function(ev){
                let id = parseInt($(this).data('id'));
                $.ajax({
                    url: '/api/tskst',
                    type: 'POST',
                    data: {id: id},
                    success: function (response) {
                        console.log('resp222:');
                        console.log(response);
                    },
                    error: function (e) {
                        e.responseText
                            ? alert(`${e.status} | ${e.responseText}`)
                            : alert(`HTTP Error: ${e.status}`)
                    }
                });
            });
        }

        function makeAdmin(){
            $('#enterForm').css('display', 'none');
            is_admin = 1;
            counter++;

            let lim1 = (curPage - 1)*3;
            let lim2 = lim1 + 3;

            $(".table1 tbody").html("");
            $("#tbl").html(tbl.replace('table1', 'table'+counter));
            for (let i in trs) {
                if (i >= lim1 && i < lim2) {
                    if (!is_admin) {
                        $("#tbl table tbody").append(trs[i]);
                    } else {
                        $("#tbl table tbody").append(trs_admin[i]);
                    }
                }
            }

            $(".table"+counter).tablesorter({
                theme : 'blue',
                sortList: [[0,0],[1,0]]
            });
            setCurPager();
        }
    });
</script>
</body>
</html>