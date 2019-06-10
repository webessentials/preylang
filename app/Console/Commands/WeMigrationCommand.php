<?php

namespace App\Console\Commands;

use App\Helpers\CategoryHelper;
use App\Helpers\ImpactHelper;
use App\Models\EditHistory;
use App\Models\File;
use App\Models\Impact;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\Villager;
use Illuminate\Console\Command;
use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery\Exception;

class WeMigrationCommand extends Command
{
    use DetectsApplicationNamespace;

    /**
     * @var string
     */
    protected $oldDatabase;

    /**
     * @var string
     */
    protected $newDatabase;

    /**
     * @var string
     */
    protected $defaultPassword;

    /**
     * @var array
     */
    protected $types = [
        'villager',
        'user',
        'impact',
        'file',
        'edit_history',
    ];

    /**
     * @var string
     */
    protected $type;

    /**
     * @var integer
     */
    protected $limitRecords = '';

    /**
     * @var integer
     */
    protected $defaultUserGroupId;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'we:migrate
                                {--all} {--only} {--limit=all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Migrate data from PreyLang's old database. Options: --user, --impact";

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->oldDatabase = config('database.connections.mysql2.database');
        $this->newDatabase = config('database.connections.mysql.database');
        $this->defaultPassword = bcrypt(env('DEFAULT_MIGRATION_USER_PASSWORD'));
    }

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $choices = [];
        if ($this->option('only')) {
            $choices = $this->choice('What do you want to import? (separate by comma)', $this->types, null, null, true);
        }

        $limit = $this->option('limit');
        if (is_null($limit)) {
            $this->limitRecords = ' LIMIT 500';
        } elseif ((int)$limit > 0) {
            $this->limitRecords = ' LIMIT ' . $limit;
        }

