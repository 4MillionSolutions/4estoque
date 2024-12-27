<?php
use App\Http\Controllers\PedidosController;
?>
@extends('adminlte::page')

@section('title', env('APP_NAME'))
<script src="vendor/jquery/jquery.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script src="js/jquery.mask.js"></script>
<script src="js/bootstrap.4.6.2.js?cache={{time()}}"></script>
<script src="js/main_custom.js"></script>
<script src="js/acoes.js?{{date('YmdHis')}}"></script>

@switch($tela)
    @case('pesquisar')
        @section('content_header')
            <div class="form-group row">
                <h1 class="m-0 text-dark col-sm-10 col-form-label">Pesquisa de {{$nome_tela}}</h1>
                <div class="col-sm-1">
                    @include('layouts.nav-open-incluir', ['rotaIncluir => $rotaIncluir'])
                </div>
            </div>
        @stop
        @section('content')
            <div id="toastsContainerTopRight" class="toasts-top-right fixed">
                <div class="toast fade show" role="alert" style="width: 350px" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="mr-auto">Alerta!</strong>
                        <small></small>
                        <button data-dismiss="toast" type="button" class="ml-2 mb-1 close" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="toast-body textoAlerta" style="text-decoration-style: solid; font-weight: bold; font-size: larger;">
                    </div>
                </div>
            </div>

            <div class="right_col" role="main">
                <form id="filtro" action="pedidos" method="get" data-parsley-validate=""
                    class="form-horizontal form-label-left" novalidate="">
                    <div class="form-group row">
                        <label for="id" class="col-sm-2 col-form-label text-right">ID</label>
                        <div class="col-sm-2">
                            <input type="text" id="id" name="id" class="form-control col-md-7 col-xs-12"
                                value="">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="data_gerado" class="col-sm-2 col-form-label text-right">Data gerado:</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control mask_date" id="data_gerado" name="data_gerado"
                                placeholder="DD/MM/AAAA">
                        </div>
                        <label for="data_gerado_fim" class="col-form-label text-right">até</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control mask_date" id="data_gerado_fim" name="data_gerado_fim"
                                placeholder="DD/MM/AAAA">
                        </div>
                        <label for="blank" class="col-sm-2 col-form-label text-right text-sm-end">Status do pedido</label>
                        <div class="col-sm-2">
                            <select class="form-control" id="status_id" name="status_id">
                                <option value=""></option>
                                @if (isset($AllStatus))
                                    @foreach ($AllStatus as $stats)
                                        <option value="{{$stats->id}}">{{$stats->nome}}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="data_entrega_prevista" class="col-sm-2 col-form-label text-right">Data entrega:</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control mask_date" id="data_entrega_prevista"
                                name="data_entrega_prevista" placeholder="DD/MM/AAAA">
                        </div>
                        <label for="data_entrega_prevista_fim" class="col-form-label text-right">até</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control mask_date" id="data_entrega_prevista_fim"
                                name="data_entrega_prevista_fim" placeholder="DD/MM/AAAA">
                        </div>
                        <label for="nome_cliente" class="col-sm-2 col-form-label text-right">Nome cliente</label>
                        <div class="col-sm-3">
                            <input type="text" id="nome_cliente" name="nome_cliente" class="form-control col-md-13"
                                value="">
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
                            <button type="submit" class="btn btn-primary">Pesquisar</button>
                        </div>
                        <div class="col-sm-5">
                            <div class="overlay" style="display: none;">
                                <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                            </div>
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
                                <table class="table table-striped  text-center">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Cliente</th>
                                            <th>Status do pedido</th>
                                            <th>Data gerado</th>
                                            <th>Data entrega prevista</th>
                                            <th>Dias Alerta</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($pedidos))
                                            @foreach ($pedidos as $pedido)
                                                <tr>
                                                    <th scope="row">
                                                        <a
                                                            href={{URL::route($rotaAlterar, ['id' => $pedido->id])}}>{{$pedido->id}}</a>
                                                    </th>
                                                    <td>{{$pedido->nome_cliente}}</td>
                                                    <td>
                                                            @if (isset($AllStatus))
                                                                @foreach ($AllStatus as $stats)
                                                                    @if (isset($pedido->id_status) && $pedido->id_status == $stats->id) {{$stats->nome}} @else{{''}} @endif
                                                                @endforeach
                                                            @endif
                                                    </td>
                                                    <?php
                                                    $entrega = \Carbon\Carbon::createFromDate($pedido->data_entrega_prevista)->format('Y-m-d');
                                                    $hoje = date('Y-m-d');
                                                    $dias_alerta = \Carbon\Carbon::createFromDate($hoje)->diffInDays($entrega, false);
                                                    if ($dias_alerta < 6) {
                                                        $class_dias_alerta = 'text-danger';
                                                    } else {
                                                        $class_dias_alerta = 'text-primary';
                                                    }
                                                    ?>
                                                    <td>{{Carbon\Carbon::parse($pedido->data_gerado)->format('d/m/Y')}}</td>
                                                    <td>{{Carbon\Carbon::parse($pedido->data_entrega_prevista)->format('d/m/Y')}}
                                                    </td>
                                                    <td class="{{$class_dias_alerta}}">{{$dias_alerta}}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @stop
    @break

    @case('alterar')
    @case('incluir')
        @section('content')
            @if ($tela == 'alterar')
                @section('content_header')
                    <h1 class="m-0 text-dark">Alteração de {{$nome_tela}}</h1>
                @stop
                <form id="alterar" action="{{$rotaAlterar}}" data-parsley-validate=""
                    class="form-horizontal form-label-left" method="post">
                    <div class="form-group row">
                        <label for="codigo" class="col-sm-2 col-form-label">Id</label>
                        <div class="col-sm-2">
                            <input type="text" id="id" name="id" class="form-control col-md-7 col-xs-12"
                                readonly="true"
                                value="@if (isset($pedidos[0]->id)) {{$pedidos[0]->id}}@else{{''}} @endif">
                        </div>
                        <label for="nome" class="col-sm-2 col-form-label">Alerta de dias</label>
                        <?php
                        $entrega = \Carbon\Carbon::createFromDate($pedidos[0]->data_entrega_prevista)->format('Y-m-d');
                        $hoje = date('Y-m-d');
                        $dias_alerta = \Carbon\Carbon::createFromDate($hoje)->diffInDays($entrega, false);
                        if ($dias_alerta < 6) {
                            $class_dias_alerta = 'text-danger';
                        } else {
                            $class_dias_alerta = 'text-primary';
                        }
                        ?><span class="{{$class_dias_alerta}}"> {{$dias_alerta}}</span>
                    </div>
                @else
                    @section('content_header')
                        <h1 class="m-0 text-dark">Cadastro de {{$nome_tela}}</h1>
                    @stop
                    <form id="incluir" action="{{$rotaIncluir}}" data-parsley-validate=""
                        class="form-horizontal form-label-left" method="post">
            @endif
            @csrf
            <div class="form-group row">
                <label for="clientes_id" class="col-sm-2 col-form-label">Cliente</label>
                <div class="col-sm-4">
                    <select class="form-control select_autocomplete" id="clientes_id" name="clientes_id">
                        <option value=""></option>
                        @if (isset($clientes))
                            @foreach ($clientes as $clientes)
                                <option value="{{$clientes->id}}"
                                    @if (isset($pedidos[0]->clientes_id) && $pedidos[0]->clientes_id == $clientes->id) selected="selected" @else{{''}} @endif>
                                    {{$clientes->id . ' - ' . $clientes->nome}}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <table class="table table-striped  text-center" id="tabela_itens">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descrição do Item</th>
                        <th>Quantidade</th>
                        <th>Valor unitário</th>
                        <th>Valor</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($itensPedidos))
                        @foreach ($itensPedidos as $itens)
                            <tr align='center' class='itens_pedido item_"{{$itens->id}}"' data-itens_id="{{$itens->id}}">
                                <td class='contador'>
                                    <input type='hidden' name='itens_existente[]' class='form-control col-sm-3'
                                        value='{{$itens->id}}' readonly>
                                    <input type='text' name='itens_contador[]' class='form-control '
                                        value='{{$itens->id}}' readonly>
                                </td>
                                <td class='descricao'>
                                    <input type='text' name='itens_descricao[]' class='form-control' required
                                        value='{{$itens->descricao}}'>
                                </td>
                                <td class='qtde'>
                                    <input type='text' name='itens_qtde[]' class='form-control qtde_{{$itens->id}} atualiza_valor_unitario'
                                        value='{{$itens->qtde}}'>
                                </td>
                                <td class='valor_unitario'>
                                    <input type='text' name='itens_valor_unitario[]' class='form-control mask_valor valor_unitario_{{$itens->id}} atualiza_valor_unitario'
                                        value='{{$itens->valor_unitario}}'>
                                </td>
                                <td class='valor'>
                                    <input type='text' name='itens_valor[]' class='form-control soma_valor mask_valor valor_{{$itens->id}}'
                                        value='{{$itens->valor}}'>
                                        <?php
                                        $valor_total = isset($valor_total) ? $valor_total + $itens->valor : $itens->valor;
                                        ?>
                                </td>
                                <td>
                                    <i class='fa fa-times acao' data-contador='"{{$itens->id}}"'
                                        style='cursor: pointer;'></i>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>
                            Total
                        </th>
                        <th>
                            <input type="text" class='form-control  mask_valor' name="valor_total" id="valor_total" value="{{isset($valor_total) ? number_format($valor_total, 2, ',', '') : '0,00'}}">

                        </th>
                        <th>
                            <button type="button" class="btn btn-success " id="novo_item">Novo Item</button>

                        </th>
                    </tr>
                </tfoot>
            </table>


            <div class="form-group row">
                <label for="data_gerado" class="col-sm-2 col-form-label">Data gerado</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control mask_date" id="data_gerado" name="data_gerado"
                        @if ($tela == 'alterar') readonly='readonly' @else {{''}} @endif
                        value="@if (isset($pedidos[0]->data_gerado)) {{Carbon\Carbon::parse($pedidos[0]->data_gerado)->format('d/m/Y')}}@else{{Carbon\Carbon::now()->format('d/m/Y')}} @endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="data_entrega_prevista" class="col-sm-2 col-form-label">Data entrega prevista</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control mask_date" id="data_entrega_prevista" name="data_entrega_prevista"
                        value="@if (isset($pedidos[0]->data_entrega_prevista)) {{Carbon\Carbon::parse($pedidos[0]->data_entrega_prevista)->format('d/m/Y')}} @else {{''}} @endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="status_id" class="col-sm-2 col-form-label">Status do pedido</label>
                <div class="col-sm-4">
                    <select class="form-control" id="status_id" name="status_id">
                        <option value=""></option>
                        @if (isset($status))
                            @foreach ($status as $stats)
                                <option value="{{$stats->id}}"
                                    @if ((isset($pedidos[0]->status_id) && $pedidos[0]->status_id == $stats->id) || ($tela == 'incluir' && $stats->id == 1)) selected="selected" @else{{''}} @endif>
                                    {{$stats->nome}}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="observacao" class="col-sm-2 col-form-label">Observações</label>
                <div class="col-sm-6">
                    <textarea class="form-control" id="observacao" name="observacao">
