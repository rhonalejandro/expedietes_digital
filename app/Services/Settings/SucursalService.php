<?php

namespace App\Services\Settings;

use App\Models\Sucursal;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * SucursalService
 *
 * Lógica de negocio para la gestión de sucursales.
 * Principio Single Responsibility: Solo maneja lógica de sucursales.
 */
class SucursalService
{
    /**
     * Obtener todas las sucursales
     *
     * @param bool $onlyActive
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(bool $onlyActive = false)
    {
        $query = Sucursal::query();

        if ($onlyActive) {
            $query->where('estado', true);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Crear nueva sucursal
     *
     * @param array $data
     * @param UploadedFile|null $imagen
     * @return Sucursal
     */
    public function createSucursal(array $data, ?UploadedFile $imagen = null): Sucursal
    {
        $sucursalData = [
            'nombre' => $data['nombre'],
            'direccion' => $data['direccion'],
            'telefono' => $data['telefono'] ?? null,
            'email' => $data['email'] ?? null,
            'encargado' => $data['encargado'] ?? null,
            'estado' => $data['estado'] ?? true,
        ];

        // Manejar imagen si se subió
        if ($imagen && $imagen->isValid()) {
            $sucursalData['imagen'] = $this->uploadImagen($imagen);
        }

        return Sucursal::create($sucursalData);
    }

    /**
     * Actualizar sucursal
     *
     * @param int $id
     * @param array $data
     * @param UploadedFile|null $imagen
     * @return Sucursal
     */
    public function updateSucursal(int $id, array $data, ?UploadedFile $imagen = null): Sucursal
    {
        $sucursal = Sucursal::findOrFail($id);

        // Manejar imagen si se subió una nueva
        if ($imagen && $imagen->isValid()) {
            $this->deleteImagen($sucursal);
            $data['imagen'] = $this->uploadImagen($imagen);
        }

        $sucursal->update($data);

        return $sucursal->fresh();
    }

    /**
     * Eliminar sucursal (soft delete)
     *
     * @param int $id
     * @return bool
     */
    public function deleteSucursal(int $id): bool
    {
        $sucursal = Sucursal::findOrFail($id);
        
        // Eliminar imagen asociada si existe
        $this->deleteImagen($sucursal);
        
        return $sucursal->delete(); // Soft delete
    }

    /**
     * Forzar eliminación de sucursal (permanent delete)
     *
     * @param int $id
     * @return bool
     */
    public function forceDeleteSucursal(int $id): bool
    {
        $sucursal = Sucursal::withTrashed()->findOrFail($id);
        
        // Eliminar imagen asociada si existe
        $this->deleteImagen($sucursal);
        
        return $sucursal->forceDelete();
    }

    /**
     * Restaurar sucursal eliminada
     *
     * @param int $id
     * @return bool
     */
    public function restoreSucursal(int $id): bool
    {
        return Sucursal::withTrashed()->findOrFail($id)->restore();
    }

    /**
     * Cambiar estado de sucursal
     *
     * @param int $id
     * @return Sucursal
     */
    public function toggleStatus(int $id): Sucursal
    {
        $sucursal = Sucursal::findOrFail($id);
        $sucursal->estado = !$sucursal->estado;
        $sucursal->save();

        return $sucursal->fresh();
    }

    /**
     * Obtener sucursal por ID
     *
     * @param int $id
     * @return Sucursal
     */
    public function getById(int $id): Sucursal
    {
        return Sucursal::findOrFail($id);
    }

    /**
     * Contar sucursales activas
     *
     * @return int
     */
    public function countActive(): int
    {
        return Sucursal::where('estado', true)->count();
    }

    /**
     * Contar total de sucursales (incluyendo eliminadas)
     *
     * @return int
     */
    public function countTotal(): int
    {
        return Sucursal::withTrashed()->count();
    }

    /**
     * Contar sucursales eliminadas
     *
     * @return int
     */
    public function countTrashed(): int
    {
        return Sucursal::onlyTrashed()->count();
    }

    /**
     * Buscar sucursales por término
     *
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function search(string $search)
    {
        return Sucursal::where('nombre', 'LIKE', "%{$search}%")
            ->orWhere('direccion', 'LIKE', "%{$search}%")
            ->orWhere('encargado', 'LIKE', "%{$search}%")
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Subir imagen de sucursal
     *
     * @param UploadedFile $imagen
     * @return string|null
     */
    private function uploadImagen(UploadedFile $imagen): ?string
    {
        $filename = bin2hex(random_bytes(16)) . '.' . $imagen->getClientOriginalExtension();
        $path = $imagen->storeAs('sucursales', $filename, 'public');

        return $path && Storage::disk('public')->exists($path) ? $path : null;
    }

    /**
     * Eliminar imagen de sucursal
     *
     * @param Sucursal $sucursal
     * @return void
     */
    private function deleteImagen(Sucursal $sucursal): void
    {
        if ($sucursal->imagen) {
            Storage::disk('public')->delete($sucursal->imagen);
        }
    }
}
