<?php

namespace App\Http\Traits;

trait Response
{
    /**
     * dataOrErrors is data if in (200,201,202) else errors
     * meta is an array as ['token'=>$token , 'count'=>20] to handle header
     * @param string $message
     * @param null $dataOrErrors
     * @param int $code
     * @param array $meta
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public  function responseApi($message = '', $dataOrErrors = null, $code = 200, $meta = [])
    {
        $array = [
            'status' => in_array($code, $this->successCode()) ? true : false,
            'message' => ($message == null) ? '' : $message,
            in_array($code, $this->successCode()) ? 'data' : 'errors'  => $dataOrErrors,
        ];
        if (!empty($meta))
            foreach ($meta as $key => $value) {
                $array[$key] = $value;
            }

        return response($array, $code);
    }
    public function successCode()
    {
        return [200, 201, 202];
    }

}
