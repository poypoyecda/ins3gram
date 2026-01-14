<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\DataTableTrait;
class BrandModel extends Model
{
    use DataTableTrait;
    protected $table            = 'brand';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name','slug'];
    protected $beforeInsert = ['generateUniqueSlug'];
    protected $beforeUpdate = ['generateUniqueSlug'];
    protected function generateUniqueSlug(array $data)
    {
        if (!isset($data['data']['name'])) return $data;

        $newName = $data['data']['name'];
        $id = $data['data']['id'] ?? null;

        if ($id) {
            $old = $this->find($id);
            if ($old && $old['name'] === $newName) return $data;
        }

        $slug = generateSlug($newName);
        $all = $this->findAll();

        $same = array_filter($all, function ($row) use ($slug, $id) {
            if ($id && $row['id'] == $id) return false;
            return $row['slug'] === $slug;
        });

        if (count($same) > 0) {
            $slug .= '-' . (count($same) + 1);
        }

        $data['data']['slug'] = $slug;
        return $data;
    }
    protected $useTimestamps = false;
    protected $validationRules = [
        'name' => 'required|max_length[255]|is_unique[brand.name,id,{id}]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'Le nom de la marque est obligatoire.',
            'max_length' => 'Le nom de la marque ne peut pas dépasser 255 caractères.',
            'is_unique'  => 'Cette marque existe déjà.',
        ],
    ];

    protected function getDataTableConfig(): array
    {
        return [
            'searchable_fields' => [
                'brand.name',
                'brand.id',
            ],
            'joins' => [
                [
                    'table' => 'media',
                    'condition' => 'brand.id = media.entity_id AND media.entity_type = \'brand\'',
                    'type' => 'left'
                ]
            ],
            'select' => 'brand.*, media.file_path as image_url',
        ];
    }
}
