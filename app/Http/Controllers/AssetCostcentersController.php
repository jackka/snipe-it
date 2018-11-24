<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use App\Models\AssetCostcenter;
use Redirect;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Requests\ImageUploadRequest;

/**
 * This class controls all actions related to asset costcenters for
 * the Snipe-IT Asset Management application.
 *
 * @version    v1.0
 * @author [A. Gianotto] [<snipe@snipe.net>]
 */
class AssetCostcentersController extends Controller
{
    /**
     * Returns a view that invokes the ajax tables which actually contains
     * the content for the accessories listing, which is generated in getDatatable.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v1.0]
     * @return View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        $this->authorize('index', AssetCostcenter::class);
        return view('costcenters/index');
    }

    /**
     * Returns a view containing the asset costcenter creation form.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v1.0]
     * @return View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $this->authorize('create', AssetCostcenter::class);
        return view('costcenters/edit')->with('category_type', 'asset')
            ->with('depreciation_list', Helper::depreciationList())
            ->with('item', new AssetCostcenter);
    }


    /**
     * Validate and process the new Asset Model data.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v1.0]
     * @param ImageUploadRequest $request
     * @return Redirect
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(ImageUploadRequest $request)
    {

        $this->authorize('create', AssetCostcenter::class);
        // Create a new asset costcenter
        $costcenter = new AssetCostcenter;

        // Save the costcenter data
        $costcenter->eol = $request->input('eol');
        $costcenter->depreciation_id = $request->input('depreciation_id');
        $costcenter->name                = $request->input('name');
        $costcenter->costcenter_number        = $request->input('costcenter_number');
        $costcenter->manufacturer_id     = $request->input('manufacturer_id');
        $costcenter->category_id         = $request->input('category_id');
        $costcenter->notes               = $request->input('notes');
        $costcenter->user_id             = Auth::id();
        $costcenter->requestable         = Input::has('requestable');

        if ($request->input('custom_fieldset')!='') {
            $costcenter->fieldset_id = e($request->input('custom_fieldset'));
        }

        $costcenter = $request->handleImages($costcenter);

            // Was it created?
        if ($costcenter->save()) {
            if ($this->shouldAddDefaultValues($request->input())) {
                $this->assignCustomFieldsDefaultValues($costcenter, $request->input('default_values'));
            }

            // Redirect to the new costcenter  page
            return redirect()->route("costcenters.index")->with('success', trans('admin/costcenters/message.create.success'));
        }
        return redirect()->back()->withInput()->withErrors($costcenter->getErrors());
    }

    /**
     * Returns a view containing the asset costcenter edit form.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v1.0]
     * @param int $costcenterId
     * @return View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($costcenterId = null)
    {
        $this->authorize('update', AssetCostcenter::class);
        if ($item = AssetCostcenter::find($costcenterId)) {
            $category_type = 'asset';
            $view = View::make('costcenters/edit', compact('item','category_type'));
            $view->with('depreciation_list', Helper::depreciationList());
            return $view;
        }

        return redirect()->route('costcenters.index')->with('error', trans('admin/costcenters/message.does_not_exist'));

    }


    /**
     * Validates and processes form data from the edit
     * Asset Model form based on the costcenter ID passed.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v1.0]
     * @param ImageUploadRequest $request
     * @param int $costcenterId
     * @return Redirect
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(ImageUploadRequest $request, $costcenterId = null)
    {
        $this->authorize('update', AssetCostcenter::class);
        // Check if the costcenter exists
        if (is_null($costcenter = AssetCostcenter::find($costcenterId))) {
            // Redirect to the costcenters management page
            return redirect()->route('costcenters.index')->with('error', trans('admin/costcenters/message.does_not_exist'));
        }

        $costcenter->depreciation_id     = $request->input('depreciation_id');
        $costcenter->eol                 = $request->input('eol');
        $costcenter->name                = $request->input('name');
        $costcenter->costcenter_number        = $request->input('costcenter_number');
        $costcenter->manufacturer_id     = $request->input('manufacturer_id');
        $costcenter->category_id         = $request->input('category_id');
        $costcenter->notes               = $request->input('notes');
        $costcenter->requestable         = $request->input('requestable', '0');

        $this->removeCustomFieldsDefaultValues($costcenter);

        if ($request->input('custom_fieldset')=='') {
            $costcenter->fieldset_id = null;
        } else {
            $costcenter->fieldset_id = $request->input('custom_fieldset');

            if ($this->shouldAddDefaultValues($request->input())) {
                $this->assignCustomFieldsDefaultValues($costcenter, $request->input('default_values'));
            }
        }

        $costcenter = $request->handleImages($costcenter);

        if ($costcenter->save()) {
            return redirect()->route("costcenters.index")->with('success', trans('admin/costcenters/message.update.success'));
        }
        return redirect()->back()->withInput()->withErrors($costcenter->getErrors());
    }

    /**
     * Validate and delete the given Asset Model. An Asset Model
     * cannot be deleted if there are associated assets.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v1.0]
     * @param int $costcenterId
     * @return Redirect
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($costcenterId)
    {
        $this->authorize('delete', AssetCostcenter::class);
        // Check if the costcenter exists
        if (is_null($costcenter = AssetCostcenter::find($costcenterId))) {
            return redirect()->route('costcenters.index')->with('error', trans('admin/costcenters/message.not_found'));
        }

        if ($costcenter->assets()->count() > 0) {
            // Throw an error that this costcenter is associated with assets
            return redirect()->route('costcenters.index')->with('error', trans('admin/costcenters/message.assoc_users'));
        }

        if ($costcenter->image) {
            try  {
                Storage::disk('public')->delete('costcenters/'.$costcenter->image);
            } catch (\Exception $e) {
                \Log::error($e);
            }
        }

        // Delete the costcenter
        $costcenter->delete();

        // Redirect to the costcenters management page
        return redirect()->route('costcenters.index')->with('success', trans('admin/costcenters/message.delete.success'));
    }


    /**
     * Restore a given Asset Model (mark as un-deleted)
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v1.0]
     * @param int $costcenterId
     * @return Redirect
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getRestore($costcenterId = null)
    {
        $this->authorize('create', AssetCostcenter::class);
        // Get user information
        $costcenter = AssetCostcenter::withTrashed()->find($costcenterId);

        if (isset($costcenter->id)) {
            $costcenter->restore();
            return redirect()->route('costcenters.index')->with('success', trans('admin/costcenters/message.restore.success'));
        }
        return redirect()->back()->with('error', trans('admin/costcenters/message.not_found'));

    }


    /**
     * Get the costcenter information to present to the costcenter view page
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v1.0]
     * @param int $costcenterId
     * @return View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show($costcenterId = null)
    {
        $this->authorize('view', AssetCostcenter::class);
        $costcenter = AssetCostcenter::withTrashed()->find($costcenterId);

        if (isset($costcenter->id)) {
            return view('costcenters/view', compact('costcenter'));
        }
        // Redirect to the user management page
        return redirect()->route('costcenters.index')->with('error', trans('admin/costcenters/message.does_not_exist'));
    }

    /**
    * Get the clone page to clone a costcenter
    *
    * @author [A. Gianotto] [<snipe@snipe.net>]
    * @since [v1.0]
    * @param int $costcenterId
    * @return View
    */
    public function getClone($costcenterId = null)
    {
        // Check if the costcenter exists
        if (is_null($costcenter_to_clone = AssetCostcenter::find($costcenterId))) {
            return redirect()->route('costcenters.index')->with('error', trans('admin/costcenters/message.does_not_exist'));
        }

        $costcenter = clone $costcenter_to_clone;
        $costcenter->id = null;

        // Show the page
        return view('costcenters/edit')
            ->with('depreciation_list', Helper::depreciationList())
            ->with('item', $costcenter)
            ->with('clone_costcenter', $costcenter_to_clone);
    }


