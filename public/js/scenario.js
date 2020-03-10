var moveX = 0;
var moveY = 0;
var startX = 0;
var startY = 0;
var endX = 0;
var endY = 0;
var fit = -30;
var cell_index = 0;
var chain_index = 0;
var sideber_offset = parseInt($("#sideber").css('width').replace('px', ''));

$(function () {
  cell_index = $("#cells").attr('data-index') ? parseInt($("#cells").attr('data-index')) : 0;
  chain_index = $("#chains").attr('data-index') ? parseInt($("#chains").attr('data-index')) : 0;

  // 保存
  $("#submit").click(function(){
    if ($("#scenario_name").val().length == 0) {
      alert('シナリオ名を入力してください');
      return;
    }
    $("#svg_inner").val($("#active_area").html());
    $("#form").submit();
  });

  // セル配置
  $("#sideber > div").draggable({
    revert: true
  });
  $("#contents").droppable({
     accept: "#sideber > div",
     drop: function( event, ui ) {
      var top = ui.draggable.offset().top;
      var left = ui.draggable.offset().left;
      var data_type = ui.draggable.attr('data-type');

      left = left - sideber_offset;

      var g = document.createElementNS("http://www.w3.org/2000/svg", "g");
      g.setAttribute('class', 'cell');
      g.setAttribute('data-index', cell_index);
      g.setAttribute('data-type', data_type);

      // 左ポインタ
      var s_circle = document.createElementNS("http://www.w3.org/2000/svg", "circle");
      s_circle.setAttribute('cx', left);
      s_circle.setAttribute('cy', top + 25);
      s_circle.setAttribute('r', 10);
      s_circle.setAttribute('class', 's_pointer');
      s_circle.setAttribute('fill', 'green');

      // 右ポインタ
      var e_circle = document.createElementNS("http://www.w3.org/2000/svg", "circle");
      e_circle.setAttribute('cx', left + 150);
      e_circle.setAttribute('cy', top + 25);
      e_circle.setAttribute('r', 10);
      e_circle.setAttribute('class', 'e_pointer');
      e_circle.setAttribute('fill', 'green');

      // 閉じる
      var del_circle = document.createElementNS("http://www.w3.org/2000/svg", "circle");
      del_circle.setAttribute('cx', left + 150);
      del_circle.setAttribute('cy', top);
      del_circle.setAttribute('r', 10);
      del_circle.setAttribute('fill', 'gray');
      del_circle.setAttribute('class', 'delete');

      var del_closs1 = document.createElementNS("http://www.w3.org/2000/svg", "line");
      del_closs1.setAttribute('x1', left + 150 - 5);
      del_closs1.setAttribute('y1', top - 5);
      del_closs1.setAttribute('x2', left + 150 + 5);
      del_closs1.setAttribute('y2', top + 5);
      del_closs1.setAttribute('stroke', 'black');
      del_closs1.setAttribute('stroke-width', 1);

      var del_closs2 = document.createElementNS("http://www.w3.org/2000/svg", "line");
      del_closs2.setAttribute('x1', left + 150 - 5);
      del_closs2.setAttribute('y1', top + 5);
      del_closs2.setAttribute('x2', left + 150 + 5);
      del_closs2.setAttribute('y2', top - 5);
      del_closs2.setAttribute('stroke', 'black');
      del_closs2.setAttribute('stroke-width', 1);
      // 閉じる

      var rect = document.createElementNS("http://www.w3.org/2000/svg", "rect");
      rect.setAttribute('x', left);
      rect.setAttribute('y', top);
      rect.setAttribute('width', 150);
      rect.setAttribute('height', 50);

      if (data_type == 'u_s' || data_type == 'u_t') {
        rect.setAttribute('fill', 'blue');
      } else if (data_type == 's_s' || data_type == 's_t') {
        rect.setAttribute('fill', 'red');
      } else {
        rect.setAttribute('fill', 'gray');
      }

      // テキスト表示（７文字程度
      var text_area = document.createElementNS("http://www.w3.org/2000/svg", "rect");
      text_area.setAttribute('x', left + 5);
      text_area.setAttribute('y', top + 5);
      text_area.setAttribute('width', 140);
      text_area.setAttribute('height', 40);
      text_area.setAttribute('fill', 'white');

      var text = document.createElementNS("http://www.w3.org/2000/svg", "text");
      text.setAttribute('x', left + 5 + 5);
      text.setAttribute('y', top + 5 + 40 - 13);
      text.setAttribute('font-size', 18);
      text.setAttribute('class', 'cell_text');
      if (data_type == 'u_s') {
        text.append('ユーザー起点');
      }
      if (data_type == 's_s') {
        text.append('システム起点');
      }

      var system;
      switch (data_type) {
        case 's_s':
          system = 0;
          break;
        case 's_t':
          system = 1;
          break;
        case 'u_s':
          system = 2;
          break;
        case 'u_t':
          system = 3;
          break;
        case 's_m':
          system = 4;
          break;
      }

      addForm(cell_index, system);

      g.append(rect);
      g.append(text_area);
      g.append(text);
      if (data_type != 'u_s' && data_type != 's_s') {
        g.append(s_circle);
      }
      if (data_type != 's_m') {
        g.append(e_circle);
      }
      g.append(del_circle);
      g.append(del_closs1);
      g.append(del_closs2);


      $('#active_area').append(g);
      cell_index++;

      cellDrag();
    }
  });

  // セル操作
  $("#contents").on("click", ".cell", function () {
    var index = $(this).attr('data-index');
    var data_type = $(this).attr('data-type');

    $("#target_index").val(index);

    $("#cells > *").hide();
    if (data_type == 'u_s' || data_type == 's_s') {
      return;
    }
    if (data_type == 'u_m') {
      return;
    }

    $("#cells > [data-index="+index+"] .input_boxs > *").removeClass('target');
    $("#cells > [data-index="+index+"] .speech").addClass('target');
    $("#cells > [data-index="+index+"]").show();
    if (data_type == 's_t') {
      $(".input_tabs > *:first-child").show();
    } else {
      $(".input_tabs > *:first-child").hide();
    }
  });
  $("#contents").on("click", ".delete", function () {
    var index = $(this).parent().attr('data-index');
    var line_index;
    $(this).parent().remove();
    $("#cells > div[data-index='"+index+"']").remove();

    $("g[data-prev-index='"+index+"']").each(function(index) {
      line_index = $(this).attr('chain-index');
      $(this).remove();
      $("#chains > div[index='"+line_index+"']").remove();
    });

    $("g[data-next-index='"+index+"']").each(function(index) {
      line_index = $(this).attr('chain-index');
      $(this).remove();
      $("#chains > div[index='"+line_index+"']").remove();
    });
  });
  $("#contents").on("click", ".del_circle", function () {
    var index = $(this).parent().attr('chain-index');
    $(this).parent().remove();
    $("#chains > div[data-index='"+index+"']").remove();
  });
  $("#contents").on("click", ".del_line1", function () {
    var index = $(this).parent().attr('chain-index');
    $(this).parent().remove();
    $("#chains > div[data-index='"+index+"']").remove();
  });
  $("#contents").on("click", ".del_line2", function () {
    var index = $(this).parent().attr('chain-index');
    $(this).parent().remove();
    $("#chains > div[data-index='"+index+"']").remove();
  });
  // detail
  // タブ選択
  $("#detail").on("click", ".input_tabs > .condition", function(){
    $(this).parent().parent().find(".input_boxs > *").removeClass('target');
    $(this).parent().parent().find(".input_boxs .condition").addClass('target');
  });
  $("#detail").on("click", ".input_tabs > .speech", function(){
    $(this).parent().parent().find(".input_boxs > *").removeClass('target');
    $(this).parent().parent().find(".input_boxs .speech").addClass('target');
  });
  $("#detail").on("click", ".input_tabs > .memory", function(){
    $(this).parent().parent().find(".input_boxs > *").removeClass('target');
    $(this).parent().parent().find(".input_boxs .memory").addClass('target');
  });


  $("#detail").on("click", ".conditions_add", function(event){
    event.preventDefault();

    var cell_index = $(this).parent().parent().parent().attr('data-index');
    var item_index = $(this).parent().find(".conditions").attr('data-index');
    item_index = parseInt(item_index);

    var add = $('#input_proto .conditions').clone();
    add.removeClass('conditions');

    add.find("input[name*='_index']").each(function() {
      var name = $(this).attr("name");
      name = name.replace('_index', item_index);
      $(this).attr("name", 'cells['+cell_index+']' + name);
    });
    $(this).parent().find(".conditions").append(add);

    $(this).parent().find(".conditions").attr('data-index', item_index);
  });

  $("#detail").on("click", ".speechs_add", function(event){
    event.preventDefault();

    var cell_index = $(this).parent().parent().parent().attr('data-index');
    var item_index = $(this).parent().find(".speechs").attr('data-index');
    item_index = parseInt(item_index);

    var add = $('#input_proto .speechs').clone();
    add.removeClass('speechs');

    add.find("input[name*='_index']").each(function() {
      var name = $(this).attr("name");
      name = name.replace('_index', item_index);
      $(this).attr("name", 'cells['+cell_index+']' + name);
    });
    $(this).parent().find(".speechs").append(add);

    $(this).parent().find(".speechs").attr('data-index', item_index);
  });

  $("#detail").on("click", ".memories_add", function(event){
    event.preventDefault();

    var cell_index = $(this).parent().parent().parent().attr('data-index');
    var item_index = $(this).parent().find(".memories").attr('data-index');
    item_index = parseInt(item_index);

    var add = $('#input_proto .memories').clone();
    add.removeClass('memories');

    add.find("input[name*='_index']").each(function() {
      var name = $(this).attr("name");
      name = name.replace('_index', item_index);
      $(this).attr("name", 'cells['+cell_index+']' + name);
    });
    $(this).parent().find(".memories").append(add);

    $(this).parent().find(".memories").attr('data-index', item_index);
  });

  $("#detail").on("change", "input[name*='[0][text]']", function(event){
    var box = $(this).parent().parent().parent().parent().parent();
    var index = box.attr('data-index');
    var text = $(this).val().slice(0, 7);
    $('.cell[data-index="'+index+'"]').find(".cell_text").html(text);
  });
  $("#detail").on("change", ".move_scenario", function(event){
    var box = $(this).parent().parent().parent();
    var index = box.attr('data-index');
    var text = $(this).find("[value='"+$(this).val()+"']")
    if (text.length == 0) {
      text = '';
    } else {
      text = text.attr('data-name').slice(0, 7);
    }
    $('.cell[data-index="'+index+'"]').find(".cell_text").html(text);
  });
  $("#detail").on("change", ".condition_type", function(event){
    $(this).parent().parent().find(".condition_type_val").val($(this).val());
    $(this).parent().parent().find(".condition_value").val('');
  });
  $("#detail").on("change", ".condition_value", function(event){
    $(this).parent().parent().find(".condition_value_val").val($(this).val());
  });

  cellDrag();
});

