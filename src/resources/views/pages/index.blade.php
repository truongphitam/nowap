@extends('layout')
@section('content')
    <div class="wrapper">
        <div class="clear-fix div-mode">
            <div role="button" id="mode_all" class="text-center item-mode" onclick="setMode('all')">
                Tất cả
            </div>
            <div role="button" id="mode_live" class="text-center item-mode mode-active" onclick="setMode('live')">
                Trực tiếp <span id="total_matches">()</span>
            </div>
            <div role="button" id="mode_end" class="text-center item-mode" onclick="setMode('end')">
                Đã kết thúc
            </div>
            <div role="button" id="mode_schedule" class="text-center item-mode" onclick="setMode('schedule')">
                Lịch thi đấu
            </div>
        </div>
        <div id="loading_container" class="loading-container">
            <div class="loading-spinner"></div>
            <p>Loading...</p>
        </div>
        <div class="clear-fix" id="content-matches">

        </div>
    </div>
@endsection

@section('js')
    <script>
        function setMode(mode) {
            $('.item-mode').removeClass('mode-active');
            $('#mode_' + mode).addClass('mode-active')

            render(mode)
        }

        function render(mode = 'live') {
            $("#loading_container").show()
            var _token = $('meta[name="csrf-token"]').attr('content');
            var formData = new FormData();
            formData.append("mode", mode);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': _token
                }
            });
            $.ajax({
                url: '{{route('web.getMatches')}}',
                type: 'post',
                data: formData,
                contentType: false,
                processData: false,
                cache: false,
                success: function (data) {
                    const {success, html, total_matches} = data
                    if (success) {
                        $("#loading_container").hide()
                        $('#content-matches').html(html)
                        if (mode == 'live') {
                            $('#total_matches').html(`(${total_matches})`)
                        }
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                }
            });
        }

        $(document).ready(function () {
            render('live')
        });

    </script>
@endsection