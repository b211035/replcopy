$('#initTopicId').on('change', function(e){
    var prevbotid = $('#botId').val(botid) ;
    var botid = $('#initTopicId option:selected').attr('botid');
    if (prevbotid != botid) {
        $('#appUserId').val('');
    }
    $('#botId').val(botid) ;
});

var bot_id;
var topic_id;
var user_id;
var text;
var ini_flg;
var url;

$('#talk').click(function(e){
    topic_id = $('#initTopicId').val();
    if (!topic_id) {
        alert('選択してください');
        return;
    }

    bot_id = $('#botId').val();
    user_id = $('#appUserId').val();

    if (user_id == '') {
        // id取得
        url = $('#userform').attr('action');

        $.ajax({
            type: "POST",
            url: url,
            data: { botId: bot_id },
            async: false
        }).done(function(res) {
            user_id = res.appUserId;
            $('#appUserId').val(user_id);
        });
    }

    var text = $('#voiceText').val();
    if (text == 'init') {
        ini_flg = true;
    } else {
        ini_flg = '';
    }
    $('#talkerea').append('<div>'+text+'</div>');

    url = $('#talkform').attr('action');

    $.ajax({
        type: "POST",
        url: url,
        data: {
            appUserId: user_id,
            botId: bot_id,
            voiceText: text,
            initTalkingFlag: ini_flg,
            initTopicId: topic_id
        }
    }).done(function(res) {
        $('#talkerea').append('<div>'+res.systemText.expression+'</div>');
    });
});
