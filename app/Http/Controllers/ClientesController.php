<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clientes;
class ClientesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware(function ($request, $next) {

        //     // if ($request->user()->can('perfil_admin') || $request->user()->can('perfil_financeiro')) {
        //     //     return $next($request);
        //     // }
        //     // abort(403, 'Acesso não autorizado');

        // });
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {

        $id = !empty($request->input('id')) ? ($request->input('id')) : ( !empty($id) ? $id : false );

        $clientes = new Clientes();

        if ($id) {
        	$clientes = $clientes->where('id', '=', $id);
        }

        if (!empty($request->input('status'))){
            $clientes = $clientes->where('status', '=', $request->input('status'));
        } else {
            $clientes = $clientes->where('status', '=', 'A');
        }

        if ($request->input('documento') != '') {
            $documento = preg_replace("/[^0-9]/", "", $request->input('documento'));
        	$clientes = $clientes->where('documento', '=', $documento);
        }

        if ($request->input('razao_social') != '') {
        	$clientes = $clientes->where('razao_social',  'like', '%'.$request->input('razao_social').'%');
        }

        if ($request->input('nome_fantasia') != '') {
        	$clientes = $clientes->where('nome_fantasia', 'like', '%'.$request->input('nome_fantasia').'%');
        }

        if ($request->input('nome') != '') {
        	$clientes = $clientes->where('nome', 'like', '%'.$request->input('nome').'%');
        }

        $clientes = $clientes->get();
        $tela = 'pesquisa';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'clientes',
				'clientes'=> $clientes,
				'request' => $request,
				'rotaIncluir' => 'incluir-clientes',
				'rotaAlterar' => 'alterar-clientes'
			);

        return view('clientes', $data);
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

    		$cliente_id = $this->salva($request);

	    	return redirect()->route('clientes', [ 'id' => $cliente_id ] );

    	}
        $tela = 'incluir';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'clientes',
				'request' => $request,
				'rotaIncluir' => 'incluir-clientes',
				'rotaAlterar' => 'alterar-clientes'
			);

        return view('clientes', $data);
    }

     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function alterar(Request $request)
    {

        $clientes = new Clientes();


        $cliente= $clientes->where('id', '=', $request->input('id'))->get();

		$metodo = $request->method();
		if ($metodo == 'POST') {

    		$cliente_id = $this->salva($request);

	    	return redirect()->route('clientes', [ 'id' => $cliente_id ] );

    	}
        $tela = 'alterar';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'clientes',
				'clientes'=> $cliente,
				'request' => $request,
				'rotaIncluir' => 'incluir-clientes',
				'rotaAlterar' => 'alterar-clientes'
			);

        return view('clientes', $data);
    }

    public function salva($request) {
        $clientes = new Clientes();

        if($request->input('id')) {
            $clientes = $clientes::find($request->input('id'));
        }


        $clientes->documento = preg_replace("/[^0-9]/", "", $request->input('documento'));
        $clientes->nome = $request->input('nome');
        $clientes->endereco = $request->input('endereco');
        $clientes->numero = $request->input('numero');
        $clientes->cep = $request->input('cep');
        $clientes->bairro = $request->input('bairro');
        $clientes->cidade = $request->input('cidade');
        $clientes->estado = $request->input('estado');
        $clientes->telefone = preg_replace("/[^0-9]/", "", $request->input('telefone'));
        $clientes->email = $request->input('email');
        $clientes->status = $request->input('status');
        $clientes->save();

        return $clientes->id;

}

public function getAllCliente() {
    $Clientes = new Clientes();
    return $Clientes->where('status', '=', 'A')->get();

}
}
