<?php

namespace App\Http\Controllers;

use App\Models\Financeiro;
use App\Models\ItensDesenvolvimento;
use App\Models\ItensObservacoes;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Pedidos;
use App\Models\Status;
use App\Models\Clientes;
use App\Http\Controllers\DateHelpers;
use App\Models\ItensCombinacao;
use App\Models\ItensEntrega;
use App\Models\ItensFinanceiro;
use App\Models\ItensPedidos;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PedidosController extends Controller
{

    /**
    * Create a new controller instance.
    *
    * @return void
    */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function index(Request $request)
    {

        $id = !empty($request->input('id')) ? ($request->input('id')) : (!empty($id) ? $id : false);
        $status_id = !empty($request->input('status_id')) ? ($request->input('status_id')) : (!empty($status_id) ? $status_id : false);
        $codigo_cliente = !empty($request->input('codigo_cliente')) ? ($request->input('codigo_cliente')) : (!empty($codigo_cliente) ? $codigo_cliente : false);
        $nome_cliente = !empty($request->input('nome_cliente')) ? ($request->input('nome_cliente')) : (!empty($nome_cliente) ? $nome_cliente : false);

        $pedidos = DB::table('pedidos')
            ->join('status', 'pedidos.status_id', '=', 'status.id')
            ->join('clientes', 'clientes.id', '=', 'pedidos.clientes_id')
            ->select('pedidos.*', 'clientes.nome as nome_cliente', 'status.nome' , 'status.id as id_status')
            ->orderby('pedidos.data_entrega');

        if (!empty($request->input('status'))){
            $pedidos = $pedidos->where('pedidos.status', '=', $request->input('status'));
        } else {
            $pedidos = $pedidos->where('pedidos.status', '=', 'A');
        }

        if ($id) {
            $pedidos = $pedidos->where('pedidos.id', '=', $id);
        }

        if ($status_id) {
            $pedidos = $pedidos->where('pedidos.status_id', '=', $status_id);
        }

        if(!empty($request->input('data_entrega')) && !empty($request->input('data_entrega_fim') )) {
            $pedidos = $pedidos->whereBetween('pedidos.data_entrega', [DateHelpers::formatDate_dmY($request->input('data_entrega')), DateHelpers::formatDate_dmY($request->input('data_entrega_fim'))]);
        }
        if(!empty($request->input('data_entrega')) && empty($request->input('data_entrega_fim') )) {
            $pedidos = $pedidos->where('pedidos.data_entrega', '>=', DateHelpers::formatDate_dmY($request->input('data_entrega')));
        }
        if(empty($request->input('data_entrega')) && !empty($request->input('data_entrega_fim') )) {
            $pedidos = $pedidos->where('pedidos.data_entrega', '<=', DateHelpers::formatDate_dmY($request->input('data_entrega_fim')));
        }
        if(!empty($request->input('data_gerado')) && !empty($request->input('data_gerado_fim') )) {
            $pedidos = $pedidos->whereBetween('pedidos.data_gerado', [DateHelpers::formatDate_dmY($request->input('data_gerado')), DateHelpers::formatDate_dmY($request->input('data_gerado_fim'))]);
        }
        if(!empty($request->input('data_gerado')) && empty($request->input('data_gerado_fim') )) {
            $pedidos = $pedidos->where('pedidos.data_gerado', '>=', DateHelpers::formatDate_dmY($request->input('data_gerado')));
        }
        if(empty($request->input('data_gerado')) && !empty($request->input('data_gerado_fim') )) {
            $pedidos = $pedidos->where('pedidos.data_gerado', '<=', DateHelpers::formatDate_dmY($request->input('data_gerado_fim')));
        }


        if ($nome_cliente) {
            $pedidos = $pedidos->where('clientes.nome', 'like', '%'.$nome_cliente.'%' );
        }



        $pedidos = $pedidos->get();

        // $is_admin = Auth::user()->can('perfil_admin');




        $data = array(
            'tela' => 'pesquisar',
            'nome_tela' => 'pedidos',
            'pedidos' => $pedidos,
            'request' => $request,
            'AllStatus' => $this->getAllStatus(),
            'rotaIncluir' => 'incluir-pedidos',
            'rotaAlterar' => 'alterar-pedidos'
        );

        return view('pedidos', $data);
    }

    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function incluir(Request $request)
    {
        $metodo = $request->method();

        if ($metodo == 'POST') {
            $id = DB::transaction(function () use ($request) {

                $pedidos_id = $this->salva($request);

                return $pedidos_id;
            });
            return redirect()->route('pedidos', ['id' => $id]);
        }

        $data = array(
            'tela' => 'incluir',
            'nome_tela' => 'pedidos',
            'request' => $request,
            'status' => $this->getAllStatus(),
            'clientes' =>$this->getAllClientes(),
            'rotaIncluir' => 'incluir-pedidos',
            'rotaAlterar' => 'alterar-pedidos'
        );

        return view('pedidos', $data);
    }

    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function alterar(Request $request)
    {

        $pedidos = new Pedidos();

        $historico = '';
        $pedidos = $pedidos->where('id', '=', $request->input('id'))->get();

        $itensPedidos = ItensPedidos::where('pedidos_id', '=', $request->input('id'))->get();

        $metodo = $request->method();
        if ($metodo == 'POST') {

            $pedidos_id = $this->salva($request);

            return redirect()->route('pedidos', ['id' => $pedidos_id]);
        }

        $data = array(
            'tela' =>'alterar',
            'nome_tela' => 'pedidos',
            'pedidos' => $pedidos,
            'request' => $request,
            'itensPedidos' => $itensPedidos,
            'status' => $this->getAllStatus(),
            'clientes' =>$this->getAllClientes(),
            'rotaIncluir' => 'incluir-pedidos',
            'rotaAlterar' => 'alterar-pedidos'
        );

        return view('pedidos', $data);
    }



    public function salva(Request $request) {

        $id = DB::transaction(function () use ($request) {
            $pedidos = new Pedidos();

            $pedidos->status = 'A';
            if($request->input('id')) {
                $pedidos = $pedidos::find($request->input('id'));
                $pedidos->status = $request->input('status');
            }
            $pedidos->data_gerado = DateHelpers::formatDate_dmY($request->input('data_gerado'));
            $pedidos->status_id = $request->input('status_id');
            $pedidos->clientes_id = $request->input('clientes_id');
            $pedidos->observacao = $request->input('observacao');
            $pedidos->data_entrega_prevista = DateHelpers::formatDate_dmY($request->input('data_entrega_prevista'));


            $pedidos->save();

            $financeiro = new Financeiro();
            $financeiro = $financeiro->where('pedidos_id', '=', $pedidos->id)->first();

            if(empty($financeiro)) {
                $financeiro = new Financeiro();
                $financeiro->pedidos_id = $pedidos->id;
                $financeiro->status = 'A';
            }

            $financeiro->clientes_id = $request->input('clientes_id');
            $financeiro->valor_combinado = DateHelpers::formatFloatValue($request->input('valor_total'));
            $financeiro->data_transacao  = DateHelpers::formatDate_dmY($request->input('data_gerado'));
            $financeiro->valor_combinado = DateHelpers::formatFloatValue($request->input('valor_total'));
            $financeiro->save();


            $itens_existente = [];
            $itens_contador = $request->input('itens_contador');
            if(!empty($request->input('itens_existente'))){
                $itens_existente = $request->input('itens_existente');

            }
            $itens_descricao = $request->input('itens_descricao');
            $itens_valor_unitario = $request->input('itens_valor_unitario');
            $itens_valor = $request->input('itens_valor');
            $itens_qtde = $request->input('itens_qtde');

            foreach($itens_contador as $key => $item_contador) {
                $itens_pedidos = new ItensPedidos();

                if(!empty($itens_existente[$key])) {
                    $itens_pedidos = $itens_pedidos::find($itens_existente[$key]);
                }
                $itens_pedidos->pedidos_id = $pedidos->id;
                $itens_pedidos->descricao = $itens_descricao[$key];
                $itens_pedidos->qtde = $itens_qtde[$key];
                $itens_pedidos->data_iniciado = DateHelpers::formatDate_dmY($request->input('data_iniciado'));
                $itens_pedidos->data_finalizado = DateHelpers::formatDate_dmY($request->input('data_finalizado'));
                $itens_pedidos->recebedor_entrega = $request->input('recebedor_entrega');
                $itens_pedidos->valor_unitario = DateHelpers::formatFloatValue($itens_valor_unitario[$key]);
                $itens_pedidos->valor = DateHelpers::formatFloatValue($itens_valor[$key]);
                $itens_pedidos->save();
            }

            return $pedidos->id;
        });

        return $id;
    }

    public function getAllStatus()
    {
        $Status = new Status();
        return $Status->where('status', '=', 'A')->get();
    }


    public function getAllUsers()
    {
        $users = new User();
        return $users->get();
    }



    public function getAllClientes() {
        $pessoa = new clientes();
        return $pessoa->where('status', '=', 'A')->orderBy('nome', 'ASC')->get();

    }

}

