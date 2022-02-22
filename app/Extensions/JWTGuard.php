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
        // return 'eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJ2Ml82aldBRkktVUc3dVdFOHdibFV5NUFsT2NabElDWmRNTzEzYUg4YTBBIn0.eyJleHAiOjE2MTI3NjMyNzUsImlhdCI6MTYxMjc2Mjk3NSwianRpIjoiYjZjOTRkYWEtYmI5Ni00ZGQyLWFjNzgtMTNkZmI1YTA4MWNkIiwiaXNzIjoiaHR0cDovL2tleWNsb2FrLmVzZXJ2aWNlLXVhdC5yc3UuYWMudGgvYXV0aC9yZWFsbXMvU1RBRkYiLCJzdWIiOiI3Yzc2NDdhZC1kMmIxLTRhODUtYThiYy1iMGY4ZDgxOGUxMjYiLCJ0eXAiOiJCZWFyZXIiLCJhenAiOiJsb2dpbi1hcHAiLCJzZXNzaW9uX3N0YXRlIjoiZTU0YTMwNWItYjY5Ny00YTI0LTlhMjItMWY4ZDFiODkzYTUwIiwiYWNyIjoiMSIsImFsbG93ZWQtb3JpZ2lucyI6WyIiXSwicmVhbG1fYWNjZXNzIjp7InJvbGVzIjpbIlIwMDAxIiwidXNlcl9zdGFmZiIsInVzZXJzIiwiUjAwMDIiXX0sInJlc291cmNlX2FjY2VzcyI6eyJsb2dpbi1hcHAiOnsicm9sZXMiOlsidXNlcl9zdGFmZiJdfX0sInNjb3BlIjoicHJvZmlsZSBlbWFpbCIsImVtYWlsX3ZlcmlmaWVkIjpmYWxzZSwibmFtZSI6IuC4meC4suC4ouC5hOC4leC4o-C4oOC4niDguKXguLLguKDguJnguYnguK3guKIiLCJwcmVmZXJyZWRfdXNlcm5hbWUiOiI2MTAwcmVxcG9zX3RlYWNoMSIsImdpdmVuX25hbWUiOiLguJnguLLguKLguYTguJXguKPguKDguJ4iLCJmYW1pbHlfbmFtZSI6IuC4peC4suC4oOC4meC5ieC4reC4oiIsImVtYWlsIjoiNjEwMHJlcXBvc190ZWFjaDFAcnN1LmFjLnRoIn0.T2fpAyH4VvkyBMgv3ACpkQ-JGDtg51Ddax_6c5tHfvyfP0MD4PbI0KJDyxywUPvm-6Ns0D6PEq5sAF9JHGwGWAyFMu911HdQ_npV10xTWttgV07X_BaMwBmL26fyOqCDTueVF7SDaKumklFcSVAHs2L3_HmhCTIrgnIMDOUcgQJ-23V4jVhtWXHCnhIbqchpevmXeNwIokgC17kKTemBFBbT02_ZFc2miIMraBNfkufyAppy-5yiZnKTtykHhdp-119qFrb3_Op_AvDWSZ4NS-gtF3S4pyLwG-2O6TowXpK43I1mzfrcJpKTvHWM-Mg7DbepfaQKIqSiDkzfjZJsrQ';
        // return 'eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJrMFVjR1RwVW16OUx1LVhYTlUtdXFBVWJ5SGc0N29vaUxMcEs0dHQ1RzFNIn0.eyJleHAiOjE2MjYxNzM5MjgsImlhdCI6MTYyNjE3Mzg2OCwianRpIjoiODA5YTA1MjYtM2JmMy00YjZkLWE3NmItZTY3MGQ0ZjM0Mzg0IiwiaXNzIjoiaHR0cDovL2tleWNsb2FrLmVzZXJ2aWNlLXVhdC5yc3UuYWMudGgvYXV0aC9yZWFsbXMvU1RBRkYiLCJhdWQiOlsicmVhbG0tbWFuYWdlbWVudCIsImFjY291bnQiXSwic3ViIjoiYzE2NTg5OWUtMzc5OC00MzgxLTgwMzktMzNkZmFiNzU3YmRiIiwidHlwIjoiQmVhcmVyIiwiYXpwIjoibG9naW4tYXBwIiwic2Vzc2lvbl9zdGF0ZSI6IjRhYTg0YTJhLTJmODEtNGRkNC1hZDJiLTg0YjNmYTY3ZjA2ZiIsImFjciI6IjEiLCJyZWFsbV9hY2Nlc3MiOnsicm9sZXMiOlsib2ZmbGluZV9hY2Nlc3MiLCJ1bWFfYXV0aG9yaXphdGlvbiIsInVzZXJfc3RhZmYiXX0sInJlc291cmNlX2FjY2VzcyI6eyJyZWFsbS1tYW5hZ2VtZW50Ijp7InJvbGVzIjpbInZpZXctaWRlbnRpdHktcHJvdmlkZXJzIiwidmlldy1yZWFsbSIsIm1hbmFnZS1pZGVudGl0eS1wcm92aWRlcnMiLCJpbXBlcnNvbmF0aW9uIiwicmVhbG0tYWRtaW4iLCJjcmVhdGUtY2xpZW50IiwibWFuYWdlLXVzZXJzIiwicXVlcnktcmVhbG1zIiwidmlldy1hdXRob3JpemF0aW9uIiwicXVlcnktY2xpZW50cyIsInF1ZXJ5LXVzZXJzIiwibWFuYWdlLWV2ZW50cyIsIm1hbmFnZS1yZWFsbSIsInZpZXctZXZlbnRzIiwidmlldy11c2VycyIsInZpZXctY2xpZW50cyIsIm1hbmFnZS1hdXRob3JpemF0aW9uIiwibWFuYWdlLWNsaWVudHMiLCJxdWVyeS1ncm91cHMiXX0sImFjY291bnQiOnsicm9sZXMiOlsibWFuYWdlLWFjY291bnQiLCJtYW5hZ2UtYWNjb3VudC1saW5rcyIsInZpZXctcHJvZmlsZSJdfX0sInNjb3BlIjoiZW1haWwgcHJvZmlsZSIsImVtYWlsX3ZlcmlmaWVkIjpmYWxzZSwibmFtZSI6ImFkbWluX3N0YWZmIGFkbWluX3N0YWZmIiwicHJlZmVycmVkX3VzZXJuYW1lIjoiYWRtaW5fc3RhZmYiLCJnaXZlbl9uYW1lIjoiYWRtaW5fc3RhZmYiLCJmYW1pbHlfbmFtZSI6ImFkbWluX3N0YWZmIiwiZW1haWwiOiJhZG1pbl9zdGFmZkByc3UtdGVzdC5hYy50aCJ9.em42mlfVUEzGH-dG1h-EAMV7-s8u-zd-0DvkChTpBlzmGxRm_Obt1BFjPEf2LUxuryAoq7NBKNAOWITwzrZYjI6QOX9qTxNHbGDxRst-lPbXvrwJuLTSRjiiFNMSsHmoX1FI8ReUsC3hm33ckRlQM9u5n6UMQK1XfX-65hyEq7OJOZnzBots4LGW5lSy1w7XeDmn7uC6QxDPAi-IvhSZ3iAdRMoPc3owZiN0_v_R2noAhvYzcr0Kaea-xNh9UEccn7-jgjv8NNj66tCHF-JO31pZng6ZQAgiAIMUXMipvJwcmFFToUL4TIy0k68yLQlD9_h5y9aiv_2UeBGbpdROmQ';
        return 'eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJrMFVjR1RwVW16OUx1LVhYTlUtdXFBVWJ5SGc0N29vaUxMcEs0dHQ1RzFNIn0.eyJleHAiOjE2MjYxNzMwMDcsImlhdCI6MTYyNjE3Mjk0NywianRpIjoiN2Q0YmQ1NDItZjQ0Mi00M2Y5LTkxNTgtMzhmOWZhYzJlMDNhIiwiaXNzIjoiaHR0cDovL2tleWNsb2FrLmVzZXJ2aWNlLXVhdC5yc3UuYWMudGgvYXV0aC9yZWFsbXMvU1RBRkYiLCJhdWQiOiJhY2NvdW50Iiwic3ViIjoiOTQ2MTY1YjItZWZmOC00ZjE1LTgwNDctZWQxMzM4OTljYTJlIiwidHlwIjoiQmVhcmVyIiwiYXpwIjoibG9naW4tYXBwIiwic2Vzc2lvbl9zdGF0ZSI6IjczOTkyOWYyLTIyYmUtNDFkNS1hNTA5LTA4Y2MxNDI3MGM1MCIsImFjciI6IjEiLCJyZWFsbV9hY2Nlc3MiOnsicm9sZXMiOlsib2ZmbGluZV9hY2Nlc3MiLCJ1bWFfYXV0aG9yaXphdGlvbiIsInVzZXJfc3RhZmYiXX0sInJlc291cmNlX2FjY2VzcyI6eyJhY2NvdW50Ijp7InJvbGVzIjpbIm1hbmFnZS1hY2NvdW50IiwibWFuYWdlLWFjY291bnQtbGlua3MiLCJ2aWV3LXByb2ZpbGUiXX19LCJzY29wZSI6ImVtYWlsIHByb2ZpbGUiLCJlbWFpbF92ZXJpZmllZCI6ZmFsc2UsIm5hbWUiOiJzdGFmZmFkbTA0IHN0YWZmYWRtMDQiLCJwcmVmZXJyZWRfdXNlcm5hbWUiOiJzdGFmZmFkbTA0IiwiZ2l2ZW5fbmFtZSI6InN0YWZmYWRtMDQiLCJmYW1pbHlfbmFtZSI6InN0YWZmYWRtMDQiLCJlbWFpbCI6InN0YWZmYWRtMDRAcnN1LXRlc3QuYWMudGgifQ.O7bvEx-j-SJRtsRvyfxKKmrlJuKfsqAifLll6kgyHlWxSyedI6IejXlW8z6C3ScKoQwau44tp643ICpVrBvu4Tell-t-j6L6arxNq02wqReLAztUyYsk8REpmZHr_3zS65VUzAET_NEmGtmw8mdNRePfVhsnkX8vU9j2b55m53tYgy-oPOLHopsWQMnxE9U6yxjlsFYMD-FPfspHlNwKysb9b1GTR8Wr14G91YRvv5188zpaFlD4t1mW15ORKdsxRunB7cWFiKUh1L9TJI3v7WRUNYjh5SV3u9LJv8M7SQaT6D2TY79lHrgTw1pLFZaFSS53XS4wHARXedH1gGTwcw';
    }
}