        $this->addTemporaryFields();
        try {
            // Create a default user group.
            $userGroupData = config('settings.default_user_group');
            $this->defaultUserGroupId = UserGroup::firstOrcreate(['name' => $userGroupData['name']], $userGroupData)->id;

            foreach ($this->types as $type) {
                if ($this->option('all') || in_array($type, $choices)) {
                    $this->migrateData($type);
                }
            }
        } catch (Exception $exception) {
            $this->removeTemporaryFields();
        }
        $this->removeTemporaryFields();
    }

    /**
     * Add temporary fields
     * @return void
     */
    private function addTemporaryFields()
    {
        if (!Schema::hasColumn('settings', 'persistence_object_identifier')) {
            DB::connection('mysql')->statement('ALTER TABLE settings ADD persistence_object_identifier VARCHAR(40);');
        }
        if (!Schema::hasColumn('categories', 'persistence_object_identifier')) {
            DB::connection('mysql')->statement('ALTER TABLE categories ADD persistence_object_identifier VARCHAR(40);');
        }
    }

    /**
     * Remove temporary fields
     * @return void
     */
    private function removeTemporaryFields()
    {
        DB::connection('mysql')->statement('ALTER TABLE settings DROP COLUMN persistence_object_identifier;');
        DB::connection('mysql')->statement('ALTER TABLE categories DROP COLUMN persistence_object_identifier;');
    }

    /**
     * @param string $type
     * @return void
     */
    private function migrateData($type)
    {
        $typeLabel = ucfirst(str_replace('_', ' ', $type));
        $countNewRecords = 0;
        $this->type = $type;
        $sqlOldData = $this->generateSqlOldData();

        $limitRecords = (int)str_replace(' LIMIT ', '', $this->limitRecords);
        $totalRecords = $this->countOldData($sqlOldData);
        $totalRecords = $limitRecords ? min($limitRecords, $totalRecords) : $totalRecords;
        if ($totalRecords === 0) {
            $this->info('No ' . $typeLabel . ' data to migrate');
            $this->line('----------------');

            return;
        }

        $this->info($typeLabel . ' migration started: ...');
        $bar = $this->output->createProgressBar($totalRecords);
        $bar->start();

        $limit = $limitRecords ?: 2000;
        for ($offset = 0; $offset < $totalRecords; $offset += $limit) {
            $records = $this->getOldData($sqlOldData, $limit, $offset);
            foreach ($records as $record) {
                if ($this->isRecordNotFound($record)) {
                    $countNewRecords += (int)$this->importData((array)$record);
                }
                $bar->advance();
            }
        }
        $bar->finish();

        $this->info('');
        $this->info($typeLabel . ' Migration Completed. ' . $countNewRecords . ' new ' . $type . '(s) added, and ' . ($totalRecords - $countNewRecords) . ' existing ' . $type . '(s) found.');
        $this->line('----------------');
    }

    /**
     * @param array $generatedSql
     *
     * @return int
     */
    private function countOldData($generatedSql)
    {
        $select = $generatedSql['select'];
        $from = $generatedSql['from'];

        if (empty($select)) {
            return 0;
        }

        $sql = 'SELECT COUNT(*) AS count ' . $from;
        $result = DB::connection('mysql2')->selectOne($sql);

        return $result->count;
    }

    /**
     * Get Old data according to types
     *
     * @param array $generatedSql
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    private function getOldData($generatedSql, $limit = 1000, $offset = 0)
    {
        $select = $generatedSql['select'];
        $from = $generatedSql['from'];

        if (empty($select)) {
            return [];
        }

        $sql = $select . ' ' . $from;
        $sql .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;

        return DB::connection('mysql2')->select($sql);
    }

    /**
     * Get Old data according to types
     *
     * @return array
     */
    private function generateSqlOldData()
    {
        $select = '';
        $from = '';
        switch ($this->type) {
            case 'user':
                $select = 'SELECT a.accountidentifier as username,
                            n.firstname as first_name,
                            n.lastname as last_name,
                            u.email,
                            u.active,
                            IF(u.languagekey IS NULL or u.languagekey = "", "en", u.languagekey) as language_key,
                            ph.deviceid as device_imei,
                            u.persistence_object_identifier,
                            r.flow_policy_role as role';
                $from = 'FROM typo3_flow_security_account as a
                            INNER JOIN prey_lang_domain_model_user as u
                                ON a.party = u.persistence_object_identifier
                            INNER JOIN typo3_flow_security_account_roles_join as r
                                ON a.persistence_object_identifier = r.flow_security_account
                            INNER JOIN typo3_party_domain_model_person as p
                                ON u.persistence_object_identifier = p.persistence_object_identifier
                            INNER JOIN typo3_party_domain_model_personname as n
                                ON p.name = n.persistence_object_identifier
                            LEFT JOIN prey_lang_domain_model_phone ph
                                ON u.phoneid = ph.persistence_object_identifier';
                break;
            case 'villager':
                $this->settingMigration();
                $records = Setting::select('sys_value')->where('type', 'province')->get();
                $provinces = $records->implode('sys_value', '","');

                $select = 'SELECT deviceid as device_imei,
                            villagerid as name,
                            province,
                            password,
                            accesstoken as access_token,
                            tokenexpirationdate as token_expiration_date';
                $from = 'FROM prey_lang_domain_model_phone
                        WHERE province in ("' . $provinces . '")';
                break;
            case 'impact':
                // Must import Raw Impacts.
                CategoryHelper::importCategories();
                $this->migrateRawImpacts();
                $select = 'SELECT i.persistence_object_identifier, i.numberofitems as number_of_items, i.name,
                            i.employer, i.license, i.agreement, i.byvisual as by_visual, i.createdat as created_at,
                            i.byaudio as by_audio, i.bytrack as by_track, i.burnedwood as burned_wood,
                            i.reportto as report_to, i.reportdate as report_date, i.active, i.note,
                            i.notekh as note_kh, i.patrollernote as patroller_note, i.latitude, i.longitude,
                            i.ismodified as modified, i.excluded, i.excludedreason as excluded_reason,
                            i.excludednote as excluded_note, i.impactnumber as impact_number,
                            i.phoneid as device_imei, i.categorymodified as category_modified, i.offender,
                            i.threatening, i.reason, i.designation, i.victimtype as victim_type, i.proof,
                            i.modifiedat as updated_at, i.witness, i.location';
                $from = 'FROM prey_lang_domain_model_impact i
                        ORDER BY i.impactnumber ASC';
                break;
            case 'file':
                $select = 'SELECT persistence_object_identifier, filename as file_name, filetype as file_type,
                            impact, isimported as is_imported, importdate as import_date, facebookpost as facebook_post,
                            latitude, longitude, reportdate as report_date, originalfile as original_file_name,
                            converted, convertedat as converted_at';
                $from = 'FROM prey_lang_domain_model_file';
                break;
            case 'edit_history':
                $select = 'SELECT t.persistence_object_identifier, t.impact, t.user as user_id,
                            t.fieldlist as field_list, t.valuelist as value_list, t.modifydate as updated_at';
                $from = 'FROM prey_lang_domain_model_trackingimpact t';
                break;
            default:
                break;
        }

        return [
            'select' => $select,
            'from' => $from
        ];
    }

    /**
     * @param object $record
     *
     * @return bool
     */
    private function isRecordNotFound($record)
    {
        switch ($this->type) {
            case 'impact':
                $record = Impact::where(
                    'persistence_object_identifier',
                    '=',
                    $record->persistence_object_identifier
                )->first();
                break;
            default:
                $record = null;
                break;
        }

        return is_null($record);
    }

    /**
     * @param array $record
     *
     * @return bool wasRecentlyCreated
     */
    private function importData($record)
    {
        switch ($this->type) {
            case 'user':
                $userRoles = config('settings.user_roles_mappings');
                $record['first_name'] = $record['first_name'] !== '...' ? $record['first_name'] : '';
                $record['last_name'] = $record['last_name'] !== '...' ? $record['last_name'] : '';
                $record['role'] = $userRoles[$record['role']];
                $record['password'] = $this->defaultPassword;
                $record['villager_id'] = ImpactHelper::getVillagerId($record['device_imei']);
                $userSuperRoles = config('settings.user_super_roles');
                if (!in_array($record['role'], $userSuperRoles)) {
                    $record['user_group_id'] = $this->defaultUserGroupId;
                }
                unset($record['phoneid']);
                $newRecord = User::withTrashed()->firstOrCreate(
                    ['persistence_object_identifier' => $record['persistence_object_identifier']],
                    $record
                );
                break;
            case 'villager':
                $province = Setting::where('type', 'province')->where('sys_value', $record['province'])->first();
                $record['province_id'] = $province ? $province->id : null;
                $record['user_group_id'] = $this->defaultUserGroupId;
                $newRecord = Villager::firstOrCreate(['device_imei' => $record['device_imei']], $record);
                break;
            case 'impact':
                $newRecord = ImpactHelper::saveImpact($record, true);
                break;
            case 'file':
                $record['impact_id'] = ImpactHelper::getImpactId($record['impact'], true);
                $newRecord = File::firstOrCreate(
                    ['persistence_object_identifier' => $record['persistence_object_identifier']],
                    $record
                );
                break;
            case 'edit_history':
                $history_types = config('settings.history_types');
                $record['user_id'] = ImpactHelper::getUserIdByIdentifier($record['user_id']);
                $record['impact_id'] = ImpactHelper::getImpactId($record['impact'], true);
                $record['type'] = $history_types['impact'];
                $newRecord = EditHistory::firstOrNew(
                    ['persistence_object_identifier' => $record['persistence_object_identifier']],
                    $record
                );
                EditHistory::withoutSyncingToSearch(function () use ($newRecord) {
                    $newRecord->save();
                });
                break;
            default:
                break;
        }

        return isset($newRecord) && $newRecord->wasRecentlyCreated;
    }

    /**
     * Migrate setting
     * @return void
     */
    private function settingMigration()
    {
        $types = Config::get('settings.setting_types');
        foreach ($types as $type) {
            $this->migrateSettingData($type);
        }
    }

    /**
     * Migrate to setting type to one setting table
     *
     * @param string $type
     *
     * @return bool
     */
    private function migrateSettingData($type)
    {
        switch ($type) {
            case 'province':
                $settings = DB::connection('mysql2')->select('SELECT persistence_object_identifier, name,
                                            namekm as name_kh, provinceid as sys_value
                                        FROM prey_lang_domain_model_' . strtolower($type));
                break;
            case 'excludedReason':
                $settings = DB::connection('mysql2')->select('SELECT persistence_object_identifier, name,
                                            sysvalue as sys_value, sorting as sorting
                                        FROM prey_lang_domain_model_excludereason');
                break;
            default:
                $settings = DB::connection('mysql2')->select('SELECT persistence_object_identifier, name,
                                            sysvalue as sys_value, defaulted as read_only
                                        FROM prey_lang_domain_model_' . strtolower($type));
                break;
        }

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['name' => $setting->name, 'sys_value' => $setting->sys_value, 'type' => $type],
                (array)$setting
            );
        }

        return true;
    }

    /**
     * Migrate Raw Impacts before migrating impacts
     * @return void
     */
    private function migrateRawImpacts()
    {
        // Use INNER JOIN because some raw impacts does not relate to impact.
        $oldRawImpacts = DB::connection('mysql2')->select('SELECT r.impactnumber as id, r.impact, p.deviceid as device_imei,
                                        c.category, c.subcategory1 as sub_category_1, c.subcategory2 as sub_category_2,
                                        c.subcategory3 as sub_category_3, c.subcategory4 as sub_category_4,
                                        c.subcategory5 as sub_category_5, c.permit, r.numberofitems as number_of_items,
                                        r.byvisual as by_visual, r.byaudio as by_audio, r.bytrack as by_track,
                                        r.reportto as report_to, r.reportdate as report_date, r.note, r.latitude,
                                        r.createdat as created_at,
                                        r.longitude, r.patrollernote as patroller_note
                                    FROM prey_lang_domain_model_rawimpact r
                                        INNER JOIN prey_lang_domain_model_phone p
                                            ON r.phoneid = p.persistence_object_identifier
                                        INNER JOIN prey_lang_domain_model_categoryconfidential c
                                            ON r.categoryconfidential = c.persistence_object_identifier
                                    ORDER BY impactnumber ASC' . $this->limitRecords);
        $totalRecords = count($oldRawImpacts);
        $rawImpactBar = $this->output->createProgressBar($totalRecords);
        $this->info('Migrating Raw Impacts');
        $rawImpactBar->start();
        foreach ($oldRawImpacts as $oldRawImpact) {
            ImpactHelper::saveRawImpact((array)$oldRawImpact, true);
            $rawImpactBar->advance();
        }
        $rawImpactBar->finish();
        $this->info('');
    }
}
