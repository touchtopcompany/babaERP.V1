<?php

namespace Modules\Subdomain\Utils;

use App\Business;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use Modules\Subdomain\Entities\Subdomain;
use Modules\Superadmin\Entities\Response;
use Modules\Superadmin\Entities\Subscription;

trait Util
{
    protected string $admin_username;

    private function businessConnection(): Connection
    {
        $business_owner = Subdomain::query()->where('admin_username', $this->admin_username)->firstOrFail();
        $connect = json_decode(decrypt($business_owner->db_connection));
        // dd($connect);
        \Config::set('database.connections.sub_domain.database', $connect->database);
        \Config::set('database.connections.sub_domain.username', $connect->username);
        \Config::set('database.connections.sub_domain.password', $connect->password);
        DB::purge('sub_domain');
        DB::reconnect('sub_domain');
        return DB::connection('sub_domain');
    }

    private function businessActiveSubscriptionRaw()
    {
        $business = $this->getBusinessInfoUsingConn(admin_username: $this->admin_username);

        return $this->businessConnection()->table('subscriptions')
            ->where('business_id', $business->id)
            ->where('status', 'approved');
    }

    public function getBusinessInfoUsingConn(string|int $admin_username): object|null
    {
        $this->admin_username = $admin_username;
        $connection = $this->businessConnection();
        //Get Users
        $business_id = $connection->table('users')->where('username', $this->admin_username)->pluck('business_id')[0];
        return $connection->table('business')->where('id', $business_id)->first();
    }

    public function getBusinessActiveSubscription(string|int $admin_username): object|null
    {

        $this->admin_username = $admin_username;

        $date_today = \Carbon::today()->toDateString();
        return $this->businessActiveSubscriptionRaw()
            ->whereDate('start_date', '<=', $date_today)
            ->whereDate('end_date', '>=', $date_today)
            ->first();
    }

    public function saveNewSubscription(string|int $admin_username, array $details): bool
    {
        $this->admin_username = $admin_username;

        return $this->businessConnection()->table('subscriptions')->insert($details);
    }

    public function getBusinessEndDateSubscription(string|int $admin_username)
    {
        $this->admin_username = $admin_username;

        $date_today = \Carbon::today();
        $subscription = $this->businessActiveSubscriptionRaw()
            ->select(DB::raw('MAX(end_date) as end_date'))->first();

        if (empty($subscription->end_date)) {
            return $date_today;
        } else {
            $end_date = \Carbon::parse($subscription->end_date)->addDay();
            if ($date_today->lte($end_date)) {
                return $end_date;
            } else {
                return $date_today;
            }
        }
    }

    public function getBusinessLastActivity(string|int $admin_username)
    {
        $this->admin_username = $admin_username;

        $business = $this->getBusinessInfoUsingConn(admin_username: $this->admin_username);

        $connection = $this->businessConnection();

        $activity = $connection->table('activity_log')->where('business_id', $business->id)->latest();

        if($activity->exists()){
            return $activity->first()->created_at;
        }
        return null;
    }

    public function updateBusinessStatus(mixed $subdomain): int
    {
        $domain = Subdomain::query()->findOrFail($subdomain);

        $this->admin_username = $domain->admin_username;

        $business = $this->getBusinessInfoUsingConn(admin_username: $this->admin_username);

        $new_status = (int)!($business->is_active);

       return $this->businessConnection()->table('business')->where('id', $business->id)->update(['is_active' => $new_status]);
    }
}