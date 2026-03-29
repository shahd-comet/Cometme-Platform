<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\Town;
use App\Models\TownHolder;
use App\Models\Household;
use App\Models\PublicStructure;
use App\Models\InternetUser;
use Carbon\Carbon;
use DB;

class InternetHolderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /**
         * this api should contains the following:
         * 1. Main users : meter number must be appeared.
         * 2. Sahred user : after getting the flag for him from here http://comet-me.info/api/energy-holder
         *    must take the name from the api (add is_main to internet is_main)
         * 3. Young holder: add his full name to add him as a new houshold on DB, then add his english name
         * 4. Main Public : meter number must be appeared.
         * 5. Sahred Public : after getting the flag from here http://comet-me.info/api/energy-holder
         *    must take the name from the api
         * 6. New Public Structures: add is_public flag to the internet flag to add them in the platform
         */

        return $this->getContractHolders();
    }

    /**
     * Get Last Internet Users & Update Exists
     *
     * @return mixed
     */
    public function getContractHolders()
    {
        $response = Http::get('http://185.190.140.86/api/users/');

        if (!$response->successful()) {
            return response()->json(['error' => 'API unavailable'], 500);
        }

        $holders = $response->json();

        DB::transaction(function () use ($holders) {

            foreach ($holders as $holder) {

                // Skip comet employees
                if ($holder['user_group_name'] === 'Comet Employee') {
                    continue;
                }

                $userType = null;
                $userId   = null;

                // Community
                $community = Community::where(
                    'arabic_name',
                    $holder['user_group_name']
                )->first();

                // Town
                $town = Town::where(
                    'comet_id',
                    $holder['comet_town_id']
                )->first(); 

                // TOWN HOLDER CASE
                if (!empty($holder['comet_town_id'])) {
                    
                    if ($town) {

                        $lastCometId = TownHolder::latest('id')->value('comet_id');

                        if (!$lastCometId) {

                            $cometId = 'th1';
                        } else {

                            // Split letters and numbers
                            preg_match('/^([a-zA-Z]+)(\d+)$/', $lastCometId, $matches);

                            $prefix = $matches[1];        // th
                            $number = (int) $matches[2];  // 1

                            $cometId = $prefix . ($number + 1); // th2
                        }
 
                        $existTownHolder = TownHolder::where("comet_id", $holder['comet_id'])->first();

                        if(!$existTownHolder) {

                            $townHolder = new TownHolder();
                            $townHolder->arabic_name = trim($holder['holder_full_name']);
                            $townHolder->phone_number = $holder['holder_mobile'];
                            $townHolder->town_id = $town->id;
                            $townHolder->has_internet = 1;
                            $townHolder->comet_id = $cometId;
                            $townHolder->save();

                            
                            $userType = 'town_holder';
                            $userId   = $townHolder->id;
                        }
                    }
                } else if(empty($holder['comet_town_id']) && ($holder['is_community_internal'] == 1 
                    || $holder['is_activist'] == 1  ) ) {

                    if($community) {

                        $lastCometId = TownHolder::latest('id')->value('comet_id');

                        if (!$lastCometId) {

                            $cometId = 'th1';
                        } else {

                            // Split letters and numbers
                            preg_match('/^([a-zA-Z]+)(\d+)$/', $lastCometId, $matches);

                            $prefix = $matches[1];        // th
                            $number = (int) $matches[2];  // 1

                            $cometId = $prefix . ($number + 1); // th2
                        }
 
                        $existTownHolder = TownHolder::where("comet_id", $holder['comet_id'])->first();
                        if(!$existTownHolder) {

                            $townHolder = new TownHolder();
                            $townHolder->arabic_name = trim($holder['holder_full_name']);
                            $townHolder->phone_number = $holder['holder_mobile'];
                            $townHolder->community_id = $community->id;
                            $townHolder->has_internet = 1;
                            $townHolder->is_activist = $holder['is_activist'];
                            $townHolder->is_community_internal = $holder['is_community_internal'];
                            $townHolder->comet_id = $cometId;
                            $townHolder->save();
                            
                            $userType = 'town_holder';
                            $userId   = $townHolder->id;
                        }
                    }
                } else {

                    // PUBLIC STRUCTURE
                    if ($holder['is_public_entity'] == 1 && $holder['comet_town_id'] == null) {

                        $publicUser = PublicStructure::where("comet_id", $holder['comet_id'])->first();
                        $publicUser->phone_number = $holder['holder_mobile'];
                        $publicUser->save();
 
                        $userType = 'public';
                        $userId   = $publicUser->id;
                    } else if ($holder['is_public_entity'] == 0 && $holder['comet_town_id'] == null){

                        $household = Household::where("comet_id", $holder['comet_id'])->first(); 
                        if($household) {

                            $household->phone_number = $holder['holder_mobile'];
                            $household->internet_system_status = 'Served';
                            $household->save();

                            $userType = 'household';
                            $userId   = $household->id;
                        }
                    }
                }

             

                if (!$userType || !$userId) {
                    continue;
                }

                // INTERNET USER CREATION
                $column = match ($userType) {
                    'public'       => 'public_structure_id',
                    'household'    => 'household_id',
                    'town_holder'  => 'town_holder_id',
                };

                InternetUser::updateOrCreate(
                    [
                        'internet_status_id' => 1,
                        $column              => $userId,
                    ],
                    [
                        'start_date'          => $holder['created_on'],
                        'active'              => $holder['active'],
                        'last_purchase_date'  => $holder['last_purchase_date'],
                        'expired_gt_than_30d' => $holder['expired_gt_than_30d'],
                        'expired_gt_than_60d' => $holder['expired_gt_than_60d'],
                        'is_expire'           => $holder['is_expire'],
                        'paid'                => $holder['paid'],
                        'community_id'        => $community->id ?? null, 
                        'town_id'             => $town->id ?? null, 
                        'is_hotspot'          => $holder['is_hotspot'],
                        'is_ppp'              => $holder['is_ppp'],
                        'from_api'            => 1,
                        'number_of_people'    => $holder['port_limit'] ?? 1,
                    ]
                );

                // COMMUNITY UPDATES
                if ($community) {
                    if (is_null($community->internet_service_beginning_year)) {
                        $community->internet_service_beginning_year =
                            Carbon::parse($holder['created_on'])->year;
                    }

                    $community->internet_service = 'Yes';
                    $community->save();
                }
            }
        });

        return response()->json(['status' => 'success']);
    }
}
