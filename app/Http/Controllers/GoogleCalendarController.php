<?php
namespace App\Http\Controllers;

use Google_Client;
use Google_Service_Calendar;
use Illuminate\Http\Request;

class GoogleCalendarController extends Controller
{
    // ...

    public function auth(Request $request)
    {
        $client = new Google_Client();
        $client->setAuthConfig('path/to/client_secret.json');
        $client->setRedirectUri(url('/oauth2callback'));
        $client->addScope(Google_Service_Calendar::CALENDAR);

        // If a "code" parameter is present in the URL, exchange it for an access token.
        if ($request->has('code')) {
            $token = $client->fetchAccessTokenWithAuthCode($request->code);
            $client->setAccessToken($token);

            // Save the access token to the session.
            session()->put('google_access_token', $token);

            return redirect('/calendar');
        }

        // If the access token is already saved in the session, use it to authenticate the client.
        if (session()->has('google_access_token')) {
            $client->setAccessToken(session()->get('google_access_token'));
            return redirect('/calendar');
        }

        // If no access token is present, redirect the user to the Google OAuth consent screen.
        $authUrl = $client->createAuthUrl();
        return redirect()->away($authUrl);
    }

    // ...
}