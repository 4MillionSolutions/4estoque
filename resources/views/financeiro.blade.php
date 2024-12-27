@extends('adminlte::page')

@section('title', env('APP_NAME'))

<script src="vendor/jquery/jquery.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script src="js/jquery.mask.js"></script>
<script src="js/bootstrap.4.6.2.js?cache={{time()}}"></script>
<script src="js/main_custom.js"></script>
<script src="js/acoes.js?{{date('YmdHis')}}"></script>

<?php
use App\Http\Controllers\DateHelpers;
?>

@if(isset($tela) and $tela == 'pesquisa')
    @section('content_header')
    <div class="form-group row">
        <h1 class="m-0 text-dark col-sm-11 col-form-label">Pesquisa de {{ $nome_tela }}</h1>
        <div class="col-sm-1">
            @include('layouts.nav-open-incluir', ['rotaIncluir => $rotaIncluir'])
        </div>
    </div>
    @stop
    @section('content')
    <div class="right_col" role="main">
        <form id="filtro" action="financeiro" method="get" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
            <div class="form-group row">
                <label for="status_pedido" class="col-sm-2 col-form-label text-right">Status do pedido</label>
                <select class="form-control col-md-2" id="status_pedido" name="status_pedido">
                    <option value="">Todos</option>
                    <option value="Q" @if (isset($request) && $request->input('status_pedido') == 'Q'){{ ' selected '}}@else @endif>Quitado</option>
                    <option value="F" @if (isset($request) && $request->input('status_pedido') == 'F'){{ ' selected '}}@else @endif>Fora do vencimento</option>
                    <option value="D" @if (isset($request) && $request->input('status_pedido') == 'D'){{ ' selected '}}@else @endif>Dentro do vencimento</option>
                </select>
                <label for="cliente" class="col-sm-1 col-form-label text-right">Cliente</label>
                <div class="col-sm-2">
                    <input type="text" id="cliente" name="cliente" class="form-control" value="@if (isset($request) && trim($request->input('cliente')) != ''){{$request->input('cliente')}}@else @endif">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-5">
                    <button type="submit" class="btn btn-primary">Pesquisar</button>
                </div>
                <div class="col-sm-5">
                </div>
            </div>
        </form>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for=""></label>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h4>Encontrados</h4>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <table class="table table-striped text-center">
                            <thead>
                                <tr>
                                    <th>Pedido</th>
                                    <th>Cliente</th>
                                    <th>Status pedido</th>
                                    <th>Valor Combinado</th>
                                    <th>Entrada</th>
                                    <th>Dívida</th>
                                    <th>Pendência</th>
                                    <th>Data Entrada</th>
                                    <th>Data Conclusão</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($financeiro))
                                    @foreach ($financeiro as $financ)
                                        <tr>
                                            <th scope="row"><a href={{ URL::route($rotaAlterar, array('id' => $financ->id )) }}>{{$financ->pedidos_id}}</a></th>
                                            <td>{{$financ->nome_cliente}}</td>
                                            <td>
                                                @if($financ->valor_combinado != '' && $financ->vencimento != '')
                                                    @if($financ->restante_calc == 0)<h4><span class="badge badge-success">Quitado</span></h4>@else
                                                        @if(\Carbon\Carbon::parse($financ->vencimento)->isBefore(\Carbon\Carbon::today()))<h4><span class="badge badge-danger">Fora do vencimento</span></h4>@else<h4><span class="badge badge-warning">Dentro do vencimento</span></h4> @endif
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="mask_valor">{{number_format($financ->valor_combinado, 2, ',', '.')}}</td>
                                            <td class="mask_valor">{{number_format($financ->valor_entrada, 2, ',', '.')}}</td>
                                            <td class="mask_valor">{{number_format($financ->restante_calc, 2, ',', '.')}}</td>
                                            <td class="text-center ">@if(!empty($financ->restante_calc > 0))<h4><span class="badge badge-warning">Pendente</span></h4>@else<h4><span class="badge badge-success">OK</span> @endif</td>
                                            <td>{{$financ->data_entrada == '' ? '' : \Carbon\Carbon::parse($financ->data_entrada)->format('d/m/Y')}}</td>
                                            <td>{{$financ->vencimento == '' ? '' : \Carbon\Carbon::parse($financ->vencimento)->format('d/m/Y')}}</td>
                                        </tr>
                                        <?php
                                            $total_valor_combinado = isset($total_valor_combinado) ? $total_valor_combinado + $financ->valor_combinado : $financ->valor_combinado;
                                            $total_valor_entrada = isset($total_valor_entrada) ? $total_valor_entrada + $financ->valor_entrada : $financ->valor_entrada;
                                            $total_restante = isset($total_restante) ? $total_restante + $financ->restante_calc : $financ->restante_calc;
                                        ?>
                                    @endforeach
                                @endif
                            </tbody>
                            <tfooter>
                                @if($financeiro->isnotEmpty())
                                    <tr>
                                        <th colspan="3" class="text-right">Total:</th>
                                        <th class="mask_valor">{{number_format($total_valor_combinado, 2, ',', '.')}}</th>
                                        <th class="mask_valor">{{number_format($total_valor_entrada, 2, ',', '.')}}</th>
                                        <th class="mask_valor">{{number_format($total_restante, 2, ',', '.')}}</th>
                                        <th colspan="5"></th>
                                    </tr>
                                @endif
                            </tfooter>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @stop
