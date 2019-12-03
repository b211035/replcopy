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

      var s_circle = document.createElementNS("http://www.w3.org/2000/svg", "circle");
      s_circle.setAttribute('cx', left);
      s_circle.setAttribute('cy', top + 25);
      s_circle.setAttribute('r', 20);
      s_circle.setAttribute('class', 's_pointer');
      s_circle.setAttribute('fill', 'green');

      var e_circle = document.createElementNS("http://www.w3.org/2000/svg", "circle");
      e_circle.setAttribute('cx', left + 150);
      e_circle.setAttribute('cy', top + 25);
      e_circle.setAttribute('r', 20);
      e_circle.setAttribute('class', 'e_pointer');
      e_circle.setAttribute('fill', 'green');

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

    $("line[data-prev-index='"+index+"']").each(function(index) {
      line_index = $(this).attr('chain-index');
      $(this).remove();
      $("#chains > div[index='"+line_index+"']").remove();
    });

    $("line[data-next-index='"+index+"']").each(function(index) {
      line_index = $(this).attr('chain-index');
      $(this).remove();
      $("#chains > div[index='"+line_index+"']").remove();
    });
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
      var top = moveY + ui.position.top;
      var left = moveX + ui.position.left;

      var target = ui.helper.first();
      target.attr('transform', 'translate('+left+', '+top+')');
      target.attr('x', left);
      target.attr('y', top);

      $('[data-prev-index="'+target.attr('data-index')+'"]').each(function(index) {
        var x = parseInt($(this).attr('spot-x1')) + ui.position.left;
        var y = parseInt($(this).attr('spot-y1')) + ui.position.top;
        $(this).attr('x1', x);
        $(this).attr('y1', y);
      });
      $('[data-next-index="'+target.attr('data-index')+'"]').each(function(index) {
        var x = parseInt($(this).attr('spot-x2')) + ui.position.left;
        var y = parseInt($(this).attr('spot-y2')) + ui.position.top;
        $(this).attr('x2', x);
        $(this).attr('y2', y);
      });

      ui.position.top = 0;
      ui.position.left = 0;
    },
    stop: function(event, ui) {
      var target = ui.helper.first();
      $('[data-prev-index="'+target.attr('data-index')+'"]').each(function(index) {
        var x = parseInt($(this).attr('x1'));
        var y = parseInt($(this).attr('y1'));
        $(this).attr('spot-x1', x);
        $(this).attr('spot-y1', y);
      });
      $('[data-next-index="'+target.attr('data-index')+'"]').each(function(index) {
        var x = parseInt($(this).attr('x2'));
        var y = parseInt($(this).attr('y2'));
        $(this).attr('spot-x2', x);
        $(this).attr('spot-y2', y);
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
          // $(event.target).parent().append(arrow);
          arrow.setAttribute('chain-index', chain_index);
          arrow.setAttribute('data-prev-index', s_cell);
          arrow.setAttribute('data-next-index', e_cell);
          $('#active_area').append(arrow);

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

  var box = $("#proto_types").clone();
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