    /**
    * Get the custom fields form
    *
    * @author [B. Wetherington] [<uberbrady@gmail.com>]
    * @since [v2.0]
    * @param int $costcenterId
    * @return View
    */
    public function getCustomFields($costcenterId)
    {
        return view("costcenters.custom_fields_form")->with("costcenter", AssetCostcenter::find($costcenterId));
    }


    /**
     * Returns true if a fieldset is set, 'add default values' is ticked and if
     * any default values were entered into the form.
     *
     * @param  array  $input
     * @return boolean
     */
    private function shouldAddDefaultValues(array $input)
    {
        return !empty($input['add_default_values'])
            && !empty($input['default_values'])
            && !empty($input['custom_fieldset']);
    }

    /**
     * Adds default values to a costcenter (as long as they are truthy)
     *
     * @param  AssetCostcenter $costcenter
     * @param  array      $defaultValues
     * @return void
     */
    private function assignCustomFieldsDefaultValues(AssetCostcenter $costcenter, array $defaultValues)
    {
        foreach ($defaultValues as $customFieldId => $defaultValue) {
            if ($defaultValue) {
                $costcenter->defaultValues()->attach($customFieldId, ['default_value' => $defaultValue]);
            }
        }
    }

    /**
     * Removes all default values
     *
     * @return void
     */
    private function removeCustomFieldsDefaultValues(AssetCostcenter $costcenter)
    {
        $costcenter->defaultValues()->detach();
    }
}
