@extends('adminlte::page')

@section('title', env('APP_NAME'))
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="js/jquery.mask.js"></script>
<script src="js/main_custom.js"></script>

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

        <form id="filtro" action="clientes" method="get" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
            {{-- <div class="form-group row">
                <label for="razao_social" class="col-sm-1 col-form-label">Razão Social</label>
                <div class="col-sm-4">
                    <input type="text" id="razao_social" name="razao_social" class="form-control col-md-7" value="@if (isset($request) && trim($request->input('razao_social')) != ''){{$request->input('razao_social')}}@else @endif">
                </div>
                <label for="nome_fantasia" class="col-sm-2 col-form-label">Nome Fantasia</label>
                <div class="col-sm-5">
                    <input type="text" id="nome_fantasia" name="nome_fantasia" class="form-control col-md-7" value="@if (isset($request) && trim($request->input('nome_fantasia')) != ''){{$request->input('nome_fantasia')}}@else @endif">
                </div>
            </div> --}}
            <div class="form-group row">
                <label for="nome" class="col-sm-2 col-form-label">Nome contato</label>
                <div class="col-sm-4">
                    <input type="text" id="nome" name="nome" class="form-control col-md-7 col-xs-12" value="@if (isset($request) && trim($request->input('nome')) != ''){{$request->input('nome')}}@else @endif">
                </div>
                <label for="documento" class="col-sm-2 col-form-label">CPF/CNPJ</label>
                <div class="col-sm-4">
                    <input type="text" id="documento" name="documento" class="form-control col-md-7 col-xs-12 mask_cpf_cnpj" value="@if (isset($request) && trim($request->input('documento')) != ''){{$request->input('documento')}}@else @endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="status" class="col-sm-2 col-form-label">Situação</label>
                <select class="form-control col-md-1" id="status" name="status">
                    <option value="A" @if (isset($request) && $request->input('status') == 'A'){{ ' selected '}}@else @endif>Ativo</option>
                    <option value="I" @if (isset($request) && $request->input('status')  == 'I'){{ ' selected '}}@else @endif>Inativo</option>
                </select>
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
                      <th>ID</th>
                      {{-- <th>Razão Social</th>
                      <th>Nome fantasia</th> --}}
                      <th>Contato</th>
                      <th>Telefone</th>
                      <th>Email</th>
                    </tr>
                  </thead>
                  <tbody>
                  @if(isset($clientes))
                        @foreach ($clientes as $cliente)
                            <tr>
                            <th scope="row"><a href={{ URL::route($rotaAlterar, array('id' => $cliente->id )) }}>{{$cliente->id}}</a></th>
                              {{-- <td>{{$cliente->razao_social}}</td> --}}
                              {{-- <td>{{$cliente->nome_fantasia}}</td> --}}
                              <td>{{$cliente->nome}}</td>
                              <td class='mask_phone'>{{$cliente->telefone}}</td>
                              <td>{{$cliente->email}}</td>
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
@else
@section('content')
        @if($tela == 'alterar')
            @section('content_header')
                <h1 class="m-0 text-dark">Alteração de {{ $nome_tela }}</h1>
            @stop
            <form id="alterar" action="{{$rotaAlterar}}" data-parsley-validate="" class="form-horizontal form-label-left"  method="post">
            <div class="form-group row">
                <label for="codigo" class="col-sm-2 col-form-label">Id</label>
                <div class="col-sm-2">
                <input type="text" id="id" name="id" class="form-control col-md-7 col-xs-12" readonly="true" value="@if (isset($clientes[0]->id)){{$clientes[0]->id}}@else{{''}}@endif">
                </div>
            </div>
        @else
            @section('content_header')
                <h1 class="m-0 text-dark">Inclusão de {{ $nome_tela }}</h1>
            @stop
            <form id="incluir" action="{{$rotaIncluir}}" data-parsley-validate="" class="form-horizontal form-label-left" method="post">
        @endif
            @csrf <!--{{ csrf_field() }}-->
            <div class="form-group row">
                <label for="nome" class="col-sm-2 col-form-label">Nome*</label>
                <div class="col-sm-6">
                <input type="text" class="form-control" id="nome"  name="nome" value="@if (isset($clientes[0]->nome)){{$clientes[0]->nome}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="documento" class="col-sm-2 col-form-label">CPF/CNPJ*</label>
                <div class="col-sm-6">
                <input type="text" class="form-control mask_cfp_cnpj" required id="documento"  name="documento" value="@if (isset($clientes[0]->documento)){{$clientes[0]->documento}}@else{{''}}@endif">
                </div>
            </div>

            <div class="form-group row">
                <label for="endereco" class="col-sm-2 col-form-label">Endereço</label>
                <div class="col-sm-7">
                <input type="text" class="form-control" id="endereco" name="endereco" value="@if (isset($clientes[0]->endereco)){{$clientes[0]->endereco}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="numero" class="col-sm-2 col-form-label">Numero</label>
                <div class="col-sm-1">
                <input type="text" class="form-control sonumeros" id="numero" name="numero" value="@if (isset($clientes[0]->numero)){{$clientes[0]->numero}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="bairro" class="col-sm-2 col-form-label">Bairro</label>
                <div class="col-sm-4">
                <input type="text" class="form-control" id="bairro" name="bairro" value="@if (isset($clientes[0]->bairro)){{$clientes[0]->bairro}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="cidade" class="col-sm-2 col-form-label">Cidade</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="cidade" name="cidade" value="@if (isset($clientes[0]->cidade)){{$clientes[0]->cidade}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="estado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="estado" name="estado" value="@if (isset($clientes[0]->estado)){{$clientes[0]->estado}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="cep" class="col-sm-2 col-form-label">Cep</label>
                <div class="col-sm-2">
                <input type="text" class="form-control cep" id="cep" name="cep" value="@if (isset($clientes[0]->cep)){{$clientes[0]->cep}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="telefone" class="col-sm-2 col-form-label">Telefone</label>
                <div class="col-sm-2">
                <input type="text" class="form-control  mask_phone" id="telefone" name="telefone" value="@if (isset($clientes[0]->telefone)){{$clientes[0]->telefone}}@else{{''}}@endif">
                </div>
            </div>
            <div class="form-group row">
                <label for="email" class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-4">
                <input type="text" class="form-control " id="email" name="email" value="@if (isset($clientes[0]->email)){{$clientes[0]->email}}@else{{''}}@endif">
                </div>
            </div>

            <div class="form-group row">
                <label for="status" class="col-sm-2 col-form-label"></label>
                <select class="form-control col-md-1" id="status" name="status">
                    <option value="A" @if (isset($clientes[0]->status) && $clientes[0]->status == 'A'){{ ' selected '}}@else @endif>Ativo</option>
                    <option value="I" @if (isset($clientes[0]->status) && $clientes[0]->status =='I'){{ ' selected '}}@else @endif>Inativo</option>
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
