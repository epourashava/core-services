<?php

namespace Core\Traits;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * This trait is used to clean storage when a model is deleted or updated.
 * 
 * @package Core\Traits
 * @author Saiful Alam<hello@msar.me>
 */
trait CleanStorage
{
    protected $storageUrl = [];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    public static function bootCleanStorage()
    {
        static::deleted(
            function (Model $model) {
                foreach ($model->columnsForStorageItems() as $column) {
                    if (
                        $model->getRawOriginal($column) &&
                        Storage::exists($model->getRawOriginal($column))
                    ) {
                        Storage::delete($model->getRawOriginal($column));
                    }
                }
            }
        );

        static::updated(
            function (Model $model) {
                foreach ($model->columnsForStorageItems() as $column) {
                    $path = parse_url($model->getRawOriginal($column, ''))['path'] ?? '';

                    if (
                        $path &&
                        $model->isDirty($column) &&
                        Storage::exists($path)
                    ) {
                        Storage::delete($path);
                    }
                }
            }
        );
    }

    /**
     * Get the attributes that can be cleaned.
     * Also used to append the attribute.
     *
     * @return array<string>
     */
    abstract protected function columnsForStorageItems(): array;

    /**
     * Get the value of the appended attribute.
     *
     * @return string
     */
    protected function getStorageUrl($value)
    {
        if (!$value) return '';

        $url = Storage::providesTemporaryUrls() ?
            Storage::temporaryUrl($value, now()->addMinutes(5)) :
            Storage::url($value);

        return str($value)->startsWith('http') ?
            $value :
            $url;
    }

    /**
     * Get the value of the appended attribute.
     *
     * @return string
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (in_array($key, $this->columnsForStorageItems())) {
            return $this->getStorageUrl($value);
        }

        return $value;
    }

    /**
     * Get the value of the appended attribute.
     *
     * @return string
     */
    public function toArray()
    {
        $array = parent::toArray();

        foreach ($this->columnsForStorageItems() as $column) {
            if (isset($array[$column])) {
                $array[$column] = $this->getStorageUrl($array[$column]);
            }
        }

        return $array;
    }
}
