<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Status;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
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

        //     if ($request->user()->can('perfil_admin') || $request->user()->can('perfil_financeiro')) {
        //         return $next($request);
        //     }
        //     abort(403, 'Acesso não autorizado');

        // });
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $users = new User();

        $id = !empty($request->input('id')) ? ($request->input('id')) : ( !empty($id) ? $id : false );

        if ($id) {
            $users = $users->where('id', '=', $id);
        }

        if ($request->input('nome') != '') {
        	$users = $users->where('nome', 'like', '%'.$request->input('nome').'%');
        }

        if (!empty($request->input('status'))){
            $users = $users->where('status', '=', $request->input('status'));
        } else {
            $users = $users->where('status', '=', 'A');
        }

        $users = $users->get();

        $tela = 'pesquisa';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'usuários',
				'users'=> $users,
				'request' => $request,
				'rotaIncluir' => 'incluir-users',
				'rotaAlterar' => 'alterar-users'
			);

        return view('users', $data);
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

    		$status_id = $this->salva($request);

	    	return redirect()->route('users', [ 'id' => $status_id ] );

    	}
        $tela = 'incluir';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'usuários',
				'request' => $request,
				'rotaIncluir' => 'incluir-users',
				'rotaAlterar' => 'alterar-users'
			);

        return view('users', $data);
    }

     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function alterar(Request $request)
    {

        $users = new User();


        $users= $users->where('id', '=', $request->input('id'))->get();

		$metodo = $request->method();
		if ($metodo == 'POST') {

    		$users_id = $this->salva($request);

	    	return redirect()->route('users', [ 'id' => $users_id ] );

    	}
        $tela = 'alterar';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'usuários',
				'users'=> $users,
				'request' => $request,
				'rotaIncluir' => 'incluir-users',
				'rotaAlterar' => 'alterar-users'
			);

        return view('users', $data);
    }

    /**
     * Saves a user to the database
     *
     * @param $request - an Illuminate\Http\Request object containing the user data
     * @return int - the id of the saved user
     */
    public function salva($request) {
        $users = new User();

        if($request->input('id')) {
            $users = $users::find($request->input('id'));
        }

        $users->name = $request->input('nome');
        $users->email = $request->input('email');
        $users->status = $request->input('status');
        if(trim($request->input('password')) != '') {
            $users->password = Hash::make($request->input('password'));
        }
        $users->perfil_acesso = $request->input('perfil_acesso');

        $users->save();

        return $users->id;
    }

    public function getAllStatus()
    {
        $users = new User();
        return $users->where('status', '=', 'A')->get();
    }
}
