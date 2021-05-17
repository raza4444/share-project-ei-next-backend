<?php
/**
 * by stephan scheide
 */

namespace App\Repositories\Customers;


use App\Entities\Customers\Customer;
use App\Entities\Customers\CustomerInfoData;
use Illuminate\Support\Facades\DB;

class CustomerRepository
{

    /**
     * @param $limit
     * @return Customer[]
     */
    public function findCustomersWithoutPassword($limit)
    {
        return $this->query()
            ->whereRaw('(password is null or length(password)<2)')
            ->limit($limit)
            ->get();
    }

    public function updateMissingUsernamesToDefault()
    {
        $c = new Customer();
        DB::statement("update " . $c->table . " set username=concat('K',id) where username is null");
    }

    /**
     * updates password of customer
     * previous password will be overwritten
     *
     * @param $customerId
     * @param $password
     */
    public function updatePassword($customerId, $password)
    {
        $this->updateValues($customerId, ['password' => $password]);
        $this->query()->where('id', '=', $customerId)->update(['password' => $password]);
    }

    public function updateLoginDataAndEnableAccount($customerId, $username, $password)
    {
        $this->updateValues($customerId,
            [
                'username' => $username,
                'password' => $password,
                'canlogin' => 1
            ]);
    }

    /**
     * @param $customerId
     * @param $password
     * @return Customer|null
     */
    public function findByIdAndPassword($customerId, $password)
    {
        return $this->query()
            ->where('id', '=', $customerId)
            ->whereRaw('(lower(password) = ?)',[strtolower($password)])
            ->where('canlogin', '=', 1)
            ->first();
    }

    public function findCustomersDomainConfiguration()
    {
        return $this->query()
            ->whereRaw('(domain is not null and length(domain)>2)')
            ->get(['id', 'ftpusername', 'ftppassword', 'ftphost', 'domain', 'ftpdirectoryhtml']);
    }

    public function deleteAllInfoDataOfCustomer($customerId)
    {
        CustomerInfoData::query()
            ->where('customerid', '=', $customerId)
            ->delete();
    }

    public function createInfoData(CustomerInfoData $data)
    {
        CustomerInfoData::query()
            ->where('customerid', '=', $data->customerid)
            ->where('name', '=', $data->name)
            ->delete();

        $data->save();
    }

    private function query()
    {
        return Customer::query();
    }

    private function updateValues($customerId, $values)
    {
        $this->query()->where('id', '=', $customerId)->update($values);
    }


}
