<?php

namespace App\Http\Controllers;

use App\Http\Requests\DirectoryRequest;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;

class CustomerController extends Controller
{
    public function index(DirectoryRequest $request)
    {
        $input = $request->validated();
        $q = trim($input['q'] ?? '');
        $sort = $input['sort'] ?? 'id';
        $direction = $input['direction'] ?? 'asc';
        $query = Customer::query();
        if ($q !== '') {
            // Values are bound by Eloquent; grouping keeps ORs inside this filter.
            $query->where(function (Builder $query) use ($q) {
                $query->where('full_name', 'like', '%'.$q.'%')
                    ->orWhere('email', 'like', '%'.$q.'%')
                    ->orWhere('phone', 'like', '%'.$q.'%');
            });
        }
        $query->orderBy($sort, $direction);
        if ($sort !== 'id') {
            $query->orderBy('id', 'asc');
        }
        $records = $query->get();
        $kind = 'customers';
        $columns = ['id' => 'ID', 'full_name' => 'Full name', 'email' => 'Email', 'phone' => 'Phone'];

        return view('directory', compact('records', 'q', 'sort', 'direction', 'kind', 'columns'));
    }
}
