<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use RealRashid\SweetAlert\Facades\Alert;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    protected array $validationRules = [];
    protected string $route;

    protected function confirmDeleteMessage()
    {
        $title = 'Hapus Data!';
        $text = 'Apakah anda yakin menghapus data ini?';
        confirmDelete($title, $text);
    }

    protected function returnToIndex($action, $title, $route)
    {
        $actionText = '';
        if ($action == 'store') {
            $actionText = 'Ditambahkan';
        } elseif ($action == 'update') {
            $actionText = 'Diubah';
        } elseif ($action == 'destroy') {
            $actionText = 'Dihapus';
        }
        Alert::success('Berhasil', 'Data ' . $title . ' Berhasil ' . $actionText . '!');
        return redirect()->route($route);
    }

    protected function generateDatatable($query, $routePrefix)
    {
        return datatables()
            ->of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) use ($routePrefix) {
                return '
                    <div class="d-flex justify-content-center gap-2">
                        <a href="' . route($routePrefix . '.edit', isset($row->id) ? $row->id : $row['id']) . '" class="btn btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="' . route($routePrefix . '.destroy', isset($row->id) ? $row->id : $row['id']) . '" class="btn btn-danger" data-confirm-delete="true">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                ';
            })
            ->addColumn('delete', function ($row) use ($routePrefix) {
                return '
                    <div class="d-flex justify-content-center gap-2">
                        <a href="' . route($routePrefix . '.destroy', isset($row->id) ? $row->id : $row['id']) . '" class="btn btn-danger" data-confirm-delete="true">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                ';
            })
            ->rawColumns(['action', 'delete'])
            ->make(true);
    }


    protected function validateRequest(Request $request)
    {
        return $request->validate($this->validationRules);
    }

    // General store method
    public function storeData(Request $request, $model)
    {
        // Validate the data based on validation rules set in each controller
        $validatedData = $this->validateRequest($request);

        // Create the model instance
        $model::create($validatedData);

        $modelName = class_basename($model);

        // Alert success message
        Alert::success('Berhasil', $modelName . ' Berhasil Ditambahkan!');

        // Redirect to index route of the model (you can define this route in the controller or pass it dynamically)
        return redirect()->route($this->route);
    }

    public function storeDataUseAjax(Request $request, $model)
    {
        $validatedData = $this->validateRequest($request);

        // Create the model instance
        $model::create($validatedData);
    }

    public function updateData(Request $request, $model, $instance)
    {
        // Validate the request using the defined rules
        $validatedData = $this->validateRequest($request);

        // Update the model instance with the validated data
        $instance->update($validatedData);

        // Use class_basename to get the class name without the namespace for the success message
        $modelName = class_basename($model);

        // Show success message
        Alert::success('Berhasil', $modelName . ' Berhasil Diubah');

        // Redirect to the index route after successful update
        return redirect()->route($this->route);
    }

    public function destroyData($model, $instance)
    {
        // Delete the model instance
        $instance->delete();

        // Use class_basename to get the class name without the namespace for the success message
        $modelName = class_basename($model);

        // Show success message
        Alert::success('Berhasil', $modelName . ' Berhasil Dihapus');

        // Redirect to the index route after successful deletion
        return redirect()->route($this->route);
    }

    public function export($title, $query, $view, $orientation = 'portrait')
    {
        $pdf = Pdf::loadView('pdf.' . $view, [
            'title' => $title,
            'data' => $query
        ])->setPaper('a4', $orientation);

        return $pdf->download('data-' . $view . '.pdf');
    }
}
