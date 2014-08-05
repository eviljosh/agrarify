<?php

class AccountsController extends ApiController {

	/**
	 * Display a listing of accounts
	 *
	 * @return Response
	 */
	public function index()
	{
		$accounts = Account::all();

		return View::make('accounts.index', compact('accounts'));
	}

	/**
	 * Show the form for creating a new accounts
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('accounts.create');
	}

	/**
	 * Store a newly created accounts in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Account::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Account::create($data);

		return Redirect::route('accounts.index');
	}

	/**
	 * Display the specified accounts.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$account = Account::findOrFail($id);

		return View::make('accounts.show', compact('accounts'));
	}

	/**
	 * Show the form for editing the specified accounts.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$account = Account::find($id);

		return View::make('accounts.edit', compact('accounts'));
	}

	/**
	 * Update the specified accounts in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$account = Account::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Account::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$account->update($data);

		return Redirect::route('accounts.index');
	}

	/**
	 * Remove the specified accounts from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Account::destroy($id);

		return Redirect::route('accounts.index');
	}

}