@if (isset($pedidos[0]->observacao))
{{trim($pedidos[0]->observacao)}}@else{{''}}
@endif
</textarea>
                </div>
            </div>
            @if (!empty($historicos))
                <div class="form-group row">
                    <label for="observacao" class="col-sm-2 col-form-label">Histórico</label>
                    <div class="col-sm-8">
                        <div class="d-flex p-2 bd-highlight overflow-auto">
                            @foreach ($historicos as $historico)
                                {{'[' . \Carbon\Carbon::parse($historico->created_at)->format('d/m/Y h:i:s') . '] ' . $historico->historico}}</br>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            <div class="form-group row">
                <label for="status" class="col-sm-2 col-form-label"></label>
                <select class="form-control col-md-1" id="status" name="status">
                    <option value="A" @if (isset($pedidos[0]->status) && $pedidos[0]->status == 'A') {{' selected '}}@else @endif>Ativo</option>
                    <option value="I" @if (isset($pedidos[0]->status) && $pedidos[0]->status == 'I') {{' selected '}}@else @endif>Inativo</option>
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
    @break

    @case('acompanhamentos')
        @section('content')
        @section('content_header')
            <h1 class="m-0 text-dark">Alteração de {{$nome_tela}}</h1>
        @stop
        <form id="alterar" action="{{$rotaAlterar}}" data-parsley-validate="" class="form-horizontal form-label-left"
            method="post">
            <div class="form-group row">
                <label for="codigo" class="col-sm-1 col-form-label">Id</label>
                <div class="col-sm-2">
                    <input type="text" id="id" name="id" class="form-control col-md-7 col-xs-12"
                        readonly="true"
                        value="@if(isset($pedidos[0]->id)){{$pedidos[0]->id}}@else{{''}} @endif">
                </div>
                <label for="clientes_id" class="col-sm-2 col-form-label">Cliente</label>
                <div class="col-sm-4">
                    <select class="form-control" readonly="true" id="clientes_id" name="clientes_id">
                        <option value=""></option>
                        @if (isset($clientes))
                            @foreach ($clientes as $cliente)
                                <option value="{{$cliente->id}}"
                                    @if (isset($pedidos[0]->clientes_id) && $pedidos[0]->clientes_id == $cliente->id) selected="selected" @else{{''}} @endif>
                                    {{$cliente->id . ' - ' . $cliente->nome}}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            @csrf
            <div class="form-group row">
                <label for="nome" class="col-sm-1 col-form-label">Data</label>
                <div class="col-sm-2">
                    <input type="text" readonly="true" class="form-control mask_date" id="data_gerado"
                        name="data_gerado"
                        value="
                {{\Carbon\Carbon::parse($pedidos[0]->data_gerado)->format('d/m/Y')}}">
                </div>
                <label for="data_entrega_prevista" class="col-sm-2 col-form-label">Data entrega prevista</label>
                <div class="col-sm-2">
                    <input type="text" readonly="true" class="form-control mask_date" id="data_entrega_prevista"
                        name="data_entrega_prevista"
                        value="
                {{\Carbon\Carbon::parse($pedidos[0]->data_entrega_prevista)->format('d/m/Y')}}">
                </div>
                <label for="nome" class="col-sm-2 col-form-label">Alerta de dias</label>
                <?php
                $entrega = \Carbon\Carbon::createFromDate($pedidos[0]->data_entrega_prevista)->format('Y-m-d');
                $hoje = date('Y-m-d');
                $dias_alerta = \Carbon\Carbon::createFromDate($hoje)->diffInDays($entrega, false);
                if ($dias_alerta < 6) {
                    $class_dias_alerta = 'text-danger';
                    $class_botao_alerta = 'btn-danger';
                } else {
                    $class_dias_alerta = 'text-primary';
                    $class_botao_alerta = 'btn-primary';
                }
                ?>
                <button type="button" class="btn {{$class_botao_alerta}}">{{$dias_alerta}}</button>
            </div>

            @foreach ($itensPedidos as $itensPedido)
                <div class="container">
                    <div class="row">
                        <div class="col">
                            <h6 class="alert alert-dark" role="alert">Item {{$itensPedido->id}} -
                                {{$itensPedido->qtde}} {{$itensPedido->descricao}}</h6>
                        </div>
                    </div>
                </div>
                <div class="container">
            @php
                $sections = [
                    'combinacao' => 'Combinação',
                    'desenvolvimento' => 'Desenvolvimento',
                    'entrega' => 'Entrega',
                    'financeiro' => 'Financeiro',
                ];
            @endphp

            {{-- Cabeçalhos e Conteúdos das Seções --}}
            <div class="row">
                @foreach ($sections as $key => $title)
                    <div class="col-sm-3">
                        {{-- Cabeçalho da Seção --}}
                        <h4 class="text-center">
                            <span
                                class="badge col-sm-10 @if (
                                    (!empty(${"itens" . ucfirst($key)}[$itensPedido->id]['tem_pendencia']) &&
                                        count(${"itens" . ucfirst($key)}[$itensPedido->id]['tem_pendencia']) > 0) ||
                                        empty(${"itens" . ucfirst($key)}[$itensPedido->id])) bg-warning @else bg-success @endif">
                                {{ $title }}
                                <i style="padding-left: 10px"
                                    class="@if (
                                        (!empty(${"itens" . ucfirst($key)}[$itensPedido->id]['tem_pendencia']) &&
                                            count(${"itens" . ucfirst($key)}[$itensPedido->id]['tem_pendencia']) > 0) ||
                                            empty(${"itens" . ucfirst($key)}[$itensPedido->id])) fa fa-thumbs-down @else fa fa-thumbs-up @endif"></i>
                            </span>
                            <a class="add" data-tipo="{{ $key }}" data-pedido="{{ $itensPedido->id }}" href="#">
                                <span class="fa fa-users text-primary"></span>
                            </a>
                        </h4>

                        {{-- Conteúdo Específico de Cada Seção --}}
                        @if ($key === 'entrega')
                            {{-- Exibição da Entrega --}}
                            @if (!empty($itensEntrega[$itensPedido->id]))
                                @foreach ($itensEntrega[$itensPedido->id]['users'] as $itensEnt)
                                    <p class="">
                                        <span style="cursor: pointer" title="Ação"
                                            id="{{ 'entrega_' . $itensEnt['users_id'] . $pedidos[0]->id . $itensPedido->id }}"
                                            data-tipo="entrega" data-usuario="{{ $itensEnt['users_id'] }}"
                                            data-pedido="{{ $pedidos[0]->id }}" data-item_pedido="{{ $itensPedido->id }}"
                                            class="acao_usuario badge @if (empty($itensEnt['data_finalizado'])) bg-warning @else bg-success @endif">
                                            <i class="fa fa-bullseye" aria-hidden="true"></i>
                                        </span>
                                        <span style="padding-right: 15%;">{{ $users[$itensEnt['users_id']] }}</span>
                                        <i title='Deleta a ação {{ $users[$itensEnt['users_id']] }}'
                                        data-tipo="entrega" data-usuario="{{ $itensEnt['users_id'] }}"
                                        data-pedido="{{ $pedidos[0]->id }}" data-item_pedido="{{ $itensPedido->id }}"
                                        class="fa fa-solid fa-trash text-danger desvincular_funcionario"
                                        style="cursor: pointer"></i>
                                    </p>
                                    <p class="">Recebedor: {{ $itensPedido->recebedor_entrega }}</p>
                                    <p class="">Data entrega:
                                        @if (!empty($itensPedido->data_entrega))
                                            {{ \Carbon\Carbon::parse($itensPedido->data_entrega)->format('d/m/Y') }}
                                        @else
                                            {{ '' }}
                                        @endif
                                    </p>
                                @endforeach
                            @else
                                <p class="text-muted text-center">Nenhum usuário atribuído.</p>
                            @endif
                        @elseif ($key === 'financeiro')
                            {{-- Exibição do Financeiro --}}
                            @if (!empty($itensFinanceiro[$itensPedido->id]))
                                @foreach ($itensFinanceiro[$itensPedido->id]['users'] as $itensFin)
                                    <p class="d-flex justify-content-between align-items-center">
                                        <span style="cursor: pointer" title="Ação"
                                            id="{{ 'financeiro_' . $itensFin['users_id'] . $pedidos[0]->id . $itensPedido->id }}"
                                            data-tipo="financeiro" data-usuario="{{ $itensFin['users_id'] }}"
                                            data-pedido="{{ $pedidos[0]->id }}" data-item_pedido="{{ $itensPedido->id }}"
                                            class="acao_usuario badge @if (empty($itensFin['data_finalizado'])) bg-warning @else bg-success @endif">
                                            <i class="fa fa-bullseye" aria-hidden="true"></i>
                                        </span>
                                        <span style="padding-right: 15%;">{{ $users[$itensFin['users_id']] }}</span>
                                        <i title='Deleta a ação {{ $users[$itensFin['users_id']] }}'
                                        data-tipo="financeiro" data-usuario="{{ $itensFin['users_id'] }}"
                                        data-pedido="{{ $pedidos[0]->id }}" data-item_pedido="{{ $itensPedido->id }}"
                                        class="fa fa-solid fa-trash text-danger desvincular_funcionario"
                                        style="cursor: pointer"></i>
                                    </p>
                                @endforeach
                                <p class="text-center">
                                    <a href="{{ URL::route('alterar-financeiro', ['id' => $financeiro[0]->id]) }}">
                                        <span class="fa fa-dollar-sign"></span>
                                    </a>
                                </p>
                            @else
                                <p class="text-muted text-center">Nenhum usuário atribuído.</p>
                            @endif
                        @else
                            {{-- Exibição Padrão para Combinação e Desenvolvimento --}}
                            @php
                                $items = ${"itens" . ucfirst($key)}[$itensPedido->id] ?? null;
                            @endphp

                            @if (!empty($items['users']))
                                @foreach ($items['users'] as $item)
                                    <p class="d-flex justify-content-between align-items-center">
                                        <span style="cursor: pointer" title="Ação"
                                            id="{{ $key . '_' . $item['users_id'] . $pedidos[0]->id . $itensPedido->id }}"
                                            data-tipo="{{ $key }}" data-usuario="{{ $item['users_id'] }}"
                                            data-pedido="{{ $pedidos[0]->id }}" data-item_pedido="{{ $itensPedido->id }}"
                                            class="acao_usuario badge @if (empty($item['data_finalizado'])) bg-warning @else bg-success @endif">
                                            <i class="fa fa-bullseye" aria-hidden="true"></i>
                                        </span>
                                        <span>{{ $users[$item['users_id']] }}</span>
                                        <i title='Deleta a ação {{ $users[$item['users_id']] }}'
                                        data-usuario="{{ $item['users_id'] }}" data-tipo="{{ $key }}"
                                        data-pedido="{{ $pedidos[0]->id }}" data-item_pedido="{{ $itensPedido->id }}"
                                        class="fa fa-solid fa-trash text-danger desvincular_funcionario"
                                        style="cursor: pointer"></i>
                                    </p>

                                    {{-- Observações - Apenas para Combinação e Desenvolvimento --}}
                                    @if (in_array($key, ['combinacao', 'desenvolvimento']))
                                        <div class="col-sm-12" style="background-color: #f8f9fa">
                                            <label class="col-sm-12 col-form-label">Observações</label>
                                            <textarea class="form-control col-sm-12 observacoes" data-tipo="{{ $key }}"
                                                    id="observacoes_{{ $key }}_{{ $itensPedido->id }}"
                                                    data-item_pedido="{{ $itensPedido->id }}"
                                                    data-item-responsavel="{{ $item['users_id'] }}"
                                                    name="observacoes" rows="3">
                                                {{ $array_itensobservacao[$itensPedido->id][$item['users_id']][$key]['observacao'] ?? '' }}
                                            </textarea>
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <p class="text-muted text-center">Nenhum usuário atribuído.</p>
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="row">

                    <div class="mb-3"></div>
            @endforeach
        </form>

        <div id='modal_funcionarios' class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content" style="width: 100%">
                    <div class="modal-header">
                        <h5 class="modal-title" id='texto_status_caixas'>Adicionar funcionário</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        @if (isset($funcionarios))
                            @foreach ($funcionarios as $funcionario)
                                <div class="form-check">
                                    <input class="form-check-input funcionarios" type="checkbox"
                                        value="{{$funcionario->id}}" id="funcionario{{$funcionario->id}}">
                                    <label class="form-check-label"
                                        for="funcionario{{$funcionario->id}}">{{$funcionario->name}}</label>
                                </div>
                            @endforeach
                        @endif
                        <input type="hidden" name="tipo" id="tipo" value="" />
                        <input type="hidden" name="item_pedido" id="item_pedido" value="" />
                        <input type="hidden" name="pedido_id" id="pedido_id" value="" />
                        <input type="hidden" name="user_id" id="user_id" value="" />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="adicionar_funcionario"
                            data-dismiss="modal">Adicionar</button>
                    </div>
                </div>
            </div>
        </div>

        <div id='modal_desvincular_funcionario' class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content" style="width: 100%">
                    <div class="modal-header">
                        <h5 class="modal-title" id='texto_status_caixas'>Deletar vínculo do reponsável?</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="tipo" id="tipo_desvincular" value="" />
                        <input type="hidden" name="item_pedido_desvincular" id="item_pedido_desvincular" value="" />
                        <input type="hidden" name="pedido_id_desvincular" id="pedido_id_desvincular" value="" />
                        <input type="hidden" name="user_id_desvincular" id="user_id_desvincular" value="" />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="desvincular_funcionario"
                            data-dismiss="modal">Desvincular</button>
                    </div>
                </div>
            </div>
        </div>
        <div id='modal_acoes' class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content" style="width: 100%">
                    <div class="modal-header">
                        <h5 class="modal-title" id='texto_status_caixas'>Deseja finalizar a etapa?</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group row" id='input_recebedor'>
                            <label for="" class="col-sm-4 col-form-label text-right">Recebido por</label>
                            <input class="form-control col-md-5" name="recebedor" id="recebedor" />
                        </div>
                        <div class="form-group row" id='input_data_entrega'>
                            <label for="" class="col-sm-4 col-form-label text-right">Data da entrega</label>
                            <input class="form-control col-md-5 mask_date" name="data_entrega" id="data_entrega" />
                        </div>

                        <button type="button" id='finalizar_etapa'
                            class="btn btn-success btn-lg btn-block">Finalizar</button>
                        <input type="hidden" name="acao_tipo" id="acao_tipo" value="" />
                        <input type="hidden" name="acao_item_pedido" id="acao_item_pedido" value="" />
                        <input type="hidden" name="acao_pedido" id="acao_pedido" value="" />
                    </div>
                </div>
            </div>
        </div>
    @stop
@break

@endswitch
