<?php

namespace App\Http\Controllers;

use App\Helpers\CategoryHelper;
use App\Helpers\ImpactHelper;
use App\Helpers\ResponseHelper;
use App\Http\Requests\ImpactRequest\UpdateImpactRequest;
use App\Jobs\GenerateExport;
use App\Models\Category;
use App\Models\EditHistory;
use App\Models\Impact;
use App\Models\RawImpact;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\Villager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;

class ImpactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return $this->listImpact($request);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function filter(Request $request)
    {
        return $this->listImpact($request);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param string $categoryId
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function filterByCategory(Request $request, $categoryId)
    {
        $request['category'] = $categoryId;
        return $this->listImpact($request, true);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param bool $isFilterSubMenu
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    private function listImpact(Request $request, $isFilterSubMenu = false)
    {
        /* @var User $user */
        $user = Auth::user();
        if ($user->can('viewListByGroup', Impact::class)) {
            $request['userGroup'] = $user->user_group_id;
        } else {
            $userGroupId = session(config('settings.global_user_group_session_name'));
            if ($userGroupId) {
                $request['userGroup'] = $userGroupId;
            }
        }
        $searchFields = ImpactHelper::defaultSearchFields();
        $searchFields = array_merge($searchFields, $request->only(array_keys($searchFields)));
        $searchRules = ImpactHelper::buildFilterQuery($searchFields);

        $sortingField = 'report_date';
        $sortingDirection = 'desc';
        if ($request->has('sort') && $request->has('direction')) {
            $sortingField = $request->sort;
            $sortingDirection = $request->direction;
        }
        if ($user->can('viewListForAllGroups', Impact::class) || $user->can('viewListByGroup', Impact::class)) {
            $query = Impact::search('ANY')
                ->rule(function () use ($searchRules) {
                    return $searchRules;
                })
                ->where('active', true);

            $query->orderBy($sortingField, $sortingDirection);
            $impacts = $query->paginate(Config::get('settings.record_per_page'));
            $request->session()->put('total_impacts', $impacts->total());
            $userGroups = UserGroup::all();
            $provinces = $this->getProvincesForFilter();
            $categories = CategoryHelper::getCategoriesByLevel();
            $subCategories1 = CategoryHelper::getCategoriesByLevel(1, intval($searchFields['category']));
            $searchFields['dateRange']['to'] = isset($searchFields['dateRange']['to']) ? $searchFields['dateRange']['to'] : '';
            $searchFields['dateRange']['from'] = isset($searchFields['dateRange']['from']) ? $searchFields['dateRange']['from'] : '';

            return view(
                'templates.impact.index',
                compact(
                    'impacts',
                    'provinces',
                    'searchFields',
                    'categories',
                    'userGroups',
                    'subCategories1',
                    'isFilterSubMenu'
                )
            );
        }
        return abort(401);
    }

    /**
     * Get sub categories
     *
     * @param integer $level
     * @param integer $categoryId
     *
     * @return Response
     */
    public function getSubCategories($level, $categoryId)
    {
        $category = Category::find($categoryId);
        $modifyChild = $category instanceof Category ? $category->modify_child : false;
        if ($modifyChild) {
            $modifyChild = $category->level === (int) $level;
        }
        $result = [
            'modify_child' => $modifyChild,
            'children' => CategoryHelper::getCategoriesByLevel(((int) $level + 1), $categoryId, $modifyChild)
        ];
        return ResponseHelper::makeResponse(
            'Sub Categories',
            $result,
            200
        );
    }

    /**
     * @param Impact $impact
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Impact $impact)
    {
        $user = Auth::user();
        if ($user->can('updateAsSuperDataManager', Impact::class) || $user->can('updateByGroup', $impact)) {
            $excludedReasons = $this->getSettingOptions('excludedReason');
            $victimTypes = $this->getSettingOptions('victimType');
            $reasons = $this->getSettingOptions('reason');
            $offenders = $this->getSettingOptions('offender');
            $threatenings = $this->getSettingOptions('threatening');
            $proofs = $this->getSettingOptions('proof');
            $designations = $this->getSettingOptions('designation');
            $categoryLevels = array_keys(Config::get('settings.category_levels'));
            $categoryFields = Config::get('settings.category_field_mapping');
            $categories[] = CategoryHelper::getCategoriesByLevel();
            foreach ($categoryLevels as $level => $value) {
                $field = $categoryFields[$value];
                // When current category has value.
                if (is_object($impact->$field)) {
                    if (is_object($impact->$field->parent) && $impact->$field->parent->modify_child && $level === $impact->$field->parent->level + 1) {
                        $categories[] = CategoryHelper::getCategoriesByLevel($level + 1, $impact->$field->parent_id, $impact->$field->modify_child);
                        continue;
                    }
                    $categories[] = CategoryHelper::getCategoriesByLevel($level + 1, $impact->$field->id, $impact->$field->modify_child);
                    continue;
                } else {
                    // When category is empty.
                    $previousField = $categoryFields[$categoryLevels[$level - 1]];
                    if (is_object($impact->$previousField)) {
                        $modifyChild = $impact->$previousField->modify_child;
                        if ($modifyChild) {
                            $categories[] = CategoryHelper::getCategoriesByLevel($level + 1, $impact->$previousField->id, true);
                            continue;
                        }
                    }
                }
                $categories[] = [];
            }
            $reportingCategories = CategoryHelper::getCategoriesBySysValues(
                Config::get('settings.reporting_categories')
            )->implode('id', ',');
            $loggingCategories = CategoryHelper::getCategoriesBySysValues(
                Config::get('settings.logging_categories'),
                0
            )->implode('id', ',');
            $dontKnowCategories = CategoryHelper::getCategoriesBySysValues(
                Config::get('settings.dont_know_categories'),
                4
            )->implode('id', ',');
            $interactionNoCategories = CategoryHelper::getCategoriesBySysValues(
                Config::get('settings.interaction_no_categories'),
                5
            )->implode('id', ',');
            $otherCategories = CategoryHelper::getCategoriesBySysValues(
                Config::get('settings.other_categories'),
                0
            )->implode('id', ',');

            return view(
                'templates.impact.edit',
                compact(
                    'impact',
                    'excludedReasons',
                    'victimTypes',
                    'reasons',
                    'offenders',
                    'threatenings',
                    'proofs',
                    'designations',
                    'categories',
                    'otherCategories',
                    'loggingCategories',
                    'dontKnowCategories',
                    'reportingCategories',
                    'interactionNoCategories'
                )
            );
        }
        return abort(401);
    }

    /**
     * @param string $type
     *
     * @return array
     */
    protected function getSettingOptions($type)
    {
        $options = Setting::whereType($type)->orderBy('sorting', 'asc')->pluck('name', 'id')->toArray();
        $allOption = [null => ''];
        return ($allOption + $options);
    }

    /**
     * @param Impact $impact
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Impact $impact)
    {
        $user = Auth::user();
        if ($user->can('showDetailForAllGroups', Impact::class) || $user->can('showByGroup', $impact)) {
            $excludedReasons = $this->getOptionForDetail($impact->excludedReason);
            $victimTypes = $this->getOptionForDetail($impact->victimType);
            $reasons = $this->getOptionForDetail($impact->reason);
            $offenders = $this->getOptionForDetail($impact->offender);
            $threatenings = $this->getOptionForDetail($impact->threatening);
            $proofs = $this->getOptionForDetail($impact->proof);
            $designations = $this->getOptionForDetail($impact->designation);
            $categoryLevels = array_keys(Config::get('settings.category_levels'));
            $categoryFields = Config::get('settings.category_field_mapping');
            foreach ($categoryLevels as $level => $value) {
                $field = $categoryFields[$value];
                if (is_object($impact->$field)) {
                    $categories[] = [$impact->$field->id => $impact->$field->name];
                    continue;
                }
                $categories[] = [];
            }
            $otherCategories = '';
            $loggingCategories = '';
            $dontKnowCategories = '';
            $reportingCategories = CategoryHelper::getCategoriesBySysValues(
                Config::get('settings.reporting_categories')
            )->implode('id', ',');
            $interactionNoCategories = '';

            return view(
                'templates.impact.show',
                compact(
                    'impact',
                    'excludedReasons',
                    'victimTypes',
                    'reasons',
                    'offenders',
                    'threatenings',
                    'proofs',
                    'designations',
                    'categories',
                    'otherCategories',
                    'loggingCategories',
                    'dontKnowCategories',
                    'reportingCategories',
                    'interactionNoCategories'
                )
            );
        }
        return abort(401);
    }

    /**
     * @param Object $obj
     *
     * @return array
     */
    private function getOptionForDetail($obj)
    {
        return is_object($obj) ? [$obj->id => $obj->name] : [];
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param integer $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateImpactRequest $request, $id)
    {
        $user = Auth::user();

        $history_types = config('settings.history_types');
        $phone = Villager::select('id')->where('device_imei', $request['device_imei'])->first();
        if (empty($phone)) {
            return redirect(route('villager.create'))->with('error', Lang::get('preylang.impact.deviceId.notFound'));
        } else {
            // 1. update to impact
            $impact = $oldImpact = Impact::find($id);
            if ($user->can('updateAsSuperDataManager', Impact::class) || $user->can('updateByGroup', $impact)) {
                $excluded = $request['excluded'] === '1' ? 1 : 0;
                $request['modified'] = true;
                $request['by_track'] = $request['by_track'] === null ? 0 : $request['by_track'];
                $request['by_audio'] = $request['by_audio'] === null ? 0 : $request['by_audio'];
                $request['by_visual'] = $request['by_visual'] === null ? 0 : $request['by_visual'];
                $request['burned_wood'] = $request['burned_wood'] === null ? 0 : $request['burned_wood'];
                $request['agreement'] = $request['agreement'] === null ? 0 : $request['agreement'];

                $newCategories = CategoryHelper::getCategories([
                    'category' => $request['category'],
                    'sub_category_1' => $this->getCategoryValue($request, 'sub_category_1'),
                    'sub_category_2' => $this->getCategoryValue($request, 'sub_category_2'),
                    'sub_category_3' => $this->getCategoryValue($request, 'sub_category_3'),
                    'sub_category_4' => $this->getCategoryValue($request, 'sub_category_4'),
                    'sub_category_5' => $this->getCategoryValue($request, 'sub_category_5'),
                    'permit' => $request['permit'],
                ]);

                if (! empty(array_diff($newCategories, $oldImpact->categories))) {
                    $request['category_modified'] = true;
                }

                $impact->excluded_reason_id = null;
                if ($excluded === 1) {
                    $impact->excluded_reason_id = $request['excluded_reason_id'];
                }

                if ($request['category_id'] !== '' || $request['category_id'] !== null) {
                    $request['category_id'] = $oldImpact->category_id;
                }

                $impact->excluded = $excluded;
                $request['excluded'] = $excluded;
                $request['categories'] = $newCategories;
                $modifiedData = $this->getModifyData($request, $oldImpact);
                $impact->modified = !empty($modifiedData);
                $impact->createUpdateRecord($request);

                // 2. save to edit_history
                if (!empty($modifiedData)) {
                    $modifiedData = serialize($modifiedData);
                    $editHistory = new EditHistory();
                    $editHistory->impact_id = $id;
                    $editHistory->user_id = Auth::id();
                    $editHistory->field_list = $modifiedData;
                    $editHistory->value_list = $modifiedData;
                    $editHistory->type = $history_types['impact'];
                    $editHistory->save();
                }

                // 3. redirect to index
                return redirect(route('impact.index'))->with('success', Lang::get('preylang.updateSuccess'));
            }
        }
        return abort(401);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param string $field
     *
     * @return mixed
     */
    private function getCategoryValue($request, $field)
    {
        return $request['text-' . $field] ? $request['text-' . $field] : $request[$field];
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function process(Request $request)
    {
        $user = Auth::user();
        if ($user->can('updateAsSuperDataManager', Impact::class) || $user->can('process', Impact::class)) {
            $action = $request['action'];
            $ids = $request['ids'];
            switch ($action) {
                case 'exclude':
                case 'include':
                    $excluded = ($action === 'exclude') ? '1' : '0';
                    $dataUpdate['excluded'] = $excluded;
                    if ($excluded === '1') {
                        $excludedReason = Setting::select('id')
                            ->where('type', 'excludedReason')
                            ->where('sys_value', 'testingEntry')->first();
                        $dataUpdate['excluded_reason_id'] = $excludedReason->id;
                    }
                    $dataUpdate['modified'] = true;

                    DB::table('impacts')->whereIn('id', $ids)->update($dataUpdate);
                    $impacts = Impact::whereIn('id', $ids)->get();
                    $impacts->searchable();
                    return redirect(route('impact.index'))->with('success', Lang::get('preylang.updateSuccess'));
                case 'export-to-Excel':
                    return $this->exportAndDownload($request, 'Excel', $ids);
                case 'export-to-CSV':
                    return $this->exportAndDownload($request, 'CSV', $ids);
                default:
                    return;
            }
        }
        return abort(401);
    }

    /**
     * @param integer $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        $user = Auth::user();
        $impact = Impact::find($id);
        if (!$impact instanceof Impact) {
            return abort(404);
        }
        if ($user->can('deleteAsSuperDataManager', Impact::class) || $user->can('deleteByGroup', $impact)) {
            $impact->active = 0;
            $impact->save();
            return redirect(route('impact.index'))->with('success', Lang::get('preylang.deleteSuccess'));
        }
        return abort(401);
    }

    /**
     * @param integer $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        $user = Auth::user();
        /* @var RawImpact $rawImpact */
        $impact = Impact::find($id);
        $rawImpact = RawImpact::find($impact->raw_impact_id);
        if (!$rawImpact) {
            return redirect(route('impact.index'))->with('Error', Lang::get('preylang.invalidImpact'));
        } else {
            if ($user->can('restoreAsSuperDataManager', Impact::class) || $user->can('restoreByGroup', $impact)) {
                $impact->categories = $rawImpact->categories;
                $impact->number_of_items = $rawImpact->number_of_items;
                $impact->by_visual = $rawImpact->by_visual;
                $impact->by_audio = $rawImpact->by_audio;
                $impact->by_track = $rawImpact->by_track;
                $impact->report_to = $rawImpact->report_to;
                $impact->patroller_note = $rawImpact->patroller_note;
                $impact->modified = false;
                $impact->category_modified = false;
                $impact->save();
                return redirect(route('impact.index'))->with('success', Lang::get('preylang.impact.restoreSuccess'));
            }
            return abort(401);
        }
    }

    /**
     * @param Request $request
     * @param Impact $impact
     *
     * @return array
     */
    private function getModifyData($request, $impact)
    {
        $modifyList = array();
        if ($request['note'] != $impact->note) {
            $modifyList[] = array(
                'field' => 'note',
                'newValue' => $request['note'],
                'oldValue' => $impact->note
            );
        }
        if ($request['categories'] != $impact->categories) {
            $changes = array_diff($request['categories'], $impact->categories);
            $fieldMappings = Config::get('settings.category_field_mapping');
            foreach ($changes as $field => $value) {
                $newValue = Category::find($value);
                $oldValue = Category::find($impact->categories[$field]);
                $modifyList[] = array(
                    'field' => $fieldMappings[$field],
                    'newValue' => $newValue ? $newValue->name : $value,
                    'oldValue' => $oldValue ? $oldValue->name : $value,
                );
            }
        }
        if ($request['note_kh'] != $impact->note_kh) {
            $modifyList[] = array(
                'field' => 'noteKh',
                'newValue' => $request['note_kh'],
                'oldValue' => $impact->note_kh
            );
        }
        if ($request['patroller_note'] != $impact->patroller_note) {
            $modifyList[] = array(
                'field' => 'patrollerNote',
                'newValue' => $request['patroller_note'],
                'oldValue' => $impact->patroller_note
            );
        }
        if ($request['name'] != $impact->name) {
            $modifyList[] = array(
                'field' => 'name',
                'newValue' => $request['name'],
                'oldValue' => $impact->name
            );
        }
        if ($request['employer'] != $impact->employer) {
            $modifyList[] = array(
                'field' => 'employer',
                'newValue' => $request['employer'],
                'oldValue' => $impact->employer
            );
        }
        if ($request['license'] != $impact->license) {
            $modifyList[] = array(
                'field' => 'license',
                'newValue' => $request['license'],
                'oldValue' => $impact->license
            );
        }
        if ((int)$request['agreement'] !== (int)$impact->agreement) {
            $modifyList[] = array(
                'field' => 'agreement',
                'newValue' => $request['agreement'],
                'oldValue' => $impact->agreement
            );
        }
        if ((int)$request['by_visual'] !== (int)$impact->by_visual) {
            $modifyList[] = array(
                'field' => 'byVisual',
                'newValue' => $request['by_visual'],
                'oldValue' => $impact->byVisual
            );
        }
        if ((int)$request['by_audio'] !== (int)$impact->by_audio) {
            $modifyList[] = array(
                'field' => 'byAudio',
                'newValue' => $request['by_audio'],
                'oldValue' => $impact->byAudio
            );
        }
        if ((int)$request['by_track'] !== (int)$impact->by_track) {
            $modifyList[] = array(
                'field' => 'byTrack',
                'newValue' => $request['by_track'],
                'oldValue' => $impact->byTrack
            );
        }
        if ((int)$request['burned_wood'] !== (int)$impact->burned_wood) {
            $modifyList[] = array(
                'field' => 'burnedWood',
                'newValue' => $request['burned_wood'],
                'oldValue' => $impact->burned_wood
            );
        }
        if ($request['report_date'] !== $impact->report_date) {
            $modifyList[] = array(
                'field' => 'reportDate',
                'newValue' => $request['report_date'],
                'oldValue' => $impact->report_date
            );
        }
        if ($request['report_to'] != $impact->report_to) {
            $modifyList[] = array(
                'field' => 'reportTo',
                'newValue' => $request['report_to'],
                'oldValue' => $impact->report_to
            );
        }
        if ((int)$request['excluded'] !== (int)$impact->excluded) {
            $modifyList[] = array(
                'field' => 'excluded',
                'newValue' => $request['excluded'],
                'oldValue' => $impact->excluded
            );
        }

        if ((int)$request['excluded_reason_id'] !== (int)$impact->excluded_reason_id
            && (int)$request['excluded'] === 1
        ) {
            $modifyList[] = array(
                'field' => 'excludedReason',
                'newValue' => $request['excluded_reason_id'],
                'oldValue' => $impact->excluded_reason_id
            );
        }

        return $modifyList;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function export(Request $request)
    {
        $oldEmail = $request['email'];
        if (is_null($oldEmail)) {
            $oldEmail = '';
        }
        $request->session()->put('old_email', $oldEmail);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', Lang::get('preylang.impact.invalidEmail'))->withErrors(['email' => Lang::get('preylang.impact.invalidEmail')]);
        }

        $totalImpacts = $request->session()->get('total_impacts');
        if (!is_null($totalImpacts)) {
            if ($totalImpacts === 0) {
                return redirect()->back()->with('error', Lang::get('preylang.impact.no.records'));
            }
        }
        $email = $request['email'];
        $ids = $request['ids'];
        if ($request->has('type_CSV')) {
            $type = 'csv';
        } elseif ($request->has('type_Excel')) {
            $type = 'excel';
        } else {
            $type = 'csv';
        }
        // Timeout is in seconds.
        return $this->exportAndDownload($request, $type, $ids, $email);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param string $type
     * @param array $ids
     * @param string $email
     *
     * @return mixed
     */
    private function exportAndDownload($request, $type, $ids, $email = null)
    {
        $user = Auth::user();
        if (!$email) {
            $email = $user->email;
        }
        GenerateExport::dispatch($request->filter, $user, $email, $type, $ids);
        return redirect()->back()->with(
            [
                'success' => Lang::get('preylang.flash.message.exportFile.sendMail', ['email' => $email])
            ]
        );
    }
}
