<?php

namespace App\Http\Controllers;

use App\Http\Requests\DirectoryRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserController extends Controller
{
    public function index(DirectoryRequest $request)
    {
        $input = $request->validated();
        $q = trim($input['q'] ?? '');
        $sort = $input['sort'] ?? 'id';
        $direction = $input['direction'] ?? 'asc';
        $query = User::query();
        if ($q !== '') {
            // Values are bound by Eloquent; grouping keeps ORs inside this filter.
            $query->where(function (Builder $query) use ($q) {
                $query->where('username', 'like', '%'.$q.'%')
                    ->orWhere('full_name', 'like', '%'.$q.'%');
            });
        }
        $query->orderBy($sort, $direction);
        if ($sort !== 'id') {
            $query->orderBy('id', 'asc');
        }
        $records = $query->get();
        $kind = 'users';
        $columns = ['id' => 'ID', 'username' => 'Username', 'full_name' => 'Full name'];

        return view('directory', compact('records', 'q', 'sort', 'direction', 'kind', 'columns'));
    }
}
