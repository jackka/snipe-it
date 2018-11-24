<?php
namespace App\Http\Controllers\Api;

use App\Models\AssetCostcenter;
use App\Models\Asset;
use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Transformers\AssetCostcentersTransformer;
use App\Http\Transformers\AssetsTransformer;
use App\Http\Transformers\SelectlistTransformer;
use Illuminate\Support\Facades\Storage;


/**
 * This class controls all actions related to asset costcenters for
 * the Snipe-IT Asset Management application.
 *
 * @version    v4.0
 * @author [A. Gianotto] [<snipe@snipe.net>]
 */
class AssetCostcentersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view', AssetCostcenter::class);
        $allowed_columns = ['id','image','name','costcenter_number','eol','notes','created_at','manufacturer','assets_count'];

        $assetcostcenters = AssetCostcenter::select([
            'costcenters.id',
            'costcenters.image',
            'costcenters.name',
            'costcenter_number',
            'eol',
            'costcenters.notes',
            'costcenters.created_at',
            'category_id',
            'manufacturer_id',
            'depreciation_id',
            'fieldset_id',
            'costcenters.deleted_at',
            'costcenters.updated_at',
         ])
            ->with('category','depreciation', 'manufacturer','fieldset')
            ->withCount('assets as assets_count');



        if ($request->filled('status')) {
            $assetcostcenters->onlyTrashed();
        }

        if ($request->filled('search')) {
            $assetcostcenters->TextSearch($request->input('search'));
        }

        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 50);
        $order = $request->input('order') === 'asc' ? 'asc' : 'desc';
        $sort = in_array($request->input('sort'), $allowed_columns) ? $request->input('sort') : 'costcenters.created_at';

        switch ($sort) {
            case 'manufacturer':
                $assetcostcenters->OrderManufacturer($order);
                break;
            default:
                $assetcostcenters->orderBy($sort, $order);
                break;
        }



        $total = $assetcostcenters->count();
        $assetcostcenters = $assetcostcenters->skip($offset)->take($limit)->get();
        return (new AssetCostcentersTransformer)->transformAssetCostcenters($assetcostcenters, $total);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', AssetCostcenter::class);
        $assetcostcenter = new AssetCostcenter;
        $assetcostcenter->fill($request->all());

        if ($assetcostcenter->save()) {
            return response()->json(Helper::formatStandardApiResponse('success', $assetcostcenter, trans('admin/models/message.create.success')));
        }
        return response()->json(Helper::formatStandardApiResponse('error', null, $assetcostcenter->getErrors()));

    }

    /**
     * Display the specified resource.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->authorize('view', AssetCostcenter::class);
        $assetcostcenter = AssetCostcenter::withCount('assets as assets_count')->findOrFail($id);
        return (new AssetCostcentersTransformer)->transformAssetCostcenter($assetcostcenter);
    }

    /**
     * Display the specified resource's assets
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function assets($id)
    {
        $this->authorize('view', AssetCostcenter::class);
        $assets = Asset::where('costcenter_id','=',$id)->get();
        return (new AssetsTransformer)->transformAssets($assets, $assets->count());
    }


    /**
     * Update the specified resource in storage.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authorize('update', AssetCostcenter::class);
        $assetcostcenter = AssetCostcenter::findOrFail($id);
        $assetcostcenter->fill($request->all());
        $assetcostcenter->fieldset_id = $request->get("custom_fieldset_id");

        if ($assetcostcenter->save()) {
            return response()->json(Helper::formatStandardApiResponse('success', $assetcostcenter, trans('admin/models/message.update.success')));
        }

        return response()->json(Helper::formatStandardApiResponse('error', null, $assetcostcenter->getErrors()));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('delete', AssetCostcenter::class);
        $assetcostcenter = AssetCostcenter::findOrFail($id);
        $this->authorize('delete', $assetcostcenter);

        if ($assetcostcenter->assets()->count() > 0) {
            return response()->json(Helper::formatStandardApiResponse('error', null,  trans('admin/models/message.assoc_users')));
        }

        if ($assetcostcenter->image) {
            try  {
                Storage::disk('public')->delete('assetcostcenters/'.$assetcostcenter->image);
            } catch (\Exception $e) {
                \Log::error($e);
            }
        }

        $assetcostcenter->delete();
        return response()->json(Helper::formatStandardApiResponse('success', null,  trans('admin/models/message.delete.success')));

    }

    /**
     * Gets a paginated collection for the select2 menus
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0.16]
     * @see \App\Http\Transformers\SelectlistTransformer
     *
     */
    public function selectlist(Request $request)
    {

        $assetcostcenters = AssetCostcenter::select([
            'costcenters.id',
            'costcenters.name',
            'costcenters.image',
            'costcenters.costcenter_number',
            'costcenters.manufacturer_id',
            'costcenters.category_id',
        ])->with('manufacturer','category');

        $settings = \App\Models\Setting::getSettings();

        if ($request->filled('search')) {
            $assetcostcenters = $assetcostcenters->SearchByManufacturerOrCat($request->input('search'));
        }

        $assetcostcenters = $assetcostcenters->OrderCategory('ASC')->OrderManufacturer('ASC')->orderby('costcenters.name', 'asc')->orderby('costcenters.costcenter_number', 'asc')->paginate(50);

        foreach ($assetcostcenters as $assetcostcenter) {

            $assetcostcenter->use_text = '';

            if ($settings->modellistCheckedValue('category')) {
                $assetcostcenter->use_text .= (($assetcostcenter->category) ? e($assetcostcenter->category->name).' - ' : '');
            }

            if ($settings->modellistCheckedValue('manufacturer')) {
                $assetcostcenter->use_text .= (($assetcostcenter->manufacturer) ? e($assetcostcenter->manufacturer->name).' ' : '');
            }

            $assetcostcenter->use_text .=  e($assetcostcenter->name);

            if (($settings->modellistCheckedValue('costcenter_number')) && ($assetcostcenter->costcenter_number!='')) {
                $assetcostcenter->use_text .=  ' (#'.e($assetcostcenter->costcenter_number).')';
            }

            $assetcostcenter->use_image = ($settings->modellistCheckedValue('image') && ($assetcostcenter->image)) ? Storage::disk('public')->url('assetcostcenters/'.e($assetcostcenter->image)) : null;
        }

        return (new SelectlistTransformer)->transformSelectlist($assetcostcenters);
    }

}
