
// $(document).ready(function(){
//     $('.date').mask('00/00/0000');
//     $('.time').mask('00:00:00');
//     $('.date_time').mask('00/00/0000 00:00:00');
//     $('.cep').mask('00000-000');
//     $('.phone').mask('0000-0000');
//     $('.phone_with_ddd').mask('(00) 0000-0000');
//     $('.phone_us').mask('(000) 000-0000');
//     $('.mixed').mask('AAA 000-S0S');
//     $('.cpf').mask('000.000.000-00', {reverse: true});
//     $('.cnpj').mask('00.000.000/0000-00', {reverse: true});
//     $('.money').mask('000.000.000.000.000,00', {reverse: true});
//     $('.money2').mask("#.##0,00", {reverse: true});
//     $('.ip_address').mask('0ZZ.0ZZ.0ZZ.0ZZ', {
//       translation: {
//         'Z': {
//           pattern: /[0-9]/, optional: true
//         }
//       }
//     });
//     $('.ip_address').mask('099.099.099.099');
//     $('.percent').mask('##0,00%', {reverse: true});
//     $('.clear-if-not-match').mask("00/00/0000", {clearIfNotMatch: true});
//     $('.placeholder').mask("00/00/0000", {placeholder: "__/__/____"});
//     $('.fallback').mask("00r00r0000", {
//         translation: {
//           'r': {
//             pattern: /[\/]/,
//             fallback: '/'
//           },
//           placeholder: "__/__/____"
//         }
//       });
//     $('.selectonfocus').mask("00/00/0000", {selectOnFocus: true});

//   });



$(function ($) {

    $('.content').css({'height':'90%', 'overflow':'auto' });

    //var baseUrl = '/proeffect/public'
    var baseUrl = ''

    $('.cep').mask('00000-000', {reverse: true});
    $('.sonumeros').mask('000000000000', {reverse: true});
    $('.mask_minutos').mask('00:00', {reverse: true});
    $('.mask_horas').mask('00:00:00', {reverse: true});
    $('.mask_valor').mask("###0,00", {reverse: true});
    $('.mask_date').mask('00/00/0000');

    var behavior = function (val) {
        return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
    },

    options = {
        onKeyPress: function (val, e, field, options) {
            field.mask(behavior.apply({}, arguments), options);
        }
    };
    $('.mask_phone').mask(behavior, options);


    var validacao_cpf_cnpj = function (val) {
        return val.replace(/\D/g, '').length === 14 ? '00.000.000/0000-00':'000.000.000-00999';
    },
    options = {
        onKeyPress: function (val, e, field, options) {
            field.mask(validacao_cpf_cnpj.apply({}, arguments), options);
        }
    };
    $('.mask_cfp_cnpj').mask(validacao_cpf_cnpj, options);

    $('.toast').hide();

    $(document).on('click', '.acao', function () {
        $contador = $(this).data('contador');
        $('.item_'+$contador).remove();
    });

    $(document).on('click', '.manutencao', function () {
        alert('manutenção');
    });

    $(document).on('click', '#novo_item', function () {
        contador = $("#tabela_itens tbody tr").length
        contador = contador + 1
        html="<tr align='center' class='item_"+contador+" itens_pedido' data-itens_id="+contador+">"+
                "<td class='contador'><input type='text' name='itens_contador[]' class='form-control' value='"+contador+"' readonly></td>"+
                "<td class='descricao'><input type='text' name='itens_descricao[]' class='form-control' value='' required></td>"+
                "<td class='qtde'><input type='text' name='itens_qtde[]' class='form-control atualiza_valor_unitario qtde_"+contador+"' value='0' required ></td>"+
                "<td class='valor_unitario'><input type='text' name='itens_valor_unitario[]' class='form-control atualiza_valor_unitario mask_valor valor_unitario_"+contador+"' value='0,00'></td>"+
                "<td class='valor'><input type='text' name='itens_valor[]' class='form-control soma_valor mask_valor valor_"+contador+"' value='0,00'></td>"+
                "<td ><i class='fa fa-times acao' data-contador='"+contador+"' style='cursor: pointer;'></i></td>"+
            "</tr>";

        $('#tabela_itens tbody').append(html)
        $('#tabela_itens tbody tr input[name="itens_descricao[]"]').last().focus();

    });

    $(document).on('blur', '.soma_valor', function () {
            var total = 0;
            $('.soma_valor').each(function () {

                total += parseFloat($(this).val().replace(',', '.'));
            });
            $('#valor_total').val(total.toFixed(2).replace('.', ','));
    });
    $(document).on('keyup', '.atualiza_valor_unitario', function () {
            var total = 0;

            $('.itens_pedido').each(function () {
                itens_id = $(this).data('itens_id');


                qtde = $('.qtde_'+itens_id).val().replace(',', '.')
                valor_unitario = $('.valor_unitario_'+itens_id).val().replace(',', '.')

                total = parseFloat(qtde) * parseFloat(valor_unitario);

                $('.valor_'+itens_id).val(total.toFixed(2).replace('.', ','));

            });

            $('.soma_valor').blur();
    });

    $(document).on("focus", ".mask_valor", function() {
        $(this).mask('###0,00', {reverse: true});
     });
    $(document).on("focus", ".data_pagamento", function() {
        $(this).mask('00/00/0000');
     });

    //INICIO bloco de select2 de autocomplete --------------------------------------------

    $('.select_autocomplete').select2({
        placeholder: "Buscar",
        allowClear: true
    });

    $('.select_autocomplete').on('select2:open', function() {
        document.querySelector('.select2-search__field').focus();
    });
    //FIM bloco de select2 de autocomplete --------------------------------------------

    $(document).on('blur', '.observacoes', function () {
        item_pedido = $(this).data('item_pedido');
        tipo = $(this).data('tipo');
        responsavel = $(this).data('item-responsavel');
        id = $('#id').val();
        observacao = $(this).val();
        var elemento = $(this);
        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        $.ajax({
            type: 'POST',
            url: baseUrl + '/pedidos/observacoes',
            data: {
                'pedidos_id': id,
                'items_id': item_pedido,
                'observacao': observacao,
                'responsavel_id': responsavel,
                'tipo': tipo,
                '_token': csrfToken
            },
            success: function (data) {
                console.log('sucesso');
                elemento.css({
                    'border': '1px solid green',
                    'transition': 'border-color 0.5s ease, border-width 0.5s ease'
                });

            },
            error: function (data) {
                console.log('erro');
                elemento.css({
                    'border': '1px solid red',
                    'transition': 'border-color 0.5s ease, border-width 0.5s ease'
                });
            }
        });

    });

    function abreAlertSuccess(texto, erro) {
        if(erro) {
            $('.toast').addClass('bg-danger')
        } else {
            $('.toast').addClass('bg-success')
        }
        $('.textoAlerta').text(texto);
        $('.toast').show();
        setTimeout(function () {
            $('.toast').hide('slow');
        }, 7000);
    };

});


