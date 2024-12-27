<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use Illuminate\Http\Request;
use App\Models\Financeiro;
use App\Models\FinanceiroPagamentos;
use App\Models\ItensFinanceiro;
use App\Models\ItensPedidos;
use App\Models\Pedidos;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FinanceiroController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(request $request)
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {

            if ($request->user()->can('perfil_admin') || $request->user()->can('perfil_financeiro')) {
                return $next($request);
            }
            abort(403, 'Acesso não autorizado');

        });
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $status_pedido = $responsavel = '';
        $financeiro = new Financeiro();
        $financeiro = DB::table('financeiro')
        ->join('clientes', 'clientes.id', '=', 'financeiro.clientes_id')
        ->join('pedidos', 'pedidos.id', '=', 'financeiro.pedidos_id')
        ->select('financeiro.*', 'clientes.nome as nome_cliente')
        ->orderby('financeiro.status', 'desc');

        $id = !empty($request->input('id')) ? ($request->input('id')) : ( !empty($id) ? $id : false );

        if (!empty($request->input('status'))){
            $financeiro = $financeiro->where('pedidos.status', '=', $request->input('status')) ;
        } else {
            $financeiro = $financeiro->where('pedidos.status', '=', 'A') ;
        }


        if ($id) {
            $financeiro = $financeiro->where('financeiro.id', '=', $id);
        }

        if (!empty($request->input('status_pedido'))){
            $status_pedido = $request->input('status_pedido');
        }

        if (!empty($request->input('responsavel'))){
            $responsavel = $request->input('responsavel');
        }

        if ($request->input('cliente') != '') {
        	$financeiro = $financeiro->where('clientes.nome', 'like', '%'.$request->input('cliente').'%');
        }

        $financeiro = $financeiro->get();

        foreach ($financeiro as $key => &$value) {
            $arr_status_pedido = [];
            $itens = new ItensPedidos();
            $itens = $itens->where('pedidos_id', '=', $value->pedidos_id)->get();
            $valor_total_itens=$valor_total_pago=0;

            foreach ($itens as $key2 => $value2) {
                $valor_total_itens = $valor_total_itens + $value2->valor;
            }

            $financeiroPagamentos = new FinanceiroPagamentos();
            $financeiroPagamentos = $financeiroPagamentos->where('financeiro_id', '=', $value->id)->get();
            foreach ($financeiroPagamentos as $key3 => $value3) {
                $valor_total_pago = $valor_total_pago + $value3->valor_pago;
            }

            $value->total_pago = $valor_total_pago + $value->valor_entrada;
            $value->restante_calc = $value->valor_combinado - $value->total_pago;
            $value->restante_calc = ($value->restante_calc <= 0) ? 0 : $value->restante_calc;

            if($value->valor_combinado != '' && $value->vencimento != '') {
                if($value->restante_calc == 0) {
                    $arr_status_pedido[]='Q';
                }
                if(\Carbon\Carbon::parse($value->vencimento)->isBefore(\Carbon\Carbon::today())){
                    $arr_status_pedido[]='F';
                } else {
                    $arr_status_pedido[]='D';
                }
            }

            if(($valor_total_itens > $valor_total_pago) || $valor_total_itens == 0) {
                $financeiro[$key]->tem_pendencia = '1';
            }
        }
        $tela = 'pesquisa';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'financeiro',
				'financeiro'=> $financeiro,
                'clientes' =>$this->getAllClientes(),
				'request' => $request,
				'rotaIncluir' => 'incluir-financeiro',
				'rotaAlterar' => 'alterar-financeiro'
			);

        return view('financeiro', $data);
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

    		$financeiro_id = $this->salva($request);

	    	return redirect()->route('financeiro', [ 'id' => $financeiro_id ] );

    	}
        $tela = 'incluir';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'financeiro',
                'clientes' =>$this->getAllClientesAtivos(),
                'pedidos' =>$this->getAllPedidos(),
				'request' => $request,
				'rotaIncluir' => 'incluir-financeiro',
				'rotaAlterar' => 'alterar-financeiro'
			);

        return view('financeiro', $data);
    }

     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function alterar(Request $request)
    {
		$metodo = $request->method();

		if ($metodo == 'POST') {

    		$financeiro_id = $this->salva($request);

	    	return redirect()->route('financeiro', [ 'id' => $financeiro_id ] );

    	}

        $financeiro = new Financeiro();
        $financeiro = DB::table('financeiro')
            ->join('clientes', 'clientes.id', '=', 'financeiro.clientes_id')
            ->select('financeiro.*', 'clientes.nome as nome_cliente', 'clientes.documento' )
            ->where('financeiro.id', '=', $request->input('id'))->get();

        $financeiroPagamentos = new FinanceiroPagamentos();
        $financeiroPagamentos = $financeiroPagamentos->where('financeiro_id', '=', $request->input('id'))->get();


        $itensPedidos = ItensPedidos::where('pedidos_id', '=', $financeiro[0]->pedidos_id)->get();

        $tela = 'alterar';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'financeiro',
				'financeiro'=> $financeiro,
                'itensPedidos' => $itensPedidos,
				'financeiroPagamentos'=> $financeiroPagamentos,
				'request' => $request,
                'pedidos' =>$this->getAllPedidos(),
                'clientes' =>$this->getAllClientesAtivos(),
				'rotaIncluir' => 'incluir-financeiro',
				'rotaAlterar' => 'alterar-financeiro'
			);

        return view('financeiro', $data);
    }

    public function salva($request) {
        try {

            $id = DB::transaction(function () use ($request) {
                $financeiro = new Financeiro();

                if($request->input('id')) {
                    $financeiro = $financeiro::find($request->input('id'));
                }
                if(!empty($request->input('clientes_id'))) {
                    $financeiro->clientes_id = $request->input('clientes_id');
                }

                $valor_combinado = '0.00';
                if(trim($request->input('valor_combinado')) != '') {
                    $valor_combinado = DateHelpers::formatFloatValue($request->input('valor_combinado'));
                }
                $valor_entrada = '0.00';
                if(trim($request->input('valor_entrada')) != '') {
                    $valor_entrada = DateHelpers::formatFloatValue($request->input('valor_entrada'));
                }

                $financeiro->pedidos_id = $request->input('pedidos_id');
                $financeiro->data_transacao = DateHelpers::formatDate_dmY($request->input('data_transacao'));
                $financeiro->data_entrada = !empty($request->input('data_entrada')) ? DateHelpers::formatDate_dmY($request->input('data_entrada')) : null;
                $financeiro->vencimento = DateHelpers::formatDate_dmY($request->input('vencimento'));

                $financeiro->valor_combinado = $valor_combinado;
                $financeiro->forma_pagamento_valor_combinado = $request->input('forma_pagamento_valor_combinado');
                $financeiro->valor_entrada = $valor_entrada;
                $financeiro->forma_pagamento_valor_entrada = $request->input('forma_pagamento_valor_entrada');

                $financeiro->observacoes = $request->input('observacoes');
                $financeiro->status = $request->input('status');
                $financeiro->save();

                $pedidos = new Pedidos();
                $pedidos = $pedidos::find($request->input('pedidos_id'));
                $pedidos->status = $request->input('status');
                $pedidos->save();


                $itens_existente = [];
                if(!empty($request->input('itens_existente'))){
                    $itens_existente = $request->input('itens_existente');

                }



                $data_pagamento = $request->input('data_pagamento', null);

                $inserts=[];

                if(!empty($data_pagamento)) {
                    $valores_pagos = $request->input('valor_pago');
                    $formas_pagamentos = $request->input('forma_pagamento');
                    $financeiro_pagamentos = new FinanceiroPagamentos();
                    $financeiro_pagamentos->where('financeiro_id', '=', $financeiro->id)->delete();
                    foreach ($data_pagamento as $key => $value) {

                        $inserts[] =[
                            'financeiro_id' => $financeiro->id,
                            'data_pagamento'=> DateHelpers::formatDate_dmY($data_pagamento[$key]),
                            'valor_pago'=> DateHelpers::formatFloatValue($valores_pagos[$key]),
                            'forma_pagamento'=> $formas_pagamentos[$key],
                        ];
                    }
                    $financeiro_pagamentos->insert($inserts);

                }

                return $financeiro->id;

            });
            return $id;

        } catch (\Throwable $th) {
            info($th);
        }

    }

    /**
     * Retorna todos os status ativos.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllStatus()
    {
        $financeiro = new Financeiro();
        return $financeiro->where('status', '=', 'A')->get();
    }

    public function getAllPedidos()
    {
        $pedidos = new Pedidos();
        return $pedidos->where('status', '=', 'A')->get();
    }

    /**
    * Show the application dashboard.
    *
    * @return object
    */
    public function getAllClientesAtivos() {
        $pessoa = new Clientes();
        return $pessoa->orderBy('nome', 'ASC')->get();

    }

    public function getAllClientes() {
        $pessoa = new Clientes();
        return $pessoa->where('status', '=', 'A')->orderBy('nome', 'ASC')->get();

    }

}

