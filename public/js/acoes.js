$(function ($) {
    // baseUrl = '/topdesign/public'
    baseUrl = ''
    $(document).on('click', '.add', function () {

        $('#tipo').val($(this).data('tipo'));
        $('#item_pedido').val($(this).data('pedido'));

        $('#modal_funcionarios').modal('show');

    });

    $(document).on('click', '.desvincular_funcionario', function () {

        $('#item_pedido_desvincular').val($(this).data('item_pedido'));
        $('#pedido_id_desvincular').val($(this).data('pedido'));
        $('#user_id_desvincular').val($(this).data('usuario'));
        $('#tipo_desvincular').val($(this).data('tipo'));
        $('#modal_desvincular_funcionario').modal('show');

    });

    $(document).on('click', '#adicionar_funcionario', function () {
        salva_funcionarios = [];
        $('.funcionarios').each(function (c, j) {
            if($(this).prop('checked') == true ) {
                salva_funcionarios.push($(this).val());
            }
        })

        item_pedido = $('#item_pedido').val();
        tipo = $('#tipo').val();
        var xhr = new XMLHttpRequest();
        xhr.open("POST",  baseUrl + '/incluir-funcionario', true);
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status >= 200 && xhr.status < 300) {
                    location.reload();
                } else {
                    var data = JSON.parse(xhr.responseText);
                    alert('Ocorreu um erro al alterar o status!')
                }
            }
        };

        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        var requestData = {
            'pedido_id': $('#id').val(),
            'item_pedido': item_pedido,
            'tipo': tipo,
            'funcionarios': JSON.stringify(salva_funcionarios),
            '_token': csrfToken
        };
        xhr.send(JSON.stringify(requestData));

    });

    $(document).on('click', '#desvincular_funcionario', function () {

        item_pedido = $('#item_pedido_desvincular').val();
        tipo = $('#tipo_desvincular').val();
        user_id_desvincular = $('#user_id_desvincular').val();

        var xhr = new XMLHttpRequest();
        xhr.open("POST",  baseUrl + '/desvincular-funcionario', true);
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status >= 200 && xhr.status < 300) {
                    location.reload();
                } else {
                    var data = JSON.parse(xhr.responseText);
                    alert('Ocorreu um erro al alterar o status!')
                }
            }
        };

        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        var requestData = {
            'pedido_id': $('#id').val(),
            'item_pedido': item_pedido,
            'tipo': tipo,
            'funcionario': user_id_desvincular,
            '_token': csrfToken
        };
        xhr.send(JSON.stringify(requestData));

    });

    $(document).on('click', '#finalizar_etapa', function () {
        item_pedido = $('#acao_item_pedido').val();
        tipo = $('#acao_tipo').val();
        user_id = $('#user_id').val();
        id = $('#id').val();
        elemento_id = tipo+'_'+user_id+id+item_pedido;
        if(tipo == 'entrega') {
            recebedor = $('#recebedor').val();
            data_entrega = $('#data_entrega').val();
        }
        var xhr = new XMLHttpRequest();
        xhr.open("POST",  baseUrl + '/finalizar_etapa', true);
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status >= 200 && xhr.status < 300) {
                    $('#'+elemento_id).removeClass('bg-warning').addClass('bg-success');
                    $('.close').click();
                } else {
                    var data = JSON.parse(xhr.responseText);
                    alert('Ocorreu um erro al alterar o status!')
                }
            }
        };

        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        var requestData = {
            'pedido_id': $('#id').val(),
            'user_id': user_id,
            'item_pedido': item_pedido,
            'tipo': tipo,
            'recebedor': recebedor,
            'data_entrega': data_entrega,
            '_token': csrfToken
        };
        xhr.send(JSON.stringify(requestData));

    });


    $(document).on('click', '.acao_usuario', function () {
        tipo = $(this).data('tipo')
        if(tipo == 'entrega') {
            $('#input_recebedor').show();
            $('#input_data_entrega').show();
        } else {
            $('#input_recebedor').hide();
            $('#input_data_entrega').hide();
        }
        $('#acao_tipo').val(tipo);
        $('#acao_pedido').val($(this).data('pedido'));
        $('#acao_item_pedido').val($(this).data('item_pedido'));
        $('#user_id').val($(this).data('usuario'));

        $('#modal_acoes').modal('show');

    });

    $(document).on('click', '#adicionar_pagamento', function () {
        contador = $("#tabela_pagamentos tbody tr").length
        contador = contador + 1

        html="<tr align='left' class='item_"+contador+"'>"+
                "<td class='descricao'><input type='text' name='data_pagamento[]' class='form-control data_pagamento col-sm-4' value=''></td>"+
                "<td class='contador'><input type='text' name='valor_pago[]' class='form-control  valor_pago col-sm-4 mask_valor' value='0,00'></td>"+
                "<td>"+
                    "<select class='form-control col-md-6' id='forma_pagamento' name='forma_pagamento[]'>"+
                        "<option value='2'>Cartão</option>"+
                        "<option value='4'>PIX</option>"+
                        "<option value='1'>Dinheiro</option>"+
                        "<option value='3'>Transferência</option>"+
                    "</select>"+
                "</td>"+
            "</tr>";

        $('#tabela_pagamentos tbody').append(html)
        $('#valor_pago, #data_pagamento').val('')

    });


});


