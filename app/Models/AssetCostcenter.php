<?php
namespace App\Models;

use App\Models\Requestable;
use App\Models\SnipeModel;
use App\Models\Traits\Searchable;
use App\Presenters\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Watson\Validating\ValidatingTrait;
use Illuminate\Support\Facades\Storage;

/**
 * Model for Asset Models. Asset Models contain higher level
 * attributes that are common among the same type of asset.
 *
 * @version    v1.0
 */
class AssetCostcenter extends SnipeModel
{
    use SoftDeletes;
    protected $presenter = 'App\Presenters\AssetModelPresenter';
    use Requestable, Presentable;
    protected $dates = ['deleted_at'];
    protected $table = 'costcenters';
    protected $hidden = ['user_id','deleted_at'];

    // Declare the rules for the costcenter validation
    protected $rules = array(
        'name'          => 'required|min:1|max:255',
        'costcenter_number'      => 'max:255|nullable',
        'category_id'       => 'required|integer|exists:categories,id',
        'manufacturer_id'   => 'required|integer|exists:manufacturers,id',
        'eol'   => 'integer:min:0|max:240|nullable',
    );

    /**
    * Whether the costcenter should inject it's identifier to the unique
    * validation rules before attempting validation. If this property
    * is not set in the costcenter it will default to true.
    *
    * @var boolean
    */
    protected $injectUniqueIdentifier = true;
    use ValidatingTrait;

    public function setEolAttribute($value)
    {
        if ($value == '') {
            $value = 0;
        }

        $this->attributes['eol'] = $value;
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'depreciation_id',
        'eol',
        'fieldset_id',
        'image',
        'manufacturer_id',
        'costcenter_number',
        'name',
        'notes',
        'user_id',
    ];

    use Searchable;
    
    /**
     * The attributes that should be included when searching the costcenter.
     * 
     * @var array
     */
    protected $searchableAttributes = ['name', 'costcenter_number', 'notes', 'eol'];

    /**
     * The relations and their attributes that should be included when searching the costcenter.
     * 
     * @var array
     */
    protected $searchableRelations = [
        'depreciation' => ['name'],
        'category'     => ['name'],
        'manufacturer' => ['name'],
    ];


    /**
     * Establishes the costcenter -> assets relationship
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v1.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function assets()
    {
        return $this->hasMany('\App\Models\Asset', 'costcenter_id');
    }

    /**
     * Establishes the costcenter -> category relationship
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v1.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function category()
    {
        return $this->belongsTo('\App\Models\Category', 'category_id');
    }

    /**
     * Establishes the costcenter -> depreciation relationship
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v1.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function depreciation()
    {
        return $this->belongsTo('\App\Models\Depreciation', 'depreciation_id');
    }


    /**
     * Establishes the costcenter -> manufacturer relationship
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v1.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function manufacturer()
    {
        return $this->belongsTo('\App\Models\Manufacturer', 'manufacturer_id');
    }

    /**
     * Establishes the costcenter -> fieldset relationship
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v2.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function fieldset()
    {
        return $this->belongsTo('\App\Models\CustomFieldset', 'fieldset_id');
    }

    /**
     * Establishes the costcenter -> custom field default values relationship
     *
     * @author hannah tinkler
     * @since [v4.3]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function defaultValues()
    {
        return $this->belongsToMany('\App\Models\CustomField', 'costcenters_custom_fields')->withPivot('default_value');
    }


    /**
     * Gets the full url for the image
     *
     * @todo this should probably be moved
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v2.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function getImageUrl() {
        if ($this->image) {
            return Storage::disk('public')->url(app('costcenters_upload_path').$this->image);
        }
        return false;
    }

    /**
    * -----------------------------------------------
    * BEGIN QUERY SCOPES
    * -----------------------------------------------
    **/

    /**
    * Query builder scope for Deleted assets
    *
    * @param  \Illuminate\Database\Query\Builder  $query  Query builder instance
    * @return \Illuminate\Database\Query\Builder          Modified query builder
    */

    public function scopeDeleted($query)
    {
        return $query->whereNotNull('deleted_at');
    }

    /**
     * scopeInCategory
     * Get all costcenters that are in the array of category ids
     *
     * @param       $query
     * @param array $categoryIdListing
     *
     * @return mixed
     * @author  Vincent Sposato <vincent.sposato@gmail.com>
     * @version v1.0
     */
    public function scopeInCategory($query, array $categoryIdListing)
    {

        return $query->whereIn('category_id', $categoryIdListing);
    }

    /**
     * scopeRequestable
     * Get all costcenters that are requestable by a user.
     *
     * @param       $query
     *
     * @return $query
     * @author  Daniel Meltzer <dmeltzer.devel@gmail.com>
     * @version v3.5
     */
    public function scopeRequestableModels($query)
    {

        return $query->where('requestable', '1');
    }  

    /**
     * Query builder scope to search on text, including catgeory and manufacturer name
     *
     * @param  Illuminate\Database\Query\Builder  $query  Query builder instance
     * @param  text                              $search      Search term
     *
     * @return Illuminate\Database\Query\Builder          Modified query builder
     */
    public function scopeSearchByManufacturerOrCat($query, $search)
    {

        return $query->where('costcenters.name', 'LIKE', "%$search%")
            ->orWhere('costcenter_number', 'LIKE', "%$search%")
            ->orWhere(function ($query) use ($search) {
                $query->whereHas('category', function ($query) use ($search) {
                    $query->where('categories.name', 'LIKE', '%'.$search.'%');
                });
            })
            ->orWhere(function ($query) use ($search) {
                $query->whereHas('manufacturer', function ($query) use ($search) {
                    $query->where('manufacturers.name', 'LIKE', '%'.$search.'%');
                });
            });

    }

    /**
     * Query builder scope to order on manufacturer
     *
     * @param  \Illuminate\Database\Query\Builder  $query  Query builder instance
     * @param  text                              $order       Order
     *
     * @return \Illuminate\Database\Query\Builder          Modified query builder
     */
    public function scopeOrderManufacturer($query, $order)
    {
        return $query->leftJoin('manufacturers', 'costcenters.manufacturer_id', '=', 'manufacturers.id')->orderBy('manufacturers.name', $order);
    }

    /**
     * Query builder scope to order on category name
     *
     * @param  \Illuminate\Database\Query\Builder  $query  Query builder instance
     * @param  text                              $order       Order
     *
     * @return \Illuminate\Database\Query\Builder          Modified query builder
     */
    public function scopeOrderCategory($query, $order)
    {
        return $query->leftJoin('categories', 'costcenters.category_id', '=', 'categories.id')->orderBy('categories.name', $order);
    }


}
