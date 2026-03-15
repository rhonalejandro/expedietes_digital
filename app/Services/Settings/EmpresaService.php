<?php

namespace App\Services\Settings;

use App\Models\Empresa;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * EmpresaService
 * 
 * Lógica de negocio para la gestión de empresas.
 * Principio Single Responsibility: Solo maneja lógica de empresas.
 */
class EmpresaService
{
    /**
     * Obtener empresa configurada (id=1)
     * Retorna null si no hay empresa configurada
     * 
     * @return Empresa|null
     */
    public function getEmpresa(): ?Empresa
    {
        return Empresa::find(1);
    }

    /**
     * Obtener empresa por ID
     * 
     * @param int $id
     * @return Empresa|null
     */
    public function getById(int $id): ?Empresa
    {
        return Empresa::find($id);
    }

    /**
     * Actualizar información de empresa (siempre la empresa con id=1)
     *
     * @param array $data
     * @param UploadedFile|null $logo
     * @param UploadedFile|null $logoRectangular
     * @return Empresa
     */
    public function updateEmpresa(array $data, ?UploadedFile $logo = null, ?UploadedFile $logoRectangular = null): Empresa
    {
        // Obtener empresa con id=1
        $empresa = $this->getEmpresa();

        // Si no existe, crearla
        if (!$empresa) {
            $empresa = new Empresa();
            $empresa->id = 1;
        }

        // Actualizar campos
        $empresa->nombre = $data['nombre'];
        $empresa->tipo_identificacion = $data['tipo_identificacion'] ?? 'RUC';
        $empresa->identificacion = $data['identificacion'];
        $empresa->email = $data['email'];
        $empresa->telefono = $data['telefono'] ?? null;
        $empresa->direccion = $data['direccion'] ?? null;

        // Manejar logo redondo - SOLO si se subió uno nuevo
        if ($logo && $logo->isValid()) {
            $logoPath = $this->uploadLogo($empresa, $logo);
            if ($logoPath) {
                $empresa->logo = $logoPath;
            }
        }

        // Manejar logo rectangular - SOLO si se subió uno nuevo
        if ($logoRectangular && $logoRectangular->isValid()) {
            $logoPath = $this->uploadLogoRectangular($empresa, $logoRectangular);
            if ($logoPath) {
                $empresa->logo_rectangular = $logoPath;
            }
        }

        $empresa->save();

        return $empresa;
    }

    /**
     * Subir logo de empresa
     *
     * @param Empresa $empresa
     * @param UploadedFile $logo
     * @return string|null Path del logo o null si falló
     */
    private function uploadLogo(Empresa $empresa, UploadedFile $logo): ?string
    {
        // Eliminar logo anterior si existe
        if ($empresa->logo) {
            Storage::disk('public')->delete($empresa->logo);
        }

        // Generar nombre único
        $filename = bin2hex(random_bytes(16)) . '.' . $logo->getClientOriginalExtension();

        // Guardar en storage/app/public/logos/empresas/
        $path = $logo->storeAs('logos/empresas', $filename, 'public');

        // Verificar que se guardó correctamente
        if ($path && Storage::disk('public')->exists($path)) {
            return $path;
        }

        return null;
    }

    /**
     * Subir logo rectangular de empresa
     *
     * @param Empresa $empresa
     * @param UploadedFile $logo
     * @return string|null Path del logo o null si falló
     */
    private function uploadLogoRectangular(Empresa $empresa, UploadedFile $logo): ?string
    {
        // Eliminar logo rectangular anterior si existe
        if ($empresa->logo_rectangular) {
            Storage::disk('public')->delete($empresa->logo_rectangular);
        }

        // Generar nombre único
        $filename = bin2hex(random_bytes(16)) . '.' . $logo->getClientOriginalExtension();

        // Guardar en storage/app/public/logos/empresas/
        $path = $logo->storeAs('logos/empresas', $filename, 'public');

        // Verificar que se guardó correctamente
        if ($path && Storage::disk('public')->exists($path)) {
            return $path;
        }

        return null;
    }

    /**
     * Eliminar logo de empresa
     *
     * @param Empresa $empresa
     * @return void
     */
    public function deleteLogo(Empresa $empresa): void
    {
        if ($empresa->logo) {
            Storage::disk('public')->delete($empresa->logo);
            $empresa->logo = null;
            $empresa->save();
        }
    }

    /**
     * Eliminar logo rectangular de empresa
     *
     * @param Empresa $empresa
     * @return void
     */
    public function deleteLogoRectangular(Empresa $empresa): void
    {
        if ($empresa->logo_rectangular) {
            Storage::disk('public')->delete($empresa->logo_rectangular);
            $empresa->logo_rectangular = null;
            $empresa->save();
        }
    }

    /**
     * Obtener estadísticas de empresa
     * 
     * @return array
     */
    public function getStats(): array
    {
        $empresa = $this->getOrCreateDefault();
        
        return [
            'nombre' => $empresa->nombre,
            'email' => $empresa->email,
            'has_logo' => !empty($empresa->logo),
            'created_at' => $empresa->created_at,
            'updated_at' => $empresa->updated_at,
        ];
    }
}