function cellDrag() {
  $(".cell").draggable({
    containment: "#active_area",
    cancel: ".pointer",
    start: function(event, ui) {
      var target = ui.helper.first();
      moveX = target.attr('x') ? parseInt(target.attr('x')) : 0;
      moveY = target.attr('y') ? parseInt(target.attr('y')) : 0;
    },
    drag: function(event, ui) {
      var dragY = (ui.position.top - ui.originalPosition.top);
      var dragX = (ui.position.left -  ui.originalPosition.left);
      var top = moveY + dragY;
      var left = moveX + dragX;
      var x;
      var y;

      var target = ui.helper.first();
      target.attr('transform', 'translate('+left+', '+top+')');
      target.attr('x', left);
      target.attr('y', top);

      $('[data-prev-index="'+target.attr('data-index')+'"]').each(function(index) {
        var arrow = $(this).find('.arrow');
        x = parseInt(arrow.attr('spot-x1')) + dragX;
        y = parseInt(arrow.attr('spot-y1')) + dragY;
        arrow.attr('x1', x);
        arrow.attr('y1', y);

        var herfX = (parseInt(arrow.attr('x1')) + parseInt(arrow.attr('x2'))) / 2;
        var herfY = (parseInt(arrow.attr('y1')) + parseInt(arrow.attr('y2'))) / 2;

        var del_circle = $(this).find('.del_circle');
        del_circle.attr('cx', herfX);
        del_circle.attr('cy', herfY);

        var del_line1 = $(this).find('.del_line1');
        del_line1.attr('x1', herfX - 5);
        del_line1.attr('y1', herfY - 5);
        del_line1.attr('x2', herfX + 5);
        del_line1.attr('y2', herfY + 5);

        var del_line2 = $(this).find('.del_line2');
        del_line2.attr('x1', herfX - 5);
        del_line2.attr('y1', herfY + 5);
        del_line2.attr('x2', herfX + 5);
        del_line2.attr('y2', herfY - 5);
      });
      $('[data-next-index="'+target.attr('data-index')+'"]').each(function(index) {
        var arrow = $(this).find('.arrow');
        x = parseInt(arrow.attr('spot-x2')) + dragX;
        y = parseInt(arrow.attr('spot-y2')) + dragY;
        arrow.attr('x2', x);
        arrow.attr('y2', y);

        var herfX = (parseInt(arrow.attr('x1')) + parseInt(arrow.attr('x2'))) / 2;
        var herfY = (parseInt(arrow.attr('y1')) + parseInt(arrow.attr('y2'))) / 2;

        var del_circle = $(this).find('.del_circle');
        del_circle.attr('cx', herfX);
        del_circle.attr('cy', herfY);

        var del_line1 = $(this).find('.del_line1');
        del_line1.attr('x1', herfX - 5);
        del_line1.attr('y1', herfY - 5);
        del_line1.attr('x2', herfX + 5);
        del_line1.attr('y2', herfY + 5);

        var del_line2 = $(this).find('.del_line2');
        del_line2.attr('x1', herfX - 5);
        del_line2.attr('y1', herfY + 5);
        del_line2.attr('x2', herfX + 5);
        del_line2.attr('y2', herfY - 5);
      });

      ui.position.top = 0;
      ui.position.left = 0;
    },
    stop: function(event, ui) {
      var target = ui.helper.first();
      $('[data-prev-index="'+target.attr('data-index')+'"]').each(function(index) {
        var arrow = $(this).find('.arrow');
        var x = parseInt(arrow.attr('x1'));
        var y = parseInt(arrow.attr('y1'));
        arrow.attr('spot-x1', x);
        arrow.attr('spot-y1', y);
      });
      $('[data-next-index="'+target.attr('data-index')+'"]').each(function(index) {
        var arrow = $(this).find('.arrow');
        var x = parseInt(arrow.attr('x2'));
        var y = parseInt(arrow.attr('y2'));
        arrow.attr('spot-x2', x);
        arrow.attr('spot-y2', y);
      });
    }
  });

  $(".e_pointer").draggable({
    snap: ".s_pointer",
    helper: function( event ) {
      return $( "<div></div>" );
    },
    start: function(event, ui) {
      var target = $(event.target);
      var parent = $(event.target).parent();

      var targetX = target.attr('cx') ? parseInt(target.attr('cx')) : 0;
      var targetY = target.attr('cy') ? parseInt(target.attr('cy')) : 0;
      var offsetX = parent.attr('x') ? parseInt(parent.attr('x')) : 0;
      var offsetY = parent.attr('y') ? parseInt(parent.attr('y')) : 0;

      startX = targetX + offsetX;
      startY = targetY + offsetY;

      endX = event.clientX - sideber_offset + fit;
      endY = event.clientY;
      setArrow();
    },
    drag: function(event, ui) {
      var box = $("#cousor");

      endX = event.clientX - sideber_offset + fit;
      endY = event.clientY;

      setArrow();
    },
    stop: function(event, ui) {
      var base = $(event.target);
      var target = $(event.originalEvent.target);
      if (target.hasClass('s_pointer')) {
        s_cell = base.parent().attr('data-index');
        e_cell = target.parent().attr('data-index');

        if ( $("line[data-prev-index='"+s_cell+"'][data-next-index='"+e_cell+"']").length == 0 ) {
          var offsetX = target.parent().attr('x') ? parseInt(target.parent().attr('x')) : 0;
          var offsetY = target.parent().attr('y') ? parseInt(target.parent().attr('y')) : 0;

          endX = parseInt(target.attr('cx')) + offsetX;
          endY = parseInt(target.attr('cy')) + offsetY;

          var g = document.createElementNS("http://www.w3.org/2000/svg", "g");
          g.setAttribute('chain-index', chain_index);
          g.setAttribute('data-prev-index', s_cell);
          g.setAttribute('data-next-index', e_cell);

          var arrow = document.createElementNS("http://www.w3.org/2000/svg", "line");
          arrow.setAttribute('x1', startX);
          arrow.setAttribute('y1', startY);
          arrow.setAttribute('x2', endX);
          arrow.setAttribute('y2', endY);

          arrow.setAttribute('spot-x1', startX);
          arrow.setAttribute('spot-y1', startY);
          arrow.setAttribute('spot-x2', endX);
          arrow.setAttribute('spot-y2', endY);

          arrow.setAttribute('stroke', 'black');
          arrow.setAttribute('stroke-width', 1);
          arrow.setAttribute('class', 'arrow');

          var herfX = (startX + endX) / 2;
          var herfY = (startY + endY) / 2;

          // 閉じる
          var del_circle = document.createElementNS("http://www.w3.org/2000/svg", "circle");
          del_circle.setAttribute('cx', herfX);
          del_circle.setAttribute('cy', herfY);
          del_circle.setAttribute('r', 10);
          del_circle.setAttribute('fill', 'gray');
          del_circle.setAttribute('class', 'del_circle');

          var del_closs1 = document.createElementNS("http://www.w3.org/2000/svg", "line");
          del_closs1.setAttribute('x1', herfX - 5);
          del_closs1.setAttribute('y1', herfY - 5);
          del_closs1.setAttribute('x2', herfX + 5);
          del_closs1.setAttribute('y2', herfY + 5);
          del_closs1.setAttribute('stroke', 'black');
          del_closs1.setAttribute('stroke-width', 1);
          del_closs1.setAttribute('class', 'del_line1');

          var del_closs2 = document.createElementNS("http://www.w3.org/2000/svg", "line");
          del_closs2.setAttribute('x1', herfX - 5);
          del_closs2.setAttribute('y1', herfY + 5);
          del_closs2.setAttribute('x2', herfX + 5);
          del_closs2.setAttribute('y2', herfY - 5);
          del_closs2.setAttribute('stroke', 'black');
          del_closs2.setAttribute('stroke-width', 1);
          del_closs2.setAttribute('class', 'del_line2');

          g.append(arrow);
          g.append(del_circle);
          g.append(del_closs1);
          g.append(del_closs2);
          $('#active_area').append(g);

          addChain(chain_index, s_cell, e_cell);

          chain_index++;
        }
      }

      startX = 0;
      startY = 0;
      endX = 0;
      endY = 0;
      setArrow();
    }
  });
}