@else
@section('content')
        @if($tela == 'alterar')
            @section('content_header')
                <h1 class="m-0 text-dark">Alteração de {{ $nome_tela }}</h1>
            @stop
            <form id="alterar" action="{{$rotaAlterar}}" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" method="post">
            <div class="form-group row">
                <input type="hidden" id="id" name="id" class="form-control col-md-7 col-xs-12" readonly="true" value="@if (isset($financeiro[0]->id)){{$financeiro[0]->id}}@else{{''}}@endif">
                <input type="hidden" id="pedidos_id" name="pedidos_id" class="form-control col-md-7 col-xs-12" @if($tela == 'alterar') readonly="true" @endif value="@if (isset($financeiro[0]->id)){{$financeiro[0]->pedidos_id}}@else{{''}}@endif">
            </div>
        @else
            @section('content_header')
                <h1 class="m-0 text-dark">Inclusão de {{ $nome_tela }}</h1>
            @stop
            <form id="incluir" action="{{$rotaIncluir}}" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" method="post">
                <div class="form-group row">
                    <label for="pedidos_id" class="col-sm-1 col-form-label">Pedido</label>
                    <div class="col-sm-2">
                        <input type=" text" id="pedidos_id" name="pedidos_id" class="form-control col-md-7 col-xs-12" @if($tela == 'alterar') readonly="true" @endif value="@if (isset($financeiro[0]->id)){{$financeiro[0]->pedidos_id}}@else{{''}}@endif">
                    </div>
                </div>
        @endif
            @csrf <!--{{ csrf_field() }}-->

            <div class="form-group row">
                <label for="clientes_id" class="col-sm-2 col-form-label">Cliente</label>
                <div class="col-sm-4">
                    <select class="form-control" @if($tela == 'alterar') disabled="true" @endif id="clientes_id" name="clientes_id">
                        <option value=""></option>
                        @if (isset($clientes))
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}"
                                    @if (isset($financeiro[0]->clientes_id) && $financeiro[0]->clientes_id == $cliente->id) selected="selected" @else{{ '' }} @endif>
                                    {{ $cliente->id . ' - ' . $cliente->nome }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <label for="documento " class="col-sm-1 col-form-label">CPF/CNPJ</label>
                <div class="col-sm-2">
                    <input class="form-control mask_cfp_cnpj" readonly id="documento" name="documento" value="@if (isset($financeiro[0]->documento)){{ trim($financeiro[0]->documento) }}@else{{ '' }}@endif"/>
                </div>
            </div>

            <div class="form-group row">

                <label for="data_transacao" class="col-form-label col-sm-2 ">Data entrega</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control mask_date" id="data_transacao" name="data_transacao"
                        placeholder="DD/MM/AAAA" value="@if (isset($financeiro[0]->data_transacao)){{ \Carbon\Carbon::parse($financeiro[0]->data_transacao)->format('d/m/Y')}}@else{{''}}@endif">
                </div>
                <label for="valor" class="col-sm-2 col-form-label">Valor combinado</label>
                <div class="col-sm-2">
                    <input class="form-control mask_valor" id="valor_combinado" name="valor_combinado" value="@if (isset($financeiro[0]->valor_combinado)){{ trim($financeiro[0]->valor_combinado) }}@else{{ '' }}@endif"/>
                </div>
                <select class="form-control col-md-2" id="forma_pagamento_valor_combinado" name="forma_pagamento_valor_combinado">
                    <option value="2" @if (isset($financeiro[0]->forma_pagamento_valor_combinado) && $financeiro[0]->forma_pagamento_valor_combinado =='2'){{ ' selected '}}@else @endif>Cartão</option>
                    <option value="4" @if (isset($financeiro[0]->forma_pagamento_valor_combinado) && $financeiro[0]->forma_pagamento_valor_combinado == '4'){{ ' selected '}}@else @endif>PIX</option>
                    <option value="1" @if (isset($financeiro[0]->forma_pagamento_valor_combinado) && $financeiro[0]->forma_pagamento_valor_combinado == '1'){{ ' selected '}}@else @endif>Dinheiro</option>
                    <option value="3" @if (isset($financeiro[0]->forma_pagamento_valor_combinado) && $financeiro[0]->forma_pagamento_valor_combinado =='3'){{ ' selected '}}@else @endif>Transferência</option>
                </select>

            </div>
            <div class="form-group row">
                <label for="data_entrada" class="col-sm-2 col-form-label">Data entrada</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control mask_date" id="data_entrada" name="data_entrada"
                        placeholder="DD/MM/AAAA" value="@if (!empty($financeiro[0]->data_entrada)){{ \Carbon\Carbon::parse($financeiro[0]->data_entrada)->format('d/m/Y')}}@else{{''}}@endif">
                </div>
                <label for="valor" class="col-sm-2 col-form-label">Valor Entrada</label>
                <div class="col-sm-2">
                    <input class="form-control mask_valor" id="valor_entrada" name="valor_entrada" value="@if (isset($financeiro[0]->valor_entrada)){{ $financeiro[0]->valor_entrada }}@else{{''}}@endif"/>
                </div>
                <select class="form-control col-md-2" id="forma_pagamento_valor_entrada" name="forma_pagamento_valor_entrada">
                    <option value="2" @if (isset($financeiro[0]->forma_pagamento_valor_entrada) && $financeiro[0]->forma_pagamento_valor_entrada =='2'){{ ' selected '}}@else @endif>Cartão</option>
                    <option value="4" @if (isset($financeiro[0]->forma_pagamento_valor_entrada) && $financeiro[0]->forma_pagamento_valor_entrada == '4'){{ ' selected '}}@else @endif>PIX</option>
                    <option value="1" @if (isset($financeiro[0]->forma_pagamento_valor_entrada) && $financeiro[0]->forma_pagamento_valor_entrada == '1'){{ ' selected '}}@else @endif>Dinheiro</option>
                    <option value="3" @if (isset($financeiro[0]->forma_pagamento_valor_entrada) && $financeiro[0]->forma_pagamento_valor_entrada =='3'){{ ' selected '}}@else @endif>Transferência</option>
                </select>
            </div>
            <table id="tabela_pagamentos" class="table table-striped">
                <thead>
                    <tr>
                        <th>Data parcela</th>
                        <th>Valor parcela</th>
                        <th>
                            <button type="button" id="adicionar_pagamento" class="btn btn-success">Adicionar</button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($financeiroPagamentos))
                    <?php $pago =0; ?>
                        @foreach ($financeiroPagamentos as $contador => $financeiro_pagamento)
                        <?php
                            $pago += $financeiro_pagamento->valor_pago;
                        ?>
                        <tr align='left' class='item_"{{ $contador }}"'>
                            <td class='descricao'>
                                <input type='text' name='data_pagamento[]' class='form-control col-sm-4  mask_date' placeholder="DD/MM/AAAA" value='{{ \Carbon\Carbon::parse($financeiro_pagamento->data_pagamento)->format('d/m/Y') }}'>
                            </td>
                            <td class='contador'>
                                <input type='text' name='valor_pago[]' class='form-control col-sm-4  mask_valor' value='{{ $financeiro_pagamento->valor_pago }}'>
                            </td>
                            <td>
                                <select class="form-control col-md-6" id="forma_pagamento" name="forma_pagamento[]">
                                    <option value="2" @if (isset($financeiro_pagamento->forma_pagamento) && $financeiro_pagamento->forma_pagamento =='2'){{ ' selected '}}@else @endif>Cartão</option>
                                    <option value="4" @if (isset($financeiro_pagamento->forma_pagamento) && $financeiro_pagamento->forma_pagamento == '4'){{ ' selected '}}@else @endif>PIX</option>
                                    <option value="1" @if (isset($financeiro_pagamento->forma_pagamento) && $financeiro_pagamento->forma_pagamento == '1'){{ ' selected '}}@else @endif>Dinheiro</option>
                                    <option value="3" @if (isset($financeiro_pagamento->forma_pagamento) && $financeiro_pagamento->forma_pagamento =='3'){{ ' selected '}}@else @endif>Transferência</option>
                                </select>
                            </td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
            <div class="form-group row">
                <label for="status" class="col-sm-2 col-form-label">vencimento final</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control mask_date" id="vencimento" name="vencimento" placeholder="DD/MM/AAAA" value="@if (isset($financeiro[0]->vencimento)){{ \Carbon\Carbon::parse($financeiro[0]->vencimento)->format('d/m/Y')}}@else{{''}}@endif">
                </div>
                <label for="status" class="col-sm-2 col-form-label">Restante</label>
                <div class="col-sm-2">
                    <?php

                        $combinado = $financeiro[0]->valor_combinado;
                        $entrada = $financeiro[0]->valor_entrada;
                        $restante_calc = ($combinado - ($entrada + $pago) <= 0) ? 0 : $combinado - ($entrada + $pago);
                        $restante =  number_format($restante_calc, 2, ',', '.') ;
                    ?>
                    <input class="form-control mask_valor" id="restante" name="restante" value="{{$restante}}"/>
                </div>
                <div class="col-sm-2">
                    @if($restante_calc == 0)<h2><span class="badge badge-success">Quitado</span></h2>@else
                        @if(\Carbon\Carbon::parse($financeiro[0]->vencimento)->isBefore(\Carbon\Carbon::today()))<h2><span class="badge badge-danger">Fora do vencimento</span></h2>@else<h2><span class="badge badge-warning">Dentro do vencimento</span></h2> @endif
                    @endif
                </div>
            </div>


            <div class="form-group row">
                <label for="observacoes" class="col-sm-2 col-form-label">Observações</label>
                <div class="col-sm-6">
                    <input class="form-control " id="observacoes" name="observacoes" value="@if (isset($financeiro[0]->observacoes)){{ trim($financeiro[0]->observacoes) }}@else{{ '' }}@endif"/>
                </div>
            </div>
            <div class="form-group row">
                <label for="status" class="col-sm-2 col-form-label"></label>
                <select class="form-control col-md-1" id="status" name="status">
                    <option value="A" @if (isset($financeiro[0]->status) && $financeiro[0]->status == 'A'){{ ' selected '}}@else @endif>Ativo</option>
                    <option value="I" @if (isset($financeiro[0]->status) && $financeiro[0]->status =='I'){{ ' selected '}}@else @endif>Inativo</option>
                </select>
            </div>
            <div class="form-group row">
                <div class="col-sm-5">
                    <button class="btn btn-danger" onclick="window.history.back();" type="button">Cancelar</button>
                </div>
                <div class="col-sm-5">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </form>

    @stop
@endif
