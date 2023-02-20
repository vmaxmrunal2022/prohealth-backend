<?php

namespace App\Http\Controllers\Code;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Nette\Utils\Json;
use Symfony\Component\HttpFoundation\Response;

use function PHPSTORM_META\elementType;

class ProviderTypeController extends Controller
{
    public function get(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "search" => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
        } else {
            $procedurecodes = DB::table('PROVIDER_TYPES')
                ->where('PROVIDER_TYPE', 'like', '%' . strtoupper($request->search) . '%')
                ->orWhere('DESCRIPTION', 'like', '%' . strtoupper($request->search) . '%')
                ->get();

            return $this->respondWithToken($this->token(), '', $procedurecodes);
        }
    }


    public function IdSearch(Request $request)
    {
        $priceShedule = DB::table('RX_NETWORKS')
            ->where('PHARMACY_NABP', 'like', '%' . strtoupper($request->search) . '%')
            ->get();
        return $this->respondWithToken($this->token(), '', $priceShedule);
    }

    public function add(Request $request)
    {
        if ($request->new) {
            $validator = Validator::make($request->all(), [
                "provider_type" => ['required', 'max:2', Rule::unique('PROVIDER_TYPES')->where(function ($q) {
                    $q->whereNotNull('provider_type');
                })],
                "description" => ['max:35']
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {

                $procedurecode = DB::table('PROVIDER_TYPES')->insert(
                    [
                        'PROVIDER_TYPE' => strtoupper($request->provider_type),
                        'DESCRIPTION' => strtoupper($request->description),
                        'DATE_TIME_CREATED' => date('y-m-d'),
                        'USER_ID_CREATED' => '',
                        'USER_ID' => '',
                        'DATE_TIME_MODIFIED' => '',
                        'FORM_ID' => '',
                        // 'COMPLETE_CODE_IND' => ''
                    ]
                );
                return  $this->respondWithToken($this->token(), 'Added successfully!', $procedurecode);
            }
        } else {
            $validator = Validator::make($request->all(), [
                "provider_type" => ['required', 'max:2'],
                "description" => ['max:35']
            ]);

            if ($validator->fails()) {
                return $this->respondWithToken($this->token(), $validator->errors(), $validator->errors(), "false");
            } else {
                $procedurecode = DB::table('PROVIDER_TYPES')
                    ->where(DB::raw('UPPER(PROVIDER_TYPE)'), strtoupper($request->provider_type))
                    ->update(
                        [
                            // 'PROVIDER_TYPE' => strtoupper($request->provider_type),
                            'DESCRIPTION' => strtoupper($request->description),
                            'DATE_TIME_CREATED' => date('y-m-d'),
                            'USER_ID_CREATED' => '',
                            'USER_ID' => '',
                            'DATE_TIME_MODIFIED' => '',
                            'FORM_ID' => '',
                            // 'COMPLETE_CODE_IND' => ''
                        ]
                    );
                // dd($procedurecode);
                return  $this->respondWithToken($this->token(), 'Updated successfully!', $procedurecode);
            }
        }

        // $procedurecode = DB::table('PROVIDER_TYPES')->where('PROVIDER_TYPE', $request->provider_type)->first();
        // 49cd477a9edf20221018112956 mobi

    }


    public function delete(Request $request)
    {
        return DB::table('PROVIDER_TYPES')->where('PROVIDER_TYPE', $request->id)->delete()
            ? $this->respondWithToken($this->token(), 'Successfully deleted')
            : $this->respondWithToken($this->token(), 'Could find data');
    }

    public function checkProviderTypeExist(Request $request)
    {
        $check = DB::table('PROVIDER_TYPES')
            ->where(DB::raw('UPPER(PROVIDER_TYPE)'), strtoupper($request->search))
            ->get()
            ->count();

        return $this->respondWithToken($this->token(), '', $check);
    }
}
