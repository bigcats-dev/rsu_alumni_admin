<?php namespace App\Extensions;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;

class JWTGuard implements Guard{

    protected $provider;
    protected $request;
    protected $user;
    public function __construct(UserProvider $provider,Request $request)
    {
        $this->provider = $provider;
        $this->request = $request;
        $this->user = null;
    }
    /**
   * Determine if the current user is authenticated.
   *
   * @return bool
   */
    public function check()
    {
        return ! is_null($this->user());
    }
    
    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest()
    {
        return ! $this->check();
    }
    
    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        // check request verify token
        // get header authorization
        $token = $this->getBearerToken();
        if(!$token) return null;
        
        // find user
        $this->user = $this->provider->retrieveByToken(null,$token);
        return $this->user;
    }
    /**
     * Get the ID for the currently authenticated user.
     *
     * @return string|null
     */
    public function id()
    {
        return $this->user()->getAuthIdentifier();
    }

    public function logout(){
        $this->user = null;
        return;
    }
    
    /**
     * Validate a user's credentials.
     *
     * @return bool
     */
    public function validate(Array $credentials=[])
    {
        if (empty($credentials['KEYCLOAK_ID'])) {
            return false;
        }
    
        $user = $this->provider->retrieveByCredentials($credentials);
        if ($user) {
            $this->setUser($user);
            return true;
        } else {
            return false;
        }
    }
    /**
     * Set the current user.
     *
     * @param  Array $user User info
     * @return void
     */
    public function setUser(Authenticatable $user)
    {
        $this->user = $user;
        return $this;
    }

    public function getBearerToken(){
        // return $this->request->bearerToken();
        return 'eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJ2Ml82aldBRkktVUc3dVdFOHdibFV5NUFsT2NabElDWmRNTzEzYUg4YTBBIn0.eyJleHAiOjE2MTI3NjMyNzUsImlhdCI6MTYxMjc2Mjk3NSwianRpIjoiYjZjOTRkYWEtYmI5Ni00ZGQyLWFjNzgtMTNkZmI1YTA4MWNkIiwiaXNzIjoiaHR0cDovL2tleWNsb2FrLmVzZXJ2aWNlLXVhdC5yc3UuYWMudGgvYXV0aC9yZWFsbXMvU1RBRkYiLCJzdWIiOiI3Yzc2NDdhZC1kMmIxLTRhODUtYThiYy1iMGY4ZDgxOGUxMjYiLCJ0eXAiOiJCZWFyZXIiLCJhenAiOiJsb2dpbi1hcHAiLCJzZXNzaW9uX3N0YXRlIjoiZTU0YTMwNWItYjY5Ny00YTI0LTlhMjItMWY4ZDFiODkzYTUwIiwiYWNyIjoiMSIsImFsbG93ZWQtb3JpZ2lucyI6WyIiXSwicmVhbG1fYWNjZXNzIjp7InJvbGVzIjpbIlIwMDAxIiwidXNlcl9zdGFmZiIsInVzZXJzIiwiUjAwMDIiXX0sInJlc291cmNlX2FjY2VzcyI6eyJsb2dpbi1hcHAiOnsicm9sZXMiOlsidXNlcl9zdGFmZiJdfX0sInNjb3BlIjoicHJvZmlsZSBlbWFpbCIsImVtYWlsX3ZlcmlmaWVkIjpmYWxzZSwibmFtZSI6IuC4meC4suC4ouC5hOC4leC4o-C4oOC4niDguKXguLLguKDguJnguYnguK3guKIiLCJwcmVmZXJyZWRfdXNlcm5hbWUiOiI2MTAwcmVxcG9zX3RlYWNoMSIsImdpdmVuX25hbWUiOiLguJnguLLguKLguYTguJXguKPguKDguJ4iLCJmYW1pbHlfbmFtZSI6IuC4peC4suC4oOC4meC5ieC4reC4oiIsImVtYWlsIjoiNjEwMHJlcXBvc190ZWFjaDFAcnN1LmFjLnRoIn0.T2fpAyH4VvkyBMgv3ACpkQ-JGDtg51Ddax_6c5tHfvyfP0MD4PbI0KJDyxywUPvm-6Ns0D6PEq5sAF9JHGwGWAyFMu911HdQ_npV10xTWttgV07X_BaMwBmL26fyOqCDTueVF7SDaKumklFcSVAHs2L3_HmhCTIrgnIMDOUcgQJ-23V4jVhtWXHCnhIbqchpevmXeNwIokgC17kKTemBFBbT02_ZFc2miIMraBNfkufyAppy-5yiZnKTtykHhdp-119qFrb3_Op_AvDWSZ4NS-gtF3S4pyLwG-2O6TowXpK43I1mzfrcJpKTvHWM-Mg7DbepfaQKIqSiDkzfjZJsrQ';
    }
}