function setArrow() {
  var arrow = $("#arrow");
  arrow.attr('x1', startX);
  arrow.attr('y1', startY);
  arrow.attr('x2', endX);
  arrow.attr('y2', endY);
}

function addForm(index, data_type) {
  // var index = $("#cells").attr('data-index');
  // index = parseInt(index);

  if (data_type == 4) {
    var box = $("#move_proto").clone();
    box.find(".move_scenario").attr("name", 'cells['+index+']' + box.find(".move_scenario").attr("name"));
  } else {
    var box = $("#proto_types").clone();
  }
  // move_proto
  box.attr("id", '');
  box.attr('data-index', index);

  box.find("input").each(function() {
    var name = $(this).attr("name");
    $(this).attr("name", 'cells['+index+']' + name);

    if (name.indexOf('system') != -1 ) {
      $(this).val(data_type);
    }
    if (name.indexOf('index') != -1 ) {
      $(this).val(index);
    }
  });
  $("#cells").attr('data-index', index + 1);
  $('#cells').append(box);
}

function addChain(index, s_cell, e_cell) {
  // var index = $("#chains").attr('data-index');
  // index = parseInt(index);

  var box = $('#chain_proto').clone();

  box.attr("id", '');
  box.attr('data-index', index);


  box.find("input").each(function() {
    var name = $(this).attr("name");
    $(this).attr("name", 'chains['+index+']' + name);

    if (name.indexOf('index') != -1 ) {
      $(this).val(index);
    }
    if (name.indexOf('prev') != -1 ) {
      $(this).val(s_cell);
    }
    if (name.indexOf('next') != -1 ) {
      $(this).val(e_cell);
    }
  });

  $('#chains').append(box);
}

