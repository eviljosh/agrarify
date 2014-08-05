<?php

class AccountsController extends ApiController {

	/**
	 * Creates an oauth2 consumer
	 *
	 * @return Response
	 */
	public function createConsumer()
	{
		// TODO: make route, check password, throw 403, generate random strings and such, save, return
	}

    /**
     * Creates an oauth2 access token
     *
     * @return Response
     */
    public function createAccessToken()
    {
        // TODO: make route, check consumer id, throw 401, generate empty account if needed, check account credentials otherwise, generate random strings and such, save, return
    }

}
